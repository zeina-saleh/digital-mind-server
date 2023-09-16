<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Collection;
use App\Models\Idea;
use App\Models\Like;
use App\Models\TextResource;
use App\Models\FileResource;

class MapController extends Controller
{
    public function getUserCollections()
    {
        $user = Auth::user();

        $collections = Collection::with(['ideas'])->where('user_id', $user->id)->get();
        return response()->json($collections);
    }

    public function getIdeas($ideaId = null)
    {
        $user = Auth::user();

        if ($ideaId) {
            $idea = Idea::find($ideaId);
            return response()->json(['idea' => $idea]);
        } else {
            $ideas = Idea::withCount('likes')->with(['collection.user'])->get();
            foreach ($ideas as $idea) {
                $existingLike = Like::where('user_id', $user->id)->where('idea_id', $idea->id)->first();

                $idea->liked = !is_null($existingLike);
            }
            return response()->json($ideas);
        }
    }

    public function likeIdea(Request $request, $ideaId)
    {
        $user = Auth::user();
        $idea = Idea::findOrFail($ideaId);

        $existingLike = Like::where('user_id', $user->id)->where('idea_id', $idea->id)->first();

        if ($existingLike) {
            $existingLike->delete();
            $idea = Idea::withCount('likes')->where('id', $ideaId)->get();
            return response()->json([
                'idea' => $idea,
                'liked' => false
            ]);
        }

        $like = new Like();
        $like->user_id = $user->id;
        $like->idea_id = $idea->id;
        $like->save();

        $idea = Idea::withCount('likes')->where('id', $ideaId)->get();

        return response()->json([
            'idea' => $idea,
            'liked' => true
        ]);
    }

    function createCollection(Request $request, $collectionId = null)
    {
        $user = Auth::user();
        try {
            $validatedData = $request->validate([
                'title' => 'required|string'
            ]);

            if ($collectionId) {
                $collection = Collection::find($collectionId);
            } else {
                $collection = new Collection;
            }

            $collection->user_id = $user->id;
            $collection->title = $validatedData['title'];
            $collection->save();
            return response()->json(['collection' => $collection], 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    function addIdea(Request $request, $collectionId)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string'
            ]);
            $idea = new Idea();
            $idea->collection_id = $collectionId;
            $idea->title = $validatedData['title'];
            $idea->save();
            return response()->json(['message' => 'Idea added successfully', 'idea' => $idea], 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    function deleteIdea($ideaId)
    {
        try {
            $idea = Idea::find($ideaId);
            $idea->delete();
            return response()->json(['message' => 'Idea deleted successfully', 'idea' => $idea], 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    function deleteCollection($collectionId)
    {
        try {
            $collection = Collection::find($collectionId);
            $collection->delete();
            return response()->json(['message' => 'Collection deleted successfully', 'collection' => $collection], 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    function addText(Request $request, $ideaId)
    {
        try {
            $validatedData = $request->validate([
                'text' => 'required|string',
                'caption' => 'nullable|string',
                'type_id' => 'required|exists:types,id',
            ]);
            $resource = new TextResource();
            $resource->idea_id = $ideaId;
            $resource->type_id = $validatedData['type_id'];
            $resource->text = $validatedData['text'];
            if ($validatedData['caption']) {
                $resource->caption = $validatedData['caption'];
            }
            $resource->save();
            return response()->json(['message' => 'Text resource added successfully', 'resource' => $resource], 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    function addFile(Request $request, $ideaId)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:jpeg,png,pdf|max:2048',
                'caption' => 'nullable|string',
                'type_id' => 'required|exists:types,id',
            ]);

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = time() . '_' . $file->getClientOriginalName();
                if ($request->type_id == 3) $file->storeAs('images', $filename);
                else $file->storeAs('documents', $filename);

                $resource = new FileResource();
                $resource->idea_id = $ideaId;
                $resource->type_id = $request->type_id;
                $resource->caption = $request->caption;
                $resource->path = $filename;
                $resource->save();
                return response()->json(['message' => 'File resource added successfully', 'resource' => $resource], 200);
            }
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
