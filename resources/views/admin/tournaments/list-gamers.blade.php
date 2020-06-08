@extends('layouts.admin')

@section('title', 'Lista de Jugadores del Torneo')
@section('page-title', 'Lista de Jugadores del Torneo')

@section('links')
<link rel="stylesheet" href="{{ asset('/admins/vendors/lobibox/Lobibox.min.css') }}">
@endsection

@section('club', $tournament->club->name)
@section('tournament', $tournament->name)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('torneos.index') }}">Torneos</a></li>
<li class="breadcrumb-item"><a href="{{ route('torneos.show', ['slug' => $tournament->slug]) }}">{{ $tournament->name }}</a></li>
<li class="breadcrumb-item active">Jugadores</li>
@endsection

@section('content')

<div class="row">
	<div class="col-12">
		<a href="{{ route('torneos.show', ['slug' => $tournament->slug]) }}" class="btn btn-secondary m-b-10">Volver</a>
	</div>

	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<div class="table-responsive">
					<table id="tablaExport" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th>#</th>
								<th>Nombres de la Pareja</th>
								@if ($tournament->type==2)
								<th>Club</th>
								@endif
								@if ($tournament->state==1)
								<th>Grupo</th>
								<th>Acciones</th>
								@endif
							</tr>
						</thead>
						<tbody>
							@foreach($gamers as $couple) 
							<tr>
								<td>{{ $num++ }}</td>
								<td>{!! couplesNames($couple->couple_id, 4) !!}</td>
								@if ($tournament->type==2)
								<td>{{ $couple->couple->club->name }}</td>
								@endif
								@if ($tournament->state==1)
								<td>{{ group($tournament->id, $couple->couple_id) }}</td>
								<td class="d-flex">
									@if(group($tournament->id, $couple->couple_id)=="Ninguno")
									<button class="btn btn-primary btn-circle btn-sm" onclick="addGroup('{{ $tournament->slug }}', {{ $couple->id }})"><i class="fa fa-users"></i></button>&nbsp;&nbsp;
									@else
									<button class="btn btn-success btn-circle btn-sm" onclick="changeGroup('{{ $tournament->slug }}', {{ $couple->id }}, '{{ group($tournament->id, $couple->couple_id) }}')"><i class="fa fa-users"></i></button>&nbsp;&nbsp;
									@endif
									<button class="btn btn-danger btn-circle btn-sm" onclick="deleteCouple('{{ $couple->couple_id }}')"><i class="fa fa-trash"></i></button>
								</td>
								@endif
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="addGroup" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Agregar pareja a un grupo</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form action="{{ route('torneos.store.group') }}" method="POST">
					@csrf
					<div class="col-12">
						<div class="row">
							<div class="col-12">
								<label>Grupo<b class="text-danger">*</b></label>
								<select class="form-control" required name="group" id="groupsAvailable">
									<option value="">Seleccione</option>
								</select>
							</div>
							<input type="hidden" name="tournament" value="{{ $tournament->slug }}">
							<input type="hidden" name="gamerTournament" value="">
							<div class="col-12">
								<span class="text-danger d-none" id="addGroupAlert">Todos los grupos estan llenos</span>
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

<div class="modal fade" id="changeGroup" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Cambiar grupo de la pareja</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form action="{{ route('torneos.update.group') }}" method="POST">
					@csrf
					@method('PUT')
					<div class="col-12">
						<div class="row">
							<div class="col-6">
								<label>Grupo Actual</label>
								<input class="form-control" type="text" disabled id="groupNow">
							</div>
							<div class="col-6">
								<label>Grupo Nuevo<b class="text-danger">*</b></label>
								<select class="form-control" required name="group" id="groupsChangeAvailable">
									<option value="">Seleccione</option>
								</select>
							</div>
							<input type="hidden" name="tournament" value="{{ $tournament->slug }}">
							<input type="hidden" name="gamerTournamentChange" value="">
							<div class="col-12">
								<span class="text-danger d-none" id="changeGroupAlert">Todos los grupos estan llenos</span>
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

<div class="modal fade" id="deleteCouple" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">¿Estás seguro de que quieres eliminar esta pareja?</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-footer">
				<form action="#" method="POST" id="formDeleteCouple">
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
<script src="{{ asset('/admins/vendors/lobibox/Lobibox.js') }}"></script>
<script src="{{ asset('/admins/vendors/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('/admins/vendors/datatables/buttons/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('/admins/vendors/datatables/buttons/dataTables.flash.min.js') }}"></script>
<script src="{{ asset('/admins/vendors/datatables/buttons/jszip.min.js') }}"></script>
<script src="{{ asset('/admins/vendors/datatables/buttons/pdfmake.min.js') }}"></script>
<script src="{{ asset('/admins/vendors/datatables/buttons/vfs_fonts.js') }}"></script>
<script src="{{ asset('/admins/vendors/datatables/buttons/buttons.html5.min.js') }}"></script>
<script src="{{ asset('/admins/vendors/datatables/buttons/buttons.print.min.js') }}"></script>
@endsection