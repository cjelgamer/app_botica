<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleEntrada extends Model
{
    use HasFactory;

    protected $table = 'Detalle_Entrada'; // Nombre exacto de la tabla
    protected $primaryKey = 'ID'; // Clave primaria personalizada

    public $timestamps = false; // Si no usas columnas created_at y updated_at

    protected $fillable = [
        'Entrada_ID', 
        'Medicamento_ID', 
        'Cantidad',
    ];

    // Relación con la tabla Entrada (muchos a 1)
    public function entrada()
    {
        return $this->belongsTo(Entrada::class, 'Entrada_ID', 'ID');
    }

    // Relación con la tabla Medicamento (muchos a 1)
    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class, 'Medicamento_ID', 'ID');
    }
}
