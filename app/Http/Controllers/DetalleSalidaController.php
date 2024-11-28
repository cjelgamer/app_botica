<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleSalida extends Model
{
    use HasFactory;

    // Especificamos la tabla si no sigue la convención plural
    protected $table = 'detalle_salida';

    // Campos asignables en masa
    protected $fillable = [
        'salida_id',
        'medicamento_id',
        'cantidad',
        'precio_unitario',
        'precio_total',
    ];

    // Relación con la venta (Salida)
    public function salida()
    {
        return $this->belongsTo(Salida::class, 'salida_id'); // Relación con la venta
    }

    // Relación con el medicamento (Producto)
    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class, 'medicamento_id'); // Relación con el medicamento
    }
}
