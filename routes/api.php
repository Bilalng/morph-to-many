<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\VideoController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::group(['prefix' => 'v1'], function () {
    Route::apiResource('videos', VideoController::class)->except(['update']);
    Route::apiResource('posts', PostController::class)->except(['update']);
    Route::apiResource('comments', CommentController::class)->except(['update']);
});
