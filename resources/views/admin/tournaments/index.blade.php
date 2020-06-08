@extends('layouts.admin')

@section('title', 'Lista de Torneos')
@section('page-title', 'Lista de Torneos')

@section('links')
<link rel="stylesheet" href="{{ asset('/admins/vendors/multiselect/bootstrap.multiselect.css') }}">
<link rel="stylesheet" href="{{ asset('/admins/vendors/lobibox/Lobibox.min.css') }}">
@endsection

@section('breadcrumb')
<li class="breadcrumb-item">Torneo</li>
<li class="breadcrumb-item active">Lista</li>
@endsection

@section('content')

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">

				@include('admin.partials.errors')
				
				<div class="table-responsive">
					<table id="tablaExport" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th>#</th>
								<th>Nombre del Torneo</th>
								<th>Fecha de Inicio</th>
								<th>Tipo</th>
								<th>Estado</th>
								<th>Acciones</th>
							</tr>
						</thead>
						<tbody>
							@foreach($tournaments as $tournament)
							<tr>
								<td>{{ $num++ }}</td>
								<td>{{ $tournament->name }}</td>
								<td>{{ date('d-m-Y', strtotime($tournament->start)) }}</td>
								<td>{{ tournamentType($tournament->type) }}</td>
								<td>{!! tournamentState($tournament->state) !!}</td>
								<td class="d-flex">
									@if($tournament->state==1)
									@if($tournament->type==1)
									<button class="btn btn-success btn-circle btn-sm" onclick="addCouples('{{ $tournament->slug }}')"><i class="mdi mdi-account-multiple"></i></button>&nbsp;&nbsp;
									@else
									<button class="btn btn-success btn-circle btn-sm" onclick="addCouples2('{{ $tournament->slug }}')"><i class="mdi mdi-account-multiple"></i></button>&nbsp;&nbsp;
									@endif
									@endif
									<a class="btn btn-primary btn-circle btn-sm" href="{{ route('torneos.show', ['slug' => $tournament->slug]) }}"><i class="fa fa-trophy"></i></a>&nbsp;&nbsp;
									@if($tournament->state==1)
									<a class="btn btn-info btn-circle btn-sm" href="{{ route('torneos.edit', ['slug' => $tournament->slug]) }}"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;
									@endif
									<button class="btn btn-danger btn-circle btn-sm" onclick="deleteTournament('{{ $tournament->slug }}')"><i class="fa fa-trash"></i></button>
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
<script src="{{ asset('/admins/vendors/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('/admins/vendors/datatables/buttons/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('/admins/vendors/datatables/buttons/dataTables.flash.min.js') }}"></script>
<script src="{{ asset('/admins/vendors/datatables/buttons/jszip.min.js') }}"></script>
<script src="{{ asset('/admins/vendors/datatables/buttons/pdfmake.min.js') }}"></script>
<script src="{{ asset('/admins/vendors/datatables/buttons/vfs_fonts.js') }}"></script>
<script src="{{ asset('/admins/vendors/datatables/buttons/buttons.html5.min.js') }}"></script>
<script src="{{ asset('/admins/vendors/datatables/buttons/buttons.print.min.js') }}"></script>
@endsection