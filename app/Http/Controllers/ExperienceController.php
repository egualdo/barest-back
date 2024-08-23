<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Experience;

class ExperienceController extends Controller
{
    public function index()
    {
        $data = Experience::activos()->select('id', 'name')->get(); 

        return $this->showAll($data);
    }
}
