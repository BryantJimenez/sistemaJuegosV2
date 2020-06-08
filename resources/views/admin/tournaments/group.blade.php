@extends('layouts.admin')

@if($phase->name=='Final')
@section('title', $phase->name." - Grupo Final")
@section('page-title', $phase->name." - Grupo Final")
@else
@section('title', $phase->name." - ".$groups->name)
@section('page-title', $phase->name." - ".$groups->name)
@endif

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('torneos.index') }}">Torneos</a></li>
<li class="breadcrumb-item"><a href="{{ route('torneos.show', ['slug' => $tournament->slug]) }}">{{ $tournament->name }}</a></li>
@if($phase->slug=='fase-de-grupos')
<li class="breadcrumb-item"><a href="{{ route('torneos.phase.groups', ['slug' => $tournament->slug]) }}">{{ $phase->name }}</a></li>
@elseif($phase->slug=='semifinal')
<li class="breadcrumb-item"><a href="{{ route('torneos.phase.semifinal', ['slug' => $tournament->slug]) }}">{{ $phase->name }}</a></li>
@else
<li class="breadcrumb-item"><a href="{{ route('torneos.phase.final', ['slug' => $tournament->slug]) }}">{{ $phase->name }}</a></li>
@endif
<li class="breadcrumb-item active">{{ $groups->name }}</li>
@endsection

@section('links')
<link rel="stylesheet" href="{{ asset('/admins/vendors/lobibox/Lobibox.min.css') }}">
<link rel="stylesheet" href="{{ asset('/admins/vendors/touchspin/jquery.bootstrap-touchspin.min.css') }}">
@endsection

@section('club', $tournament->club->name)
@section('tournament', $tournament->name)

@section('content')

<div class="row">
	<div class="col-12 m-b-10">
		<div class="btn-group" role="group">
			<a href="{{ route('torneos.table', ['slug' => $tournament->slug, 'phase' => $phase->slug, 'group' => $groups->slug]) }}" class="btn btn-primary">Tabla</a>
			@if($phase->slug=='fase-de-grupos')
			<a href="{{ route('torneos.phase.groups', ['slug' => $tournament->slug]) }}" class="btn btn-secondary">Volver</a>
			@elseif($phase->slug=='semifinal')
			<a href="{{ route('torneos.phase.semifinal', ['slug' => $tournament->slug]) }}" class="btn btn-secondary">Volver</a>
			@else
			<a href="{{ route('torneos.phase.final', ['slug' => $tournament->slug]) }}" class="btn btn-secondary">Volver</a>
			@endif
		</div>
		<p class="text-themecolor mt-2 h5 d-lg-none d-md-none">{{ $tournament->club->name }}</p>
		<p class="text-themecolor mt-2 h3 d-lg-none d-md-none">{{ $tournament->name }}</p>
	</div>

	@if($gamesFinish==0 && $phase->name=="Final" && $tournament->state==2)
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-12 text-center">
						<p class="h3">Ya han culminado todos los juegos del torneo, finalizalo para conocer al ganador.</p>
						<form method="POST" action="{{ route('torneos.final', ['slug' => $tournament->slug]) }}">
							@csrf
							<button type="submit" class="btn btn-primary">Finalizar Torneo</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	@endif

	<div class="col-12">
		<div class="row">
			@foreach($games as $game)
			<div class="col-lg-4 col-md-6 col-12">
				<div class="card">
					<div class="card-body">
						<div class="row">
							<div class="col-12 text-center">
								<p class="h3">Juego {{ $num++ }}</p>
							</div>
							<div class="col-12">
								<div class="row d-flex-wrap-center">
									<div class="col-6 text-center">
										<p class="h4">Pareja 1</p>
										@if($game->couple_game[0]->points==0 && $game->couple_game[1]->points==0 && $game->state==3)
										<b class="h3">@if($game->game_winner->couple_id==$game->couple_game[0]->couple_id) <p class="badge badge-success">G</p> @else <p class="badge badge-danger">P</p> @endif</b>
										@else
										<b class="h1">{{ $game->couple_game[0]->points }}</b>
										@endif
										<p>{!! couplesNames($game->couples, 1) !!}</p>
										@if($tournament->type==2)
										<p class="badge badge-success">{{ $game->couples[0]->club->name }}</p>
										@endif
									</div>
									<div class="col-6 text-center">
										<p class="h4">Pareja 2</p>
										@if($game->couple_game[0]->points==0 && $game->couple_game[1]->points==0 && $game->state==3)
										<b class="h3">@if($game->game_winner->couple_id==$game->couple_game[1]->couple_id) <p class="badge badge-success">G</p> @else <p class="badge badge-danger">P</p> @endif</b>
										@else
										<b class="h1">{{ $game->couple_game[1]->points }}</b>
										@endif
										<p>{!! couplesNames($game->couples, 2) !!}</p>
										@if($tournament->type==2)
										<p class="badge badge-success">{{ $game->couples[1]->club->name }}</p>
										@endif
									</div>
								</div>
							</div>
							@if($lastPhase->phase_id==$phase->id && $tournament->state<3)

							@if(2>$game->couple_game[0]->points && 2>$game->couple_game[1]->points && $game->state!=3)
							<div class="col-12 text-center">
								<button class="btn btn-primary" onclick="addGame('{{ $game->slug }}')">Jugar</button>
								<button class="btn btn-danger" onclick="addGameBack('{{ $game->slug }}')">W/O</button>
							</div>
							@else
							<div class="col-12 text-center">
								<button class="btn btn-success" onclick="addGame('{{ $game->slug }}')">Jugar</button>
							</div>
							@endif

							@endif
						</div>
					</div>
				</div>
			</div>
			@endforeach
		</div>
	</div>
</div>

<div class="modal fade" id="addGame" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Introduzca el resultado del juego</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form action="#" method="POST" id="formAddGame">
					@csrf
					<div class="col-12">
						<div class="row d-flex-wrap-center">
							<div class="col-6 text-center">
								<p class="h4">Pareja 1</p>
								<p><b id="couple1Game"></b></p>
							</div>
							<div class="col-6 text-center">
								<p class="h4">Pareja 2</p>
								<p><b id="couple2Game"></b></p>
							</div>
							<div class="col-6 text-center">
								<input type="text" class="form-control numberPoint" required name="points1" id="points1Game">
							</div>
							<div class="col-6 text-center">
								<input type="text" class="form-control numberPoint" required name="points2" id="points2Game">
							</div>
						</div>
					</div>
					<div class="form-group col-12 text-right m-t-5">
						<button type="submit" class="btn btn-primary">Guardar</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="addGameBack" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Introduzca el resultado del juego</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form action="#" method="POST" id="formAddGameBack">
					@csrf
					<div class="col-12">
						<div class="row">
							<div class="col-12">
								<label>Ganador<b class="text-danger">*</b></label>
								<select class="form-control" required name="winnerCouple" id="couplesWinBack"></select>
							</div>
						</div>
					</div>
					<div class="form-group col-12 text-right m-t-5">
						<button type="submit" class="btn btn-primary">Guardar</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

@endsection

@section('script')
<script src="{{ asset('/admins/vendors/lobibox/Lobibox.js') }}"></script>
<script src="{{ asset('/admins/vendors/touchspin/jquery.bootstrap-touchspin.min.js') }}"></script>
@endsection