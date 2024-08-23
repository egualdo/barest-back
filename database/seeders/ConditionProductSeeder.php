<?php

namespace Database\Seeders;

use App\Models\ConditionProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConditionProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ConditionProduct::truncate();
        
        ConditionProduct::create(['name' => 'Nuevo']);
        ConditionProduct::create(['name' => 'Perfecto estado' ]);
        ConditionProduct::create(['name' => 'Buen estado' ]);
        ConditionProduct::create(['name' => 'Condiciones aceptables']);
    }
}
