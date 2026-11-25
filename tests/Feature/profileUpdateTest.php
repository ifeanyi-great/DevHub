<?php
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use function Pest\Laravel\assertDatabaseHas;

uses (RefreshDatabase::class);

test('user can update their profile without uploading a new picture', function () {
   //Given: a user and their profile picture
   $user =User::factory()->create([
    'profile_picture' => 'profile_pictures/test.jpg',
   ]);

   //When: user updates their profile
  $response = $this->actingAs($user)
            ->put(route('users.update', [
                'user' => $user
                ]));

    //Then: allow update even without upload
            $response->assertOk();

         $this->assertDatabaseHas('users',[
            'profile_picture' => 'profile_pictures/test.jpg'
         ]);

});