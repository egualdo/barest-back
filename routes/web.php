<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\PaypalController;
use App\Http\Controllers\Pdf\PurchaseController;
use App\Http\Controllers\Pdf\SuscriptionController;
use App\Http\Controllers\PlanController;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return view('welcome');
});

Route::get('/login/{socialNetwork}', [ AuthController::class, 'redirectToSocialNetwork' ])->name('social.auth');
Route::get('/login/{socialNetwork}/callback', [ AuthController::class, 'handleSocialNetworkCallback' ]);

Route::get('/suscription/{suscription}/invoice', [ SuscriptionController::class, 'suscription' ]);
Route::get('/product/{purchase}/invoice', [ PurchaseController::class, 'purchase' ]);

Route::get('/show-product/{id}', [ ProductController::class, 'showCheckout'])->name('producto.show.checkout');
Route::get('/show-plan/{plan}/{planSelected}', [ PlanController::class, 'showCheckoutPlan'])->name('plan.show.checkout');

Route::get('/show-product-s/{id}', [ ProductController::class, 'showCheckout2'])->name('producto.show.checkout2');
Route::get('/show-plan-s/{id}/{planSelected}', [ PlanController::class, 'showCheckoutPlan2'])->name('plan.show.checkout2');

Route::prefix('payments')->group(function() {
    Route::get('/paypal/approval', [ PaypalController::class, 'approval' ])->name('paypal.approval');
    Route::get('/paypal/cancelled', [ PaypalController::class, 'cancelled' ])->name('paypal.cancelled');

    Route::get('/stripe/approval', [ StripeController::class, 'approval' ])->name('stripe.approval');
    Route::get('/stripe/cancelled', [ StripeController::class, 'cancelled' ])->name('stripe.cancelled');
});
