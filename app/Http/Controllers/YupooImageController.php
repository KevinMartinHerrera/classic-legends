<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class YupooImageController extends Controller
{
    public function show(Request $request)
    {
        $url = (string) $request->query('u', '');

        if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
            if (str_starts_with($url, '/storage/') || str_starts_with($url, 'storage/')) {
                $path = str_starts_with($url, '/storage/') ? $url : '/'.ltrim($url, '/');

                return response()->file(public_path(ltrim($path, '/')));
            }

            abort(404);
        }

        $parts = parse_url($url);
        if (! isset($parts['scheme'], $parts['host']) || $parts['scheme'] !== 'https' || $parts['host'] !== 'photo.yupoo.com') {
            abort(403);
        }

        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0 Safari/537.36',
            'Accept' => 'image/avif,image/webp,image/apng,image/*,*/*;q=0.8',
            'Referer' => 'https://classic-football-fhirts052.x.yupoo.com/albums/?page=1',
            'Accept-Language' => 'es-ES,es;q=0.9,en;q=0.8',
        ])
            ->timeout(30)
            ->retry(2, 300)
            ->get($url);

        if (! $response->successful()) {
            abort(404);
        }

        $contentType = $response->header('content-type', 'image/jpeg');
        if (! str_starts_with($contentType, 'image/')) {
            abort(404);
        }

        return response($response->body(), 200, [
            'Content-Type' => $contentType,
            'Cache-Control' => 'public, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
