<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{
    protected $table = 'Salida';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    
    protected $fillable = [
        'Vendedor_ID',
        'Cliente_ID',
        'Monto_Total',
        'Tipo_de_Pago',
        'Fecha_Venta'
    ];

    // Relaciones
    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class, 'Vendedor_ID', 'ID');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'Cliente_ID', 'ID');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleSalida::class, 'Salida_ID', 'ID');
    }
}