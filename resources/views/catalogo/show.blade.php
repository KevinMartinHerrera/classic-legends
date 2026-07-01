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
        <div class="col-12 col-lg-8">
            <div class="soft-card overflow-hidden">
                <div id="productGalleryCarousel" class="carousel slide" data-bs-touch="true">
                    <div class="carousel-indicators mb-0">
                        @foreach ($galleryUrls as $index => $url)
                            <button type="button" data-bs-target="#productGalleryCarousel" data-bs-slide-to="{{ $index }}" @class(['active' => $index === 0]) aria-label="Imagen {{ $index + 1 }}"></button>
                        @endforeach
                    </div>

                    <div class="carousel-inner">
                        @foreach ($galleryUrls as $index => $url)
                            <div @class(['carousel-item', 'active' => $index === 0])>
                                <div class="position-relative bg-light" style="aspect-ratio: 4 / 5; max-height: 640px;">
                                    <img src="{{ $url }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $producto->titulo }}" loading="{{ $index === 0 ? 'eager' : 'lazy' }}">
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

        <div class="col-12 col-lg-4">
            <div class="soft-card h-100 p-4 p-lg-5 d-flex flex-column justify-content-between">
                <div>
                    <p class="text-secondary fs-5 mb-4">Ficha de producto con fotos y referencia.</p>

                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <div class="border rounded-4 p-3 bg-white h-100">
                                <div class="small text-secondary mb-1">Referencia</div>
                                <div class="fw-semibold text-truncate">{{ $producto->yupoo_album_id }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded-4 p-3 bg-white h-100">
                                <div class="small text-secondary mb-1">Fotos</div>
                                <div class="fw-semibold">{{ $galleryImages->count() }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge rounded-pill text-bg-light text-dark px-3 py-2 border">{{ $producto->categoria?->titulo ?? 'Sin categoría' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="soft-card">
        <div class="card-body p-4">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                <h2 class="h6 text-uppercase letter-spacing-1 mb-0">Galería</h2>
                <span class="small text-secondary">Toca una miniatura para cambiar la foto</span>
            </div>
            <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-2">
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

    <div class="mt-4 d-flex justify-content-center">
        <a href="{{ url('/') }}" class="btn btn-outline-dark rounded-pill px-4">Volver al catálogo</a>
    </div>
@endsection
