<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class PostService
{
    public function index(array $filters = [])
    {
        $query = Post::where('is_published', true)
            ->with('user:id,name,email');

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $sortMap = [
            'date_asc'   => ['created_at', 'asc'],
            'date_desc'  => ['created_at', 'desc'],
            'title_asc'  => ['title', 'asc'],
            'title_desc' => ['title', 'desc'],
        ];

        $sortKey = $filters['sort'] ?? 'date_desc';
        [$column, $direction] = $sortMap[$sortKey] ?? $sortMap['date_desc'];
        
        $query->orderBy($column, $direction);

        return $query->paginate(10);
    }

    public function myPosts(int $userId, array $filters = [])
    {
        $query = Post::where('user_id', $userId)
            ->with('user:id,name,email'); 

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        $sortMap = [
            'date_asc'   => ['created_at', 'asc'],
            'date_desc'  => ['created_at', 'desc'],
            'title_asc'  => ['title', 'asc'],
            'title_desc' => ['title', 'desc'],
        ];

        $sortKey = $filters['sort'] ?? 'date_desc';
        [$column, $direction] = $sortMap[$sortKey] ?? $sortMap['date_desc'];
        
        $query->orderBy($column, $direction);

        return $query->paginate(10);
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
        return $post->load('user:id,name,email');
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