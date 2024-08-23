<?php

namespace App\Http\Controllers;

use App\Models\Idiom;
use Illuminate\Http\Request;

class IdiomController extends Controller
{
    public function all(){
        $idioms = Idiom::all();

        return $this->showAll($idioms);
    }
}
