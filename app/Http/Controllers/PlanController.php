<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\Plan;
use Illuminate\Support\Facades\DB;

class PlanController extends Controller
{

    public function index()
    {
        try {
            $plans = Plan::activos()->get();

            return $this->showAll($plans);
        } catch (\Exception $exc) {
            return $this->error($exc->getMessage(), 400);
        }   
    }

   
    // public function create()
    // {
    //     // if (\Auth::user()->type == 'admin') {
    //         if ((env('ENABLE_STRIPE') == 'on' &&
    //              !empty(env('STRIPE_KEY')) &&
    //               !empty(env('STRIPE_SECRET'))) ||
    //                (env('ENABLE_PAYPAL') == 'on' && !empty(env('PAYPAL_CLIENT_ID')) && !empty(env('PAYPAL_SECRET_KEY')))) {
    //             $plan = new Plan();

    //             return view('plans.create', compact('plan'));
    //         } else {
    //             return redirect()->back()->with('error', __('Permission Denied.'));
    //         }
    //     // } else {
    //     //     return redirect()->back()->with('error', __('Permission Denied.'));
    //     // }
    // }

  
    public function store(Request $request)
    {
            if (empty(env('STRIPE_KEY')) && empty(env('STRIPE_SECRET')) && empty(env('PAYPAL_CLIENT_ID')) && empty(env('PAYPAL_SECRET_KEY'))) {
                
                  return response()->json(["state"=>'Error','error'=>'Please set stripe api key & secret key for add new plan'], 200);
            } else {

                try {
                 
                        $validation                 = [];
                        $validation['name']         = 'required|unique:plans';
                        $validation['price']        = 'required|numeric|min:0';
                        $validation['duration']     = 'required';
                        $validation['max_adversitements']    = 'required|numeric';

                        $validator = \Validator::make($request->all(), $validation);

                        if ($validator->fails()) {
                            $messages = $validator->getMessageBag();

                            return response()->json(["state"=>'Error','error'=>$messages->first()], 200);
                        }

                        $pl = $request->all();

                        if (Plan::create($pl)) {
                            // return redirect()->back()->with('success', __('Plan created Successfully!'));
                            return response()->json(["state"=>'Plan created Successfully!'], 200);
                        }


                } catch (Exception $th) {
                     return response()->json(['state'=>'Error','error'=>$th], 500);
                }
               
                
            }
    }

    public function update(Request $request,$planid)
    {

        $plan=Plan::find($planid);
            try {
                if ($plan) {
                    $validation                 = [];
                    $validation['name']         = 'required|unique:plans,name,' . $plan->id;
                    $validation['price']        = 'required|numeric|min:0';
                    $validation['duration']     = 'required';
                    $validation['max_adversitements']    = 'required|numeric';

                    $validator = \Validator::make($request->all(), $validation);
                    if ($validator->fails()) {
                        $messages = $validator->getMessageBag();
                        return response()->json(["state"=>'Error','error'=>$messages->first()], 200);
                    }

                    $p = $request->all();

                    if ($plan->update($p)) {
                        // return redirect()->back()->with('success', __('Plan updated Successfully!'));
                        return response()->json(["state"=>'Plan updated Successfully!'], 200);
                    }
                 
                } else {
                    return response()->json(['state'=>'Error','error'=>'Plan not found'], 200);
                }
            } catch (Exception $th) {
               return response()->json(['state'=>'Error','error'=>$th], 500);
            }
          
    }


    public function userPlan(Request $request)
    {
        $objUser = Auth::user();
        $planID  = \Illuminate\Support\Facades\Crypt::decrypt($request->code);
        $plan    = Plan::find($planID);

        try {
            if ($plan) {
                if ($plan->price <= 0) {
                    $objUser->assignPlan($plan->id);
                    return response()->json(['state'=>'Plan activated Successfully'], 200);
                }

            
            } else {
                return response()->json(['state'=>'Error','error'=>'Plan not found'], 200);
            }
        }catch (Exception $th) {
                return response()->json(['state'=>'Error','error'=>$th], 500);
        }
        
    }


     public function orderList()
    {
        // if (\Auth::user()->type == 'admin') {
            $orders = Order::select(
                                [
                                    'orders.*',
                                    'users.name as user_name',
                                ]
                            )->join('users', 'orders.user_id', '=', 'users.id')
                            ->orderBy('orders.created_at', 'DESC')
                            ->get();

            // return view('plans.orderlist', compact('orders'));
            $orders->paginate(8);
            return response()->json(['state'=>'Success','data'=>$orders], 200);
        // } else {
        //     return redirect()->back()->with('error', __('Permission Denied.'));
        // }
    }

    public function showCheckoutPlan(Plan $plan, $planSelected)
    {
        $plan->load(['prices']);
            
        return view('Checkout.Plan', compact('plan', 'planSelected'));
    }

    public function showCheckoutPlan2($id , $planSelected)
    {
        $plan = Plan::find($id)->load(['prices']);
        
        return view('Checkout.Plan2', compact('plan','planSelected'));
    }

    public function callOffSubscriptionRecurrence()
    {
        $user = Auth::user()->load([ 'active_plan' ]);
        
        $plan = $user->active_plan->first();
        
        DB::beginTransaction();
        try {
            
            $order = $plan->suscription->payment;
            
            if( $order->payment_method === 'Paypal' ){                
                $paymentPlatform = resolve(PayPalService::class);
                $paymentPlatform->cancelSubscription($order->transaction_number);
            } else {
                $paymentPlatform = resolve(StripeService::class);
                $paymentPlatform->deleteSubscription($order->transaction_number);
            }
            
            $plan->suscription->status = "CANCELADA";
            $plan->suscription->update();

            DB::commit();
            return $this->success('Cancelled subscription.');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->error( 'Cancelation failed: '.$e->getMessage(), 422 );
        }
    }

}
