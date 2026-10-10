<?php

namespace App\View\Components\Comments;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Comment as CommentModel;
use App\Models\Post;

class Comment extends Component
{
    public CommentModel $comment;
    public Post $post;
    
    public function __construct(CommentModel $comment, Post $post)
    {
        $this->comment = $comment;
        $this->post = $post;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.comments.comment');
    }
}
