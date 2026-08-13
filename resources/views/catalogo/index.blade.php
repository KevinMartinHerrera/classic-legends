@extends('layouts.app')

@section('title', 'Classic Legends')

@section('content')
    @php
        $categoryCover = function ($categoria) {
            $producto = $categoria->productos
                ->first(fn ($producto) => $producto->portada_url || $producto->imagenes->isNotEmpty())
                ?: $categoria->productos->first();

            if (! $producto) {
                return null;
            }

            if ($producto->portada_url) {
                return route('yupoo.image', ['u' => $producto->portada_url]);
            }

            return $producto->imagenes->first() ? route('yupoo.image', ['u' => $producto->imagenes->first()->url]) : null;
        };
    @endphp

    <section class="section-soft mb-5 pt-2 pt-lg-3">
        <div class="home-header home-header--compact d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 px-4 py-3 px-lg-4 py-lg-3">
            <div>
                <span class="eyebrow mb-1">Catálogos</span>
                <h1 class="h2 fw-bold mb-1">Categorías</h1>
                <p class="text-secondary mb-0 small">Abrí una categoría para ver todos sus productos.</p>
            </div>
        </div>

        @php
            $whatsappMessage = rawurlencode('Hola, no encuentro la jersey que busco. ¿Me pueden ayudar a conseguirla?');
            $whatsappUrl = 'https://wa.me/529671348034?text='.$whatsappMessage;
        @endphp
        <div class="home-helpline small text-secondary mb-4 px-1">
            <span class="fw-semibold text-dark">¿No encuentras la jersey que buscabas?</span>
            <span>Te la podemos conseguir.</span>
            <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer">Escríbenos por WhatsApp</a>
        </div>

        <div class="row g-4 g-lg-5">
            @foreach ($categorias as $categoria)
                <div class="col-12 col-sm-6 col-xl-4">
                    <a href="{{ route('categoria.show', $categoria) }}" class="catalog-tile catalog-tile--minimal text-decoration-none h-100 d-block">
                        <div class="catalog-tile-media catalog-tile-media--minimal">
                            @if ($categoryCover($categoria))
                                <img src="{{ $categoryCover($categoria) }}" alt="{{ $categoria->titulo }}">
                            @else
                                <div class="tile-fallback">{{ $categoria->titulo }}</div>
                            @endif
                        </div>
                        <div class="catalog-tile-copy catalog-tile-copy--minimal">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div>
                                    <h2 class="h5 fw-bold mb-1 text-dark">{{ $categoria->titulo }}</h2>
                                    <p class="small text-secondary mb-0">Ver colección</p>
                                </div>
                                <span class="tile-count">{{ $categoria->productos_count }}</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </section>
@endsection
