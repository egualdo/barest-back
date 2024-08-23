<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    function destroy(Review $review) {
        try {
            
            $review->report_received()->update([ 'status' => 'ACCEPTED' ]);
            $review->delete();

            return $this->success('review deleted');
        } catch (\Exception $exc) {
            return $this->error('Error: '.$exc, 400);
        }
    }
}
