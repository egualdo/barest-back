<?php

namespace App\Services;

use Illuminate\Http\Request;
use App\Traits\ConsumesExternalServices;
use App\Models\Product;
use App\Models\User;
use Cache;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Tymon\JWTAuth\Facades\JWTAuth;

class PayPalService
{
    use ConsumesExternalServices;

    protected $baseUri;

    protected $clientId;

    protected $clientSecret;

    protected $plans;

    protected $currency = 'EUR';

    public function __construct()
    {
        $this->baseUri = config('services.paypal.base_uri');
        $this->clientId = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.client_secret');
        $this->plans = config('services.paypal.plans');
    }

    public function resolveAuthorization(&$queryParams, &$formParams, &$headers)
    {
        $headers['Authorization'] = $this->resolveAccessToken();
    }

    public function decodeResponse($response)
    {
        return json_decode($response);
    }

    public function resolveAccessToken()
    {
        $credentials = base64_encode("{$this->clientId}:{$this->clientSecret}");

        return "Basic {$credentials}";
    }

    public function handlePayment(Request $request)
    {
        Cache::flush();

        $product = Product::findOrFail( $request->product_id );

        $order = $this->createOrder( $product->price );
        $orderLinks = collect($order->links);
        $approve = $orderLinks->where('rel', 'approve')->first();
        
        Cache::put('approvalId', $order->id);
        Cache::put('product_id', $product->id);

        return $approve->href;
    }

    public function handleApproval($request)
    {                
        if ( !Cache::has('approvalId') ) {
            info('Approval data not exists.');
            return false;
        }

        DB::beginTransaction();
        try {
            
            $approval_id = Cache::get('approvalId');
            $product_id = Cache::get('product_id');
            
            $payment = $this->capturePayment($approval_id);
        
            $transactionId=$payment->purchase_units[0]->payments->captures[0]->id;
            $payment = $payment->purchase_units[0]->payments->captures[0]->amount;
            $amount = $payment->value;
            
            $user = User::find( $request->query('user_id') );
    
            $user->products()
                ->attach( $product_id, [
                    'active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            
            $product_acquired = $user->products()
                                    ->where('product_id', $product_id)
                                    ->latest()
                                    ->first()
                                    ->purchase;
            
            $product_acquired->payment()->create([
                                            "user_id" => $user->id,
                                            "mount" => $amount,
                                            "status" => "approved",
                                            "payment_method" => "Paypal",
                                            "concept" => "Compra de slot de anuncio",
                                            "transaction_number" => $transactionId
                                        ]);

            Cache::flush();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            Cache::flush();
            DB::rollback();
            abort(422, $e->getMessage());
        }

    }

    public function handleSubscription(Request $request)
    {   
        Cache::flush();

        $user = \Auth::user();//JWTAuth::parseToken()->authenticate();

        $customerName = $user->hasRole('company')
                            ? $user->company_name
                            : "$user->first_name $user->last_name";

        $customerEmail = $user->email;

        $subscription = $this->createSubscription(
            $request->plan,
            $customerName,
            $customerEmail
        );

        
        Cache::put('subscriptionId', $subscription->id);
        $subscriptionLinks = collect($subscription->links);

        $approve = $subscriptionLinks->where('rel', 'approve')->first();
       
        return $approve->href;
    }

    public function validateSubscription(Request $request)
    {
        if ( !Cache::has('subscriptionId') )
            return false;

        $subscriptionId = Cache::get('subscriptionId');

        return $request->subscription_id == $subscriptionId;        
    }

    public function createOrder($value)
    {
        $user = \Auth::user();//JWTAuth::parseToken()->authenticate();
        
        return $this->makeRequest(
            'POST',
            '/v2/checkout/orders',
            [],
            [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    0 => [
                        'amount' => [
                            'currency_code' => strtoupper( $this->currency ),
                            'value' => round($value * $factor = $this->resolveFactor( $this->currency )) / $factor,
                        ]
                    ]
                ],
                'application_context' => [
                    'brand_name' => config('app.name'),
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'PAY_NOW',
                    'return_url' => URL::temporarySignedRoute( 'paypal.approval', now()->addMinutes(10), [ 'user_id' => $user->id ] ),
                    'cancel_url' => URL::temporarySignedRoute( 'paypal.cancelled', now()->addMinutes(10) ),
                ]
            ],
            [],
            $isJsonRequest = true,
        );
    }

    public function capturePayment($approvalId)
    {
        return $this->makeRequest(
            'POST',
            "/v2/checkout/orders/{$approvalId}/capture",
            [],
            [],
            [
                'Content-Type' => 'application/json',
            ],
        );
    }

    public function createSubscription($planSlug, $name, $email)
    {   
        $user = \Auth::user();// JWTAuth::parseToken()->authenticate();
        
        try {
            
            return $this->makeRequest(
                'POST',
                '/v1/billing/subscriptions',
                [],
                [
                    'plan_id' => $this->plans[$planSlug],
                    'subscriber' => [
                        'name' => [
                            'given_name' => $name,
                        ],
                        'email_address' => $email,
                    ],
                    'application_context' => [
                        'brand_name' => config('app.name'),
                        'shipping_preference' => 'NO_SHIPPING',
                        'user_action' => 'SUBSCRIBE_NOW',
                        'return_url' => URL::temporarySignedRoute( 'paypal.subscribe.approval', now()->addMinutes(10), [ 'plan' => $planSlug, 'user_id' => $user->id ] ),
                        'cancel_url' => URL::temporarySignedRoute( 'paypal.subscribe.cancelled', now()->addMinutes(10) ),
                    ]
                ],
                [],
                $isJsonRequest = true,
            );
        } catch(\Throwable $th) {          
            abort( 422, 'An error occurs during the request.' );
            //  if ($th instanceof ClientException) {
            //     $r = $th->getResponse();
            //     $responseBodyAsString = json_decode($r->getBody()->getContents());
            //     dd($responseBodyAsString);
            // } 
        }

    }

    public function cancelSubscription($id)
    {
        return $this->makeRequest(
            'POST',
            '/v1/billing/subscriptions/'.$id.'/cancel',
            [],
            [
                'reason' => "cancelada",
            ],
            [],
            $isJsonRequest = true,
        );
    }

    public function resolveFactor($currency)
    {
        $zeroDecimalCurrencies = ['JPY'];

        if (in_array(strtoupper($currency), $zeroDecimalCurrencies))
            return 1;
        
        return 100;
    }
}