<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'difficulty' => $this->difficulty,
            'code_snippet' => $this->code_snippet,

            'user' => $this->whenLoaded('user', function(){
                if(!$this->user){
                    return null;
                    }

                    return [
                        'id' => $this->user->id,
                        'name' => $this->user->name,
                        'profile_picture' => $this->user->profile_picture
                    ];
                })
        ];
    }
}
