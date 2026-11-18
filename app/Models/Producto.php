<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'tipo',
        'nombre',
        'descripcion',
        'presentacion',
        'imagen',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    /**
     * Un producto tiene muchos precios (uno por tamaño).
     */
    public function precios(): HasMany
    {
        return $this->hasMany(ProductoPrecio::class);
    }

    /**
     * Un producto aparece en muchos renglones de órdenes.
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(OrdenDetalle::class);
    }

    /**
     * Un producto se vende en varios tamaños (muchos a muchos
     * a través de la tabla producto_precios).
     */
    public function tamanos(): BelongsToMany
    {
        return $this->belongsToMany(Tamano::class, 'producto_precios')
            ->withPivot('precio')
            ->withTimestamps();
    }
}