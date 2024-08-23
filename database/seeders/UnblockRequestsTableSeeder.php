<?php

namespace Database\Seeders;

use App\Models\UnblockRequest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnblockRequestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UnblockRequest::truncate();

        UnblockRequest::factory(25)->create();
    }
}
