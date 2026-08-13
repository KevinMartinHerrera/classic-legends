<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    private const SESSION_KEY = 'cart_products';
    private const ITEMS_KEY = 'cart_items';

    public function index(Request $request): View
    {
        $lineKeys = array_values(array_unique($request->session()->get(self::SESSION_KEY, [])));
        $items = $request->session()->get(self::ITEMS_KEY, []);
        $products = Producto::query()->with(['categoria', 'imagenes'])
            ->whereIn('slug', collect($items)->pluck('slug')->filter()->unique()->all())
            ->get()
            ->keyBy('slug');

        $cartLines = collect($lineKeys)
            ->map(function (string $lineKey) use ($items, $products) {
                $item = $items[$lineKey] ?? null;

                if (! $item) {
                    return null;
                }

                $product = $products[$item['slug']] ?? null;

                if (! $product) {
                    return null;
                }

                return [
                    'key' => $lineKey,
                    'product' => $product,
                    'talla' => $item['talla'],
                    'cantidad' => (int) ($item['cantidad'] ?? 1),
                ];
            })
            ->filter()
            ->values();

        $whatsappUrl = $this->buildWhatsAppUrl($cartLines);

        return view('cart.index', [
            'cartLines' => $cartLines,
            'whatsappUrl' => $whatsappUrl,
            'items' => $items,
        ]);
    }

    public function add(Request $request, Producto $producto): RedirectResponse
    {
        $validated = $request->validate([
            'talla' => ['required', 'string', 'max:10'],
            'cantidad' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        $cart = $request->session()->get(self::SESSION_KEY, []);
        $items = $request->session()->get(self::ITEMS_KEY, []);
        $lineKey = $this->lineKey($producto, $validated['talla']);
        $currentQuantity = (int) ($items[$lineKey]['cantidad'] ?? 0);
        $newQuantity = min(99, $currentQuantity + max(1, (int) ($validated['cantidad'] ?? 1)));

        if (! in_array($lineKey, $cart, true)) {
            $cart[] = $lineKey;
        }

        $items[$lineKey] = [
            'slug' => $producto->slug,
            'talla' => $validated['talla'],
            'cantidad' => $newQuantity,
        ];

        $request->session()->put(self::SESSION_KEY, $cart);
        $request->session()->put(self::ITEMS_KEY, $items);

        return back()->with('status', 'Producto agregado al carrito.');
    }

    public function update(Request $request, Producto $producto): RedirectResponse
    {
        $validated = $request->validate([
            'talla' => ['required', 'string', 'max:10'],
            'action' => ['required', 'in:increase,decrease'],
        ]);

        $items = $request->session()->get(self::ITEMS_KEY, []);
        $cart = $request->session()->get(self::SESSION_KEY, []);
        $lineKey = $this->lineKey($producto, $validated['talla']);
        $currentQuantity = (int) ($items[$lineKey]['cantidad'] ?? 1);
        $newQuantity = $validated['action'] === 'decrease' ? max(1, $currentQuantity - 1) : min(99, $currentQuantity + 1);

        if (! in_array($lineKey, $cart, true)) {
            $cart[] = $lineKey;
        }

        $items[$lineKey] = [
            'slug' => $producto->slug,
            'talla' => $validated['talla'],
            'cantidad' => $newQuantity,
        ];

        $request->session()->put(self::SESSION_KEY, $cart);
        $request->session()->put(self::ITEMS_KEY, $items);

        return back()->with('status', 'Carrito actualizado.');
    }

    public function remove(Request $request, Producto $producto): RedirectResponse
    {
        $validated = $request->validate([
            'talla' => ['required', 'string', 'max:10'],
        ]);

        $lineKey = $this->lineKey($producto, $validated['talla']);
        $cart = array_values(array_filter(
            $request->session()->get(self::SESSION_KEY, []),
            fn ($slug) => $slug !== $lineKey
        ));

        $items = $request->session()->get(self::ITEMS_KEY, []);
        unset($items[$lineKey]);

        $request->session()->put(self::SESSION_KEY, $cart);
        $request->session()->put(self::ITEMS_KEY, $items);

        return back()->with('status', 'Producto eliminado del carrito.');
    }

    public function clear(Request $request): RedirectResponse
    {
        $request->session()->forget([self::SESSION_KEY, self::ITEMS_KEY]);

        return back()->with('status', 'Carrito vaciado.');
    }

    private function buildWhatsAppUrl(iterable $cartLines): string
    {
        $lines = [
            'Hola, quiero pedir estos artículos:',
            '',
        ];

        foreach ($cartLines as $line) {
            $product = $line['product'];
            $lines[] = '- '.$product->titulo.' / Talla '.$line['talla'];
            $lines[] = '  Cantidad: '.$line['cantidad'];
            $lines[] = '  '.route('producto.show', $product);
        }

        $lines[] = '';
        $lines[] = 'Gracias.';

        return 'https://wa.me/529671348034?text='.rawurlencode(implode("\n", $lines));
    }

    private function lineKey(Producto $producto, string $talla): string
    {
        return $producto->slug.'|'.strtolower(trim($talla));
    }
}
