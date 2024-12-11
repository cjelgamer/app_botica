<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleSalida extends Model
{
    protected $table = 'Detalle_Salida';
    protected $primaryKey = 'ID';
    public $timestamps = false;
    
    protected $fillable = [
        'Salida_ID',
        'Medicamento_ID',
        'Cantidad'
    ];

    public function salida()
    {
        return $this->belongsTo(Salida::class, 'Salida_ID', 'ID');
    }

    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class, 'Medicamento_ID', 'ID');
    }
}