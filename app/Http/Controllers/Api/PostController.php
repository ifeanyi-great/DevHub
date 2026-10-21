<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Http\Resources\PostResource;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('user')->paginate(5);

        return PostResource::collection($posts);
    }

    public function show(Post $post)
    {
        $post->load('user');

        return new PostResource($post);
    }
}
