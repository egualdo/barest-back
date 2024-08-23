<?php

namespace Database\Seeders;

use App\Models\Report;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReportsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Report::truncate();

        Report::factory(20)->create();

        $idsToDelete = Report::select('id')->where('reportable_type', 'App\\Models\\Review')->groupBy('reportable_id')->havingRaw('count(reportable_id) > ?', [1])->get()->toArray();
        $deleted = Report::whereIn('id', $idsToDelete)->forceDelete();
    }
}
