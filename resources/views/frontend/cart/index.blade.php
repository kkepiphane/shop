	@extends('app')

	@section('content')
	<!-- Start Hero Section -->
	<div class="hero">
		<div class="container">
			<div class="row justify-content-between">
				<div class="col-lg-5">
					<div class="intro-excerpt">
						<h1>Cart</h1>
					</div>
				</div>
				<div class="col-lg-7">

				</div>
			</div>
		</div>
	</div>

	<div class="untree_co-section before-footer-section">
		<div class="container">
			<div class="row mb-5">
				<div class="site-blocks-table">
					<table class="table">
						<thead>
							<tr>
								<th class="product-thumbnail">Image</th>
								<th class="product-name">Produit</th>
								<th class="product-price">Prix</th>
								<th class="product-quantity">Quantité</th>
								<th class="product-total">Total</th>
								<th class="product-remove">Supprimer</th>
							</tr>
						</thead>
						<tbody>
							@if($cartItems && count($cartItems) > 0)
							@foreach($cartItems as $item)
							<tr>
								<td class="product-thumbnail">
									<img src="{{ asset('storage/' . $item->attributes->image) }}" alt="Image" class="img-fluid">
								</td>
								<td class="product-name">
									<h2 class="h5 text-black">{{ $item->name }}</h2>
								</td>
								<td>{{ $item->price }} FCFA</td>
								<td>
									<div class="input-group mb-3 d-flex align-items-center quantity-container" style="max-width: 120px;">
										<div class="input-group-prepend">
											<button class="btn btn-outline-black decrease" type="button">&minus;</button>
										</div>
										<input type="text" class="form-control text-center quantity-amount" min="1" value="{{ $item->quantity }}" placeholder="" aria-label="Example text with button addon" aria-describedby="button-addon1">
										<div class="input-group-append">
											<button class="btn btn-outline-black increase" type="button">&plus;</button>
										</div>
									</div>
								</td>
								<td>{{ $item->price * $item->quantity }} FCFA</td>
								<td>
									<form action="{{ route('cart.remove') }}" method="POST">
										@csrf
										<input type="hidden" name="product_id" value="{{ $item->id }}">
										<button type="submit" class="btn btn-black btn-sm">X</button>
									</form>
								</td>
							</tr>
							@endforeach

							@else
							<tr>
								<td colspan="6" class="text-center"> <i>Panier vide</i></td>
							</tr>
							@endif
						</tbody>
					</table>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6">
					<div class="row mb-5">
						<div class="col-md-6">
							<a href="{{ route('shop') }}" class="btn btn-outline-black btn-sm btn-block">Continue Shopping</a>
						</div>
					</div>
				</div>
				<div class="col-md-6 pl-5">
					<div class="row justify-content-end">
						<div class="col-md-7">
							<div class="row">
								<div class="col-md-12 text-right border-bottom mb-5">
									<h3 class="text-black h4 text-uppercase">Totaux du panier</h3>
								</div>
							</div>
							<div class="row mb-3">
								<div class="col-md-6">
									<span class="text-black">Subtotal</span>
								</div>
								<div class="col-md-6 text-right">
									<strong class="text-black">{{ \Cart::getSubTotal() }} FCFA</strong>
								</div>
							</div>
							<div class="row mb-5">
								<div class="col-md-6">
									<span class="text-black">Total</span>
								</div>
								<div class="col-md-6 text-right">
									<strong class="text-black">{{ \Cart::getTotal() }} FCFA</strong>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">
									<a href="{{ route('checkout') }}" class="btn btn-black btn-lg py-3 btn-block">Passer la commande</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	@endsection
	@push('scripts')

	@endpush