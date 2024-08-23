<!DOCTYPE html>
<html>
<head>
    <title>Orden de suscripción</title>
</head>
<style type="text/css">
    body{
        font-family: 'Roboto Condensed', sans-serif;
    }
    p {
        text-transform: capitalize;
    }
    ul li  {
        text-transform: capitalize;
    }
    .m-0{
        margin: 0px;
    }
    .p-0{
        padding: 0px;
    }
    .pt-5{
        padding-top:5px;
    }
    .mt-10{
        margin-top:10px;
    }
    .text-center{
        text-align:center !important;
    }
    .w-100{
        width: 100%;
    }
    .w-50{
        width:50%;   
    }
    .w-85{
        width:85%;   
    }
    .w-15{
        width:15%;   
    }
    .logo img{
        width:30%;
    }
    .gray-color{
        color:#5D5D5D;
    }
    .text-bold{
        font-weight: bold;
    }
    .border{
        border:1px solid black;
    }
    table tr,th,td{
        border: 1px solid #d2d2d2;
        border-collapse:collapse;
        padding:7px 8px;
    }
    table tr th{
        background: #F4F4F4;
        font-size:15px;
    }
    table tr td{
        font-size:13px;
    }
    table{
        border-collapse:collapse;
    }
    .box-text p{
        line-height:10px;
    }
    .float-left{
        float:left;
    }
    .float-right{
        float:right;
    }
    .total-part{
        font-size:16px;
        line-height:12px;
    }
    .total-right p{
        padding-right:20px;
    }
    .primary-bg {
        background-color: #222240;
        color: white;
    }
</style>
<body>
<div class="logo mt-10 text-center">
    <img src="{{ asset('images/logo.png') }}"
        alt="Autos Motos"
        style="font-family: Avenir, Helvetica, sans-serif; box-sizing: border-box; border: none;">
</div>
<hr>
<div class="head-title mt-10">
    <h2 class="text-center m-0 p-0">Orden de suscripción</h2>
</div>
<div class="add-detail mt-10">
    <div class="w-50 mt-10">
        <p class="m-0 pt-5 text-bold w-100">Identificador de factura: <br><span class="gray-color">#{{ $suscription->payment->id }}</span></p>
        <p class="m-0 pt-5 text-bold w-100">Identificador de la orden: <br><span class="gray-color">{{ $suscription->payment->transaction_number }}</span></p>
        <p class="m-0 pt-5 text-bold w-100">Fecha de la orden: <br><span class="gray-color">{{ $suscription->payment->created_at->format('d/m/y h:m a') }}</span></p>
    </div>
</div>
<div class="table-section bill-tbl w-100 mt-10">
    <table class="table w-100 mt-10">
        <tr>
            <th class="w-50 primary-bg">Cliente</th>
        </tr>
        <tr>
            <td>
                <div class="box-text">
                    @if( is_null( $suscription->customer->company_name ) )
                        <p>{{ $suscription->customer->name }} {{ $suscription->customer->last_name }},</p>
                    @else
                        <p>{{ $suscription->customer->company_name }},</p>
                    @endif
                    <!-- <p>{{ $suscription->customer->address }}</p> -->
                    <!-- <p>{{ $suscription->customer->postal_code }}</p> -->
                    <!-- <p>{{ $suscription->customer->city }}</p> -->
                    <!-- <p>{{ $suscription->customer->country }}</p> -->
                    <p>Contacto: <span>{{ $suscription->customer->country_code }}</span> <span>{{ $suscription->customer->phone_number }}</span> </p>
                </div>
            </td>            
        </tr>
    </table>
</div>
<div class="table-section bill-tbl w-100 mt-10">
    <table class="table w-100 mt-10">
        <tr>
            <th class="w-50 primary-bg">Metodo de pago</th>
        </tr>
        <tr>
            @if( $suscription->payment->payment_method === 'Stripe' )
                <td>Tarjeta de credito</td>
            @else
                <td>{{ $suscription->payment->payment_method }}</td>
            @endif
        </tr>
    </table>
</div>
<div class="head-title mt-10">
    <h4 class="text-center m-0 p-0">Detalles de orden</h4>
</div>
<div class="table-section bill-tbl w-100">
    <p class="m-0 pt-5 text-bold w-100 mt-10">Plan: <span class="gray-color">{{ $suscription->plan->name }}</span></p>
    <p class="m-0 pt-5 text-bold w-100 mt-10">Fecha de vencimiento: <br><span class="gray-color">{{ $suscription->date_end_at->format('d-m-Y') }}</span></p>
    <p class="m-0 pt-5 text-bold w-100 mt-10">Importe: <br><span class="gray-color">€{{ $suscription->payment->mount }}</span></p>
    <p class="m-0 pt-5 text-bold w-100 mt-10">Incluido:</p>
    <ul>
        @foreach( json_decode( $suscription->plan->benefits, true) as $key => $benefit )
            <li>
                <span class="gray-color">{{ $benefits_translated[$key] }}: {{ $benefit }}</span>
            </li>
        @endforeach
    </ul>    
</div>
</body>
</html>