	<!doctype html>
	<html lang="en">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="author" content="Untree.co">
		<link rel="shortcut icon" href="favicon.png">

		<meta name="description" content="" />
		<meta name="keywords" content="bootstrap, bootstrap4" />

		<!-- Bootstrap CSS -->
		<link href="{{ asset('assets/css/bootstrap.min.css')}}" rel="stylesheet">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
		<link href="{{ asset('assets/css/tiny-slider.css')}}" rel="stylesheet">
		<link href="{{ asset('assets/css/style.css')}}" rel="stylesheet">
		<title>{{ config('app.name') }}</title>
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<style>
			.alert-top-left {
				position: fixed;
				top: 10px;
				right: 10px;
				width: auto;
				max-width: 300px;
			}
		</style>

	</head>

	<body>
		@include('frontend.layouts.header')
		@yield('content')
		@include('frontend.layouts.footer')

		<script src="{{ asset('assets/js/bootstrap.bundle.min.js')}}"></script>
		<script src="{{ asset('assets/js/tiny-slider.js')}}"></script>
		<script src="{{ asset('assets/js/custom.js')}}"></script>
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
		<script>
			// @if(session('success'))
			// swal({
			// 	title: "Succès !",
			// 	text: "{{ session('success') }}",
			// 	icon: "success",
			// 	button: "OK",
			// });
			// @endif

			// @if(session('error'))
			// swal({
			// 	title: "Erreur !",
			// 	text: "{{ session('error') }}",
			// 	icon: "error",
			// 	button: "OK",
			// });
			// @endif
		</script>
		<script>
			$(document).ready(function() {
				// Add to cart
				$('.add-to-cart').click(function(e) {
					e.preventDefault();
					let productId = $(this).data('product-id');

					$.ajax({
						url: '{{ route("cart.add") }}',
						method: "POST",
						data: {
							_token: '{{ csrf_token() }}',
							product_id: productId
						},
						success: function(response) {
							$('.cart-count-badge').text(response.cart_count);
							const alertHTML = `
									<div class="alert alert-success alert-dismissible fade show alert-top-left" role="alert">
										<strong>Succès!</strong> Produit ajouté au panier!
										<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
									</div>
								`;

							// Ajouter l'alerte au corps du document
							$('body').append(alertHTML);

							// Supprimer l'alerte après 1,5 seconde (1500 millisecondes)
							setTimeout(function() {
								$('.alert-top-left').alert('close');
							}, 1500);
						}
					});
				});

				// Quantity buttons
				$('.increase').click(function() {
					let input = $(this).siblings('.quantity-amount');
					let value = parseInt(input.val()) + 1;
					input.val(value);
					updateCartItem($(this).closest('tr').find('form'));
				});

				$('.decrease').click(function() {
					let input = $(this).siblings('.quantity-amount');
					let value = parseInt(input.val()) - 1;
					if (value < 1) value = 1;
					input.val(value);
					updateCartItem($(this).closest('tr').find('form'));
				});

				function updateCartItem(form) {
					let productId = form.find('input[name="product_id"]').val();
					let quantity = form.closest('tr').find('.quantity-amount').val();

					$.ajax({
						url: '{{ route("cart.update") }}',
						method: "POST",
						data: {
							_token: '{{ csrf_token() }}',
							product_id: productId,
							quantity: quantity
						},
						success: function() {
							location.reload();
						}
					});
				}
			});
		</script>

		@stack('scripts')
	</body>

	</html>