<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{
    use HasFactory;

    // Especificamos la tabla si no sigue la convención plural
    protected $table = 'salida';

    // Campos asignables en masa
    protected $fillable = [
        'cliente_id', // La relación con el cliente
        'fecha',
        'total',
    ];

    // Relación con el modelo Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id'); // Asumiendo que 'cliente_id' es la clave foránea
    }

    // Relación con el modelo DetalleSalida (productos vendidos)
    public function detalles()
    {
        return $this->hasMany(DetalleSalida::class, 'salida_id'); // Relación con los detalles de la venta
    }
}
