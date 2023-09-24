<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatsController;
use App\Http\Controllers\IdeasController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\PlannerController;
use App\Models\Discussion;

Route::post("/login", [AuthController::class, "login"]);
Route::post("/register", [AuthController::class, "register"]);

Route::group(["middleware" => "auth:api"], function () {
    Route::get("/logout", [AuthController::class, "logout"]);
    Route::get('/getUserCollections', [IdeasController::class, 'getUserCollections']);
    Route::get('/getIdeas/{ideaId?}', [IdeasController::class, 'getIdeas']);
    Route::post('/likeIdea/{ideaId}', [IdeasController::class, 'likeIdea']);
    Route::post('/createCollection/{collectionId?}', [IdeasController::class, 'createCollection']);
    Route::post('/addIdea/{collectionId}/{ideaId?}', [IdeasController::class, 'addIdea']);
    Route::get('/deleteIdea/{ideaId}', [IdeasController::class, 'deleteIdea']);
    Route::get('/deleteCollection/{collectionId}', [IdeasController::class, 'deleteCollection']);
    Route::post('/search', [IdeasController::class, 'search']);

    Route::post('/addResource/text/{ideaId}', [MapController::class, 'addText']);
    Route::post('/addResource/file/{ideaId}', [MapController::class, 'addFile']);
    Route::post('/updateScreenshot/{ideaId}', [MapController::class, 'updateScreenshot']);
    Route::get('/getUsers', [MapController::class, 'getUsers']);

    Route::post('/createMeeting/{ideaId}', [PlannerController::class, 'createMeeting']);
    Route::get('/getDate', [PlannerController::class, 'getDate']);

    Route::post('/createDiscussion/{ideaId}', [ChatsController::class, 'createDiscussion']);
    Route::get('/getUserDiscussions', [ChatsController::class, 'getUserDiscussions']);
    Route::get('/exitDiscussion/{discussionId}', [ChatsController::class, 'exitDiscussion']);

});
