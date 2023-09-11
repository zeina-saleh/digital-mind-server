<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post("/login", [AuthController::class, "login"]);
Route::post("/register", [AuthController::class, "register"]);

Route::group(["middleware" => "auth:api"], function () {
});