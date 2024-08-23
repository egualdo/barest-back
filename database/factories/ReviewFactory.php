<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {   
        $users = User::get();
        $posts = Post::where('user_id', '>', 10 )->get();

        return [
            "ranked_user_id" => function (array $attributes) {
                return Post::find( $attributes['post_id'] )->user_id;
            },
            "ranking" => $this->faker->numberBetween(1, 5),
            "reviewer_user_id" => $users->filter( fn( $user ) => $user->id < 10 )->random()->id,
            "post_id" => $posts->random()->id,
            "created_at" => now(),
            "updated_at" => now()
        ];
    }
}
