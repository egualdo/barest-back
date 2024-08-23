<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Experience;

class ExperienceSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Experience::truncate();
        Experience::create([
                            'name' => 'Sin experiencia',
                            'status' => 1,
                            'minimum' => 0
                        ]);

        Experience::create([
                            'name' => 'Mínimo 1 año',
                            'status' => 1,
                            'minimum' => 1
                        ]);

        Experience::create([
                            'name' => 'Mínimo 3 años',
                            'status' => 1,
                            'minimum' => 3
                        ]);

        Experience::create([
                            'name' => 'Mínimo 5 años',
                            'status' => 1,
                            'minimum' => 5
                        ]);

    }
}
