<h1>posts</h1>

@foreach ($posts as $post)
    <article>
        <h2>{{ $post->title }}</h2>

        <p>{{ $post->user->name }}</p>

        <h3>Comments</h3>

        @foreach ($post->comments as $comment )
             <p>
                {{ $comment->body }}
                -- {{ $comment->user->name }}
             </p>
        @endforeach
        
    </article>
@endforeach