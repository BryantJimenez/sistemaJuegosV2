<aside class="left-sidebar">
    <div class="scroll-sidebar">
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <li class="user-profile"> <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false"><img src="{{ asset('/admins/img/users/'.Auth::user()->photo) }}" alt="Foto de perfil" /><span class="hide-menu">{{ Auth::user()->name." ".Auth::user()->lastname }} </span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Cerrar Sección</a></li>
                    </ul>
                </li>
                <li class="nav-devider"></li>
                <li class="nav-small-cap">MÓDULOS</li>
                <li><a class="waves-effect waves-dark" href="{{ route('home') }}"><i class="mdi mdi-coffee"></i><span class="hide-menu">Inicio</span></a></li>
                <li> <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false"><i class="fa fa-trophy"></i><span class="hide-menu">Torneos</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('torneos.create') }}">Registrar</a></li>
                        <li><a href="{{ route('torneos.index') }}">Lista</a></li>
                    </ul>
                </li>
                <li> <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false"><i class="mdi mdi-account-multiple"></i><span class="hide-menu">Jugadores</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('jugadores.create') }}">Registrar</a></li>
                        <li><a href="{{ route('jugadores.index') }}">Lista</a></li>
                    </ul>
                </li>
                <li> <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false"><i class="fa fa-building"></i><span class="hide-menu">Clubes</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('clubes.create') }}">Registrar</a></li>
                        <li><a href="{{ route('clubes.index') }}">Lista</a></li>
                    </ul>
                </li>
                <li> <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false"><i class="fa fa-star"></i><span class="hide-menu">Usuarios</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('usuarios.create') }}">Registrar</a></li>
                        <li><a href="{{ route('usuarios.index') }}">Lista</a></li>
                    </ul>
                </li>
                {{-- <li> <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false"><i class="mdi mdi-cards-playing-outline"></i><span class="hide-menu">Juegos</span></a>
                    <ul aria-expanded="false" class="collapse">
                        <li><a href="{{ route('juegos.create') }}">Registrar</a></li>
                        <li><a href="{{ route('juegos.index') }}">Lista</a></li>
                    </ul>
                </li> --}}
            </ul>
        </nav>
    </div>
</aside>