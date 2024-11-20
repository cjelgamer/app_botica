<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicamento extends Model
{
    protected $table = 'Medicamento';  // Nombre de la tabla en la base de datos
    protected $primaryKey = 'ID';      // Clave primaria si no es 'id'
    public $timestamps = false;       // Si no tienes 'created_at' y 'updated_at'

    protected $fillable = [
        'Nombre', 'Descripcion', 'Precio', 'Stock', 'Tipo',
    ];
}
