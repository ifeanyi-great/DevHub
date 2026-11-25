<?php

use App\Http\Controllers\Api\CommentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PostController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{post}', [PostController::class, 'show']);

Route::get('/posts/{post}/comments', [CommentController::class, 'index'])
    ->scopeBindings();

 Route::post('/posts/{post}/comments', [CommentController::class, 'store'])
      ->middleware('auth:sanctum')   
      ->scopeBindings();

 Route::patch('posts/{post}/comments/{comment}', [CommentController::class, 'update'])
        ->middleware('auth:sanctum')     
        ->scopeBindings();
   
Route::delete('posts/{post}/comments/{comment}', [CommentController::class, 'destroy'])
         ->middleware('auth:sanctum')
         ->scopeBindings();
        