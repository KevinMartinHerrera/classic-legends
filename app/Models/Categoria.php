<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Categoria extends Model
{
    protected $table = 'categorias';

    protected $fillable = [
        'parent_id',
        'yupoo_category_id',
        'titulo',
        'slug',
        'yupoo_url',
    ];

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'categoria_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }
}
