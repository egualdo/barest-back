<?php

namespace App\Http\Controllers;

use App\Models\ContractType;

class ContractTypeController extends Controller
{
    public function index()
    {
        $data = ContractType::get(); 

        return $this->showAll($data);
    }
}
