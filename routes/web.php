<?php

use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\YupooImageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogoController::class, 'index'])->name('catalogo.index');
Route::get('/categoria/{categoria:slug}', [CatalogoController::class, 'category'])->name('categoria.show');
Route::get('/producto/{producto:slug}', [CatalogoController::class, 'show'])->name('producto.show');
Route::get('/yupoo/image', [YupooImageController::class, 'show'])->name('yupoo.image');
