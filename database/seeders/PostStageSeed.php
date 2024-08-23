<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PostStage;

class PostStageSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // PostStage::truncate();
        
        PostStage::create(['name' => 'en progreso','status'=>1]);
        PostStage::create(['name' => 'finalizado','status'=>1]);
        PostStage::create(['name' => 'cancelado','status'=>1]);
        // PostType::create(['name' => 'Cafetería','status'=>1]);
        // PostType::create(['name' => 'Bares','status'=>1]);
        // PostType::create(['name' => 'Empresas Proveedoras','status'=>1]);
        // PostType::create(['name' => 'Otros','status'=>1]);

    }
}
