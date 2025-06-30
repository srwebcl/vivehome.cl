@props(['property'])

{{-- 
    El componente recibe una variable '$property' con los datos.
    Usamos clases de Bootstrap 5 para la estructura y el diseño.
--}}
<div class="card h-100 shadow-sm border-0 property-card">
    {{-- Toda la tarjeta es un enlace a la página de detalles de la propiedad --}}
    <a href="{{ route('public.properties.show', $property) }}" class="text-decoration-none text-dark">
        
        <div class="position-relative">
            {{-- Mostramos la primera foto de la propiedad. Si no tiene, muestra una imagen genérica. --}}
            <img src="{{ $property->photos->first() ? Storage::url($property->photos->first()->file_path) : 'https://via.placeholder.com/400x250.png?text=Vive+Home' }}" 
                 class="card-img-top property-card-img" 
                 alt="Foto principal de {{ $property->title }}">
            
            {{-- Etiquetas (badges) sobre la imagen para información rápida --}}
            <div class="property-card-tags">
                <span class="badge bg-primary">{{ $property->operation_type }}</span>
                <span class="badge bg-secondary">{{ $property->category->name ?? 'Sin Categoría' }}</span>
            </div>
        </div>

        <div class="card-body d-flex flex-column">
            {{-- Título y Ubicación --}}
            <h5 class="card-title fw-bold">{{ Str::limit($property->title, 50) }}</h5>
            <p class="card-text text-muted mb-2 small">
                <i class="bi bi-geo-alt-fill me-1"></i>
                {{ $property->commune ?? 'Ubicación no especificada' }}{{ $property->city ? ', ' . $property->city : '' }}
            </p>
            
            {{-- Precio --}}
            <h4 class="card-text fw-bold text-primary my-2">
                {{ $property->currency == 'UF' ? 'UF' : '$' }} {{ number_format($property->price, $property->currency == 'UF' ? 2 : 0, ',', '.') }}
            </h4>
            
            {{-- Separador y Características --}}
            <div class="mt-auto pt-2">
                <hr class="my-2">
                <div class="d-flex justify-content-between text-muted small">
                    <span><i class="bi bi-rulers me-1" title="Superficie construida"></i>{{ $property->built_area_m2 ?? 'N/A' }} m²</span>
                    <span><i class="bi bi-door-closed-fill me-1" title="Dormitorios"></i>{{ $property->bedrooms ?? 'N/A' }}</span>
                    <span><i class="bi bi-droplet-fill me-1" title="Baños"></i>{{ $property->bathrooms ?? 'N/A' }}</span>
                    <span><i class="bi bi-car-front-fill me-1" title="Estacionamientos"></i>{{ $property->parking_lots ?? '0' }}</span>
                </div>
            </div>
        </div>
    </a>
</div>

{{-- 
    Añadimos estilos directamente aquí usando @push para que el componente sea autocontenido.
    Estos estilos se cargarán en la sección de 'styles' del layout si la definimos, o al final del body.
    Por ahora, para simplicidad, los incluimos aquí.
--}}
@once
    @push('styles')
    <style>
        .property-card {
            transition: all 0.3s ease-in-out;
        }
        .property-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        .property-card-img {
            height: 220px;
            object-fit: cover;
        }
        .property-card-tags {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 1;
        }
        .property-card-tags .badge {
            font-size: 0.8rem;
            padding: 0.4em 0.7em;
        }
    </style>
    @endpush
@endonce