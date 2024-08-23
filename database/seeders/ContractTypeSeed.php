<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ContractType;

class ContractTypeSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ContractType::truncate();
        
        ContractType::create(['name' => 'Indefinido']);
        ContractType::create(['name' => 'Full-Time' ]);
        ContractType::create(['name' => 'Part-Time' ]);
        ContractType::create(['name' => 'Freelancer']);
    }
}
