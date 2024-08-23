<?php

namespace App\Http\Controllers;

use App\Models\ConditionProduct;
use Illuminate\Http\Request;

class ConditionProductController extends Controller
{
    public function all()
    {
        $data = ConditionProduct::all(); 

         return $this->showAll($data);
    }

}
