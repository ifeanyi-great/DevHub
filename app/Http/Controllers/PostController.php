<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class PostController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    
{   

   $posts = Post::with([
    'user',
    'comments.user'
   ])->get();

    
   return view('posts.index', [
    'posts' => $posts,
   ]);


}  

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
       return view('posts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $validated = $request->validate([
       'title' => ['required', 'string', 'max:255'],
       'description' => ['required', 'string'],
       'difficulty' => [
        'required',
         'string',
         'in:Beginner,Intermediate,Advanced',
         ]
       ]);

       $user = $request->User();

       $post = new Post($validated);

       $post->user_id = $user->id;

       $post->save();

        return redirect()->route('posts.show', [
            'post' => $post->id,
        ]);       
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
       return view('posts.show', [
        'post'=> $post,
       ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        $this->authorize('update', $post);

        return view('posts.edit', [
            'post'=> $post,
        ]);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
         $this->authorize('update', $post);

         $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'difficulty' => ['required', 'string', 'in:Beginner,Intermediate,Advanced']
         ]);

          $post->update($validated);

         return redirect()->route('posts.show', ['post' => $post]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return redirect('/posts');
    }
}
