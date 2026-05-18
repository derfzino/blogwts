<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::where('is_published', true)
            ->with('user:id,name')
            ->latest()
            ->paginate(10);

        return response()->json($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255|min:3',
            'body' => 'required|string|min:10',
            'is_published' => 'sometimes|boolean',
        ]);


        $post = Auth::user()->posts()->create($validated);

        return response()->json($post, 201);
    }

    /**
     * Display the specified resource.
     */
       public function show(Post $post)
    {
        if (!$post->is_published && Auth::id() !== $post->user_id) {
            abort(404, 'Статья не найдена');
        }

        return response()->json([
            'data' => $post->load('user:id,name')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        if (Auth::id() !== $post->user_id) {
            abort(403, 'Нет прав на редактирование этой статьи');
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255|min:3',
            'body' => 'sometimes|required|string|min:10',
            'is_published' => 'sometimes|boolean',
        ]);

        $post->update($validated);

        return response()->json($post);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if (Auth::id() !== $post->user_id) {
            abort(403, 'Нет прав на удаление этой статьи');
        }

        $post->delete();

        return response()->json(['message' => 'Статья удалена']);
    }
}
