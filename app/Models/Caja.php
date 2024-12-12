<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $table = 'Caja';
    
    protected $fillable = [
        'Fecha',
        'Saldo_Inicial',
        'Saldo_Final',
        'Vendedor_ID'
    ];

    // Convertir automáticamente estos campos a Carbon
    protected $dates = [
        'Fecha'
    ];

    // Convertir estos campos a decimal cuando se recuperen de la base de datos
    protected $casts = [
        'Saldo_Inicial' => 'decimal:2',
        'Saldo_Final' => 'decimal:2'
    ];

    // Relación con el vendedor
    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class, 'Vendedor_ID', 'ID');
    }
}