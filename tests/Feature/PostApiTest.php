 <?php
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Post;
use App\Models\User;

uses(RefreshDatabase::class);

test('requesting a non-existent post returns a 404', function () {
    $response = $this->getJson('/api/posts/99999');

    $response->assertNotFound();

    $response->assertExactJson([
        'message' => 'The requested resource was not found.',
        'error' => 'RESOURCE_NOT_FOUND',
    ]);
});

test('a nonexistent API route returns a consistent JSON 404', function () {
    $response = $this->getJson('/api/does-not-exist');

    $response->assertNotFound();

    $response->assertExactJson([
        'message' => 'The requested resource was not found.',
        'error' => 'RESOURCE_NOT_FOUND',
    ]);
});

test('posts API returns selected post and author fields only', function () {
    $user = User::factory()->create();

    $post = Post::factory()->create([
        'user_id' => $user->id,
        'title' => 'Learning Laravel API',
    ]);

    $response = $this->getJson('/api/posts');

    $response->assertOk()
        ->assertJsonPath('data.0.id', $post->id)
        ->assertJsonPath('data.0.title', 'Learning Laravel API')
        ->assertJsonPath('data.0.user.id', $user->id)
        ->assertJsonMissingPath('data.0.user.email')
        ->assertJsonMissingPath('data.0.user.password');
});