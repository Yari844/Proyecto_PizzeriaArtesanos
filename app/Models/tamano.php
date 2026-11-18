<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tamano extends Model
{
    use HasFactory;

    protected $table = 'tamanos';

    protected $fillable = [
        'nombre',
        'precio_orilla_queso',
        'orden',
    ];

    protected function casts(): array
    {
        return [
            'precio_orilla_queso' => 'decimal:2',
        ];
    }

    /**
     * Un tamaño tiene muchos precios (uno por producto).
     */
    public function precios(): HasMany
    {
        return $this->hasMany(ProductoPrecio::class);
    }

    /**
     * Un tamaño aplica a muchos productos (muchos a muchos
     * a través de la tabla producto_precios).
     */
    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'producto_precios')
            ->withPivot('precio')
            ->withTimestamps();
    }

    /**
     * Un tamaño aparece en muchos renglones de órdenes.
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(OrdenDetalle::class);
    }
}