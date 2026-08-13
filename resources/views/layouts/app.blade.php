<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Classic Legends')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --cl-bg: #ece9e5;
            --cl-surface: rgba(255, 255, 255, 0.92);
            --cl-surface-strong: rgba(255, 255, 255, 0.98);
            --cl-ink: #151311;
            --cl-muted: #6d6a66;
            --cl-line: rgba(21, 19, 17, 0.07);
            --cl-accent: #c58a43;
            --cl-accent-dark: #7f5222;
            --cl-accent-soft: rgba(197, 138, 67, 0.12);
            --cl-accent-soft-2: rgba(197, 138, 67, 0.06);
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            color: var(--cl-ink);
            background:
                radial-gradient(circle at top left, rgba(197, 138, 67, 0.1), transparent 28%),
                radial-gradient(circle at top right, rgba(21, 19, 17, 0.05), transparent 24%),
                linear-gradient(180deg, #f8f7f5 0%, var(--cl-bg) 100%);
        }

        .site-nav {
            background: rgba(27, 27, 27, 0.92);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .brand-mark {
            width: 3.15rem;
            height: 3.15rem;
            border-radius: 1rem;
            background: linear-gradient(145deg, rgba(255,255,255,.98) 0%, rgba(238,238,238,.88) 100%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 12px 22px rgba(0, 0, 0, 0.22);
            border: 1px solid rgba(255,255,255,.08);
        }

        .brand-mark img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .brand-title {
            line-height: 1;
            font-size: 1.08rem;
            letter-spacing: 0.01em;
        }

        .brand-subtitle {
            font-size: 0.69rem;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.48);
        }

        .soft-card {
            background: var(--cl-surface);
            border: 1px solid var(--cl-line);
            border-radius: 1.4rem;
            box-shadow: 0 14px 34px rgba(20, 20, 20, 0.05);
            backdrop-filter: blur(10px);
        }

        .catalog-card {
            overflow: hidden;
            border-radius: 1.35rem;
            transition: transform 0.22s ease, border-color 0.22s ease, box-shadow 0.22s ease;
            border: 1px solid rgba(23, 20, 18, 0.08);
            box-shadow: 0 10px 26px rgba(20, 20, 20, 0.05);
        }

        .catalog-card:hover {
            transform: translateY(-4px);
            border-color: rgba(240, 139, 24, 0.22);
            box-shadow: 0 18px 32px rgba(20, 20, 20, 0.08);
        }

        .hero-shell {
            border-radius: 2rem;
            overflow: hidden;
        }

        .hero-copy {
            min-height: 100%;
            background:
                radial-gradient(circle at top left, rgba(219, 138, 44, 0.16), transparent 40%),
                linear-gradient(135deg, rgba(255,255,255,.98), rgba(255,255,255,.9));
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--cl-accent-dark);
        }

        .section-title {
            font-size: clamp(1.2rem, 2vw, 1.6rem);
            font-weight: 800;
        }

        .hero-stats .stat-pill {
            display: inline-flex;
            flex-direction: column;
            gap: 0.1rem;
            padding: 0.8rem 1rem;
            border-radius: 1rem;
            border: 1px solid rgba(23, 20, 18, 0.08);
            background: rgba(255,255,255,.82);
            min-width: 118px;
            box-shadow: 0 8px 18px rgba(20, 20, 20, 0.04);
        }

        .stat-pill--wide {
            min-width: 145px;
        }

        .stat-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--cl-muted);
        }

        .stat-value {
            font-size: 1.2rem;
            font-weight: 800;
        }

        .hero-slide {
            aspect-ratio: 16 / 10;
            background: #f2f0ec;
        }

        .hero-panel {
            border-radius: 1.8rem;
            background:
                radial-gradient(circle at top right, rgba(240, 139, 24, 0.22), transparent 32%),
                linear-gradient(160deg, rgba(18, 18, 18, 0.98), rgba(49, 35, 20, 0.96));
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 24px 55px rgba(20, 20, 20, 0.18);
        }

        .hero-panel-inner {
            border-radius: 1.35rem;
            padding: 1rem;
            background: linear-gradient(180deg, rgba(255,255,255,.08), rgba(255,255,255,.03));
        }

        .hero-panel--light {
            border-radius: 2rem;
            background: linear-gradient(145deg, rgba(255,255,255,.98), rgba(255,255,255,.92));
            border: 1px solid rgba(23,20,18,.08);
            box-shadow: 0 20px 42px rgba(20,20,20,.05);
        }

        .hero-focus {
            position: relative;
            display: block;
            height: 100%;
            border-radius: 2rem;
            overflow: hidden;
            box-shadow: 0 22px 50px rgba(20, 20, 20, 0.08);
            background: #fff;
        }

        .hero-focus-media {
            position: relative;
            height: 100%;
            min-height: 420px;
        }

        .hero-focus-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero-focus-fallback {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--cl-ink);
            background: linear-gradient(135deg, rgba(255,255,255,.96), rgba(219,138,44,.08));
            font-weight: 700;
        }

        .hero-focus-media::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(255,255,255,0.04) 24%, rgba(0,0,0,.6) 100%);
        }

        .hero-focus-copy {
            position: absolute;
            left: 1.4rem;
            right: 1.4rem;
            bottom: 1.4rem;
            z-index: 2;
            color: #fff;
        }

        .feature-step {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 999px;
            background: var(--cl-accent-soft);
            color: var(--cl-accent-dark);
            font-weight: 800;
        }

        .tile-media--tall {
            aspect-ratio: 3 / 4;
        }

        .section-head {
            position: relative;
        }

        .section-head::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: -0.75rem;
            height: 1px;
            background: linear-gradient(90deg, rgba(21, 19, 17, 0.16), transparent);
        }

        .section-soft {
            position: relative;
            overflow: hidden;
        }

        .home-header {
            border-radius: 1.8rem;
            background: linear-gradient(135deg, rgba(255,255,255,.94), rgba(247,246,244,.9));
            border: 1px solid rgba(23,20,18,.08);
            box-shadow: 0 16px 34px rgba(20,20,20,.05);
        }

        .home-header--compact {
            border-radius: 1.2rem;
            box-shadow: 0 10px 22px rgba(20,20,20,.04);
        }

        .home-helpline {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.5rem 0.65rem;
            line-height: 1.4;
            padding: 0 0.25rem;
        }

        .home-helpline a {
            color: var(--cl-accent-dark);
            text-decoration: none;
            font-weight: 700;
        }

        .home-helpline a:hover {
            text-decoration: underline;
        }

        .section-soft::before {
            content: '';
            position: absolute;
            inset: auto -15% -30% auto;
            width: 18rem;
            height: 18rem;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(240, 139, 24, 0.12), transparent 68%);
            pointer-events: none;
        }

        .catalog-tile {
            display: block;
            border-radius: 1.6rem;
            overflow: hidden;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(23,20,18,.08);
            box-shadow: 0 12px 28px rgba(20,20,20,.05);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .catalog-tile:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 36px rgba(20,20,20,.08);
        }

        .catalog-tile-media--minimal {
            aspect-ratio: 4 / 4.75;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, rgba(125,125,125,.08), rgba(255,255,255,.96));
        }

        .catalog-tile-media--minimal img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .catalog-tile-copy--minimal {
            padding: 1.15rem 1.1rem 1.15rem;
            background: rgba(255,255,255,.98);
        }

        .product-card-float {
            position: relative;
            overflow: hidden;
            border-radius: 1.65rem;
            background: #fff;
            border: 1px solid rgba(23,20,18,.08);
            box-shadow: 0 14px 30px rgba(20,20,20,.06);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .product-card-float--tall {
            min-height: 100%;
        }

        .product-card-float:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(20,20,20,.1);
        }

        .product-card-media {
            position: relative;
            aspect-ratio: 4 / 5.2;
            overflow: hidden;
            background: #f4f1ec;
        }

        .product-card-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .product-card-body {
            padding: 1.05rem 1rem 1.15rem;
        }

        .detail-showcase {
            padding-top: 0.25rem;
        }

        .detail-stage {
            border-radius: 2rem;
        }

        .detail-zoom-wrap {
            overflow: hidden;
            border-radius: 1.55rem;
            background: #f4f1ec;
        }

        .detail-image-shell {
            aspect-ratio: 4 / 5.2;
            overflow: hidden;
            background: #f4f1ec;
        }

        .detail-image-zoom {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .25s ease;
            transform-origin: center center;
        }

        .detail-image-zoom:hover {
            transform: scale(1.08);
        }

        .detail-thumb {
            display: block;
            border: 1px solid rgba(23, 20, 18, 0.1);
            border-radius: 0.95rem;
            overflow: hidden;
            background: #fff;
            transition: transform 0.18s ease, border-color 0.18s ease;
        }

        .detail-thumb:hover {
            transform: translateY(-2px);
            border-color: rgba(197, 138, 67, 0.25);
        }

        .detail-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            aspect-ratio: 1 / 1;
        }

        .detail-panel--clean {
            border-radius: 2rem;
            background: rgba(255,255,255,.96);
            border: 1px solid rgba(23,20,18,.08);
            box-shadow: 0 18px 38px rgba(20,20,20,.06);
        }

        .detail-note {
            border-radius: 1.35rem;
            padding: 1rem;
            background: rgba(255,255,255,.8);
            border: 1px solid rgba(23,20,18,.08);
        }

        .detail-actions {
            position: sticky;
            top: 1rem;
        }

        .detail-actions .btn-dark {
            background: linear-gradient(180deg, #2d3136 0%, #22262a 100%);
        }

        .search-box {
            border-radius: 999px;
            border: 1px solid rgba(23, 20, 18, 0.08);
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 12px 24px rgba(20, 20, 20, 0.05);
        }

        .search-box .form-control {
            border-radius: 999px;
            padding-left: 1rem;
        }

        .hero-slide img {
            object-fit: cover;
            width: 100%;
            height: 100%;
        }

        .hero-slide .overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, transparent 30%, rgba(0,0,0,.68) 100%);
        }

        .slide-copy {
            position: absolute;
            left: 1.1rem;
            right: 1.1rem;
            bottom: 1.1rem;
            z-index: 2;
        }

        .bg-soft {
            background: linear-gradient(135deg, rgba(239,139,22,.09), rgba(255,255,255,.95));
        }

        .category-tile,
        .feature-card {
            display: block;
            color: inherit;
        }

        .tile-media,
        .feature-media {
            position: relative;
            overflow: hidden;
            border-radius: 1.15rem;
            background: #f2f0ec;
            aspect-ratio: 4 / 3;
        }

        .tile-media img,
        .feature-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.22s ease;
        }

        .category-tile:hover .tile-media img,
        .feature-card:hover .feature-media img {
            transform: scale(1.03);
        }

        .tile-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, transparent 35%, rgba(0,0,0,.26) 100%);
        }

        .tile-fallback {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            padding: 1rem;
            text-align: center;
            font-weight: 700;
            color: var(--cl-ink);
            background: linear-gradient(135deg, rgba(255,255,255,.95), rgba(239,139,22,.1));
        }

        .tile-body,
        .feature-body {
            padding: 0.95rem 0.15rem 0.2rem;
        }

        .tile-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2rem;
            height: 2rem;
            padding: 0 0.55rem;
            border-radius: 999px;
            background: var(--cl-accent-soft);
            color: var(--cl-accent-dark);
            font-size: 0.82rem;
            font-weight: 800;
        }

        .feature-card {
            border-radius: 1.2rem;
            overflow: hidden;
            background: rgba(255,255,255,.85);
            border: 1px solid rgba(23,20,18,.08);
            box-shadow: 0 8px 22px rgba(20,20,20,.05);
        }

        .feature-body {
            padding: 1rem;
        }

        @media (max-width: 991.98px) {
            .hero-copy {
                padding-bottom: 1.5rem;
            }
        }

        .catalog-card img {
            object-fit: cover;
        }

        .btn-dark {
            background: linear-gradient(180deg, #2c3136 0%, #1f2327 100%);
            border: 1px solid rgba(0, 0, 0, 0.08);
            box-shadow: 0 8px 18px rgba(17, 17, 17, 0.12);
            transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
        }

        .btn-dark:hover,
        .btn-dark:focus {
            background: linear-gradient(180deg, #343a40 0%, #23282d 100%);
            box-shadow: 0 10px 22px rgba(17, 17, 17, 0.16);
            transform: translateY(-1px);
        }

        .btn-dark:active {
            transform: translateY(0);
            box-shadow: 0 6px 14px rgba(17, 17, 17, 0.12);
        }

        .letter-spacing-1 {
            letter-spacing: 0.14em;
        }

        .page-shell {
            position: relative;
            z-index: 1;
        }

        .page-footer {
            color: var(--cl-muted);
            border-top: 1px solid rgba(23, 20, 18, 0.08);
            margin-top: 2.5rem;
            padding: 1.5rem 0 0.75rem;
        }

        .footer-copy {
            color: var(--cl-muted);
            line-height: 1.5;
        }

        .footer-copy strong {
            color: var(--cl-ink);
        }

        .footer-link {
            display: inline-flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.72rem 1rem;
            border-radius: 999px;
            border: 1px solid rgba(23, 20, 18, 0.1);
            background: rgba(255, 255, 255, 0.72);
            color: var(--cl-ink);
            text-decoration: none;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }

        .footer-link:hover {
            transform: translateY(-1px);
            border-color: rgba(239, 139, 22, 0.22);
            box-shadow: 0 10px 24px rgba(20, 20, 20, 0.08);
            color: var(--cl-ink);
        }

        .footer-link svg {
            width: 1rem;
            height: 1rem;
            flex: 0 0 auto;
        }

        .pagination-clean .page-link {
            border: 1px solid rgba(23, 20, 18, 0.1);
            border-radius: 999px;
            color: var(--cl-ink);
            background: rgba(255, 255, 255, 0.9);
            padding: 0.65rem 1rem;
            box-shadow: 0 4px 10px rgba(20, 20, 20, 0.04);
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }

        .pagination-clean .page-link:hover {
            border-color: rgba(239, 139, 22, 0.28);
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(20, 20, 20, 0.08);
        }

        .pagination-clean .page-item.active .page-link {
            background: linear-gradient(180deg, #2c3136 0%, #1f2327 100%);
            border-color: transparent;
            color: #fff;
            box-shadow: 0 10px 20px rgba(17, 17, 17, 0.12);
        }

        .pagination-clean .page-item.disabled .page-link {
            background: rgba(255, 255, 255, 0.55);
            color: rgba(23, 20, 18, 0.38);
        }

    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark sticky-top site-nav">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-3" href="{{ url('/') }}">
            <span class="brand-mark">
                <img src="{{ asset('logo.jpg') }}" alt="Classic Legends logo">
            </span>
            <span class="d-flex flex-column">
                <span class="brand-title fw-bold">Classic Legends</span>
            </span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
        </div>
    </div>
</nav>

<main class="py-4 py-lg-5 page-shell">
    <div class="container">
        @yield('content')

        <footer class="page-footer d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 small">
            <div>
                <div class="fw-semibold text-dark mb-1">Classic Legends</div>
                <div class="footer-copy">&copy; {{ date('Y') }} Classic Legends. Todos los derechos reservados.</div>
            </div>
            <a class="footer-link" href="https://www.instagram.com/classiclegendsfotball?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" target="_blank" rel="noopener noreferrer">
                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <rect x="3" y="3" width="18" height="18" rx="5" stroke="currentColor" stroke-width="1.7" />
                    <circle cx="12" cy="12" r="4.2" stroke="currentColor" stroke-width="1.7" />
                    <circle cx="17.3" cy="6.7" r="1.1" fill="currentColor" />
                </svg>
                <span>@classiclegendsfotball</span>
            </a>
        </footer>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
