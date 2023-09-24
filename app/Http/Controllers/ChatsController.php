<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Discussion;
use App\Models\User;
use App\Models\Message;

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

            $discussion->users()->attach([$user->id]);
            $discussion->users()->attach($validatedData['members']);

            return response()->json(['message' => 'Discussion created successfully'], 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getUserDiscussions()
    {
        $user = Auth::user();
        $discussions = User::find($user->id)->discussions()->with('idea', 'users')->get();

        return response()->json([
            "discussions" => $discussions,
            "authUser" => $user->name
        ]);
    }

    public function sendMessage(Request $request, $discussionId)
    {
        $user = Auth::user();

        try {
            $validatedData = $request->validate([
                'text' => 'required|string',
            ]);

            $message = new Message();
            $message->discussion_id = $discussionId;
            $message->user_id = $user->id;
            $message->text = $validatedData['text'];
            $message->save();

            return response()->json($message);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    function exitDiscussion($discussionId)
    {
        try {
            $user = Auth::user();
            $discussion = Discussion::find($discussionId);
            $discussion->users()->detach([$user->id]);
            $discussion->delete();
            return response()->json($discussion);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
