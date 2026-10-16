<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use AuthorizesRequests;

    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'body' => ['required','string', 'max:2000'],
        ]);

        $comment = new Comment($validated);

        $comment->user_id = $request->user()->id;

        $comment->post_id = $post->id;

        $comment->save();

        $html = view('components.comments.comment', ['comment' => $comment, 'post' => $post])->render();

        return response()->json([
        'html' => $html,
        ]);
    }
 
    public function update(Request $request, Post $post, Comment $comment)
   {
     $this->authorize('update', $comment);
      
      $validated = $request->validate([
         'body' => ['required','string', 'max:2000' ],
 ]);
      
           
      $comment->update($validated);        
     
      $html = view('components.comments.comment', [
        'comment' => $comment, 'post' => $post
        ])->render();

     return response()->json([
        'html' => $html,
     ]);

   } 

   public function destroy(Post $post, Comment $comment)
   {
    $this->authorize('delete', $comment);

    $comment->delete();

    return response()->json([
        'success' => true,
    ]);

//    return redirect()->route('posts.show', ['post' => $post->id]);
   }

}
