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
            'body' => ['required','string'],
        ]);

        $comment = new Comment($validated);

        $comment->user_id = $request->user()->id;

        $comment->post_id = $post->id;

        $comment->save();

        return response()->json([
            'id' => $comment->id,
            'body' => $comment->body,
            'user_name' => $comment->user->name,
        ]);
    }

    public function edit(Post $post, Comment $comment)
    {
     $this->authorize('update', $comment);
 
     return view('comments.edit', [
         'post' => $post,
         'comment' => $comment
     ]);
    }
    public function update(Request $request, Post $post, Comment $comment)
   {
     $this->authorize('update', $comment);
      
      $validated = $request->validate([
         'body' => ['required','string'],
 ]);
      
           
      $comment->update($validated);        
     
     return response()->json([
        'body' => $comment->body ,
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
