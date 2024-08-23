<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <style type="text/css">
    /**
     * The CSS shown here will not be introduced in the Quickstart guide, but shows
     * how you can use CSS to style your Element's container.
     */
    .StripeElement {
      box-sizing: border-box;
      height: 40px;
      padding: 10px 12px;
      border: 1px solid transparent;
      border-radius: 4px;
      background-color: white;
      box-shadow: 0 1px 3px 0 #e6ebf1;
      -webkit-transition: box-shadow 150ms ease;
      transition: box-shadow 150ms ease;
    }
    .StripeElement--focus {
      box-shadow: 0 1px 3px 0 #cfd7df;
    }
    .StripeElement--invalid {
      border-color: #fa755a;
    }
    .StripeElement--webkit-autofill {
      background-color: #fefde5 !important;
    }
    .flexi{
        display:flex;
        width:100%;
        flex-direction:column;
    }
</style>
        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            
            <!-- Page Heading -->
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        <script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe('{{ config('services.stripe.key') }}');
    const elements = stripe.elements({locale: 'en'});
    const cardElement = elements.create('card');
    cardElement.mount('#cardElement');
</script>

<script>
    const form = document.getElementById('paymentFormStripe');
    const payButton = document.getElementById('payButton');
 
    payButton.addEventListener('click', async(e) => {
            e.preventDefault();
   
            const { paymentMethod, error} = await stripe.createPaymentMethod(
                'card', cardElement, {
                    billing_details: {
                        "name": "John Doe",
                        "email": "JohnD@correo.com"
                    }
                }
            );
            if (error) {
                console.log('error');
                const displayError = document.getElementById('cardErrors');
                displayError.textContent = error.message;
            } else {
                console.log('correcto');
                const tokenInput = document.getElementById('paymentMethod');
                tokenInput.value = paymentMethod.id;
            
                form.submit();
            }
        
    });
</script>

    </body>
</html>
