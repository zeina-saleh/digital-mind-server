<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Models\Collection;
use App\Models\Idea;
use App\Models\Like;
use App\Models\TextResource;
use App\Models\FileResource;
use App\Models\User;

class MapController extends Controller
{
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
                $storagePath = $request->type_id == 3 ? 'images' : 'documents';

                $file->storeAs($storagePath, $filename, 'public');

                $resource = new FileResource();
                $resource->idea_id = $ideaId;
                $resource->type_id = $request->type_id;
                $resource->caption = $request->caption;
                $resource->path = 'storage/' . $storagePath . '/' . $filename;
                $resource->save();

                return response()->json(['message' => 'File resource added successfully', 'resource' => $resource], 200);
            }
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    function updateScreenshot(Request $request, $ideaId)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:jpeg,png|max:2048',
            ]);
            $previousScreenshot = Idea::find($ideaId);

            if ($previousScreenshot) {
                $path = $previousScreenshot->path;
                if ($path != 'storage/images/logo.svg') {
                    File::delete($path);
                }
            }

            if ($request->hasFile('file')) {
                $file = $request->file('file');
                $filename = time() . '_' . $file->getClientOriginalName();
                $storagePath = 'screenshots';
                $file->storeAs($storagePath, $filename, 'public');
                Idea::where('id', $ideaId)->update(['path' => 'storage/' . $storagePath . '/' . $filename]);
                return response()->json(['message' => 'Screenshot updated successfully'], 200);
            }
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    function getUsers()
    {
        $user = Auth::user();

        $users = User::where('id', '!=', $user->id)->get();
        return response()->json($users);
    }
}
