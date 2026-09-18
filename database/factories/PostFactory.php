<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        //create fake model data for development/testing.

        return [
            //if no user exists,create one and use its ID
          'user_id' => \App\Models\User::factory(),

          'title' => fake()->sentence(),
          'description' => fake()->paragraph(),

          //Each generated post gets one of these three difficulty levels.
          'difficulty' => fake()->randomElement([
                   'Beginner',
                   'Intermediate',
                   'Advanced' ,
          ]),

          //If the coin flip is true, generate some text. Otherwise, store null.
          'code_snipet' => fake()->boolean(50)
            ? fake()->text()
            : null,
        ];
    }
}
