<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function index(Request $request)
    {
        $query = Producto::query()->with(['categoria', 'imagenes']);

        if ($search = trim((string) $request->query('q', ''))) {
            $query->where('titulo', 'like', '%'.$search.'%');
        }

        $productos = $query->latest()->paginate(28)->withQueryString();
        $categorias = Categoria::query()->orderBy('titulo')->get();
        $totalProductos = Producto::query()->count();
        $totalCategorias = $categorias->count();
        $featuredProductos = Producto::query()->with(['categoria', 'imagenes'])->latest()->take(3)->get();

        return view('catalogo.index', compact('productos', 'categorias', 'search', 'totalProductos', 'totalCategorias', 'featuredProductos'));
    }

    public function show(Producto $producto)
    {
        $producto->load(['categoria', 'imagenes']);

        return view('catalogo.show', compact('producto'));
    }
}
