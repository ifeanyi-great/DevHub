<?php
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Post;
use App\Models\User;

uses(RefreshDatabase::class);

test('user cannot update another users post', function () {
   
//Given: two users and a post from the first user
$user1 = User::factory()->create();
$user2 = User::factory()->create();

$post = Post::factory()->create([
    'user_id' => $user1->id,
    'title' => 'Main title',
    'description' => 'Main description',
]);

//When: user2 attemps to update user1's post
$response = $this->actingAs($user2)
    ->put("/posts/{$post->id}", [
        'title' => 'Updated title',
        'description' => 'Updated description',
        'difficulty' => 'Intermediate',
    ]);

//Then: the update should be forbidden and the post should remain unchanged
   $response->assertForbidden();

    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'title' => 'Main title',
        'description' => 'Main description',
    ]);
});

test('user can update their own post', function(){
    
    //Given: a user and a post from that user
    $user = User::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $user->id,
        'title' => 'Original title',
        'description' => 'Original description',
    ]);

    //When: the user updates their own post
    $response = $this->actingAs($user)
        ->put("/posts/{$post->id}", [
            'title' => 'Updated title',
            'description' => 'Updated description',
            'difficulty' => 'Advanced',
        ]);

    //Then: the update should be successful and the post should be updated in the database
    $response->assertRedirect(
        route('posts.show', ['post' => $post->id,])
        );

    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'title' => 'Updated title',
        'description' => 'Updated description',
        'difficulty' => 'Advanced',
    ]);
});