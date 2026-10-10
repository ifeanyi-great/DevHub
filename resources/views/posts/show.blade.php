<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->title }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body>

 <h2>{{ $post->title }}</h2>

<a href="{{ route('users.show', ['user' => $post->user->id] )}}">
    <p>By {{ $post->user->name }}</p>
</a>

<p> {{ $post->description }}</p>

<h2>Comments</h2>

<div class="comments-list">
@foreach ($post->comments as $comment)

    <x-comments.comment :comment="$comment" :post="$post" />

@endforeach

<form method="POST" action="/posts/{{ $post->id }}/comments" 
    class="create-comment-form max-w-lg shadow-md m-2 rounded-lg p-2">

    <div class="relative">
    @csrf
    <textarea name="body" class="w-full focus:outline-none resize-none pr-3 pb-4 min-h-20" placeholder="add comment"></textarea>

    <button type="submit" class=" absolute bottom-2 right-2 cursor-pointer p-2 bg-blue-600 hover:bg-blue-700 transition text-white rounded-full text-xl w-10 h-10 flex items-center justify-center">↑</button>
</div>
</form>

 @error('body')
 {{ $message }}
 @enderror

</div>

 <script>

 
    // Handle create comment
    const createCommentForm = document.querySelector('.create-comment-form');
    const commentsList = document.querySelector('.comments-list');
    const createCommentUrl = createCommentForm.action;
    const postId = createCommentUrl.split('/')[4];
  
    const textarea = document.querySelector('.create-comment-form textarea');
    textarea.addEventListener('input', ()=> {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px'
    });

    createCommentForm.addEventListener('submit', async function(event){
        event.preventDefault();

        const formData = new FormData(createCommentForm);
        
            const response = await fetch(createCommentUrl,{
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();
            createCommentForm.reset();

            commentsList.insertAdjacentHTML('beforeend', data.html);

    })

    commentsList.addEventListener('click', async (event)=>{
        const button = event.target.closest('button, a');

          if(!button) return;

          if(button.classList.contains('edit-comment')){
                  event.preventDefault();

                    const commentContainer = event.target.closest('.control-comment');
                    const editForm = commentContainer.querySelector('form[data-comment-id]');
                    const commentDisplay = commentContainer.querySelector('p');

                    editForm.hidden = false;
                    commentDisplay.hidden = true;
            }     
             else if (button.classList.contains('delete-comment')) {
                 event.preventDefault();

                 const deleteForm = event.target.closest('form');

                  const commentId = event.target.dataset.commentId;

                const deleteUrl = deleteForm.action;
                
                const commentContainer = event.target.closest('.control-comment');

                const formData = new FormData(deleteForm);

                    try{

                        const response = await fetch(deleteUrl, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json',
                            },
                        });

                        if(!response.ok){
                            const deleteError = commentContainer.querySelector('.comment-error');
                            deleteError.textContent = "Failed to delete comment.";
                            return;
                        }

                        commentContainer.remove();

                    }
                    catch(error){
                        const deleteError = commentContainer.querySelector('.comment-error');
                        deleteError.textContent = "Network error. Please check your internet connection and try again.";
                        return;

                    }

            
          }
          else if(button.classList.contains('cancel-comment-edit')){
                const editForm = event.target.closest('form');
                const commentDisplay = editForm.closest('.control-comment').querySelector('p');
             
                editForm.hidden = true;
                commentDisplay.hidden = false;
            }

   })
      
      commentsList.addEventListener('submit', async(event) => {
        event.preventDefault();

        if (event.target.dataset.commentId) {
            const editForm = event.target;
            const editError = editForm.querySelector('.edit-error');
            editError.textContent = '';
            const commentContainer = editForm.closest('.control-comment');


            const formData = new FormData(editForm);
             
            const commentUpdateUrl = editForm.action;

            const response = await fetch(commentUpdateUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                },
                body: formData,
            });

             if(!response.ok){
                editError.textContent = "Failed to update comment. ";
                return;
             }
            const data = await response.json();

             commentContainer.outerHTML = data.html;
    }
});

</script>

</body>
</html>