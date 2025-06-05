{{-- En resources/views/admin/all-properties/show.blade.php --}}
@extends('layouts.admin')

@section('title', 'Detalle de Propiedad: ' . $property->title)

@section('header')
    {{ __('Detalle de Propiedad: ') }} {{ $property->title }}
@endsection

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4>Información Completa</h4>
            <a href="{{ route('admin.all-properties.index') }}" class="btn btn-secondary">Volver al Listado</a>
        </div>
        <div class="card-body">
            <div class="row">
                {{-- Columna Izquierda - Información Principal --}}
                <div class="col-md-8">
                    <h5>Datos Principales</h5>
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th style="width: 30%;">ID Propiedad:</th>
                                <td>{{ $property->id }}</td>
                            </tr>
                            <tr>
                                <th>Título Descriptivo:</th>
                                <td>{{ $property->title }}</td>
                            </tr>
                            <tr>
                                <th>Asesor Inmobiliario:</th>
                                <td>{{ $property->user->name ?? 'No asignado' }} ({{ $property->user->email ?? '' }})</td>
                            </tr>
                            <tr>
                                <th>Categoría:</th>
                                <td>{{ $property->category->name ?? 'No asignada' }}</td>
                            </tr>
                            <tr>
                                <th>Tipo de Operación:</th>
                                <td>{{ ucfirst($property->type_operation) }}</td> {{-- [cite: 12] --}}
                            </tr>
                            <tr>
                                <th>Tipo de Propiedad:</th>
                                <td>{{ $property->type_property }}</td> {{-- Asumiendo que tienes un campo type_property [cite: 13] --}}
                            </tr>
                            <tr>
                                <th>Precio:</th>
                                <td>{{ $property->price_currency ?? 'CLP' }} {{ number_format($property->price, 0, ',', '.') }}</td> {{-- [cite: 14] --}}
                            </tr>
                            <tr>
                                <th>Estado Actual:</th>
                                <td><span class="badge bg-info">{{ $property->status ?? 'No definido' }}</span></td> {{-- [cite: 15] --}}
                            </tr>
                            <tr>
                                <th>Dirección Completa:</th>
                                <td>{{ $property->address ?? 'No especificada' }}, {{ $property->commune ?? '' }}, {{ $property->city ?? '' }}</td> {{-- [cite: 14] --}}
                            </tr>
                            <tr>
                                <th>Descripción Detallada:</th>
                                <td>{!! nl2br(e($property->description)) !!}</td> {{-- [cite: 13] --}}
                            </tr>
                        </tbody>
                    </table>

                    <h5 class="mt-4">Dimensiones y Características</h5>
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr><th style="width: 30%;">Superficie Total:</th><td>{{ $property->surface_total ?? 'N/A' }} m²</td></tr> {{-- [cite: 14] --}}
                            <tr><th>Superficie Construida:</th><td>{{ $property->surface_built ?? 'N/A' }} m²</td></tr> {{-- [cite: 15] --}}
                            <tr><th>Número de Dormitorios:</th><td>{{ $property->bedrooms ?? 'N/A' }}</td></tr> {{-- [cite: 15] --}}
                            <tr><th>Número de Baños:</th><td>{{ $property->bathrooms ?? 'N/A' }}</td></tr> {{-- [cite: 15] --}}
                            <tr><th>Estacionamientos:</th><td>{{ $property->parking_lots ?? 'N/A' }}</td></tr> {{-- [cite: 15] --}}
                            <tr><th>Bodegas:</th><td>{{ $property->cellars ?? 'N/A' }}</td></tr> {{-- [cite: 15] --}}
                        </tbody>
                    </table>

                    @if($property->features && $property->features->count() > 0)
                        <h5 class="mt-4">Características Adicionales</h5> {{-- [cite: 16] --}}
                        <ul class="list-group">
                            @foreach($property->features as $feature)
                                <li class="list-group-item">{{ $feature->name }}</li>
                            @endforeach
                        </ul>
                    @endif

                    @if($property->propertyCustomFieldValues && $property->propertyCustomFieldValues->count() > 0)
                        <h5 class="mt-4">Campos Personalizados</h5> {{-- [cite: 17] --}}
                        <table class="table table-bordered table-striped">
                            <tbody>
                                @foreach($property->propertyCustomFieldValues as $customValue)
                                    <tr>
                                        <th style="width: 30%;">{{ $customValue->customFieldDefinition->name ?? 'Campo Desconocido' }}:</th>
                                        <td>{{ $customValue->value }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                {{-- Columna Derecha - Multimedia y Mapa --}}
                <div class="col-md-4">
                    @if($property->propertyPhotos && $property->propertyPhotos->count() > 0)
                        <h5 class="mt-4 mt-md-0">Fotos</h5> {{-- [cite: 18] --}}
                        <div class="row g-2">
                            @foreach($property->propertyPhotos as $photo)
                                <div class="col-6 col-md-12 col-lg-6">
                                    {{-- Asumiendo que tienes una URL para la imagen. Ajusta 'image_path' al nombre de tu campo --}}
                                    <img src="{{ Storage::url($photo->image_path) }}" alt="Foto de la propiedad" class="img-fluid rounded mb-2" style="width: 100%; height: 150px; object-fit: cover;">
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p>No hay fotos disponibles para esta propiedad.</p>
                    @endif

                    @if($property->propertyVideos && $property->propertyVideos->count() > 0)
                        <h5 class="mt-4">Videos</h5> {{-- [cite: 20] --}}
                        @foreach($property->propertyVideos as $video)
                            @if($video->is_external_link)
                                <p><a href="{{ $video->video_url }}" target="_blank">Ver Video (Enlace Externo)</a></p>
                                {{-- Aquí podrías embeber el video si es de YouTube/Vimeo usando un iframe --}}
                            @else
                                <p><a href="{{ Storage::url($video->video_path) }}" target="_blank">Ver Video (Subido)</a></p>
                                {{-- Aquí podrías usar una etiqueta <video> si el formato es compatible --}}
                            @endif
                        @endforeach
                    @else
                        <p class="mt-3">No hay videos disponibles para esta propiedad.</p>
                    @endif

                    <h5 class="mt-4">Ubicación en Mapa (Próximamente)</h5>
                    {{-- Aquí irá la integración del mapa [cite: 14] --}}
                    <div id="map" style="height: 250px; background-color: #e9e9e9;" class="border rounded d-flex align-items-center justify-content-center">
                        <small>Visualización de mapa no implementada aún.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
{{-- Si necesitas scripts específicos para esta página, como para un carrusel de fotos o un mapa --}}
@endpush