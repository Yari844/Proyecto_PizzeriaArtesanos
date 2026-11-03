<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenDetalle extends Model
{
    use HasFactory;

    protected $table = 'orden_detalles';

    protected $fillable = [
        'orden_id',
        'producto_id',
        'tamano_id',
        'producto_nombre',
        'tamano_nombre',
        'orilla_queso',
        'precio_unitario',
        'precio_orilla',
        'cantidad',
        'importe',
        'notas',
    ];

    protected function casts(): array
    {
        return [
            'orilla_queso' => 'boolean',
            'precio_unitario' => 'decimal:2',
            'precio_orilla' => 'decimal:2',
            'importe' => 'decimal:2',
        ];
    }

    public function orden(): BelongsTo
    {
        return $this->belongsTo(Orden::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function tamano(): BelongsTo
    {
        return $this->belongsTo(Tamano::class);
    }
}