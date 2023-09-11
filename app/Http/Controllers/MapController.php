<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Collection;
use App\Models\Idea;

class MapController extends Controller
{
    public function getUserCollections()
    {
        $user = Auth::user();
        $user_id = $user->id;
        $collections = Collection::with(['ideas'])->where('user_id', $user_id)->get();
        return response()->json($collections);
    }

    
}
