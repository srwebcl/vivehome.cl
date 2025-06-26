@extends('layouts.admin')

@section('title', 'Crear Nueva Propiedad - Vive Home')

@section('header')
    {{ __('Cargar Nueva Propiedad') }}
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h4>Completa los datos de la propiedad</h4>
    </div>
    <div class="card-body">
        {{-- 
            Este formulario enviará los datos al método 'store' de nuestro PropertyController.
            'enctype="multipart/form-data"' es crucial para permitir la subida de archivos (fotos).
        --}}
        <form action="{{ route('asesor.properties.store') }}" method="POST" enctype="multipart/form-data">
            
            {{-- Incluimos el formulario parcial, pasándole una nueva instancia vacía de Property --}}
            @include('asesor.properties._form', ['property' => new \App\Models\Property()])

        </form>
    </div>
</div>
@endsection