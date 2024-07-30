<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::apiResource('posts', PostController::class);
Route::apiResource('tasks', TaskController::class);


Route::prefix('posts')->group(function () {
    Route::post('/{post}/comments', [CommentController::class, 'store']);
    Route::get('/{post}/comments', [CommentController::class, 'index']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
});


    Route::prefix('tasks')->group(function () {
        Route::post('/{task}/comments', [CommentController::class, 'store']);
        Route::get('/{task}/comments', [CommentController::class, 'index']);
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
    });

