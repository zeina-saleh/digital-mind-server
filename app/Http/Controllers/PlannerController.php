<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meeting;
use Illuminate\Support\Facades\Auth;

class PlannerController extends Controller
{
    function createMeeting(Request $request, $ideaId)
    {
        $user = Auth::user();
        try {
            $validatedData = $request->validate([
                'title' => 'required|string',
                'date' => 'required|string',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);
            $meeting = new Meeting();
            $meeting->idea_id = $ideaId;
            $meeting->user_id = $user->id;
            $meeting->title = $validatedData['title'];
            $meeting->datetime = $validatedData['date'];
            $meeting->latitude = $validatedData['latitude'];
            $meeting->longitude = $validatedData['longitude'];
            $meeting->save();
            return response()->json(['message' => 'Meeting added successfully', 'meeting' => $meeting], 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    function getDate()
    {
        $user_id = Auth::user()->id;

        $plans = Meeting::where('user_id', $user_id)->get();
        $schedule = $plans->map(function ($plan) {
            return [
                "id" => $plan->id,
                "date" => $plan->datetime,
                "time" => $plan->datetime,
                "title" => $plan->title,
                "latitude" => $plan->latitude,
                "longitude" => $plan->longitude
            ];
        });

        return response()->json(["schedule" => $schedule]);
    }
}
