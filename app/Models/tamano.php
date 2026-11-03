<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    public function precios(): HasMany
    {
        return $this->hasMany(ProductoPrecio::class);
    }
}
