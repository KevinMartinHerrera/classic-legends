@extends('layouts.app')

@section('title', 'Classic Legends')

@section('content')
    @php
        $heroSlideImage = function ($producto) {
            if (! $producto) {
                return null;
            }

            if ($producto->portada_url) {
                return route('yupoo.image', ['u' => $producto->portada_url]);
            }

            return $producto->imagenes->first() ? route('yupoo.image', ['u' => $producto->imagenes->first()->url]) : null;
        };
    @endphp

    <div class="soft-card overflow-hidden mb-4 mb-lg-5 w-100">
        <div class="row g-0 align-items-stretch">
            <div class="col-12 col-lg-5 p-4 p-lg-5 d-flex flex-column justify-content-center" style="min-height: 380px;">
                <h1 class="display-5 fw-bold mb-3">Catálogo de jerseys Classic Legends.</h1>
                <p class="lead text-secondary mb-4">Explora modelos, compara fotos y entra al detalle de cada jersey en un solo lugar.</p>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="#catalogo-grid" class="btn btn-dark rounded-pill px-4">Explorar colección</a>
                </div>
            </div>
            <div class="col-12 col-lg-7">
                @if ($featuredProductos->isNotEmpty())
                    <div id="featuredCarousel" class="carousel slide h-100 p-2 p-lg-3" data-bs-ride="carousel">
                        <div class="carousel-indicators mb-0">
                            @foreach ($featuredProductos as $index => $producto)
                                <button type="button" data-bs-target="#featuredCarousel" data-bs-slide-to="{{ $index }}" @class(['active' => $index === 0]) aria-label="Slide {{ $index + 1 }}"></button>
                            @endforeach
                        </div>

                        <div class="carousel-inner rounded-4 overflow-hidden">
                            @foreach ($featuredProductos as $index => $producto)
                                @php $slideImage = $heroSlideImage($producto); @endphp
                                <div @class(['carousel-item', 'active' => $index === 0])>
                                    @if ($slideImage)
                                        <div class="position-relative" style="aspect-ratio: 16 / 10; background: #f2f0ec;">
                                            <img src="{{ $slideImage }}" alt="{{ $producto->titulo }}" class="w-100 h-100" style="object-fit: cover;">
                                            <div class="position-absolute top-0 start-0 p-3">
                                                <span class="badge rounded-pill text-bg-light text-dark px-3 py-2 border">Destacado</span>
                                            </div>
                                            <div class="position-absolute bottom-0 start-0 end-0 p-4 p-lg-5" style="background: linear-gradient(180deg, transparent 0%, rgba(0,0,0,.62) 100%);">
                                                <div class="text-white fw-semibold fs-5" style="text-shadow: 0 2px 16px rgba(0,0,0,.45);">
                                                    {{ $producto->titulo }}
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center justify-content-center" style="aspect-ratio: 16 / 10; background: #f2f0ec;">
                                            <div class="text-center text-secondary">
                                                <div class="h5 fw-semibold mb-0">{{ $producto->titulo }}</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <button class="carousel-control-prev" type="button" data-bs-target="#featuredCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#featuredCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Siguiente</span>
                        </button>
                    </div>
                @else
                    <div class="d-flex h-100 align-items-center justify-content-center p-5" style="aspect-ratio: 4 / 3; background: #f2f0ec;">
                        <div class="text-center text-secondary">
                            <div class="h5 fw-semibold mb-0">Catálogo de jerseys</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-12">
        <form id="buscador" method="GET" class="soft-card p-3 p-lg-4 mb-4">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold">Buscar por título</label>
                    <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Ej. Real Madrid, Barcelona...">
                </div>
                <div class="col-12 col-md-6 d-grid">
                    <label class="form-label d-none d-md-block invisible">Accion</label>
                    <button class="btn btn-dark rounded-pill px-4 py-2 fw-semibold">Aplicar búsqueda</button>
                </div>
            </div>
        </form>
    </div>

    <div id="catalogo-grid" class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
        @forelse ($productos as $producto)
            <div class="col">
                <div class="card h-100 catalog-card border-0 soft-card">
                    <div class="ratio ratio-4x3 bg-light position-relative">
                        <div class="img-stage h-100 w-100">
                        <img src="{{ $producto->portada_url ? route('yupoo.image', ['u' => $producto->portada_url]) : ($producto->imagenes->first() ? route('yupoo.image', ['u' => $producto->imagenes->first()->url]) : 'https://via.placeholder.com/800x600?text=Sin+imagen') }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $producto->titulo }}" loading="lazy">
                        </div>
                        <div class="position-absolute top-0 start-0 p-3">
                            <span class="badge rounded-pill text-bg-light text-dark px-3 py-2 border">{{ $producto->categoria?->titulo ?? 'Sin categoría' }}</span>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column p-4">
                        <h2 class="h6 card-title fw-bold mb-2">{{ $producto->titulo }}</h2>
                        <p class="small text-secondary mb-3">Abre la ficha para ver más fotos y detalles del modelo.</p>
                        <div class="mt-auto">
                            <a href="{{ route('producto.show', $producto) }}" class="btn btn-dark w-100 rounded-pill">Ver detalle</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning mb-0">No se encontraron productos.</div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $productos->links() }}
    </div>
@endsection
