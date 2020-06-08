@extends('layouts.admin')

@section('title', 'Lista de Jugadores')
@section('page-title', 'Lista de Jugadores')

@section('links')
<link rel="stylesheet" href="{{ asset('/admins/vendors/lobibox/Lobibox.min.css') }}">
@endsection

@section('breadcrumb')
<li class="breadcrumb-item">Jugadores</li>
<li class="breadcrumb-item active">Lista</li>
@endsection

@section('content')

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<div class="table-responsive">
					<table id="tabla" class="display nowrap table table-hover table-striped table-bordered" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th>#</th>
								<th>Nombre Completo</th>
								<th>Acciones</th>
							</tr>
						</thead>
						<tbody>
							@foreach($gamers as $gamer)
							<tr>
								<td>{{ $num++ }}</td>
								<td>{{ $gamer->name." ".$gamer->lastname }}</td>
								<td class="d-flex">
									<a class="btn btn-primary btn-circle btn-sm" href="{{ route('jugadores.show', ['slug' => $gamer->slug]) }}"><i class="mdi mdi-account-card-details"></i></a>&nbsp;&nbsp;
									<a class="btn btn-info btn-circle btn-sm" href="{{ route('jugadores.edit', ['slug' => $gamer->slug]) }}"><i class="fa fa-edit"></i></a>&nbsp;&nbsp;
									<button class="btn btn-danger btn-circle btn-sm" onclick="deleteGamer('{{ $gamer->slug }}')"><i class="fa fa-trash"></i></button>
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

<div class="modal fade" id="deleteGamer" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">¿Estás seguro de que quieres eliminar este jugador?</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-footer">
				<form action="#" method="POST" id="formDeleteGamer">
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
@endsection