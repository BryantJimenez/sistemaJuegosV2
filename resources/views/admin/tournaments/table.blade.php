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
<li class="breadcrumb-item"><a href="{{ route('torneos.group', ['slug' => $tournament->slug, 'phase' => $phase->slug, 'group' => $groups->slug]) }}">{{ $groups->name }}</a></li>
<li class="breadcrumb-item active">Tabla</li>
@endsection

@section('links')
<link rel="stylesheet" href="{{ asset('/admins/vendors/lobibox/Lobibox.min.css') }}">
<link rel="stylesheet" href="{{ asset('/admins/vendors/touchspin/jquery.bootstrap-touchspin.min.css') }}">
@endsection

@section('club', $tournament->club->name)
@section('tournament', $tournament->name)

@section('content')

<div class="row">
	<div class="col-12">
		<a href="{{ route('torneos.group', ['slug' => $tournament->slug, 'phase' => $phase->slug, 'group' => $groups->slug]) }}" class="btn btn-secondary m-b-10">Volver</a>
		<p class="text-themecolor mt-2 h5 d-lg-none d-md-none">{{ $tournament->club->name }}</p>
		<p class="text-themecolor mt-2 h3 d-lg-none d-md-none">{{ $tournament->name }}</p>
	</div>

	<div class="col-12">
		<table class="display nowrap table table-hover table-bordered bg-white" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th>Posición</th>
					<th>Pareja</th>
					<th>PG</th>
					<th>PF</th>
					<th>PC</th>
					<th>DIFERENCIA</th>
				</tr>
			</thead>
			<tbody>
				@foreach($couplesPointsOrder as $couplePointsOrder)
				<tr>
					<td @if($loop->first) style="background-color: #f3f70073; font-weight: bold;" @elseif($loop->iteration==2) style="background-color: #ddd; font-weight: bold;" @endif>{{ $num++."°" }}</td>
					<td @if($loop->first) style="background-color: #f3f70073; font-weight: bold;" @elseif($loop->iteration==2) style="background-color: #ddd; font-weight: bold;" @endif>{!! couplesNames($couplePointsOrder['couple_id'], 4, 1, 1) !!}</td>
					<td @if($loop->first) style="background-color: #f3f70073; font-weight: bold;" @elseif($loop->iteration==2) style="background-color: #ddd; font-weight: bold;" @endif>{{ $couplePointsOrder['wins'] }}</td>
					<td @if($loop->first || $loop->iteration==2) style="background-color: #398bf7a1; font-weight: bold;" @endif>{{ $couplePointsOrder['points'] }}</td>
					<td @if($loop->first || $loop->iteration==2) style="background-color: #ef5350bf; font-weight: bold;" @endif>{{ $couplePointsOrder['counter'] }}</td>
					<td @if($loop->first) style="background-color: #f3f70073; font-weight: bold;" @elseif($loop->iteration==2) style="background-color: #ddd; font-weight: bold;" @endif>{{ $couplePointsOrder['total'] }}</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>

	<div class="col-12">

		<table class="display nowrap table table-hover table-bordered bg-white" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th class="text-center">Pareja 1</th>
					<th colspan="3" class="text-center">Puntaje</th>
					<th class="text-center">Pareja 2</th>
				</tr>
			</thead>
			<tbody>
				@foreach($games as $game)
				<tr>
					<td class="text-center">
						{!! couplesNames($game->couples, 1, 1) !!}
						@if($tournament->type==2)
						<br>
						<b class="badge badge-success">{{ $game->couples[0]->club->name }}</b>
						@endif
					</td>
					<td class="text-center">
						@if($game->couple_game[0]->points==0 && $game->couple_game[1]->points==0 && $game->state==3)
						<b class="h4">@if($game->game_winner->couple_id==$game->couple_game[0]->couple_id) <p class="badge badge-success">G</p> @else <p class="badge badge-danger">P</p> @endif</b>
						@else
						<b class="h4">{{ $game->couple_game[0]->points }}</b>
						@endif
					</td>
					<td class="text-center">
						@if($lastPhase->phase_id==$phase->id && $tournament->state<3)

						@if(2>$game->couple_game[0]->points && 2>$game->couple_game[1]->points && $game->state!=3)
						<button class="btn btn-primary btn-sm" onclick="addGame('{{ $game->slug }}')"><i class="mdi mdi-cards-playing-outline"></i></button>
						<button class="btn btn-danger btn-sm" onclick="addGameBack('{{ $game->slug }}')"><i class="fa fa-sign-out"></i></button>
						@else
						<button class="btn btn-success btn-sm" onclick="addGame('{{ $game->slug }}')"><i class="mdi mdi-cards-playing-outline"></i></button>
						@endif

						@else
						<b class="badge badge-success"><i class="fa fa-check"></i></b>
						@endif
					</td>
					<td class="text-center">
						@if($game->couple_game[0]->points==0 && $game->couple_game[1]->points==0 && $game->state==3)
						<b class="h4">@if($game->game_winner->couple_id==$game->couple_game[1]->couple_id) <p class="badge badge-success">G</p> @else <p class="badge badge-danger">P</p> @endif</b>
						@else
						<b class="h4">{{ $game->couple_game[1]->points }}</b>
						@endif
					</td>
					<td class="text-center">
						{!! couplesNames($game->couples, 2, 1) !!}
						@if($tournament->type==2)
						<br>
						<b class="badge badge-success">{{ $game->couples[1]->club->name }}</b>
						@endif
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>

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