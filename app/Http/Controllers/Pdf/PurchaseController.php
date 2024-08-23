<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\ProductUser;
use Barryvdh\DomPDF\Facade\Pdf;

class PurchaseController extends Controller
{
    public function purchase(ProductUser $purchase)
    {
        $purchase->load([ 'product', 'customer', 'payment' ]);

        $products_translated = [
            'publication' => trans('Publicación'),
        ];

        $pdf = Pdf::loadView('pdf.purchase', [ 'purchase' => $purchase, 'products_translated' => $products_translated ]);

        return $pdf->stream('purchase.pdf');
    }
}
