
<h1>Edit Post</h1>


<form method="POST" action="/posts/{{ $post->id }}">
 @csrf
 @method('PUT')

 <label for="title">Title</label>
 <input type="text" name="title" value="{{ $post->title }}">

 <label for="description">Description</label>
 <textarea name="description" >{{ $post->description }}</textarea>

<label for="difficulty">Difficulty:</label>
 <select name="difficulty">
    <option value="Beginner" {{ $post->difficulty === 'Beginner' ? 'selected' : '' }}>Beginner</option>
    <option value="Intermediate" {{ $post->difficulty === 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
    <option value="Advanced" {{ $post->difficulty === 'Advanced' ?'selected' : ''}}>Advanced</option>
 </select>

 <button type="submit">Update</button>
</form>

{{ auth()->id() }}
{{ $post->user_id }}
@can('delete', $post)
<form method="post" action="/posts/{{ $post->id }}">
@csrf
@method('DELETE')

<button type="submit">delete</button>
</form>
@endcan
