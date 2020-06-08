@extends('layouts.admin')

@section('title', $phase->name)
@section('page-title', $phase->name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('torneos.index') }}">Torneos</a></li>
<li class="breadcrumb-item"><a href="{{ route('torneos.show', ['slug' => $tournament->slug]) }}">{{ $tournament->name }}</a></li>
<li class="breadcrumb-item active">{{ $phase->name }}</li>
@endsection

@section('links')
<link rel="stylesheet" href="{{ asset('/admins/vendors/lobibox/Lobibox.min.css') }}">
@endsection

@section('club', $tournament->club->name)
@section('tournament', $tournament->name)

@section('content')

<div class="row">
	<div class="col-12">
		<a href="{{ route('torneos.show', ['slug' => $tournament->slug]) }}" class="btn btn-secondary m-b-10">Volver</a>
		<p class="text-themecolor mt-2 h5 d-lg-none d-md-none">{{ $tournament->club->name }}</p>
		<p class="text-themecolor mt-2 h3 d-lg-none d-md-none">{{ $tournament->name }}</p>
	</div>

	@if($gamesFinish==0 && $phase->name!="Final" && $tournament->state==2 && $groupsCount>1 && $currentPhase->phase_id==$phase->id)
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-12 text-center">
						<p class="h3">Ya han culminado todos los juegos de esta fase, avanza a la siguiente.</p>
						<form method="POST" action="{{ route('torneos.next.phase', ['slug' => $tournament->slug]) }}">
							@csrf
							<input type="hidden" name="phase" value="{{ $phase->slug }}">
							<button type="submit" class="btn btn-primary">Siguiente Fase</button> 
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	@endif

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

	@if($groupsCount==1)
	<div class="col-6">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-12 text-center">
						<p class="h3">Grupo Final</p>
						<div class="row">
							@foreach($groups[0]->couples as $couples)
							<div class="col-6 text-left">
								<p class="my-1">{{ couplesNames($couples->id, 4, 1, 1) }}</p>
							</div>
							@endforeach
						</div>
						<a class="btn btn-primary" href="{{ route('torneos.group', ['slug' => $tournament->slug, 'phase' => $phase->slug, 'group' => $groups[0]->slug]) }}">Ver Más</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	@else
	@foreach($groups as $group)
	<div class="col-lg-6 col-md-6 col-12">
		<div class="card">
			<div class="card-body">
				<div class="row">
					<div class="col-12 text-center">
						<p class="h3">{{ 'Grupo '.$num++ }}</p>
						<div class="row">
							@foreach($group->couples as $couples)
							<div class="col-6 text-left">
								<p class="my-1">
									{!! couplesNames($couples->id, 4) !!}
									@if($tournament->type==2)
									<br>
									<span class="badge badge-success">{{ $couples->club->name }}</span>
									@endif
								</p> 
							</div>
							@endforeach
						</div>
						<a class="btn btn-primary" href="{{ route('torneos.group', ['slug' => $tournament->slug, 'phase' => $phase->slug, 'group' => $group->slug]) }}">Ver Más</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	@endforeach
	@endif
</div>

@endsection

@section('script')
<script src="{{ asset('/admins/vendors/lobibox/Lobibox.js') }}"></script>
@endsection