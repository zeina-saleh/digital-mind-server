<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MapController;

Route::post("/login", [AuthController::class, "login"]);
Route::post("/register", [AuthController::class, "register"]);

Route::group(["middleware" => "auth:api"], function () {
    Route::get('/getUserCollections', [MapController::class, 'getUserCollections']);
    Route::get('/getIdeas', [MapController::class, 'getIdeas']);
    Route::post('/likeIdea/{ideaId}', [MapController::class, 'likeIdea']);
});