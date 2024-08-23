<?php

namespace App\Services;

use Illuminate\Http\Request;

use App\Traits\ConsumesExternalServices;
use Illuminate\Support\Facades\Auth;
use App\Models\Relaciones\NotificationByUser;
use App\Models\Relaciones\ProductoByUser;
use App\Models\Order\Order;
use App\Models\PaymentHistory;
use App\Models\Product;
use App\Models\User;
use Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Tymon\JWTAuth\Facades\JWTAuth;

class StripeService
{
    use ConsumesExternalServices;

    protected $key;

    protected $secret;

    protected $baseUri;

    protected $plans;

    protected $currency = 'EUR';

    public function __construct()
    {
        $this->baseUri = config('services.stripe.base_uri');
        $this->key = config('services.stripe.key');
        $this->secret = config('services.stripe.secret');
        $this->plans = config('services.stripe.plans');
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
        return "Bearer {$this->secret}";
    }

    public function handlePayment(Request $request)
    {
        Cache::flush();

        $product = Product::findOrFail( $request->product_id );
        
        $intent = $this->createIntent($product->price, $request->payment_method);
        
        Cache::put('paymentIntentId', $intent->id);
        Cache::put('product_id', $product->id);
        \Auth::login( User::find(1) );
        $user = \Auth::user();
        
        return URL::temporarySignedRoute( 'stripe.approval', now()->addMinutes(10), [ 'user_id' => $user->id ] );
    }

    public function handleApproval(Request $request)
    {
        if( !Cache::has('paymentIntentId') )
            abort(422, 'Payment intent not exist.');

        DB::beginTransaction();
        try {
            
            $paymentIntentId = Cache::get('paymentIntentId');
            $product_id = Cache::get('product_id');
            
            $confirmation = $this->confirmPayment($paymentIntentId);
        
            if( $confirmation->status === 'requires_action' ) {
                
                $clientSecret = $confirmation->client_secret;
    
                return view('stripe.3d-secure')->with([
                    'clientSecret' => $clientSecret,
                ]);
            }
    
            if( $confirmation->status !== 'succeeded' )
                abort(422, 'Payment was not succeded.');
    
            $transactionId = $confirmation->id;
            
            $amount = $confirmation->amount / $this->resolveFactor( $this->currency );
            
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
                "payment_method" => "Stripe",
                "concept" => "Compra de slot de anuncio",
                "transaction_number" => $transactionId
            ]);

            Cache::flush();
            DB::commit();
            return true;
        } catch (\Exception $e) {
            Cache::flush();
            DB::commit();
            abort(422, $e->getMessage());
        }
                
    }

    public function handleSubscription(Request $request)
    {        
        $user = \Auth::user();

        $customerName = $user->hasRole('company')
                            ? $user->company_name
                            : "$user->first_name $user->last_name";

        $customerEmail = $user->email;

        $customer = $this->createCustomer(
            $customerName,
            $customerEmail,
            $request->payment_method
        );

        $subscription = $this->createSubscription(
            $customer->id,
            $request->payment_method,
            $this->plans[$request->plan]
        );

        Cache::put('subscriptionId', $subscription->id);

        if( $subscription->status == 'active' ) {
            Cache::put('subscriptionId', $subscription->id);
            
            return (object)[
                'plan' => $request->plan,
                'subscription_id' => $subscription->id,
            ];
        }

        $paymentIntent = $subscription->latest_invoice->payment_intent;

        if ($paymentIntent->status === 'requires_action') {

            $clientSecret = $paymentIntent->client_secret;
            
            Cache::put('subscriptionId', $subscription->id);
            
            return view('stripe.3d-secure-subscription')->with([
                'clientSecret' => $clientSecret,
                'plan' => $request->plan,
                'paymentMethod' => $request->payment_method,
                'subscriptionId' => $subscription->id,
            ]);
        }

        return false;
            
    }
    public function validateSubscription( $orderSuscription )
    {
        if( !Cache::has('subscriptionId') )
            return false;
            
        $subscriptionId = Cache::get('subscriptionId');
        
        return $orderSuscription->subscription_id == $subscriptionId;
    }
    public function createIntent($value, $paymentMethod)
    {   
        return $this->makeRequest(
            'POST',
            '/v1/payment_intents',
            [],
            [
                'amount' => round( $value * $this->resolveFactor( $this->currency ) ),
                'currency' => strtolower( $this->currency ),
                'payment_method' => $paymentMethod,
                'confirmation_method' => 'manual',
            ],
        );
    }
    public function confirmPayment($paymentIntentId)
    {
        return $this->makeRequest(
            'POST',
            "/v1/payment_intents/{$paymentIntentId}/confirm",
        );
    }

    public function createCustomer($name, $email, $paymentMethod)
    {
        return $this->makeRequest(
            'POST',
            '/v1/customers',
            [],
            [
                'name' => $name,
                'email' => $email,
                'payment_method' => $paymentMethod,
            ],
        );
    }
    public function createSubscription($customerId, $paymentMethod, $priceId)
    {
        return $this->makeRequest(
            'POST',
            '/v1/subscriptions',
            [],
            [
                'customer' => $customerId,
                'items' => [
                    ['price' => $priceId],
                ],
                'default_payment_method' => $paymentMethod,
                'expand' => ['latest_invoice.payment_intent']
            ],
        );
    }
    public function deleteSubscription($Id)
    {
        return $this->makeRequest(
            'DELETE',
            '/v1/subscriptions/'.$Id,
            [],
            [],
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