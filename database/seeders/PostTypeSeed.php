<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PostType;

class PostTypeSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PostType::truncate();
        
        PostType::create([ 'name' => 'Servicio' ]);
        PostType::create([ 'name' => 'Producto' ]);
        PostType::create([ 'name' => 'Trabajo' ]);        
    }
}
