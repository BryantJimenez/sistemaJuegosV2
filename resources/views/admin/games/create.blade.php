@extends('layouts.admin')

@section('title', 'Registro de Juego')
@section('page-title', 'Registro de Juego')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('juegos.index') }}">Juegos</a></li>
<li class="breadcrumb-item active">Registro</li>
@endsection

@section('links')
<link rel="stylesheet" href="{{ asset('/admins/vendors/multiselect/bootstrap.multiselect.css') }}">
<link rel="stylesheet" href="{{ asset('/admins/vendors/touchspin/jquery.bootstrap-touchspin.min.css') }}">
@endsection

@section('content')

<div class="row">
	<div class="col-12">
		<div class="card">
			<div class="card-body">

				@include('admin.partials.errors')

				<h6 class="card-subtitle">Campos obligatorios (<b class="text-danger">*</b>)</h6>
				<form action="{{ route('juegos.store') }}" method="POST" class="form" id="formGame">
					@csrf
					<div class="row">
						<div class="form-group col-12">
							<label class="col-form-label">Tipo</label>
							<input class="form-control" type="text" readonly value="Slam">
						</div>
						<div class="form-group col-lg-6 col-md-6 col-12">
							<label class="col-form-label">Pareja 1 (2 máximo)<b class="text-danger">*</b></label>
							<select class="form-control" name="couple1[]" required multiple id="multiselectCouples">
								@foreach($gamers as $gamer)
								<option value="{{ $gamer->slug }}">{{ $gamer->name." ".$gamer->lastname }}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group col-lg-6 col-md-6 col-12">
							<label class="col-form-label">Pareja 2 (2 máximo)<b class="text-danger">*</b></label>
							<select class="form-control" name="couple2[]" required multiple id="multiselectCouples2">
								@foreach($gamers as $gamer)
								<option value="{{ $gamer->slug }}">{{ $gamer->name." ".$gamer->lastname }}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group col-lg-6 col-md-6 col-12">
							<label class="col-form-label">Puntaje de la Pareja 1<b class="text-danger">*</b></label>
							<input class="form-control numberPoint" type="text" name="points1" required placeholder="Introduzca el puntaje" value="0">
						</div>
						<div class="form-group col-lg-6 col-md-6 col-12">
							<label class="col-form-label">Puntaje de la Pareja 2<b class="text-danger">*</b></label>
							<input class="form-control numberPoint" type="text" name="points2" required placeholder="Introduzca el puntaje" value="0">
						</div>
						<div class="form-group col-12">
							<div class="btn-group" role="group">
								<button type="submit" class="btn btn-primary" action="game">Guardar</button>
								<a href="{{ route('juegos.index') }}" class="btn btn-secondary">Volver</a>
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
<script src="{{ asset('/admins/vendors/multiselect/bootstrap-multiselect.js') }}"></script>
<script src="{{ asset('/admins/vendors/touchspin/jquery.bootstrap-touchspin.min.js') }}"></script>
<script src="{{ asset('/admins/vendors/validate/jquery.validate.js') }}"></script>
<script src="{{ asset('/admins/vendors/validate/additional-methods.js') }}"></script>
<script src="{{ asset('/admins/vendors/validate/messages_es.js') }}"></script>
<script src="{{ asset('/admins/js/validate.js') }}"></script>
@endsection