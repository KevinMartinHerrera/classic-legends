@extends('layouts.app')

@section('title', $producto->titulo.' | Classic Legends')

@section('content')
    @php
        $mainImageUrl = $producto->portada_url
            ? route('yupoo.image', ['u' => $producto->portada_url])
            : ($producto->imagenes->first() ? route('yupoo.image', ['u' => $producto->imagenes->first()->url]) : 'https://via.placeholder.com/1200x1500?text=Sin+imagen');
        $galleryImages = $producto->imagenes->take(8);
        $galleryUrls = collect([$mainImageUrl])
            ->merge($galleryImages->map(fn ($imagen) => route('yupoo.image', ['u' => $imagen->url])))
            ->unique()
            ->values();
    @endphp

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <div class="small text-uppercase text-secondary letter-spacing-1 mb-2">Catálogo / Detalle</div>
            <h1 class="h2 fw-bold mb-1">{{ $producto->titulo }}</h1>
            <p class="text-secondary mb-0">Ficha de producto con fotos y referencia.</p>
        </div>
        <a href="{{ url('/') }}" class="btn btn-outline-dark rounded-pill px-4">Volver al catálogo</a>
    </div>

    <div class="row g-4 mb-4 align-items-start">
        <div class="col-12 col-lg-7">
            <div class="soft-card overflow-hidden h-100">
                <div id="productGalleryCarousel" class="carousel slide h-100" data-bs-touch="true">
                    <div class="carousel-indicators mb-0">
                        @foreach ($galleryUrls as $index => $url)
                            <button type="button" data-bs-target="#productGalleryCarousel" data-bs-slide-to="{{ $index }}" @class(['active' => $index === 0]) aria-label="Imagen {{ $index + 1 }}"></button>
                        @endforeach
                    </div>

                    <div class="carousel-inner h-100">
                        @foreach ($galleryUrls as $index => $url)
                            <div @class(['carousel-item', 'active' => $index === 0])>
                                <div class="position-relative bg-light" style="height: 640px; overflow: hidden;">
                                    <img src="{{ $url }}" class="w-100 h-100" style="object-fit: cover; object-position: center center; transform: scale(1.1); transform-origin: center center;" alt="{{ $producto->titulo }}" loading="{{ $index === 0 ? 'eager' : 'lazy' }}">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button class="carousel-control-prev" type="button" data-bs-target="#productGalleryCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Anterior</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#productGalleryCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Siguiente</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-5">
            <div class="soft-card h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                        <h2 class="h6 text-uppercase letter-spacing-1 mb-0">Galería</h2>
                        <span class="small text-secondary">Selecciona una imagen</span>
                    </div>
                    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-2">
                        @foreach ($galleryUrls as $index => $url)
                            <div class="col">
                                <button type="button" class="border-0 p-0 bg-transparent w-100" data-bs-target="#productGalleryCarousel" data-bs-slide-to="{{ $index }}" aria-label="Ver imagen {{ $index + 1 }}">
                                    <img src="{{ $url }}" class="img-fluid rounded-3 border w-100" loading="lazy" style="object-fit: cover; aspect-ratio: 4 / 5;" alt="{{ $producto->titulo }}">
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center">
        <a href="{{ url('/') }}" class="btn btn-outline-dark rounded-pill px-4">Volver al catálogo</a>
    </div>
@endsection
