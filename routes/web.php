<?php

use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\YupooImageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogoController::class, 'index'])->name('catalogo.index');
Route::get('/categoria/{categoria:slug}', [CatalogoController::class, 'category'])->name('categoria.show');
Route::get('/producto/{producto:slug}', [CatalogoController::class, 'show'])->name('producto.show');
Route::get('/carrito', [CartController::class, 'index'])->name('cart.index');
Route::post('/carrito/{producto:slug}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/carrito/{producto:slug}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/carrito/{producto:slug}', [CartController::class, 'remove'])->name('cart.remove');
Route::delete('/carrito', [CartController::class, 'clear'])->name('cart.clear');
Route::get('/yupoo/image', [YupooImageController::class, 'show'])->name('yupoo.image');
