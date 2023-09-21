<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Discussion;

class ChatsController extends Controller
{
    function createDiscussion(Request $request, $ideaId)
    {
        $user = Auth::user();
        try {
            $validatedData = $request->validate([
                'title' => 'required|string',
                'members' => 'required|array',
            ]);

            $discussion = new Discussion();
            $discussion->idea_id = $ideaId;
            $discussion->title = $validatedData['title'];
            $discussion->save();

            $discussion->users()->attach($validatedData['members']);

            return response()->json(['message' => 'Discussion created successfully'], 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
