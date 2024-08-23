<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;
use App\Models\PlanPrice;
use Illuminate\Support\Facades\DB;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {   
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        PlanPrice::truncate();
        Plan::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $plan1 = Plan::create([
                    'name' => 'Básico',                
                    'description'=> 'Con el pack Básico podrás publicar hasta 10 anuncios en todas las categorías y en las ofertas urgentes',
                    'benefits' => json_encode([ 'publications' => 5 ])
                ]);

        $prices = [
            [
                'duration' =>  1,
                'price' =>  39.99,
                'slug' => 'mensual'
            ],
            [
                'duration' =>  3,
                'price' =>  119.97,
                'slug' => 'trimestral'
            ],
            [
                'duration' =>  6,
                'price' =>  239.94,
                'slug' => 'semestral'
            ],
        ];

        $plan1->prices()->createMany( $prices ); 

        $plan2 = Plan::create([
                    'name' => 'Premium',                
                    'description'=> 'Con el pack Premium podrás publicar hasta 30 anuncios en todas las categorías y en las ofertas urgentes',
                    'benefits' => json_encode([ 'publications' => 5 ])                
                ]);

        $prices = [
            [
                'duration' =>  1,
                'price' =>  39.99,
                'slug' => 'mensual'
            ],
            [
                'duration' =>  3,
                'price' =>  119.97,
                'slug' => 'trimestral'
            ],
            [
                'duration' =>  6,
                'price' =>  239.94,
                'slug' => 'semestral'
            ],
        ];

        $plan2->prices()->createMany( $prices ); 

        $plan3 = Plan::create([
                    'name' => 'Plus',                
                    'description'=> 'Con el pack Plus podrás publicar anuncios las veces que quieras en todas las categorías y en las ofertas urgentes',
                    'benefits' => json_encode([ 'publications' => 10 ])                
                ]);

        $prices = [
            [
                'duration' =>  1,
                'price' =>  39.99,
                'slug' => 'mensual'
            ],
            [
                'duration' =>  3,
                'price' =>  119.97,
                'slug' => 'trimestral'
            ],
            [
                'duration' =>  6,
                'price' =>  239.94,
                'slug' => 'semestral'
            ],
        ];

        $plan3->prices()->createMany( $prices );
        
    }

}
