<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MapController;

Route::post("/login", [AuthController::class, "login"]);
Route::post("/register", [AuthController::class, "register"]);

Route::group(["middleware" => "auth:api"], function () {
    Route::get("/logout", [AuthController::class, "logout"]);
    Route::get('/getUserCollections', [MapController::class, 'getUserCollections']);
    Route::get('/getIdeas/{ideaId?}', [MapController::class, 'getIdeas']);
    Route::post('/likeIdea/{ideaId}', [MapController::class, 'likeIdea']);
    Route::post('/createCollection/{collectionId?}', [MapController::class, 'createCollection']);
    Route::post('/addIdea/{collection_id}/{ideaId?}', [MapController::class, 'addIdea']);
    Route::get('/deleteIdea/{ideaId}', [MapController::class, 'deleteIdea']);
    Route::get('/deleteCollection/{collectionId}', [MapController::class, 'deleteCollection']);
});
