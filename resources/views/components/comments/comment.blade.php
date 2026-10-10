<div class="control-comment">
    @can('update', $comment)
     <button type="button" class="edit-comment cursor-pointer" >edit</button>
    @endcan

    @can('delete', $comment)
        <form method="POST"  action="{{ route('comments.destroy', ['post' => $post->id, 'comment' => $comment->id]) }} " class="dark:bg-gray-800">
            @csrf
            @method('DELETE')

            <button type="submit" class="delete-comment cursor-pointer">
                delete
            </button>
        </form>
    @endcan

        <p>
           <span class="comment-body">{{ $comment->body }}</span> 
            -- {{ $comment->user->name }}
        </p>

  @can('update', $comment)

           <form method="POST" data-comment-id="{{ $comment->id }}" action="{{ route('comments.update', ['post' => $post->id, 'comment' => $comment->id ]) }}"
            class="space-y-2 rounded-lg  p-4 max-w-lg shadow-sm" hidden
            >
                @csrf
                @method('PUT')
              
                <textarea name="body"
                 class="textarea w-full rounded-md focus:outline-none focus:ring-0 resize-none overflow-hidden" >{{  $comment->body }}</textarea>
                
                <div class="flex items-center justify-end gap-2">
                <button type="submit" class="done-button px-4 py-2 rounded-md border bg-blue-600 text-white hover:bg-blue-700 font-medium transition cursor-pointer">
                    Done
                </button>

            <button type="button" class="cancel-comment-edit  bg-gray-600 rounded-md border px-4 py-2 font-medium text-white font-meduim hover:bg-gray-700 transition cursor-pointer">
                Cancel
            </button>
            
        </div>
                <p class="edit-error text-red-600 text-sm"></p>
            </form> 
            
     @endcan

       <div class="comment-error" ></div> 

</div> 