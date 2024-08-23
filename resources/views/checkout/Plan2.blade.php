<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <form id="paymentFormStripe" action="{{ route('stripe.subscribe') }}" method="POST">
    @csrf
    <input type="hidden" name="payment_method" id="paymentMethod">
    @foreach ($plan->prices as $precio)
                    <label
                        class="btn btn-outline-info rounded m-2 p-3"
                    >
                        <input
                            type="radio"
                            name="plan"
                            value="{{$plan->id}}-{{ $precio->slug }}"
                            {{($planSelected==$plan->id."-".$precio->slug) ? "checked" : "" }}
                            required
                        >
                        <p class="h2 font-weight-bold text-capitalize">
                            {{ $precio->slug }}
                        </p>

                        <p class="display-4 text-capitalize">
                            ${{ $precio->price }}
                        </p>
                    </label>
                    @endforeach

    <label class="mt-3">Card details:</label>
    <div id="cardElement"></div>
    <small class="form-text text-muted" id="cardErrors" role="alert"></small>
    <div class="text-center mt-3">
            <button type="submit" id="payButton" class="btn btn-primary btn-lg">Pay</button>
    </div>
</form>
 
</x-app-layout>




