@extends('layouts.admin')

@section('title', 'Ver Torneo')
@section('page-title', 'Ver Torneo')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('torneos.index') }}">Torneos</a></li>
<li class="breadcrumb-item active">{{ $tournament->name }}</li>
@endsection

@section('links')
<link rel="stylesheet" href="{{ asset('/admins/vendors/multiselect/bootstrap.multiselect.css') }}">
<link rel="stylesheet" href="{{ asset('/admins/vendors/lobibox/Lobibox.min.css') }}">
@endsection

@section('club', $tournament->club->name)
@section('tournament', $tournament->name)

@section('content')

@include('admin.partials.errors')

<div class="row">
	@isset($winners)
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-12 text-center">
						<p class="h2">GANADORES</p>
					</div>
					<div class="col-lg-6 col-md-6 col-12 text-center">
						<p class="h3"><i class="fa fa-trophy"></i> Primer Lugar <i class="fa fa-trophy"></i></p>
						<p>{!! couplesNames($winners[0]->couple, 3) !!}</p>
						@if($tournament->type==2)
						<p class="badge badge-success">{{ $winners[0]->couple->club->name }}</p>
						@endif
					</div>
					<div class="col-lg-6 col-md-6 col-12 text-center">
						<p class="h3"><i class="fa fa-trophy"></i> Segundo Lugar <i class="fa fa-trophy"></i></p>
						<p>{!! couplesNames($winners[1]->couple, 3) !!}</p>
						@if($tournament->type==2)
						<p class="badge badge-success">{{ $winners[1]->couple->club->name }}</p>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>
	@endif

	<div class="col-lg-6 col-md-6 col-12">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-12">
						<p class="h3 text-center">Datos del Torneo</p>
						<p><b>Club:</b>{{ $tournament->club->name }}</p>
						<p><b>Nombre:</b> {{ $tournament->name }}</p>
						<p><b>Tipo:</b> {{ tournamentType($tournament->type) }}</p>
						<p><b>Parejas de la primera fase:</b> {{ $tournament->couples }}</p>
						<p><b>Grupos de la primera fase:</b> {{ $tournament->groups }}</p>
						<p><b>Participantes:</b> {{ $participants }}</p>
						<p><b>Fecha de inicio:</b> {{ date('d-m-Y', strtotime($tournament->start)) }}</p>
						<p><b>Estado:</b> {!! tournamentState($tournament->state) !!}</p>
					</div>
					<div class="col-12 m-b-10">
						<div class="btn-group" role="group">
							<a class="btn btn-success" href="{{ route('torneos.list.gamers', ['slug' => $tournament->slug]) }}">Jugadores</a>
							<button class="btn btn-danger" onclick="deleteTournament('{{ $tournament->slug }}')">Eliminar</button>
							<a href="{{ route('torneos.index') }}" class="btn btn-secondary">Volver</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-lg-6 col-md-6 col-12">
		@if(isset($gamesFinish) && isset($currentPhase))
		@if($gamesFinish==0 && $currentPhase->phase_id==3 && $tournament->state==2)
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
		@endif

		@if($gamesFinish==0 && $tournament->state==2 && $currentPhase->phase_id<3)
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-12 text-center">
						<p class="h3">Ya han culminado todos los juegos de la fase actual, avanza a la siguiente.</p>
						<form method="POST" action="{{ route('torneos.next.phase', ['slug' => $tournament->slug]) }}">
							@csrf
							<input type="hidden" name="phase" value="{{ $phase->slug }}">
							<button type="submit" class="btn btn-primary">Siguiente Fase</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		@endif
		@endif

		@if($tournament->couples*2-$participants>0 && $tournament->state==1)
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-12 text-center">
						<p class="h3">Aún no has ingresado a todos los participantes!</p>
						@if($tournament->type==1)
						<button type="button" class="btn btn-success" onclick="addCouples('{{ $tournament->slug }}')">Agregar Pareja</button>
						@else
						<button type="button" class="btn btn-success" onclick="addCouples2('{{ $tournament->slug }}')">Agregar Pareja</button>
						@endif
					</div>
				</div>
			</div>
		</div>
		@endif

		@if($tournament->couples*2-$participants==0 && $tournament->state==1 && ($assignments==0 || $assignments==$tournament->couples))
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-12 text-center">
						<p class="h3">Ya puedes iniciar el torneo!</p>
						<form method="POST" action="{{ route('torneos.start', ['slug' => $tournament->slug]) }}">
							@csrf
							<button type="submit" class="btn btn-dark">Empezar Torneo</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		@endif

		@if($tournament->couples*2-$participants==0 && $tournament->state==1 && $assignments>0 && $assignments<$tournament->couples)
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-12 text-center">
						<p class="h3">Aún te falta asignarle un grupo a parejas en este torneo!</p>
							<a href="{{ route('torneos.list.gamers', ['slug' => $tournament->slug]) }}" class="btn btn-success">Asignar Parejas a Grupos</a>
					</div>
				</div>
			</div>
		</div>
		@endif

		@if($groups->count()>0)
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-12 text-center">
						<p class="h3">Fase de Grupos</p>
						<a class="btn btn-primary" href="{{ route('torneos.phase.groups', ['slug' => $tournament->slug]) }}">Ver Más</a>
					</div>
				</div>
			</div>
		</div>
		@endif

		@if($semifinal->count()>0)
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-12 text-center">
						<p class="h3">Semifinal</p>
						<a class="btn btn-primary" href="{{ route('torneos.phase.semifinal', ['slug' => $tournament->slug]) }}">Ver Más</a>
					</div>
				</div>
			</div>
		</div>
		@endif

		@if($final->count()>0)
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-12 text-center">
						<p class="h3">Final</p>
						<a class="btn btn-primary" href="{{ route('torneos.phase.final', ['slug' => $tournament->slug]) }}">Ver Más</a>
					</div>
				</div>
			</div>
		</div>
		@endif
	</div>
</div>

<div class="modal fade" id="addCouples" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Seleccione a los jugadores que conformaran la pareja</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form action="#" method="POST" id="formAddCouples">
					@csrf
					<div class="form-group col-lg-12 col-md-12 col-12">
						<label class="col-form-label">Jugadores (2 mínimo)<b class="text-danger">*</b></label>
						<select class="form-control" name="gamers[]" required multiple id="multiselectCouples">
						</select>
					</div>
					<div class="form-group col-12 text-right">
						<button type="submit" class="btn btn-primary">Guardar</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
					</div>
				</form>
				<p class="h3 text-danger text-center" id="formAddCouplesFull">Este torneo esta lleno</p>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="addCouples2" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Seleccione a los jugadores que conformaran la pareja</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form action="#" method="POST" id="formAddCouples2">
					@csrf
					<div class="form-group col-lg-12 col-md-12 col-12">
						<label class="col-form-label">Jugadores (2 mínimo)<b class="text-danger">*</b></label>
						<select class="form-control" name="gamers[]" required multiple id="multiselectCouples2">
						</select>
					</div>
					<div class="form-group col-lg-12 col-md-12 col-12">
						<label class="col-form-label">Club<b class="text-danger">*</b></label>
						<select class="form-control" name="club" required>
							<option value="">Seleccione</option>
							@foreach ($clubs as $club)
							<option value="{{ $club->slug }}">{{ $club->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group col-12 text-right">
						<button type="submit" class="btn btn-primary">Guardar</button>
						<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
					</div>
				</form>
				<p class="h3 text-danger text-center" id="formAddCouplesFull2">Este torneo esta lleno</p>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="deleteTournament" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">¿Estás seguro de que quieres eliminar este torneo?</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-footer">
				<form action="#" method="POST" id="formDeleteTournament">
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
<script src="{{ asset('/admins/vendors/multiselect/bootstrap-multiselect.js') }}"></script>
<script src="{{ asset('/admins/vendors/lobibox/Lobibox.js') }}"></script>
@endsection