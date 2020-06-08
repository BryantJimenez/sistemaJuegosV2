@extends('layouts.auth')

@section('title', 'Restaurar Contraseña')

@section('content')

<section id="wrapper" class="login-register login-sidebar login-image">
    <div class="login-box card">
        <div class="card-body">
            <form class="form-horizontal form-material mt-md-5 mt-4" action="{{ route('password.update') }}" method="POST">
                {{ csrf_field() }}
                <input type="hidden" name="token" value="{{ $token }}">
                <p class="h2 text-center db">RESTAURAR CONTRASEÑA</p>

                <div class="form-group m-t-40">
                    <div class="col-xs-12">
                        <label>Correo Electrónico</label>
                        <input class="form-control @error('email') is-invalid @enderror" type="email" name="email" required placeholder="{{ 'ejemplo@gmail.com' }}" value="{{ old('email') }}" autocomplete="email" autofocus>
                    </div>

                    @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group">
                    <div class="col-xs-12">
                        <label>Nueva Contraseña</label>
                        <input id="password" class="form-control @error('password') is-invalid @enderror" type="password" required name="password" placeholder="********" autocomplete="new-password">
                    </div>

                    @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
                </div>
                <div class="form-group">
                    <div class="col-xs-12">
                        <label>Confirmar Contraseña</label>
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="********">
                    </div>
                </div>
                <div class="form-group text-center m-t-20">
                    <div class="col-xs-12">
                        <button class="btn btn-primary btn-lg btn-block text-uppercase waves-effect waves-light" type="submit">Enviar</button>
                    </div>
                </div>
                <div class="form-group m-b-0">
                    <div class="col-sm-12 text-center">
                        Deseas ingresar? <a href="{{ route('login') }}" class="text-primary m-l-5"><b>Ingresa</b></a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection
