<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDzfWNgLGyWJCvTfQgOjdp4u1BR9VHedLQ"
    defer
    ></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/fontawesome.min.css" integrity="sha512-d0olNN35C6VLiulAobxYHZiXJmq+vl+BGIgAxQtD5+kqudro/xNMvv2yIHAciGHpExsIbKX3iLg+0B6d0k4+ZA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/regular.min.css" integrity="sha512-TzeemgHmrSO404wTLeBd76DmPp5TjWY/f2SyZC6/3LsutDYMVYfOx2uh894kr0j9UM6x39LFHKTeLn99iz378A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light shadow-sm mb-4" style="position: fixed;
			top: 0;
			left: 0;
			width: 100%;
			display: flex;
			justify-content: space-around;
			transition: 0.7s;
			z-index: 10;
            background: linear-gradient(90deg, #0C81E6, #0C81E6, #0C81E6);">
            <div class="container">
                <a style="color: white" class="navbar-brand" href="{{ url('/') }}">
                    Rutas
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    @guest

                    @else
                    <ul class="navbar-nav me-auto" >
                        <li class="nav-link" style="color: white">
                            <router-link class="dropdown-item" to="home">Home</router-link>
                        </li>
                        <li class="nav-link" style="color: white">
                            <router-link class="dropdown-item" to="estudiantes">Estudiantes</router-link>
                        </li>
                        <li class="nav-link" style="color: white">
                            <router-link class="dropdown-item" to="buses">Buses</router-link>
                        </li>
                        <li class="nav-link" style="color: white">
                            <router-link class="dropdown-item" to="rutas">Rutas Óptimas</router-link>
                        </li>
                        <li class="nav-link" style="color: white">
                            <router-link class="dropdown-item" to="reporte">Reporte</router-link>
                        </li>
                        <li class="nav-item dropdown">
                            <a style="color: white" id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                Menu
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <router-link class="dropdown-item" to="estudiantes">Estudiantes</router-link>
                                <router-link class="dropdown-item" to="/home">Home</router-link>
                            </div>
                        </li>
                    </ul>
                    @endguest
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a style="color: white" id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown" >
                                    <a class="dropdown-item" href="{{ route('logout') }}" 
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        Cerrar Sesión
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4" style="margin-top:50px">
            <router-view></router-view>
            @yield('content')
        </main>
    </div>
</body>
</html>
