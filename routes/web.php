<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\BlockPostCreation;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::get('/dashboard', function (Request $request) {
dd($request->user());
})->middleware('auth');

Route::get('/posts', [PostController::class, 'index']);

Route::get('/posts/create', [PostController::class, 'create']);         

Route::post('/posts', [PostController::class, 'store'])->middleware(['auth',BlockPostCreation::class]);

Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show'); 

Route::get('/login', [AuthController::class,'create'])->name('login');
Route::post('/login', [AuthController::class,'store']);

Route::get('/posts/{post}/edit', [PostController::class,'edit'])->middleware('auth');
Route::put('/posts/{post}', [PostController::class,'update'])->middleware('auth');

Route::delete('/posts/{post}', [PostController::class,'destroy'])->middleware('auth');

Route::post('posts/{post}/comments/', [CommentController::class, 'store'])->middleware('auth');

Route::get('/posts/{post}/comments/{comment}/edit', [CommentController::class,'edit'])->name('comments.edit')->middleware('auth');
Route::put('/posts/{post}/comments/{comment}', [CommentController::class,'update'])->name('comments.update')->middleware('auth');

Route::delete('/posts/{post}/comments/{comment}', [CommentController::class, 'destroy'])->middleware('auth');

Route::get('users/{user}', [UserController::class, 'show'])->name('users.show')->middleware('auth');
Route::put('users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('auth');    