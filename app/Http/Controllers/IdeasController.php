<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Models\Collection;
use App\Models\Idea;
use App\Models\Like;
use App\Models\User;

class IdeasController extends Controller
{
    public function getUserCollections()
    {
        $user = Auth::user();

        $collections = Collection::with(['ideas' => function ($query) {
            $query->withCount('likes');
        }])->where('user_id', $user->id)->get();
        return response()->json($collections);
    }

    public function getIdeas($ideaId = null)
    {
        $user = Auth::user();
        try{
        if ($ideaId) {
            $idea = Idea::with(['text_res', 'file_res'])->where('id', $ideaId)->first();
            return response()->json($idea);
        } else {
            $ideas = Idea::withCount('likes')->with(['collection.user'])->orderBy('created_at', 'desc')->paginate(4);
            foreach ($ideas as $idea) {
                $existingLike = Like::where('user_id', $user->id)->where('idea_id', $idea->id)->first();

                $idea->liked = !is_null($existingLike);
            }
            return response()->json($ideas);
            }
        }  catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function likeIdea(Request $request, $ideaId)
    {
        $user = Auth::user();
        $idea = Idea::findOrFail($ideaId);

        $existingLike = Like::where('user_id', $user->id)->where('idea_id', $idea->id)->first();

        if ($existingLike) {
            $likeId = $existingLike->id;
            $existingLike->delete();
            $idea = Idea::withCount('likes')->where('id', $ideaId)->get();
            return response()->json([
                'idea' => $idea,
                'id' => $likeId,
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
            'id' => $like->id,
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

    function addIdea(Request $request, $collectionId, $ideaId = null)
    {
        try {
            $validatedData = $request->validate([
                'title' => 'required|string'
            ]);

            if ($ideaId) {
                $idea = Idea::find($ideaId);
            } else {
                $idea = new Idea;
            }

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
            if ($idea) {
                $screenshot = $idea->path;
                if ($screenshot != 'storage/images/logo.svg') {
                    File::delete($screenshot);
                }
            }
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

    function search(Request $request)
    {
        $param = $request->input('param');

        if (empty($param)) {
            return response()->json(["empty" => '[]']);
        }

        $result = Idea::where('title', 'LIKE', '%' . $param . '%')->paginate(4);
        if ($result->count() > 0) {
            return response()->json(["idea" => $result]);
        } else {
            $userIds = User::where('name', 'LIKE', '%' . $param . '%')->pluck('id');
            $collectionIds = Collection::whereIn('user_id', $userIds)->pluck('id');
            $result2 = Idea::whereIn('collection_id', $collectionIds)->paginate(4);
            if ($result2->count() > 0) {
                return response()->json(["user_ideas" => $result2]);
            } else {
                return response()->json(['no_result' => '[]']);
            }
        }
    }
}
