<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <form id="paymentFormStripe" action="{{ route('stripe.payment') }}" method="POST">
        @csrf
        <input type="hidden" name="payment_method" id="paymentMethod">
        <input type="hidden" name="currency" value="eur">
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" name="value"  value="{{ $product->price }}"> 
        <label class="mt-3">Card details:</label>
        
        <div id="cardElement"></div>
        <small class="form-text text-muted" id="cardErrors" role="alert"></small>
    
        <div class="text-center mt-3">
            <button type="submit" id="payButton" class="btn btn-primary btn-lg">Pay</button>
        </div>
    </form>
</x-app-layout>
