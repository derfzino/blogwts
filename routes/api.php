<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;

Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/login', [AuthController::class, 'login'])->name('auth.login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(Request $r) => $r->user())->name('auth.user');
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
    
    Route::get('/my-posts', [PostController::class, 'myPosts'])->name('posts.my');
     
    Route::apiResource('posts', PostController::class)->only(['store', 'update', 'destroy']);
});

Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');