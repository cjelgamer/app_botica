<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleSalida extends Model
{
    use HasFactory;

    // Especificar la tabla si el nombre no sigue la convención plural
    protected $table = 'detalle_salida';

    // Especificar la clave primaria personalizada
    protected $primaryKey = 'ID';

    // Indicar si la clave primaria es autoincrementable
    public $incrementing = true;

    // Desactivar timestamps si no se usan columnas created_at/updated_at
    public $timestamps = false;

    protected $fillable = [
        'salida_id',
        'medicamento_id',
        'cantidad',
    ];

    // Relación con la salida
    public function salida()
    {
        return $this->belongsTo(Salida::class, 'salida_id');
    }

    // Relación con el medicamento
    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class,'medicamento_id');
    }
}
