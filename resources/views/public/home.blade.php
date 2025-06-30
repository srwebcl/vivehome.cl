@extends('layouts.public')

@section('title', 'Inicio - Vive Home Asesores Inmobiliarios')

@section('content')

    {{-- ============================================= --}}
    {{-- INICIO: NUEVA SECCIÓN HERO CON SLIDER Y FILTRO --}}
    {{-- ============================================= --}}
    <div id="hero-carousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
        {{-- Indicadores del carrusel (los puntos de abajo) --}}
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#hero-carousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#hero-carousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#hero-carousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        </div>

        {{-- Contenido del carrusel (las imágenes de fondo) --}}
        <div class="carousel-inner">
            <div class="carousel-item active" style="background-image: url('{{ asset('assets/images/fondo.jpg') }}')">
                {{-- Contenido superpuesto para el Slide 1 --}}
                <div class="carousel-caption-custom d-none d-md-block text-start">
                    <h1 class="display-4 fw-bold">Encuentra tu próximo hogar</h1>
                    <p class="lead">La propiedad que buscas en la IV Región está aquí. ¿Qué esperas?</p>
                </div>
            </div>
            <div class="carousel-item" style="background-image: url('{{ asset('assets/images/fondo1.jpg') }}')">
                 {{-- Contenido superpuesto para el Slide 2 --}}
                 <div class="carousel-caption-custom d-none d-md-block text-start">
                    <h1 class="display-4 fw-bold">Asesoría de Confianza</h1>
                    <p class="lead">Te acompañamos en cada paso para que tomes la mejor decisión.</p>
                </div>
            </div>
            <div class="carousel-item" style="background-image: url('{{ asset('assets/images/fondo2.jpg') }}')">
                 {{-- Contenido superpuesto para el Slide 3 --}}
                 <div class="carousel-caption-custom d-none d-md-block text-start">
                    <h1 class="display-4 fw-bold">Invierte con Seguridad</h1>
                    <p class="lead">Descubre las mejores oportunidades de inversión inmobiliaria.</p>
                </div>
            </div>
        </div>
        
        {{-- Controles de Anterior/Siguiente --}}
        <button class="carousel-control-prev" type="button" data-bs-target="#hero-carousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#hero-carousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span><span class="visually-hidden">Next</span>
        </button>

        {{-- Formulario de búsqueda superpuesto --}}
        <div class="filter-form-container">
            <div class="container">
                <div class="card p-3 shadow-lg">
                    <span class="d-block mb-2 text-muted">Filtro de búsqueda</span>
                    <form action="{{ route('public.properties.index') }}" method="GET">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <select name="operation_type" class="form-select">
                                    <option value="">Operación</option>
                                    <option value="Venta">Venta</option>
                                    <option value="Arriendo">Arriendo</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select name="category_id" class="form-select">
                                    <option value="">Tipo de Propiedad</option>
                                     @foreach($filterData['categories'] as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                 <select name="commune" class="form-select">
                                    <option value="">Comuna</option>
                                    @foreach($filterData['communes'] as $commune)
                                        <option value="{{ $commune }}">{{ $commune }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Buscar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- ============================================= --}}
    {{-- FIN: NUEVA SECCIÓN HERO --}}
    {{-- ============================================= --}}


    {{-- Sección de Propiedades Destacadas --}}
    <div class="container my-5">
        <h2 class="text-center mb-4">Propiedades Destacadas</h2>
        
        {{-- Verificamos si la colección de propiedades destacadas no está vacía --}}
        @if($featuredProperties->isNotEmpty())
            <div class="row">
                {{-- Recorremos las propiedades y usamos nuestro componente para cada una --}}
                @foreach($featuredProperties as $property)
                    <div class="col-md-4 mb-4">
                        <x-property-card :property="$property" />
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-3">
                <a href="{{ route('public.properties.index') }}" class="btn btn-outline-primary">Ver todas las propiedades</a>
            </div>
        @else
            {{-- Mensaje a mostrar si no hay propiedades destacadas --}}
            <p class="text-center text-muted">Próximamente tendremos nuevas propiedades destacadas para ti.</p>
        @endif
    </div>

@endsection

{{-- Estilos personalizados para lograr el diseño de SJ Group --}}
@push('styles')
<style>
    #hero-carousel {
        height: 60vh; /* Altura del carrusel */
        position: relative;
        margin-bottom: 80px; /* Espacio para que el filtro no se pegue al contenido de abajo */
    }

    .carousel-item {
        height: 60vh;
        background-size: cover;
        background-position: center center;
    }

    /* Overlay oscuro sobre las imágenes para que el texto resalte */
    .carousel-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.4);
    }

    .carousel-caption-custom {
        position: absolute;
        top: 50%;
        left: 15%;
        transform: translateY(-50%);
        z-index: 10;
        color: white;
        text-shadow: 1px 1px 3px rgba(0,0,0,0.7);
    }

    /* Contenedor del formulario de búsqueda */
    .filter-form-container {
        position: absolute;
        bottom: -50px; /* La mitad de la altura del formulario para que quede "flotando" */
        left: 0;
        right: 0;
        z-index: 15;
    }

    .filter-form-container .card {
        border-radius: 0.5rem;
    }
</style>
@endpush