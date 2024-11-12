<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendedorSimple extends Model
{
    protected $table = 'vendedor'; // Nombre de la tabla
    protected $primaryKey = 'ID';  // Clave primaria si no es 'id'
    public $timestamps = false;    // Si no tienes los campos 'created_at' y 'updated_at'
    
    protected $fillable = [
        'Nombre', 'Telefono', 'Estado', 'Rol',
    ];
}
