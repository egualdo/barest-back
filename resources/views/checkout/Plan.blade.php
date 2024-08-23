<form id="paymentFormStripe" action="{{ route('paypal.subscribe') }}" method="POST">
    @csrf
    @foreach ($plan->prices as $price)
                                            <label
                                                class="btn btn-outline-info rounded m-2 p-3"
                                            >
                                                <input
                                                    type="radio"
                                                    name="plan"
                                                    value="{{$plan->id}}-{{ $price->slug }}"
                                                    {{($planSelected==$plan->id."-".$price->slug) ? "checked" : "" }}
                                                    required
                                                >
                                                <p class="h2 font-weight-bold text-capitalize">
                                                    {{ $price->slug }}
                                                </p>

                                                <p class="display-4 text-capitalize">
                                                    ${{ $price->price }}
                                                </p>
                                            </label>
                                            @endforeach
    <div class="text-center mt-3">
            <button type="submit" id="payButton" class="btn btn-primary btn-lg">Pay</button>
    </div>
</form>

