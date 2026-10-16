<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses( RefreshDatabase::class);

test('a comment exceeding 2000 characters is rejected', function () {

    //Given: an authenticated user and an existing post
    $user = User::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $user->id,
    ]);

    $this->actingAs($user)

         //When: the user attempts to create a comment with more than 2000 characters
        ->post("/posts/{$post->id}/comments", [
            'body' => str_repeat('a', 2001),
        ])
        //Then: the comment should not be created or saved in the database
        ->assertSessionHasErrors('body');

    $this->assertDatabaseMissing('comments', [
        'post_id' => $post->id,
        'user_id' => $user->id,
    ]);
});

test ('a user cannot update another users comment', function () {

    //Given: two users and a post with a comment from the first user
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $userA->id,
    ]);

    //Given: userA owns the comment
    $comment = new App\Models\Comment([
        'body' => 'This is a comment from userA',
    ]);

    $comment->user()->associate($userA);
    $comment->post()->associate($post);
    $comment->save();

    //When: userB attempts to update userA's comment
    $this->actingAs($userB);

    $response = $this->put(
        "/posts/{$post->id}/comments/{$comment->id}",
        [
            'body' => 'I am trying to change this comment',
        ]
    );

    //Then: the request should be forbidden
    $response->assertForbidden();

    //And: the comment should remain unchanged in the database
    $this->assertDatabaseHas('comments',[
        'id' => $comment->id,
        'body' => $comment->body,
    ]);

});

test('a post owner can delete another users comment', function () {

    // Given: two users
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    // Given: userA owns the post
    $post = Post::factory()->create([
        'user_id' => $userA->id,
    ]);

    // Given: userB owns the comment
    $comment = new App\Models\Comment([
        'body' => 'A comment from userB',
    ]);

    $comment->user()->associate($userB);
    $comment->post()->associate($post);
    $comment->save();

    // When: userA attempts to delete userB's comment
    $response = $this->actingAs($userA)
        ->delete("/posts/{$post->id}/comments/{$comment->id}");

    // Then: the request should succeed
    $response->assertSuccessful();

    // And: the comment should no longer exist
    $this->assertDatabaseMissing('comments', [
        'id' => $comment->id,
    ]);
});

test('a user cannot delete another users comment', function () {

    // Given: three users
    $userA = User::factory()->create();
    $userB = User::factory()->create();
    $userC = User::factory()->create();

    // Given: userA owns the post
    $post = Post::factory()->create([
        'user_id' => $userA->id,
    ]);

    // Given: userB owns the comment
    $comment = new App\Models\Comment([
        'body' => 'A comment from userB',
    ]);

    $comment->user()->associate($userB);
    $comment->post()->associate($post);
    $comment->save();

    // When: userC attempts to delete userB's comment
    $response = $this->actingAs($userC)
        ->delete("/posts/{$post->id}/comments/{$comment->id}");

    // Then: the request should be forbidden
    $response->assertForbidden();

    // And: the comment should remain in the database
    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
        'body' => 'A comment from userB',
    ]);
});


   test('a comment author can delete their own comment', function () {

    // Given: two users
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    // Given: userA owns the post
    $post = Post::factory()->create([
        'user_id' => $userA->id,
    ]);

    // Given: userB owns the comment
    $comment = new App\Models\Comment([
        'body' => 'A comment from userB',
    ]);

    $comment->user()->associate($userB);
    $comment->post()->associate($post);
    $comment->save();

    // When: userB attempts to delete their own comment
    $response = $this->actingAs($userB)
        ->delete("/posts/{$post->id}/comments/{$comment->id}");

    // Then: the request should succeed
    $response->assertSuccessful();

    // And: the comment should no longer exist
    $this->assertDatabaseMissing('comments', [
        'id' => $comment->id,
    ]);
});