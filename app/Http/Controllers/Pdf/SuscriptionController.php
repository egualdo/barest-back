<?php

namespace App\Http\Controllers\Pdf;

use App\Http\Controllers\Controller;
use App\Models\PlanUser;
use Barryvdh\DomPDF\Facade\Pdf;

class SuscriptionController extends Controller
{
    public function suscription(PlanUser $suscription)
    {
        $suscription->load([ 'plan', 'customer', 'payment' ]);
        
        $benefits_translated = [
            "publications" => trans("publicaciones de anuncios"),
        ];

        $pdf = Pdf::loadView('pdf.suscription', [ 'suscription' => $suscription, 'benefits_translated' => $benefits_translated ]);
        
        return $pdf->stream('suscription.pdf');
    }
}
