<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StripeService;
use App\Models\PlanPrice;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Cache;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Validation\Rules\Enum;
use App\Enums\ProductTypesEnum;
use App\Models\User;

class StripeController extends Controller
{
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

        $paymentPlatform = resolve(StripeService::class);
        $response = $paymentPlatform->handlePayment($request);

        return $this->showMessage( $response );
    }

    public function approval(Request $request)
    {
        $paymentPlatform = resolve(StripeService::class);

        return $paymentPlatform->handleApproval( $request )
                    ? redirect()->away( env("HOST_DESTINATION")."/seller/success" )
                    : redirect()->away( env("HOST_DESTINATION")."/seller/cancel" );
    }
    
    public function cancelled()
    {
        return redirect()
            ->route('dashboard')
            ->withErrors('You cancelled the payment');
    }
    public function store(Request $request)
    {
        \Auth::login( User::find(1) );
        $user = \Auth::user();

        if( $user->has_active_plan() )
            return $this->error('You already have an active plan on course.', 400);
            
        $rules = [
            'plan' => ['required'],
        ];

        $request->validate($rules);
        $paymentPlatform = resolve(StripeService::class);

        $orderSuscription = $paymentPlatform->handleSubscription($request);

        return $this->subscribeApproval( $orderSuscription )
                    ? $this->success('Subscription done.')
                    : $this->error('Subscription failed.', 422);
    }

    public function subscribeApproval( $orderSuscription )
    {
        if( !$orderSuscription->plan )
            return $this->error('Choosed suscription not exists', 422);
        
        $paymentPlatform = resolve(StripeService::class);
        $datavalid = $paymentPlatform->validateSubscription( $orderSuscription );
        
        if( !$datavalid )
            return $this->error('Invalid data.', 422);

        DB::beginTransaction();
        try {

            switch( $orderSuscription->plan ) {
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
                    return $this->error('Invalid period plan choosed', 422);
                break;
            }
            
            $user = \Auth::user();
            
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
                "mount" => $price->price,
                "status" => "approved",
                "payment_method" => "Stripe",
                "concept" => "Suscripción de plan",
                "transaction_number" => Cache::get('subscriptionId')
            ]);

            Cache::flush('subscriptionId');
            DB::commit();            
            return true;
        } catch (\Exception $e) {
            Cache::flush();
            DB::rollback();
            throw new \Exception( $e->getMessage() );
        }        
        
        return false;
    }

    public function suscriptionCancelledRecurrence()
    {       
        $user = Auth::user()->load([ 'active_plan' ]);
        
        $active_plan = $user->active_plan->first();

        try {
            $order = $active_plan->suscription->payment;
    
            $paymentPlatform = resolve(StripeService::class);
            
            $paymentPlatform->deleteSubscription( $order->transaction_number );
            
            return $this->success('Subscription cancelled.');
        } catch (\Throwable $th) {
            return $this->error('Error canceling the subscription', 422);
        }
    }

    public function subscribeCancelled()
    {
        return redirect()
            ->route('dashboard')
            ->withErrors('You cancelled. Comeback whenever you\'re ready :)');
    }
   
}