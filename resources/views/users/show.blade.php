<h1>{{ $user->name }}</h1>

<form method="POST" action="{{ route('users.update', ['user' =>$user->id]) }}" 
      enctype="multipart/form-data">

      @csrf
      @method('PUT')


      <input type="file" name="profile_picture">  

      <button type="submit">Upload Profile Picture</button>
</form>
@error('profile_picture')
  <div>{{ $message }}</div>
@enderror

@if ($user->profile_picture)
  <img src="{{ \Illuminate\Support\Facades\Storage::url($user->profile_picture) }}" 
  alt="{{ $user->name }}'s profile picture"
  width="150"
  height="150"
  >
@endif


@foreach ($user->posts as $post)

<a href="{{ route('posts.show', ['post' => $post->id]) }}">
  <h3>{{ $post->title }}</h3>
</a>

<p>{{ $post->description }}</p>

<h4>comments</h4>

<p>{{ $post->comments }}</p>
@endforeach