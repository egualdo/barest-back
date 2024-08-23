<?php

namespace Database\Seeders;

use App\Models\Idiom;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IdiomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Idiom::truncate();
        Idiom::create(['name' => 'Ingles']);
        Idiom::create(['name' => 'Español']);
        Idiom::create(['name' => 'Rumano']);
    }
}
