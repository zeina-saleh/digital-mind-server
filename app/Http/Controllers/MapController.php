<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Collection;
use App\Models\Idea;
use App\Models\Like;

class MapController extends Controller
{
    public function getUserCollections()
    {
        $user = Auth::user();

        $collections = Collection::with(['ideas'])->where('user_id', $user->id)->get();
        return response()->json($collections);
    }

    public function getIdeas()
    {
        $user = Auth::user();

        $ideas = Idea::withCount('likes')->with(['collection.user'])->get();
        foreach ($ideas as $idea) {
            $existingLike = Like::where('user_id', $user->id)->where('idea_id', $idea->id)->first();

            $idea->liked = !is_null($existingLike);
        }
        return response()->json($ideas);
    }

    public function likeIdea(Request $request, $ideaId)
    {
        $user = Auth::user();
        $idea = Idea::findOrFail($ideaId);

        $existingLike = Like::where('user_id', $user->id)->where('idea_id', $idea->id)->first();

        if ($existingLike) {
            $existingLike->delete();
            $idea = Idea::withCount('likes')->where('id', $ideaId)->get();
            return response()->json(['idea' => $idea]);
        }

        $like = new Like();
        $like->user_id = $user->id;
        $like->idea_id = $idea->id;
        $like->save();

        $idea = Idea::withCount('likes')->where('id', $ideaId)->get();

        return response()->json(['idea' => $idea]);
    }

    function createCollection(Request $request)
    {
        $user = Auth::user();
        try {
            $validatedData = $request->validate([
                'title' => 'required|string'
            ]);
            $collection = new Collection();
            $collection->user_id = $user->id;
            $collection->title = $validatedData['title'];
            $collection->save();
            return response()->json(['message' => 'Collection added successfully', 'collection' => $collection], 200);

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
}
