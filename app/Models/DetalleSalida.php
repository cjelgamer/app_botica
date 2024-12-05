<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleSalida extends Model
{
    use HasFactory;

    protected $table = 'detalle_salida';

    protected $fillable = [
        'salida_id',
        'medicamento_id',
        'cantidad',
    ];

    // Relación con la salida
    public function salida()
    {
        return $this->belongsTo(Salida::class);
    }

    // Relación con el medicamento
    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class);
    }
}
