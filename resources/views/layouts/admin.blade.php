<!DOCTYPE html>
<html lang="es" class="no-js">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta http-equiv="Expires" content="0">
	<meta http-equiv="Last-Modified" content="0">
	<meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
	<meta http-equiv="Pragma" content="no-cache">
	
	<link rel="icon" href="{{ asset('/auth/images/icons/favicon.ico') }}" type="image/x-icon" />
	<title>@yield('title')</title>
	<meta name="author" content="">

	<link href="{{ asset('/admins/css/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ asset('/admins/css/style.css') }}" rel="stylesheet">
	<link href="{{ asset('/admins/vendors/perfect-scrollbar/css/perfect-scrollbar.css') }}" rel="stylesheet">
	
	@yield('links')
	<link href="{{ asset('/admins/css/colors/green.css') }}" id="theme" rel="stylesheet" type="text/css">
</head>
<body class="fix-header fix-sidebar card-no-border mini-sidebar">

	@include('auth.partials.loader')
	
	<div id="main-wrapper">
		
		@include('admin.partials.header')
		@include('admin.partials.sidebar')
		
		<div class="page-wrapper" style="background-color: #dcdcdc57;">
			<div class="container-fluid r-aside">

				<div class="row page-titles">
					<div class="col-md-5 align-self-center">
						<h3 class="text-themecolor">@yield('page-title')</h3>
					</div>
					<div class="col-md-7 align-self-center">
						<ol class="breadcrumb">
							<li class="breadcrumb-item"><a href="{{ route('home') }}">Inicio</a></li>
							@yield('breadcrumb')
						</ol>
					</div>
					{{-- <div class="">
						<button class="right-side-toggle waves-effect waves-light btn-inverse btn btn-circle btn-sm pull-right m-l-10"><i class="ti-settings text-white"></i></button>
					</div> --}}
				</div>

				@yield('content')

				{{-- @include('admin.partials.sidebarRight') --}}

			</div>

			@include('admin.partials.footer')

		</div>
	</div>

	<script src="{{ asset('/admins/js/jquery/jquery.min.js') }}"></script>
	<script src="{{ asset('/admins/js/bootstrap/popper.min.js') }}"></script>
	<script src="{{ asset('/admins/js/bootstrap/bootstrap.min.js') }}"></script>
	<script src="{{ asset('/admins/js/perfect-scrollbar.jquery.min.js') }}"></script>
	<script src="{{ asset('/admins/js/waves.js') }}"></script>
	<script src="{{ asset('/admins/js/sidebarmenu.js') }}"></script>
	@yield('script')
	<script src="{{ asset('/admins/js/custom.min.js') }}"></script>
	@include('admin.partials.notifications')
</body>
</html>