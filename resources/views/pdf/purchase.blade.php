<!DOCTYPE html>
<html>
<head>
    <title>Orden de producto</title>
</head>
<style type="text/css">
    body{
        font-family: 'Roboto Condensed', sans-serif;
    }
    p {
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
    <h2 class="text-center m-0 p-0">Orden de producto</h2>
</div>
<div class="add-detail mt-10">
    <div class="w-50 mt-10">
        <p class="m-0 pt-5 text-bold w-100">Identificador de factura: <br><span class="gray-color">#{{ $purchase->payment->id }}</span></p>
        <p class="m-0 pt-5 text-bold w-100">Identificador de la orden: <br><span class="gray-color">{{ $purchase->payment->transaction_number }}</span></p>
        <p class="m-0 pt-5 text-bold w-100">Fecha de la orden: <br><span class="gray-color">{{ $purchase->payment->created_at->format('d/m/y h:m a') }}</span></p>
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
                    @if( is_null( $purchase->customer->company_name ) )
                        <p>{{ $purchase->customer->name }} {{ $purchase->customer->last_name }},</p>
                    @else
                        <p>{{ $purchase->customer->company_name }},</p>
                    @endif
                    <!-- <p>{{ $purchase->customer->address }}</p> -->
                    <!-- <p>{{ $purchase->customer->postal_code }}</p> -->
                    <!-- <p>{{ $purchase->customer->city }}</p> -->
                    <!-- <p>{{ $purchase->customer->country }}</p> -->
                    <p>Contacto: <span>{{ $purchase->customer->country_code }}</span> <span>{{ $purchase->customer->phone_number }}</span> </p>
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
            @if( $purchase->payment->payment_method === 'Stripe' )
                <td>Tarjeta de credito</td>
            @else
                <td>{{ $purchase->payment->payment_method }}</td>
            @endif
        </tr>
    </table>
</div>
<div class="head-title mt-10">
    <h4 class="text-center m-0 p-0">Detalles de orden</h4>
</div>
<div class="table-section bill-tbl w-100">
    <p class="m-0 pt-5 text-bold w-100 mt-10">Producto: <span class="gray-color">{{ $purchase->product->name }}</span></p>
    <p class="m-0 pt-5 text-bold w-100 mt-10">Tiempo de vigencia (Días): <span class="gray-color">{{ $purchase->product->days_duration }}</span></p>
    <p class="m-0 pt-5 text-bold w-100 mt-10">Importe: <br><span class="gray-color">€{{ $purchase->payment->mount }}</span></p>
    <p class="m-0 pt-5 text-bold w-100 mt-10">Incluido:</p>
    <ul>        
        <li>
            <span class="gray-color">{{ $products_translated[$purchase->product->type] }}:</span>
            @if( is_null( $purchase->date_end_at ) )
                <span>
                    DISPONIBLE
                </span>
            @else
                <span style="color:#ed1c24;">
                    UTILIZADO
                </span>
            @endif
        </li>        
    </ul>    
</div>
</body>
</html>