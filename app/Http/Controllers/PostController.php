<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\StorePostRequest;
use App\Http\Requests\Post\UpdatePostRequest;
use App\Models\Post;
use App\Services\PostService;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function __construct(private PostService $postService) {}

    public function index(Request $request)
    {
        $filters = $request->only(['date_from', 'date_to', 'sort']);
        return PostResource::collection($this->postService->index($filters));
    }

    public function myPosts(Request $request)
    {
        $filters = $request->only(['date_from', 'date_to', 'sort']);
        $userId = Auth::id();
        
        return PostResource::collection(
            $this->postService->myPosts($userId, $filters)
        );
    }

    public function store(StorePostRequest $request)
    {
        $post = $this->postService->store($request->validated());
        return response()->json(new PostResource($post), 201);
    }

    public function show(Post $post)
    {
        return response()->json(new PostResource($this->postService->show($post)));
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $post = $this->postService->update($post, $request->validated());
        return response()->json(new PostResource($post));
    }

    public function destroy(Post $post)
    {
        $this->postService->destroy($post);
        return response()->json(['message' => 'Статья успешно удалена']);
    }
}