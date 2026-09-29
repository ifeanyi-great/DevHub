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

   <div class="control-comment">
                   

    @can('update', $comment)
     <a class="edit-comment" href="{{ route('comments.edit', ['post' => $post->id, 'comment' => $comment->id]) }}">edit</a>
    @endcan

    @can('delete', $comment)
        <form method="POST"  action="/posts/{{ $post->id }}/comments/{{ $comment->id }}">
            @csrf
            @method('DELETE')

            <button type="submit" class="delete-comment">delete</button>
        </form>
    @endcan


        <p>
           <span class="comment-body">{{ $comment->body }}</span> 
            -- {{ $comment->user->name }}
        </p>

       <div class="comment-error" ></div> 
      @can('update', $comment)
           <form method="POST" data-comment-id="{{ $comment->id }}" action="{{ route('comments.update', ['post' => $post->id, 'comment' => $comment->id ]) }}"
            class="space-y-2 rounded-lg  p-4 max-w-lg shadow-sm"
            >
                @csrf
                @method('PUT')

                
                <textarea name="body"
                 class="textarea w-full rounded-md focus:outline-none focus:ring-0 resize-none overflow-hidden" >
                {{  $comment->body }}</textarea>
                
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
     
       </div>
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

// Handle edit comment
document.querySelectorAll('.edit-comment').forEach(function(button){
        
        const commentContainer = button.parentElement;
        const editForm = commentContainer.querySelector('[data-comment-id]');
        const commentText = commentContainer.querySelector('.comment-body');
        const commentDisplay = commentContainer.querySelector('p');
        const cancelButton = commentContainer.querySelector('.cancel-comment-edit');
        const doneButton = commentContainer.querySelector('.done-button');
        const textarea = editForm.querySelector('textarea');
          
        textarea.value =  commentText.textContent;

      textarea.addEventListener('input', function() {
       
        if (textarea.value.trim() === '') {
            doneButton.disabled = true;
        } else {
            doneButton.disabled = false;
        }

     })

              
        editForm.hidden = true;
        
        button.addEventListener('click', function(event) {
         event.preventDefault();

        const originalBody = commentText.textContent;
        editForm.querySelector('textarea').value = originalBody;

         editForm.hidden = false;
         commentDisplay.hidden = true;
        });
        
        cancelButton.addEventListener('click', (event)=> {
        event.preventDefault();
        editForm.querySelector('textarea').value = '';
         editForm.hidden = true;
         commentDisplay.hidden = false;
        })

        editForm.addEventListener('submit', function (event) {
             event.preventDefault();

            const formData = new FormData(editForm);

            console.log('SUBMITTED BODY:', formData.get('body'));

            fetch(editForm.action, {
                method: 'POST',
                redirect: 'manual',
                headers: {
                    'Accept': 'application/json', 
                },
                body: formData
            })
            .then(async response => {
            console.log('status:', response.status);
        
            const data = await response.json();

          if (!response.ok) {
            console.log('ERROR RESPONSE:', data);
              return;
          }

        return data;

         })
           .then(data => commentText.textContent = data.body);

             editForm.hidden = true;
             commentDisplay.hidden = false;
        });
     
        
    })

// Handle delete comment
    document.querySelectorAll('.delete-comment').forEach(button => {
            button.addEventListener('click',async function(event){
            event.preventDefault();
            button.disabled = true;

            const form = button.closest('form');
            const url = form.action;

            const formData =new FormData(form);

            try{
            const response = await fetch(url,{
                method: 'POST',
                body: formData,
                headers: {
                'Accept': 'application/json',
                }
            }) 
            const data = await response.json();

            if(response.ok && data.success){
                const controlComment = button.closest('.control-comment');
                controlComment.remove();  
            }else{
                const controlComment = button.closest('.control-comment');
                const deleteErrorMessage = controlComment.querySelector('.comment-error');           
               //deleteErrorMessage.textContent = 'Comment could not be deleted. Please try again.';
                button.disabled = false;
            }
            
        }catch(error){
          const controlComment = button.closest('.control-comment');
          const deleteErrorMessage = controlComment.querySelector('.comment-error');           
          setTimeout(()=>{
          deleteErrorMessage.textContent = 'Comment could not be deleted. Please try again.';
          },1000)
          // remove the error message after 3 seconds
          setTimeout(() => {
            deleteErrorMessage.textContent = '';
          }, 3000);
          button.disabled = false;
      }     


        })
    })

 
    // Handle create comment
    const createCommentForm = document.querySelector('.create-comment-form');
    const commentsList = document.querySelector('.comments-list');
    const url = createCommentForm.action;
    const postId = url.split('/')[4];
  
    const textarea = document.querySelector('.create-comment-form textarea');
    textarea.addEventListener('input', ()=> {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px'
    });

    createCommentForm.addEventListener('submit', async function(event){
        event.preventDefault();

        const formData = new FormData(createCommentForm);
        
            const response = await fetch(url,{
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                }
            });

            const data = await response.json();
            createCommentForm.reset();

            const newComment = document.createElement('div');
            
            newComment.classList.add('control-comment');

            commentsList.appendChild(newComment);

            const commentDisplay = document.createElement('p');

            const commentBody = document.createElement('span');
            commentBody.classList.add('comment-body');
            commentBody.textContent = data.body;
           
            commentDisplay.appendChild(commentBody);
            commentDisplay.append(` --- ${data.user_name}`);

            newComment.appendChild(commentDisplay);
            
            
            
            // Create the dynamic edit button and append it to the newComment
            const editButton = document.createElement('button');
            editButton.textContent = 'edit';
            newComment.appendChild(editButton);
            editButton.dataset.commentId = data.id;
            editButton.classList.add('edit-comment', 'cursor-pointer');

            //create the dynamic edit form and append it to the newComment
          const editForm = document.createElement('form');
          editForm.classList.add('space-y-2', 'shadow-md', 'rounded-lg', 'p-4', 'max-w-lg')

          editForm.action = `/posts/${postId}/comments/${data.id}`;
          editForm.dataset.commentId = data.id; 

          const methodInput = document.createElement('input');
          methodInput.type = 'hidden';
          methodInput.name = '_method';
          methodInput.value = 'PUT';

          editForm.appendChild(methodInput);
          const csrfToken = createCommentForm.querySelector('input[name="_token"]').cloneNode();
          editForm.appendChild(csrfToken);
    
          const textarea = document.createElement('textarea');
          textarea.name = 'body';
          textarea.classList.add('textarea', 'w-full', 'rounded-md', 'resize-none', 'focus:ring-0', 'focus:outline-none', 'overflow-hidden');  
          textarea.value = data.body;
          editForm.appendChild(textarea);
          newComment.appendChild(editForm);
          editForm.hidden = true;



          // Add the done  button to the edit form
            const doneButton = document.createElement('button');
            doneButton.type = 'submit';
            doneButton.classList.add('done-button', 'px-4', 'py-2', 'rounded-md', 'border', 'bg-blue-600', 'text-white', 'hover:bg-blue-700', 'font-medium', 'transition', 'cursor-pointer');
            doneButton.textContent = 'Done';
            editForm.appendChild(doneButton);
            
            textarea.addEventListener('input', function(){
                doneButton.disabled = textarea.value.trim() === '';

                textarea.style.height = 'auto';
                textarea.style.height = textarea.scrollHeight + 'px';
            });

         // Add the cancel button to the edit form
            const cancelButton = document.createElement('button');
           cancelButton.type = 'button';
            cancelButton.classList.add('cancel-comment-edit', 'px-4', 'py-2', 'rounded-md', 'border', 'bg-gray-600', 'text-white', 'hover:bg-gray-700', 'font-medium', 'transition', 'cursor-pointer');
            cancelButton.textContent = 'Cancel';
            editForm.appendChild(cancelButton);
           
          const div = document.createElement('div');
          div.classList.add('flex', 'items-center', 'justify-end', 'gap-2');
          editForm.appendChild(div);
          div.append(doneButton,cancelButton);

            const editError = document.createElement('p');
            editError.classList.add('edit-error', 'text-red-600', 'text-sm', 'mt-4');
            editForm.appendChild(editError);

            // Create the dynamic delete button and append it to the newComment
            const deleteButton = document.createElement('button');
            deleteButton.textContent = 'delete';
            deleteButton.dataset.commentId = data.id;
            deleteButton.classList.add('delete-comment');
            newComment.appendChild(deleteButton);
            
    })

    commentsList.addEventListener('click', async (event)=>{
        const button = event.target.closest('button');

          if(!button) return;

          if(button.classList.contains('edit-comment')){
                    const commentContainer = event.target.parentElement;
                    const commentBody = commentContainer.querySelector('.comment-body');
                    const editForm = commentContainer.querySelector('form');
                    const commentDisplay = commentContainer.querySelector('p');             
                    editForm.hidden = false;
                    commentDisplay.hidden = true;

                   
            }     
             else if (button.classList.contains('delete-comment')) {
                    const commentContainer = event.target.parentElement;
                    const commentId = event.target.dataset.commentId;

                    const deleteUrl = `/posts/${postId}/comments/${commentId}`;
                    const formData = new FormData();
                    formData.append('_method','DELETE')

                    const csrfToken = createCommentForm.querySelector('input[name="_token"]').cloneNode()
                    formData.append('_token',csrfToken.value)
                    
                    const response = await fetch(deleteUrl, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                        },
                });
                
                if(response.ok){
                    commentContainer.remove();
                }
            
          }
          else if(button.classList.contains('cancel-comment-edit')){
                const editForm = event.target.parentElement;
                const commentDisplay = editForm.parentElement.querySelector('p');
             
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
            const commentContainer = editForm.parentElement;
            const commentBody = commentContainer.querySelector('.comment-body');
            const commentDisplay = commentContainer.querySelector('p');

            const formData = new FormData(editForm);
             
            const url = editForm.action;

            const response = await fetch(url, {
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
             commentBody.textContent = data.body;
             editForm.hidden = true;
             commentDisplay.hidden = false;

    }
});

</script>





</body>
</html>