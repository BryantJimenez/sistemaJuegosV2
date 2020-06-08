<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

	<link rel="icon" href="{{ asset('/auth/images/icons/favicon.ico') }}" type="image/x-icon" />
	<title>@yield('title')</title>

    <meta name="description" content="Inicia sección en esta app para el control del inventario de activos.">
    <meta name="author" content="">

    <link rel="stylesheet" type="text/css" href="{{ asset('/auth/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/auth/css/login-register-lock.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('/auth/css/style.css') }}">
</head>
<body>
    @include('auth.partials.loader')
	@yield('content')

    <script src="{{ asset('/auth/vendor/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('/auth/vendor/bootstrap/js/popper.min.js') }}"></script>
    <script src="{{ asset('/auth/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('/auth/js/main.js') }}"></script>
</body>
</html>