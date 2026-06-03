<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostService
{
    public function index()
    {
        return Post::where('is_published', true)
            ->with('user:id,name')
            ->latest()
            ->paginate(10);
    }

    public function store(array $data): Post
    {
        return Auth::user()->posts()->create($data);
    }

    public function show(Post $post): Post
    {
        if (!$post->is_published && Auth::id() !== $post->user_id) {
            abort(404, 'Статья не найдена');
        }
        return $post->load('user:id,name');
    }

    public function update(Post $post, array $data): Post
    {
        if (Auth::id() !== $post->user_id) {
            abort(403, 'Нет прав на редактирование этой статьи');
        }

        $post->update($data);

        return $post->load('user:id,name,email');
    }

    public function destroy(Post $post): void
    {
        if (Auth::id() !== $post->user_id) {
            abort(403, 'Нет прав на удаление этой статьи');
        }
        $post->delete();
    }
}
