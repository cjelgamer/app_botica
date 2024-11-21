<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    use HasFactory;

    protected $table = 'Entrada'; // Nombre exacto de la tabla
    protected $primaryKey = 'ID'; // Clave primaria personalizada

    public $timestamps = false; // Si no usas columnas created_at y updated_at

    protected $fillable = [
        'Fecha_Entrega',
        'Total',
        'Laboratorio_ID',
    ];

    // Relaciones si las tienes configuradas
    public function laboratorio()
    {
        return $this->belongsTo(Laboratorio::class, 'Laboratorio_ID');
    }
}

