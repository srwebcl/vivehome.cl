<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Título dinámico, con un valor por defecto --}}
    <title>@yield('title', 'Vive Home - Asesores Inmobiliarios')</title>

    {{-- SEO y Metatags - Se podrán llenar dinámicamente --}}
    <meta name="description" content="@yield('description', 'Encuentra la propiedad de tus sueños en La Serena y Coquimbo. Casas, departamentos y terrenos en venta y arriendo.')">
    
    {{-- Fuentes y Estilos --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Carga de assets (CSS y JS) a través de Vite, como se definió en el plan --}}
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="d-flex flex-column min-vh-100">

    {{-- Cabecera y Navegación Principal --}}
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}">
                    <img src="{{ asset('assets/images/logo.webp') }}" alt="Vive Home Logo" style="height: 40px;" onerror="this.style.display='none'; this.onerror=null;">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Inicio</a>
                        </li>
                        <li class="nav-item">
                            {{-- Esta ruta mostrará tanto ventas como arriendos inicialmente [cite: 24] --}}
                            <a class="nav-link {{ request()->routeIs('public.properties.index') ? 'active' : '' }}" href="{{ route('public.properties.index') }}">Propiedades</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('public.about') ? 'active' : '' }}" href="{{ route('public.about') }}">Quiénes Somos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('public.contact') ? 'active' : '' }}" href="{{ route('public.contact') }}">Contacto</a>
                        </li>
                    </ul>
                    <a href="{{ route('login') }}" class="btn btn-primary ms-lg-3">Acceso Asesores</a>
                </div>
            </div>
        </nav>
    </header>

    {{-- Contenido principal de la página --}}
    <main class="flex-grow-1">
        @yield('content')
    </main>

    {{-- Pie de Página --}}
    <footer class="bg-dark text-white pt-5 pb-4">
        <div class="container text-center text-md-start">
            <div class="row">
                <div class="col-md-4 col-lg-4 col-xl-4 mx-auto mb-4">
                    <h6 class="text-uppercase fw-bold">Vive Home</h6>
                    <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: #7c4dff; height: 2px"/>
                    <p>
                        Tu socio estratégico en la búsqueda y gestión de propiedades. Te acompañamos en cada paso del proceso de compra, venta o arriendo.
                    </p>
                </div>

                <div class="col-md-4 col-lg-2 col-xl-2 mx-auto mb-4">
                    <h6 class="text-uppercase fw-bold">Enlaces</h6>
                    <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: #7c4dff; height: 2px"/>
                    <p><a href="{{ route('public.properties.index') }}" class="text-white">Propiedades</a></p>
                    <p><a href="{{ route('public.about') }}" class="text-white">Quiénes Somos</a></p>
                    <p><a href="{{ route('public.contact') }}" class="text-white">Contacto</a></p>
                </div>
                
                <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
                    <h6 class="text-uppercase fw-bold">Contacto</h6>
                    <hr class="mb-4 mt-0 d-inline-block mx-auto" style="width: 60px; background-color: #7c4dff; height: 2px"/>
                    <p><i class="bi bi-geo-alt-fill me-3"></i> La Serena, Coquimbo, Chile</p>
                    <p><i class="bi bi-envelope-fill me-3"></i> info@vivehome.cl</p>
                    <p><i class="bi bi-telephone-fill me-3"></i> +56 9 1234 5678</p>
                </div>
            </div>
        </div>
        <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2)">
            © {{ date('Y') }} Vive Home. Todos los derechos reservados.
        </div>
    </footer>

    {{-- Espacio para scripts específicos de cada página --}}
    @stack('scripts')
</body>
</html>