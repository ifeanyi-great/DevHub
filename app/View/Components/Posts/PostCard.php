<?php

namespace App\View\Components\Posts;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Models\Post as PostModel;

class PostCard extends Component
{
    public PostModel $post;
    public function __construct(PostModel $post)
    {
        $this->post = $post;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.posts.post-card');
    }
}
