<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $table = 'productos';

    protected $fillable = [
        'categoria_id',
        'yupoo_album_id',
        'titulo',
        'slug',
        'portada_url',
        'yupoo_url',
        'descripcion',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function imagenes(): HasMany
    {
        return $this->hasMany(ProductoImagen::class, 'producto_id')->orderBy('orden');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
