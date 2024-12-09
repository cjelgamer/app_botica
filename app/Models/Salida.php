<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{
    use HasFactory;
    protected $table = 'salida';
    protected $primaryKey = 'ID'; // Clave primaria personalizada
    // Especificar la tabla si no sigue la convención plural
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    // Campos asignables en masa
    protected $fillable = [
        'cliente_id',     // ID del cliente asociado
        'vendedor_id',    // ID del vendedor
        'monto_total',    // Total de la venta
        'Tipo_de_Pago',      // Tipo de pago
        'fecha_venta',    // Fecha de la venta
    ];

    // Relación con los detalles de la salida (DetalleSalida)
    public function detalles()
    {
        return $this->hasMany(DetalleSalida::class, 'salida_id');
    }

    // Relación con el cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    // Relación con el vendedor
    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id'); // Suponiendo que usas la tabla 'users' para vendedores
    }
}

