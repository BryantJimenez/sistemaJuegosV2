@extends('layouts.admin')

@section('title', 'Ver Jugador')
@section('page-title', $gamer->name." ".$gamer->lastname)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('jugadores.index') }}">Jugadores</a></li>
<li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">

				@include('admin.partials.errors')

				<div class="row">
					<div class="col-12 m-b-10">
						<div class="btn-group" role="group">
							<a href="{{ route('jugadores.index') }}" class="btn btn-secondary">Volver</a>
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-12">
						<img src="{{ '/admins/img/users/'.$gamer->photo }}" class="img-fluid">
					</div>
					<div class="col-lg-8 col-md-8 col-12">
						<div class="row">
							<div class="col-lg-6 col-md-6 col-12">
								<div class="card bg-success text-white">
									<div class="card-body">
										<div class="d-flex">
											<div class="stats">
												<h1 class="text-white">{{ $games }}</h1>
												<h6 class="text-white">Partidas Jugadas</h6>
											</div>
											<div class="stats-icon text-right ml-auto"><i class="fa fa-check display-5 op-3 text-dark"></i></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-12">
								<div class="card bg-primary text-white">
									<div class="card-body">
										<div class="d-flex">
											<div class="stats">
												<h1 class="text-white">{{ $winners }}</h1>
												<h6 class="text-white">Partidas Ganadas</h6>
											</div>
											<div class="stats-icon text-right ml-auto"><i class="mdi mdi-cards-playing-outline display-5 op-3 text-dark"></i></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-12">
								<div class="card bg-danger text-white">
									<div class="card-body">
										<div class="d-flex">
											<div class="stats">
												<h1 class="text-white">{{ $loses }}</h1>
												<h6 class="text-white">Partidas Perdidas</h6>
											</div>
											<div class="stats-icon text-right ml-auto"><i class="fa fa-close display-5 op-3 text-dark"></i></div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-12">
								<div class="card bg-info text-white">
									<div class="card-body">
										<div class="d-flex">
											<div class="stats">
												<h1 class="text-white">{{ $winnerTournaments }}</h1>
												<h6 class="text-white">Torneos Ganados</h6>
											</div>
											<div class="stats-icon text-right ml-auto"><i class="fa fa-trophy display-5 op-3 text-dark"></i></div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<div class="table-responsive">
					<table id="tabla" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th>#</th>
								<th>Tipo del Juego</th>
								<th>Estado</th>
								<th>Fecha</th>
								<th>Acciones</th>
							</tr>
						</thead>
						<tbody>
							@foreach($gamesInfo as $game)
							<tr>
								<td>{{ $num++ }}</td>
								<td>{!! gameType($game['type']) !!}</td>
								<td>{!! gameState($game['state']) !!}</td>
								<td>{{ date('d-m-Y', strtotime($game['created_at'])) }}</td>
								<td class="d-flex">
									<button class="btn btn-primary btn-circle btn-sm" onclick="showGame('{{ $game['slug'] }}')"><i class="mdi mdi-cards-playing-outline"></i></button>&nbsp;&nbsp;
									<button class="btn btn-danger btn-circle btn-sm" onclick="deleteGame('{{ $game['slug'] }}')"><i class="fa fa-trash"></i></button>
								</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="showGame" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Información del Juego</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-6">
						<label class="col-form-label">Tipo</label>
						<p id="typeGame"></p>
					</div>
					<div class="col-6">	
						<label class="col-form-label">Estado</label>
						<p id="stateGame"></p>
					</div>
					<div class="col-6">
						<label class="col-form-label">Pareja 1</label>
						<p id="couple1Game"></p>
					</div>
					<div class="col-6">	
						<label class="col-form-label">Pareja 2</label>
						<p id="couple2Game"></p>
					</div>	
					<div class="col-6">
						<label class="col-form-label">Puntos de Pareja 1</label>
						<p id="points1Game"></p>
					</div>
					<div class="col-6">	
						<label class="col-form-label">Puntos de Pareja 2</label>
						<p id="points2Game"></p>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="deleteGame" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">¿Estás seguro de que quieres eliminar este juego?</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-footer">
				<form action="#" method="POST" id="formDeleteGame">
					@csrf
					@method('DELETE')
					<button type="submit" class="btn btn-primary">Eliminar</button>
				</form>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
		</div>
	</div>
</div>

@endsection

@section('script')
<script src="{{ asset('/admins/vendors/datatables/jquery.dataTables.min.js') }}"></script>
@endsection