<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Product::truncate();
        
        //product 1
        $Product1 = Product::create([
            'name' => 'Publication',
            'price' => 1.99,
            'type' => 'publication',
            'days_duration' => 30,
            'active' => TRUE
        ]);
        
    }
}
