@extends('layouts.app')

@section('title', $categoria->titulo.' | Classic Legends')

@section('content')
    @php
        $coverImage = function ($producto) {
            if ($producto->portada_url) {
                return route('yupoo.image', ['u' => $producto->portada_url]);
            }

            return $producto->imagenes->first() ? route('yupoo.image', ['u' => $producto->imagenes->first()->url]) : null;
        };

        $categoryCover = $productos->first(function ($producto) {
            return $producto->portada_url || $producto->imagenes->isNotEmpty();
        });
    @endphp

    <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">
        <div>
            <div class="eyebrow mb-2">Catálogo / Categoría</div>
            <h1 class="h2 fw-bold mb-1">{{ $categoria->titulo }}</h1>
            <p class="text-secondary mb-0">{{ $categoria->productos_count }} productos en esta colección.</p>
        </div>
        <form method="GET" class="search-box d-flex gap-2 align-items-center p-2 flex-grow-1 flex-md-grow-0" style="max-width: 420px;">
            <input type="text" name="q" value="{{ $search ?? '' }}" class="form-control border-0 bg-transparent shadow-none" placeholder="Buscar por nombre...">
            <button class="btn btn-dark rounded-pill px-4 fw-semibold">Buscar</button>
        </form>
    </div>

    <div class="row g-4 g-lg-5 row-cols-1 row-cols-sm-2 row-cols-xl-3">
        @forelse ($productos as $producto)
            <div class="col">
                <a href="{{ route('producto.show', ['producto' => $producto->slug, 'from' => request()->fullUrl()]) }}" class="product-card-float product-card-float--tall text-decoration-none d-block h-100">
                    <div class="product-card-media">
                        <img src="{{ $coverImage($producto) ?? 'https://via.placeholder.com/800x1000?text=Sin+imagen' }}" alt="{{ $producto->titulo }}" loading="lazy">
                        <div class="position-absolute top-0 start-0 p-3">
                            <span class="badge rounded-pill text-bg-light text-dark px-3 py-2 border">{{ $producto->categoria?->titulo ?? $categoria->titulo }}</span>
                        </div>
                    </div>
                    <div class="product-card-body">
                        <h2 class="h6 fw-bold mb-2 text-dark">{{ $producto->titulo }}</h2>
                        <p class="small text-secondary mb-0">{{ $producto->descripcion ?? 'Abrir ficha completa' }}</p>
                    </div>
                </a>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning mb-0">No se encontraron productos en esta categoría.</div>
            </div>
        @endforelse
    </div>

    <div class="mt-4">
        {{ $productos->links() }}
    </div>
@endsection
