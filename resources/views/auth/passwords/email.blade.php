@extends('layouts.auth')

@section('title', 'Recuperar Contraseña')

@section('content')

<section id="wrapper" class="login-register login-sidebar login-image">
    <div class="login-box card">
        <div class="card-body">
            <form class="form-horizontal form-material my-5" action="{{ route('password.email') }}" method="POST">
                {{ csrf_field() }}
                <p class="h2 text-center db">RECUPERAR CONTRASEÑA</p>
                <p class="text-muted">Ingresa tu correo y te enviaremos un link para que recuperes tu contraseña.</p>

                @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
                @endif

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
