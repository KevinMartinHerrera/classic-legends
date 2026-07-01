<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Classic Legends')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --cl-bg: #f6f1ea;
            --cl-surface: rgba(255, 255, 255, 0.94);
            --cl-ink: #171412;
            --cl-muted: #5f5a54;
            --cl-line: rgba(23, 20, 18, 0.1);
            --cl-accent: #ef8b16;
            --cl-accent-dark: #9a4d00;
            --cl-accent-soft: rgba(239, 139, 22, 0.12);
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            color: var(--cl-ink);
            background: linear-gradient(180deg, #fcfbf8 0%, var(--cl-bg) 100%);
        }

        .site-nav {
            background: rgba(17, 17, 17, 0.9);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(239, 139, 22, 0.12);
        }

        .brand-mark {
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 0.85rem;
            background: linear-gradient(145deg, #ffbe5a 0%, var(--cl-accent) 58%, var(--cl-accent-dark) 100%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .brand-mark img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .brand-title {
            line-height: 1;
            font-size: 0.98rem;
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
            border-radius: 1.1rem;
            box-shadow: 0 8px 22px rgba(20, 20, 20, 0.05);
            backdrop-filter: blur(8px);
        }

        .catalog-card {
            overflow: hidden;
            border-radius: 1rem;
            transition: transform 0.18s ease, border-color 0.18s ease;
            border: 1px solid rgba(23, 20, 18, 0.08);
        }

        .catalog-card:hover {
            transform: translateY(-2px);
            border-color: rgba(239, 139, 22, 0.2);
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
