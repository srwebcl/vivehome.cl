@extends('layouts.admin')

@section('title', 'Editar Propiedad - Vive Home')

@section('header')
    {{-- Usamos el título de la propiedad para que el asesor sepa qué está editando --}}
    {{ __('Editar Propiedad: ') }} {{ $property->title }}
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Modifica los datos de la propiedad</h4>
    </div>
    <div class="card-body">
        {{--
            Este formulario apunta a la ruta 'update' y pasa el ID de la propiedad.
            Usamos @method('PUT') para indicarle a Laravel que es una solicitud de actualización.
        --}}
        <form action="{{ route('asesor.properties.update', $property) }}" method="POST" enctype="multipart/form-data">
            @method('PUT')
            
            {{-- Reutilizamos el mismo formulario, pero esta vez le pasamos la variable $property --}}
            {{-- que contiene los datos a editar. El formulario se llenará automáticamente. --}}
            @include('asesor.properties._form', ['property' => $property])

        </form>
    </div>
</div>
@endsection