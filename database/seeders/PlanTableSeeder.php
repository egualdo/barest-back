<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;
use App\Models\PlanPrice;

class PlanTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Plan::truncate();
        // PlanPrice::truncate();
        
        //plan 1
        $benefits1 = json_encode([
            'vehicle_pubs' => 45,
            'promoted' => 10,
            'replacement_pubs' => 20
        ]);
        
        $plan1 = Plan::create([
            'name' =>  'Basico',
            //'type' =>  'basic',
            'benefits' => $benefits1,
            //'remarkable' =>  0,
            'active' =>  TRUE,
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
        //fin plan 1

        //plan 2
        $benefits2 = json_encode([
            'vehicle_pubs' => 75,
            'promoted' => 25,
            'replacement_pubs' => 40
        ]);

        $plan2 = Plan::create([
            'name' =>  'Premium',
            //'type' =>  'premium',
            'benefits' => $benefits2,
            //'remarkable' =>  0,
            'active' =>  TRUE,
        ]); 
        
        $prices = [
            [
                'duration' =>  1,
                'price' =>  49.99,
                'slug' => 'mensual'
            ],
            [
                'duration' =>  3,
                'price' =>  149.97,
                'slug' => 'trimestral'
            ],
            [
                'duration' =>  6,
                'price' =>  299.94,
                'slug' => 'semestral'
            ],
        ];

        $plan2->prices()->createMany( $prices );        
        //fin plan 2

        //plan 3
        $benefits3 = json_encode([
            'vehicle_pubs' => 100,
            'promoted' => 50,
            'replacement_pubs' => 100,
            'rental_pub' => 1,
            'garage_pub' => 1,
        ]);

        $plan3 = Plan::create([
            'name' =>  'Premium+',
            //'type' =>  'premium-plus',
            'benefits' => $benefits3,
            //'remarkable' =>  0,
            'active' =>  TRUE,
        ]); 
        
        $prices = [
            [
                'duration' =>  1,
                'price' =>  69.99,
                'slug' => 'mensual'
            ],
            [
                'duration' =>  3,
                'price' =>  209.97,
                'slug' => 'trimestral'
            ],
            [
                'duration' =>  6,
                'price' =>  419.94,
                'slug' => 'semestral'
            ],
        ];

        $plan3->prices()->createMany( $prices );        
        //fin plan 3
    }
}
