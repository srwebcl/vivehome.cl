<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Vive Home')</title>

    <!-- Fonts (Opcional, puedes usar las de Bootstrap o las que prefieras) -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

      <!-- Bootstrap Icons CDN -->
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Estilos y Scripts Compilados por Vite -->
    @vite(['resources/css/app.scss', 'resources/js/app.js'])

</head>
<body class="d-flex flex-column min-vh-100 bg-light">
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('admin.dashboard') }}">
                    <img src="{{ asset('assets/images/logo.webp') }}" alt="Vive Home Logo" style="height: 30px; margin-right: 10px;" onerror="this.style.display='none'; this.onerror=null;"> <!-- Añade tu logo aquí -->
                    Admin Vive Home
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Dashboard</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">Gestionar Usuarios</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">Gestionar Categorías</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.custom_fields.*') ? 'active' : '' }}" href="{{ route('admin.custom_fields.index') }}">Campos Personalizados</a>
    </li>
    <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.settings.edit') ? 'active' : '' }}" href="{{ route('admin.settings.edit') }}">
        <i class="bi bi-gear-fill me-2"></i>Configuración del Sitio
    </a>
    </li>
    <li class="nav-item">
    <a class="nav-link {{ request()->routeIs('admin.all-properties.index') ? 'active' : '' }}" href="{{ route('admin.all-properties.index') }}">
        <i class="bi bi-building me-2"></i>Todas las Propiedades
    </a>
</li>
</ul>
                    <ul class="navbar-nav ms-auto">
                        @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUserLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                {{ Auth::user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownUserLink">
                            <li><a class="dropdown-item" href="{{ route('admin.profile.edit') }}">Perfil</a></li>                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Cerrar Sesión</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Contenido Principal de la Página -->
    <main class="flex-grow-1 container mt-4 mb-4">
        @hasSection('header')
            <header class="py-3 mb-4 border-bottom">
                <div class="container">
                    <h2 class="h4">@yield('header')</h2>
                </div>
            </header>
        @endif

        @yield('content')
    </main>

    <footer class="bg-dark text-white text-center p-3 mt-auto">
        <div class="container">
            &copy; {{ date('Y') }} Vive Home Inmobiliaria. Todos los derechos reservados.
        </div>
    </footer>
</body>
</html>