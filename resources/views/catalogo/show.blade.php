@extends('layouts.app')

@section('title', $producto->titulo.' | Classic Legends')

@section('content')
    @php
        $mainImageUrl = $producto->portada_url
            ? route('yupoo.image', ['u' => $producto->portada_url])
            : ($producto->imagenes->first() ? route('yupoo.image', ['u' => $producto->imagenes->first()->url]) : 'https://via.placeholder.com/1200x1500?text=Sin+imagen');
        $galleryImages = $producto->imagenes->take(10);
        $galleryUrls = collect([$mainImageUrl])
            ->merge($galleryImages->map(fn ($imagen) => route('yupoo.image', ['u' => $imagen->url])))
            ->unique()
            ->values();
        $returnUrl = request()->query('from') ?: ($producto->categoria ? route('categoria.show', $producto->categoria) : route('catalogo.index'));
        $productUrl = route('producto.show', $producto);
        $whatsappMessage = rawurlencode("Hola, me interesa este articulo:\n{$producto->titulo}\nVer producto: {$productUrl}");
        $whatsappUrl = 'https://wa.me/529671348034?text='.$whatsappMessage;
    @endphp

    <section class="detail-showcase mb-4 mb-lg-5">
        <div class="row g-4 g-lg-5 align-items-start">
            <div class="col-12 col-lg-7">
                <div class="detail-stage soft-card p-3 p-lg-4">
                    <div class="detail-zoom-wrap">
                        <div id="productGalleryCarousel" class="carousel slide h-100" data-bs-touch="true">
                            <div class="carousel-indicators mb-0">
                                @foreach ($galleryUrls as $index => $url)
                                    <button type="button" data-bs-target="#productGalleryCarousel" data-bs-slide-to="{{ $index }}" @class(['active' => $index === 0]) aria-label="Imagen {{ $index + 1 }}"></button>
                                @endforeach
                            </div>

                            <div class="carousel-inner h-100">
                                @foreach ($galleryUrls as $index => $url)
                                    <div @class(['carousel-item', 'active' => $index === 0])>
                                        <div class="detail-image-shell">
                                            <img src="{{ $url }}" class="detail-image-zoom" alt="{{ $producto->titulo }}" loading="{{ $index === 0 ? 'eager' : 'lazy' }}">
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

                    <div class="row g-2 mt-3 row-cols-3 row-cols-md-5">
                        @foreach ($galleryUrls as $index => $url)
                            <div class="col">
                                <button type="button" class="detail-thumb w-100 p-0 bg-transparent border-0" data-bs-target="#productGalleryCarousel" data-bs-slide-to="{{ $index }}" aria-label="Ver imagen {{ $index + 1 }}">
                                    <img src="{{ $url }}" alt="{{ $producto->titulo }}" loading="lazy">
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-5">
                <div class="detail-panel detail-panel--clean p-4 p-lg-4">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-3 small text-secondary">
                        <a href="{{ $returnUrl }}" class="text-decoration-none text-secondary">← Volver</a>
                        <span>{{ $producto->categoria?->titulo ?? 'Sin categoría' }}</span>
                    </div>

                    <div class="mb-3">
                        <div class="eyebrow mb-2">Catálogo / Detalle</div>
                        <h1 class="h2 fw-bold mb-3">{{ $producto->titulo }}</h1>
                        <p class="text-secondary mb-0">{{ $producto->descripcion ?? 'Ficha de producto con fotos y referencia.' }}</p>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="meta-pill">{{ $producto->categoria?->titulo ?? 'Sin categoría' }}</span>
                        <span class="meta-pill">{{ $galleryUrls->count() }} fotos</span>
                    </div>

                    <div class="detail-note mb-4">
                        <div class="small text-uppercase text-secondary letter-spacing-1 mb-1">Descripción</div>
                        <div class="fw-semibold">{{ $producto->nombre_original ?? $producto->titulo }}</div>
                    </div>

                    <div class="detail-actions d-flex flex-column gap-2">
                        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-dark rounded-pill px-4 py-3 fw-semibold">Me interesa este articulo</a>
                        <a href="{{ $returnUrl }}" class="btn btn-outline-dark rounded-pill px-4 py-3 fw-semibold">Volver al catálogo</a>
                    </div>

                    <div class="small text-secondary mt-3">También puedes explorar más productos de la misma colección.</div>
                </div>
            </div>
        </div>
    </section>
@endsection
