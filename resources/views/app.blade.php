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
	</head>

	<body>
		@include('frontend.layouts.header')
		@yield('content')
		@include('frontend.layouts.footer')

		<script src="{{ asset('assets/js/bootstrap.bundle.min.js')}}"></script>
		<script src="{{ asset('assets/js/tiny-slider.js')}}"></script>
		<script src="{{ asset('assets/js/custom.js')}}"></script>
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
		<script>
        @if (session('success'))
            swal({
                title: "Succès !",
                text: "{{ session('success') }}",
                icon: "success",
                button: "OK",
            });
        @endif

        @if (session('error'))
            swal({
                title: "Erreur !",
                text: "{{ session('error') }}",
                icon: "error",
                button: "OK",
            });
        @endif
    </script>

		@stack('scripts')
	</body>

	</html>