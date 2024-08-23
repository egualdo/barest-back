<?php

namespace Database\Seeders;

use App\Models\ContractType;
use Illuminate\Database\Seeder;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

class PostSeed extends Seeder
{    
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // DB::table('idiom_posts')->truncate();
        Post::truncate();

        $contract_types = ContractType::get();
        DB::table('post_contract_types')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $factory = Post::factory(20);
        
        $factory->hasAttached( $contract_types->random(2) );
        
        $factory->create();
    }
}
