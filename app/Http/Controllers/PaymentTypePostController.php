<?php

namespace App\Http\Controllers;

use App\Models\PaymentModality;
use Illuminate\Http\Request;

class PaymentTypePostController extends Controller
{   
    public function all($post_type_id = null)
    {
        $optionsToShow = $post_type_id == 3 ? [ 1, 4, 5 ] : [];

        $query = PaymentModality::select();

        if(count( $optionsToShow ))
            $query->whereIn('id', $optionsToShow);

        return $this->showAll( $query->get() );
    }
    
    public function store(Request $request)
    {   

        
    }
   
    public function show($id)
    {
        //
    }
   
    public function update(Request $request, $id)
    {
        //
    }
   
    public function destroy($id)
    {
        //
    }

}
