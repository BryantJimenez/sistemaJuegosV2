<header class="topbar">
    <nav class="navbar top-navbar navbar-expand-md navbar-light">
        <div class="navbar-header">
            <a class="navbar-brand" href="{{ route('home') }}">
                <b>
                    <img src="{{ asset('/admins/img/logo.png') }}" alt="icono" class="dark-logo" width="50" />
                    <img src="{{ asset('/admins/img/logo.png') }}" alt="icono" class="light-logo" width="50" />
                </b>
                <span>
                    <img src="{{ asset('/admins/img/nombreSistema.png') }}" alt="logo" class="dark-logo d-none" />   
                    <img src="{{ asset('/admins/img/nombreSistema.png') }}" class="light-logo" alt="logo" />
                </span>
            </a>
        </div>
        <div class="navbar-collapse">
            <ul class="navbar-nav">
                <li class="nav-item"> <a class="nav-link nav-toggler hidden-md-up waves-effect waves-dark" href="javascript:void(0)"><i class="ti-menu"></i></a> </li>
                <li class="nav-item"> <a class="nav-link sidebartoggler hidden-sm-down waves-effect waves-dark" href="javascript:void(0)"><i class="ti-menu"></i></a> </li>
            </ul>
            <ul class="navbar-nav ml-2 mr-auto d-none d-lg-block d-md-block">
                <span class="text-white font-bold">@yield('club')<br>@yield('tournament')</span>
            </ul>
            <ul class="navbar-nav my-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="{{ asset('/admins/img/users/'.Auth::user()->photo) }}" alt="user" class="profile-pic" /></a>
                    <div class="dropdown-menu dropdown-menu-right animated flipInY">
                        <ul class="dropdown-user">
                            <li>
                                <div class="dw-user-box">
                                    <div class="u-img"><img src="{{ asset('/admins/img/users/'.Auth::user()->photo) }}" alt="user"></div>
                                    <div class="u-text">
                                        <h4>{{ Auth::user()->name." ".Auth::user()->lastname }}</h4>
                                        <p class="text-muted">{!! userState(Auth::user()->state) !!}</p>
                                    </div>
                                </div>
                            </li>
                            <li role="separator" class="divider"></li>
                            <li><a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-power-off"></i> Cerrar Sesión</a></li>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
</header>