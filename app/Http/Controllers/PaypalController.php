<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PayPalService;
use App\Models\PlanPrice;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Cache;
use Illuminate\Validation\Rules\Enum;
use App\Enums\ProductTypesEnum;

class PaypalController extends Controller
{
    protected $paymentPlatformResolver;
    
    public function pay(Request $request)
    {
        $rules = [
            'product_id' => 'required|numeric|exists:products,id',
            'type' => [
                'sometimes',
                'required',
                ( new Enum( ProductTypesEnum::class ) )
            ]
        ];

        $request->validate($rules);

        $paymentPlatform = resolve(PayPalService::class);
        $response = $paymentPlatform->handlePayment($request);

        return $this->showMessage( $response );
    }

    public function approval(Request $request)
    {
        if( !$request->hasValidSignatureWhileIgnoring([ 'subscription_id', 'ba_token', 'token', 'PayerID' ]) )
            abort(403, 'Invalid Signature');

        $paymentPlatform = resolve(PayPalService::class);
        
        return $paymentPlatform->handleApproval( $request )
                    ? redirect()->away( env("HOST_DESTINATION")."/seller/success" )
                    : redirect()->away( env("HOST_DESTINATION")."/seller/cancel" );
    }

    public function cancelled(Request $request)
    {
        if( !$request->hasValidSignatureWhileIgnoring([ 'subscription_id', 'ba_token', 'token' ]) )
            abort(403, 'Invalid Signature');

        return $this->success('cancelled payment.');
    }

    public function store(Request $request)
    {
        $user = \Auth::user();

        if( $user->has_active_plan() )
            return $this->error('You already have an active plan on course.', 400);

        $rules = [
            'plan' => ['required'],
        ];

        $request->validate($rules);
        $paymentPlatform = resolve(PayPalService::class);

        $response = $paymentPlatform->handleSubscription($request);

        return $this->showMessage( $response );
    }

    public function subscribeApproval(Request $request)
    {
        if ( !$request->hasValidSignatureWhileIgnoring([ 'subscription_id', 'ba_token', 'token' ]) )
            abort(403, 'Invalid Signature');
            
        $rules = [
            'plan' => ['required'],
        ];

        $request->validate($rules);
        $paymentPlatform = resolve(PayPalService::class);
        
        $datavalid=$paymentPlatform->validateSubscription($request);

        DB::beginTransaction();
        try {

            if( !$datavalid )
                return $this->error('Invalid data, try again.', 403);

            switch( $request->plan ) {
                case '1-mensual':
                    $price = PlanPrice::where('plan_id',1)->where('slug','mensual')->first();
                    $plan_id=1;
                break;
                case '1-trimestral':
                    $price = PlanPrice::where('plan_id',1)->where('slug','trimestral')->first();
                    $plan_id=1;
                break;
                case '1-semestral':
                    $price = PlanPrice::where('plan_id',1)->where('slug','semestral')->first();
                    $plan_id=1;
                break;
                case '2-mensual':
                    $price = PlanPrice::where('plan_id',2)->where('slug','mensual')->first();
                    $plan_id=2;
                break;
                case '2-trimestral':
                    $price = PlanPrice::where('plan_id',2)->where('slug','trimestral')->first();
                    $plan_id=2;
                break;
                case '2-semestral':
                    $price = PlanPrice::where('plan_id',2)->where('slug','semestral')->first();
                    $plan_id=2;
                break;
                case '3-mensual':
                    $price = PlanPrice::where('plan_id',3)->where('slug','mensual')->first();
                    $plan_id=3;
                break;
                case '3-trimestral':
                    $price = PlanPrice::where('plan_id',3)->where('slug','trimestral')->first();
                    $plan_id=3;
                break;
                case '3-semestral':
                    $price = PlanPrice::where('plan_id',3)->where('slug','semestral')->first();
                    $plan_id=3;
                break;
                case '4-mensual':
                    $price = PlanPrice::where('plan_id',4)->where('slug','mensual')->first();
                    $plan_id=4;
                break;
                case '4-trimestral':
                    $price = PlanPrice::where('plan_id',4)->where('slug','trimestral')->first();
                    $plan_id=4;
                break;
                case '4-semestral':
                    $price = PlanPrice::where('plan_id',4)->where('slug','semestral')->first();
                    $plan_id=4;
                break;                    
                default:
                    return $this->error( 'Invalid plan selected', 403 );
                break;
            }
            
            $user = User::find( $request->query('user_id') );
                                    
            $user->plans()
                ->attach( $plan_id, [
                    'active' => 1,
                    'date_end_at' => Carbon::today()->addMonths( $price->duration ),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            
            $active_plan = $user->active_plan->first();
        
            $active_plan->suscription->payment()->create([
                "user_id" => $user->id,
                "transaction_number" => Cache::get('subscriptionId'),
                "mount" => $price->price,
                "concept" => "Suscripción de plan",
                "payment_method" => "Paypal",
                "status" => "approved",
            ]);

            Cache::flush();
            DB::commit();
            return redirect()->away( env("HOST_DESTINATION")."/seller/success" );
        } catch (\Exception $e) {
            Cache::flush();
            DB::rollback();
            info( $e->getMessage() );
            return redirect()->away( env("HOST_DESTINATION")."/seller/cancel" );
        }

    }

    public function suscriptionCancelledRecurrence()
    {       
        $user = Auth::user()->load([ 'active_plan' ]);
        
        $active_plan = $user->active_plan->first();
        
        DB::beginTransaction();
        try {
            
            $order = $active_plan->suscription->payment;
    
            $paymentPlatform = resolve(PayPalService::class);
            
            $paymentPlatform->cancelSubscription( $order->transaction_number );        
                        
            DB::commit();
            return $this->success('Cancelled subscription.');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error( 'Cancelation failed: '.$e->getMessage(), 422 );
        }
    }

    public function suscriptionCancelled(Request $request)
    {
        if( !$request->hasValidSignatureWhileIgnoring([ 'subscription_id', 'ba_token', 'token' ]) )
            abort(403, 'Invalid Signature');

        return $this->success('Cancel done.');
    }
    
}