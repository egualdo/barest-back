<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PostType;

class PostTypeController extends Controller
{

    public function postTypeAll()
    {
        $data=PostType::activos()->get(); 
          return $this->showAll($data);
    }
}
