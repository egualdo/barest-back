<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
class ProductController extends Controller
{
    public function index() {
        return $this->showAll( Product::disponibles()->get() );
    }

    public function showCheckout($id)
    {
        $product = Product::find($id);
            
        return view('Checkout.Product',compact('product'));
    }

    public function showCheckout2($id)
    {   
        $product = Product::find($id);
            
        return view( 'Checkout.Product2', compact('product') );
    }
}
