<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentModality;

class PaymentModalitySeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentModality::truncate();
        
        PaymentModality::create([
                            'name' => 'Por hora',
                            'abbrev_name' => '/ Hora',
                            'status' => true
                        ]);

        PaymentModality::create([
                            'name' => 'Por día',
                            'abbrev_name' => '/ Día',
                            'status' => true
                        ]);

        PaymentModality::create([
                            'name' => 'Quincenal',
                            'abbrev_name' => '/ Quincenal',
                            'status' => true
                        ]);

        PaymentModality::create([
                            'name' => 'Por mes',
                            'abbrev_name' => '/ Mensual',
                            'status' => true
                        ]);

        PaymentModality::create([
                            'name' => 'Por año',
                            'abbrev_name' => '/ Año',
                            'status' => true
                        ]);

        PaymentModality::create([
                            'name' => 'Por trabajo realizado',
                            'abbrev_name' => '/ Pago unico',
                            'status' => true
                        ]);
    }
}
