<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Modality;

class ModalitySeed extends Seeder
{
     /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Modality::truncate();
        Modality::create([ 'name'   =>  'Turno de mañana']);
        Modality::create([ 'name'   =>  'Turno de tarde']);
        Modality::create([ 'name'   =>  'Turno de noche']);
        Modality::create([ 'name'   =>  'Turno rotativo']);
    }
}
