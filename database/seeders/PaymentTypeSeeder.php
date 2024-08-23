<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentType;

class PaymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentType::truncate();

        PaymentType::create([
                        'name' => 'Paypal',
                        'status'=> true
                    ]);

        PaymentType::create([
                        'name' => 'Tarjeta de Débito/Crédito',
                        'status'=> true
                    ]);
    }
}
