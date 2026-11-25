<?php
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('authenticated user can create a comment', function () {
    $user = User::factory()->create();

    $post = Post::factory()->create();

    $response = $this->actingAs($user)
            ->postJson("/api/posts/{$post->id}/comments", [
                'body' => 'This is my first Api comment'
            ]);

    $response->assertCreated()
              ->assertJsonPath('data.body', 'This is my first Api comment')
              ->assertJsonPath('data.user.id', $user->id);

              $this->assertDatabaseHas('comments', [
                'body' => 'This is my first Api comment',
                'user_id' => $user->id,
                'post_id' => $post->id,
              ]);
    
});

test('comment body is required', function() {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $response = $this->actingAs($user)
                ->postJson("/api/posts/{$post->id}/comments", [
                    'body' => '',
                ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('body');            
});

test('comment author can update their comment', function() {
    $user = User::factory()->create();
    
    $post = Post::factory()->create();
    
    $comment = $post->comments()->create([
        'body' => 'Original comment',
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)
                ->patchJson("/api/posts/{$post->id}/comments/{$comment->id}", [
                    'body' => 'Updated comment',
                ]);

    $response->assertOk()
       ->assertJsonPath('data.body', 'Updated comment');
       
    $this->assertDatabaseHas('comments', [
        'id' =>  $comment->id,
        'body' => 'Updated comment'
    ]);   
});

test('another user cannot update someone elses comment', function () {
    $author = User::factory()->create();
    $anotherUser = User::factory()->create();

    $post = Post::factory()->create();

    $comment = $post->comments()->create([
        'body' => 'Original comment',
        'user_id' => $author->id,
    ]);

    $response = $this->actingAs($anotherUser)
        ->patchJson("/api/posts/{$post->id}/comments/{$comment->id}", [
            'body' => 'Trying to change someone elses comment',
        ]);

    $response->assertForbidden();

    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
        'body' => 'Original comment',
    ]);
});

test('comment update requires a body', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $comment = $post->comments()->create([
        'body' => 'Original comment',
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)
        ->patchJson("/api/posts/{$post->id}/comments/{$comment->id}", [
            'body' => '',
        ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('body');

    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
        'body' => 'Original comment',
    ]);
});

test('comment author can delete their comment', function () {
    $user = User::factory()->create();
    $post = Post::factory()->create();

    $comment = $post->comments()->create([
        'body' => 'Comment to delete',
        'user_id' => $user->id,
    ]);

    $response = $this->actingAs($user)
        ->deleteJson("/api/posts/{$post->id}/comments/{$comment->id}");

    $response->assertOk()
        ->assertJson([
            'message' => 'Comment deleted successfully.',
        ]);

    $this->assertDatabaseMissing('comments', [
        'id' => $comment->id,
    ]);
});


test('post owner can delete another users comment', function () {
    $postOwner = User::factory()->create();
    $commentAuthor = User::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $postOwner->id,
    ]);

    $comment = $post->comments()->create([
        'body' => 'Comment written by another user',
        'user_id' => $commentAuthor->id,
    ]);

    $response = $this->actingAs($postOwner)
        ->deleteJson("/api/posts/{$post->id}/comments/{$comment->id}");

    $response->assertOk();

    $this->assertDatabaseMissing('comments', [
        'id' => $comment->id,
    ]);
});

test('unrelated user cannot delete another users comment', function () {

    // Given: three users
    $postOwner = User::factory()->create();
    $commentAuthor = User::factory()->create();
    $unrelatedUser = User::factory()->create();

    // Given: the post belongs to the post owner
    $post = Post::factory()->create([
        'user_id' => $postOwner->id,
    ]);

    // Given: the comment belongs to another user
    $comment = $post->comments()->create([
        'body' => 'Original comment',
        'user_id' => $commentAuthor->id,
    ]);

    // When: an unrelated user attempts to delete it
    $response = $this->actingAs($unrelatedUser)
        ->delete("/posts/{$post->id}/comments/{$comment->id}");

    // Then: Laravel should reject the request
    $response->assertForbidden();

    // And: the comment should remain in the database
    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
        'body' => 'Original comment',
    ]);
});

test('comments API returns selected comment and author fields only', function () {
    $user = User::factory()->create([
        'name' => 'John Developer',
    ]);

    $post = Post::factory()->create();

    $comment = $post->comments()->create([
        'body' => 'Learning Laravel Resources',
        'user_id' => $user->id,
    ]);

    $response = $this->getJson("/api/posts/{$post->id}/comments");

    $response->assertOk()
        ->assertJsonPath('data.0.id', $comment->id)
        ->assertJsonPath('data.0.body', 'Learning Laravel Resources')
        ->assertJsonPath('data.0.post_id', $post->id)
        ->assertJsonPath('data.0.user.id', $user->id)
        ->assertJsonPath('data.0.user.name', 'John Developer')
        ->assertJsonMissingPath('data.0.user.email')
        ->assertJsonMissingPath('data.0.user.password');
});