@extends('layouts.admin')

@section('title', 'Inicio')
@section('page-title', 'Panel de Inicio')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-body little-profile text-center">
				<h3 class="m-b-3">Bienvenid@ {{ Auth::user()->name." ".Auth::user()->lastname }}</h3>
			</div>
			<div class="text-center bg-light">
				<div class="row">
					<div class="col-6  p-20 b-r">
						<h4 class="m-b-0 font-medium">{{ $active }}</h4>
						<small>Usuarios Activos</small>
					</div>
					<div class="col-6  p-20">
						<h4 class="m-b-0 font-medium">{{ $inactive }}</h4>
						<small>Usuarios Inactivos</small>
					</div>
				</div>
			</div>
			<div class="card-body text-center">
				<a href="{{ route('juegos.index') }}" class="m-t-10 m-b-20 waves-effect waves-dark btn btn-success btn-md btn-rounded">Ver Listado Juegos</a>
			</div>
		</div>
	</div>

	<div class="col-lg-4">
		<div class="card bg-info text-white">
			<div class="card-body">
				<div class="d-flex">
					<div class="stats">
						<h1 class="text-white">{{ $gamers }}</h1>
						<h6 class="text-white">Jugadores</h6>
						<a href="{{ route('jugadores.index') }}" class="btn btn-rounded btn-outline btn-light m-t-10 font-14">Ver Listado</a>
					</div>
					<div class="stats-icon text-right ml-auto"><i class="fa fa-users display-5 op-3 text-dark"></i></div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="card bg-primary text-white">
			<div class="card-body">
				<div class="d-flex">
					<div class="stats">
						<h1 class="text-white">{{ $tournaments }}</h1>
						<h6 class="text-white">Torneos</h6>
						<a href="{{ route('torneos.index') }}" class="btn btn-rounded btn-outline btn-light m-t-10 font-14">Ver Listado</a>
					</div>
					<div class="stats-icon text-right ml-auto"><i class="fa fa-trophy display-5 op-3 text-dark"></i></div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-4">
		<div class="card bg-success text-white">
			<div class="card-body">
				<div class="d-flex">
					<div class="stats">
						<h1 class="text-white">{{ $clubs }}</h1>
						<h6 class="text-white">Clubes</h6>
						<a href="{{ route('clubes.index') }}" class="btn btn-rounded btn-outline btn-light m-t-10 font-14">Ver Listado</a>
					</div>
					<div class="stats-icon text-right ml-auto"><i class="fa fa-building display-5 op-3 text-dark"></i></div>
				</div>
			</div>
		</div>
	</div>

</div>

@endsection