<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Motive;

class MotivesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Motive::truncate();

        Motive::create([
                    'name' => 'Perfil falso',
                    'entity' => 'user',
                    'type' => 'reported',
                    'active' => TRUE,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

        Motive::create([
                    'name' => 'Empresa Engañosa',
                    'entity' => 'user',
                    'type' => 'reported',
                    'active' => TRUE,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

        Motive::create([
                    'name' => 'Información falsa',
                    'entity' => 'post',
                    'type' => 'reported',
                    'active' => TRUE,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

        Motive::create([
                    'name' => 'Contenido inapropiado',
                    'entity' => 'review',
                    'type' => 'reported',
                    'active' => TRUE,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

        Motive::create([
                    'name' => 'Información falsa',
                    'entity' => 'review',
                    'type' => 'reported',
                    'active' => TRUE,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

        Motive::create([
                    'name' => 'Incitación a la violencia o actividades ilegales',
                    'entity' => 'review',
                    'type' => 'reported',
                    'active' => TRUE,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

        Motive::create([
                    'name' => 'Spam',
                    'entity' => 'review',
                    'type' => 'reported',
                    'active' => TRUE,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

        Motive::create([
                    'name' => 'Otros motivos',
                    'entity' => 'review',
                    'type' => 'reported',
                    'active' => TRUE,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

    }
}
