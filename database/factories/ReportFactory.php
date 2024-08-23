<?php

namespace Database\Factories;

use App\Models\Motive;
use App\Models\Post;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ReportFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $user = User::inRandomOrder()->first();
        $reported_user = User::has('posts')->inRandomOrder()->first();
        
        $motives = Motive::where('type', 'reported')->get();
        
        $reportable = $this->reportable();
        $modelPaths = explode('\\', $reportable);
        
        try {
            if( $modelPaths[2] == 'Review' )
                $reportable = $reportable::activos()->has('user')->inRandomOrder()->first();
            else if( $modelPaths[2] == 'Post' )
                $reportable = $reportable::activos()->has('user')->inRandomOrder()->first();
            
            return [
                'creator_user_id' => $modelPaths[2] == 'Review' ? $reportable->rankedUser : $user,
                'reported_user_id' => $reportable->user,
                'reportable_id' => $reportable->id,
                'reportable_type' => $reportable::class,
                'motive_id' => $motives->random()->id,
                "created_at" => fake()->dateTimeThisYear(),
                "updated_at" => fake()->dateTimeThisYear(),
            ];
        } catch (\Throwable $th) {
            return "Error en {$modelPaths[2]}: {$th->getMessage()}";
        }
        // contar usuarios con mas de 1 reporte
        /* SELECT reports.id, users.name, count(reported_user_id)
        FROM `reports`
        inner join users on reports.reported_user_id = users.id
        group by reported_user_id
        having count(reported_user_id) > 1; */
    }

    public function reportable()
    {
        return $this->faker->randomElement([
            Post::class,
            Review::class,
        ]);
    }
}
