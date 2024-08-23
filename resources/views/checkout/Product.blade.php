<form  action="{{ route('paypal.payment') }}" class=" d-flex flex-column mx-2" id="paypal" method="POST" enctype="multipart/form-data">
	@csrf
    <input type="hidden" name="currency" value="eur">
    <input type="hidden" name="product_id" value="{{$product->id}}">
    <input type="hidden" name="value"  value="{{$product->price}}"> 
	<input type="submit" class="btn btn-block btn-success py-2 my-2" value="Completar pago con paypal">
	<span class="h6 color-plomo text-center my-3 mx-auto">
		Estas muy cerca 
	</span>
</form>