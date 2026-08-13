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
        'nombre_original',
        'nombre_es',
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

    public function isKidsCategory(): bool
    {
        $category = mb_strtolower((string) ($this->categoria?->titulo ?? ''));

        return str_contains($category, 'niñ') || str_contains($category, 'kid') || str_contains($category, 'infantil');
    }

    public function sizeOptions(): array
    {
        if ($this->isKidsCategory()) {
            return ['16', '18', '20', '22', '24', '26', '28'];
        }

        return ['S', 'M', 'L', 'XL', '2XL'];
    }
}
