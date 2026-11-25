<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function index(Post $post)
    {
        $comments = $post->comments()->with('user')->get();

        return CommentResource::collection($comments);
    }

    public function store(Request $request,Post $post)
    {
        $validate = $request->validate([
            'body' => ['required','string', 'max:2000']
        ]);

        $comment = $post->comments()->create([
            'body' =>$validate['body'],
            'user_id' => $request->user()->id
        ]);

        return response()->json([
            'message' => 'Comment created successfully.',
            'data' => new CommentResource($comment->load('user')),
        ],201);
        }
        
    public function update(Request $request,Post $post, Comment $comment)
        {
          $this->authorize('update', $comment);

          $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000']
          ]);

          $comment->update($validated);

          return response()->json([
            'message' => 'comment updated successful.',
            'data' => new CommentResource($comment->load('user'))
          ]);

   }

   public function destroy(Post $post, Comment $comment)
   {
    $this->authorize('delete', $comment);

    $comment->delete();

    return response()->json([
      'message' => 'Comment deleted successfully.',
    ]);
   }
}
