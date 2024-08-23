<?php

namespace Database\Seeders;

use App\Models\PaymentHistory;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentHistoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentHistory::truncate();

        PaymentHistory::factory(200)
                    ->create();
    }
}
