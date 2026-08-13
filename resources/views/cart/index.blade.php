@extends('layouts.app')

@section('title', 'Carrito | Classic Legends')

@section('content')
    <div class="d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-3 mb-4">
        <div>
            <div class="eyebrow mb-2">Carrito</div>
            <h1 class="h2 fw-bold mb-1">Artículos guardados</h1>
            <p class="text-secondary mb-0">Ajusta cantidad y talla, luego envíalo por WhatsApp.</p>
        </div>
        <a href="{{ route('catalogo.index') }}" class="btn btn-outline-dark rounded-pill px-4">Seguir viendo</a>
    </div>

    @if ($cartLines->isEmpty())
        <div class="soft-card p-4 p-lg-5 text-center">
            <h2 class="h5 fw-bold mb-2">Tu carrito está vacío</h2>
            <p class="text-secondary mb-4">Agrega productos desde cualquier ficha.</p>
            <a href="{{ route('catalogo.index') }}" class="btn btn-dark rounded-pill px-4 py-3 fw-semibold">Ir al catálogo</a>
        </div>
    @else
        <div class="row g-4">
            @foreach ($cartLines as $line)
                @php $product = $line['product']; @endphp
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="product-card-float h-100">
                        <div class="product-card-media">
                            <img src="{{ $product->portada_url ? route('yupoo.image', ['u' => $product->portada_url]) : ($product->imagenes->first() ? route('yupoo.image', ['u' => $product->imagenes->first()->url]) : 'https://via.placeholder.com/800x1000?text=Sin+imagen') }}" alt="{{ $product->titulo }}">
                        </div>
                        <div class="product-card-body d-flex flex-column gap-3">
                            <div>
                                <h2 class="h6 fw-bold mb-1">{{ $product->nombre_es ?: $product->titulo }}</h2>
                                <p class="small text-secondary mb-0">{{ $product->categoria?->nombre_es ?: $product->categoria?->titulo ?? 'Sin categoría' }}</p>
                            </div>
                            <span class="meta-pill">Talla {{ $line['talla'] }}</span>
                            @php $cantidad = (int) ($line['cantidad'] ?? 1); @endphp
                            <div class="d-flex align-items-center justify-content-between gap-2 p-2 rounded-pill border bg-white">
                                <form action="{{ route('cart.update', $product) }}" method="POST" class="m-0">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="talla" value="{{ $line['talla'] }}">
                                    <input type="hidden" name="action" value="decrease">
                                    <button type="submit" class="btn btn-link text-decoration-none px-2" aria-label="Disminuir cantidad">−</button>
                                </form>
                                <span class="fw-semibold">{{ $cantidad }}</span>
                                <form action="{{ route('cart.update', $product) }}" method="POST" class="m-0">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="talla" value="{{ $line['talla'] }}">
                                    <input type="hidden" name="action" value="increase">
                                    <button type="submit" class="btn btn-link text-decoration-none px-2" aria-label="Aumentar cantidad">+</button>
                                </form>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('producto.show', ['producto' => $product->slug]) }}" class="btn btn-outline-dark rounded-pill px-4">Ver</a>
                                <form action="{{ route('cart.remove', $product) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="talla" value="{{ $line['talla'] }}">
                                    <button class="btn btn-link text-secondary text-decoration-none px-0">Quitar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 soft-card p-4 p-lg-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <div class="eyebrow mb-2">Enviar pedido</div>
                <h2 class="h5 fw-bold mb-1">Manda tu carrito por WhatsApp</h2>
                <p class="text-secondary mb-0">Te respondemos directo con el detalle de los artículos.</p>
            </div>
            <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-dark rounded-pill px-4 py-3 fw-semibold">Enviar carrito</a>
        </div>
    @endif
@endsection
