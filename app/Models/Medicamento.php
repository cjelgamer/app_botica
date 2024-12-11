<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicamento extends Model
{
    protected $table = 'Medicamento';  // Nombre de la tabla en la base de datos
    protected $primaryKey = 'ID';      // Clave primaria si no es 'id'
    // Especificamos que 'ID' es autoincrementable
    public $incrementing = true;
    public $timestamps = false;       // Si no tienes 'created_at' y 'updated_at'

    protected $fillable = [
        'Nombre', 'Descripcion', 'Precio', 'Stock', 'Tipo',
    ];

    public function detalles()
    {
    return $this->hasMany(DetalleSalida::class, 'medicamento_id');
    }

}
