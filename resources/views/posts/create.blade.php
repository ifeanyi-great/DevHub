<h1>Create Post</h1>

<form method="POST" action="/posts">
    @csrf

     <label for="title">Title
        <input type="text" name="title" value="{{ old('title') }}">
     </label>

     <label for="description">Description
        <textarea name="description" value="{{ old('description') }}"></textarea>
     </label>

     <label for="difficulty">Difficulty:</label>
     <select name="difficulty" id="difficulty">
            <option value="Beginner" >Beginner</option>
            <option value="Intermediate">Intermediate</option>
            <option value="Advanced" >Advanced</option>
     </select>

     <button type="submit">Create Post</button>

</form>