@extends('layouts.admin')

@section('title', 'Crear Torneo')
@section('page-title', 'Crear Torneo')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('torneos.index') }}">Torneos</a></li>
<li class="breadcrumb-item active">Registro</li>
@endsection

@section('links')
<link rel="stylesheet" href="{{ asset('/admins/vendors/datepicker/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}">
<link rel="stylesheet" href="{{ asset('/admins/vendors/touchspin/jquery.bootstrap-touchspin.min.css') }}">
@endsection

@section('content')

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">

				@include('admin.partials.errors')

				<h6 class="card-subtitle">Campos obligatorios (<b class="text-danger">*</b>)</h6>
				<form action="{{ route('torneos.store') }}" method="POST" class="form" id="formTournament">
					@csrf
					<div class="row">
						<div class="form-group col-lg-6 col-md-6 col-12">
							<label class="col-form-label">Club<b class="text-danger">*</b></label>
							<select class="form-control" name="club_id" required>
								<option value="">Seleccione</option>
								@foreach($clubs as $club)
								<option value="{{ $club->slug }}" @if(old('club_id')==$club->slug) selected @endif>{{ $club->name }}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group col-lg-6 col-md-6 col-12">
							<label class="col-form-label">Nombre del Torneo<b class="text-danger">*</b></label>
							<input class="form-control alfa" type="text" name="name" required placeholder="Introduzca un nombre" value="{{ old('name') }}">
						</div>
						<div class="form-group col-lg-6 col-md-6 col-12">
							<label class="col-form-label">Tipo<b class="text-danger">*</b></label>
							<select class="form-control" name="type" required>
								<option value="">Seleccione</option>
								<option value="1" @if(old('type')==1) selected @endif>Interno</option>
								<option value="2" @if(old('type')==2) selected @endif>Interclubes</option>
							</select>
						</div>
						<div class="form-group col-lg-6 col-md-6 col-12">
							<label class="col-form-label">Máximo de Grupos<b class="text-danger">*</b></label>
							<input class="form-control numberTournament" type="text" name="groups" required placeholder="Introduzca el número máximo de grupos" value="2" id="maxGroup">
						</div>
						<div class="form-group col-lg-6 col-md-6 col-12">
							<label class="col-form-label">Máximo de Parejas<b class="text-danger">*</b></label>
							<input class="form-control numberCouplesTournament" type="text" name="couples" required placeholder="Introduzca el número máximo de parejas" value="6">
						</div>
						<div class="form-group col-lg-6 col-md-6 col-12">
							<label class="col-form-label">Fecha de Inicio<b class="text-danger">*</b></label>
							<input class="form-control date" type="text" name="start" required placeholder="Seleccione una fecha" value="{{ old('start') }}">
						</div>
						<div class="form-group col-12">
							<div class="btn-group" role="group">
								<button type="submit" class="btn btn-primary" action="tournament">Guardar</button>
								<a href="{{ route('torneos.index') }}" class="btn btn-secondary">Volver</a>
							</div>
						</div>
					</div>
					
				</form>
			</div>
		</div>
	</div>
</div>

@endsection

@section('script')
<script src="{{ asset('/admins/vendors/touchspin/jquery.bootstrap-touchspin.min.js') }}"></script>
<script src="{{ asset('/admins/vendors/validate/jquery.validate.js') }}"></script>
<script src="{{ asset('/admins/vendors/validate/additional-methods.js') }}"></script>
<script src="{{ asset('/admins/vendors/validate/messages_es.js') }}"></script>
<script src="{{ asset('/admins/js/validate.js') }}"></script>
<script src="{{ asset('/admins/vendors/moment/moment.js') }}"></script>
<script src="{{ asset('/admins/vendors/datepicker/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}"></script>
@endsection