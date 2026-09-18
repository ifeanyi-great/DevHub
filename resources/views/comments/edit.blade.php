<form method="POST" action="{{ route('comments.update', ['post' => $post->id,'comment' => $comment->id]) }}">
 @csrf
 @method('PUT')

 @error('body')
    <div>{{ $message }}</div>
@enderror
 <textarea name="body" >{{ old('body', $comment->body) }}</textarea>


 <button type="submit">Done</button>

</form>
 

<a href="{{ route('posts.show', ['post' => $post->id]) }}">
    Cancel
</a>
