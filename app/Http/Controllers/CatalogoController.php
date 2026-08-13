<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;

class CatalogoController extends Controller
{
    public function index(Request $request)
    {
        $categorias = Categoria::query()
            ->withCount('productos')
            ->orderByRaw('productos_count desc')
            ->orderBy('titulo')
            ->get();

        $totalCategorias = $categorias->count();

        return view('catalogo.index', compact('categorias', 'totalCategorias'));
    }

    public function category(Request $request, Categoria $categoria)
    {
        $search = trim((string) $request->query('q', ''));
        $query = $categoria->productos()->with(['categoria', 'imagenes'])->latest();

        if ($search !== '') {
            $query->where('titulo', 'like', '%'.$search.'%');
        }

        $productos = $query->paginate(24)->withQueryString();

        $categoria->loadCount('productos');

        return view('catalogo.category', compact('categoria', 'productos', 'search'));
    }

    public function show(Producto $producto)
    {
        $producto->load(['categoria', 'imagenes']);

        return view('catalogo.show', compact('producto'));
    }
}
