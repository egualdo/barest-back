<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UnblockRequest>
 */
class UnblockRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $user = User::inRandomOrder()->first();
        
        $user->status = 'BLOCKED';
        $user->block_reason = 'inapropiate_user';
        $user->update();

        return [
            'user_id' => $user->id,
            'text' => $this->faker->text(150),
            'status' => 'PENDANT',
            "created_at" => fake()->dateTimeThisMonth(),
            "updated_at" => fake()->dateTimeThisMonth(),
        ];
    }
}
