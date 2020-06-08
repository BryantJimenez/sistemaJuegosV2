@extends('layouts.auth')

@section('title', 'Ingresar')

@section('content')

<section id="wrapper" class="login-register login-sidebar login-image">
  <div class="login-box card">
    <div class="card-body">
      <form class="form-horizontal form-material mt-md-5 mt-4" action="{{ route('login') }}" method="POST">
        {{ csrf_field() }}
        <p class="h2 text-center db">INICIA SESSIÓN</p>
        <div class="form-group m-t-40">
          <div class="col-xs-12">
            <label>Correo Electrónico</label>
            <input class="form-control @error('email') is-invalid @enderror" type="email" name="email" required placeholder="{{ 'ejemplo@gmail.com' }}" value="{{ old('email') }}">
          </div>

          @error('email')
          <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
          </span>
          @enderror
        </div>
        <div class="form-group">
          <div class="col-xs-12">
            <label>Contraseña</label>
            <input class="form-control @error('password') is-invalid @enderror" type="password" required name="password" placeholder="********">
          </div>

          @error('password')
          <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
          </span>
          @enderror
        </div>
        <div class="form-group row">
          <div class="col-md-12">
            <div class="checkbox checkbox-primary pull-left p-t-0">
              <input id="checkbox-signup" type="checkbox" class="filled-in chk-col-light-blue" {{ old('remember') ? 'checked' : '' }}>
              <label for="checkbox-signup"> Recuerdame</label>
            </div>
          </div>
        </div>
        <div class="form-group text-center m-t-20">
          <div class="col-xs-12">
            <button class="btn btn-info btn-lg btn-block text-uppercase btn-rounded" type="submit">Ingresar</button>
          </div>
        </div>
        <div class="form-group m-b-0">
          <div class="col-sm-12 text-center">
            Olvidaste tu contraseña? <a href="{{ route('password.request') }}" class="text-primary m-l-5"><b>Recuperala</b></a>
          </div>
        </div>
      </form>
    </div>
  </div>
</section>

@endsection