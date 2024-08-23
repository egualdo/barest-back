<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Modality;

class ModalityController extends Controller
{
    public function all()
    {
        $data = Modality::activos()->get(); 

         return $this->showAll($data);
    }
}
