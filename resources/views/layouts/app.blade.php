<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>E-Borrow</title>
	<link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" />

	{{-- NOTE: CSS --}}
	<!-- Styles -->
	<!-- Google Font: Source Sans Pro -->
	<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
	<!-- icheck bootstrap -->
	<link rel="stylesheet" href="{{ asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
	<!-- Theme style -->
	<link rel="stylesheet" href="{{ asset('css/adminlte.min.css') }}">
	<!-- Scripts -->
	<script src="{{ asset('js/app.js') }}" defer></script>

</head>

<body class="hold-transition login-page">
	<div id="app" class="login-box">
		@yield('content')
	</div>
</body>

</html>
