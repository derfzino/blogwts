<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::where('is_published', true)
            ->with('user:id,name')
            ->latest()
            ->paginate(10);

        return response()->json($posts);
    }

    public function store(StorePostRequest $request)
    {
        $validated = $request->validated();

        $post = Auth::user()->posts()->create($validated);

        return response()->json($post, 201);
    }

    public function show(Post $post)
    {
        if (!$post->is_published && Auth::id() !== $post->user_id) {
            abort(404, 'Статья не найдена');
        }

        return response()->json($post->load('user:id,name'));
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $validated = $request->validated();

        $post->update($validated);

        return response()->json($post);
    }
    
    public function destroy(Post $post)
    {
        if (Auth::id() !== $post->user_id) {
            abort(403, 'Нет прав на удаление этой статьи');
        }

        $post->delete();

        return response()->json(['message' => 'Статья удалена']);
    }
}
