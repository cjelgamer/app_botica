<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class VendedorSimple extends Model
{
    protected $table = 'vendedor'; // Nombre de la tabla
    protected $primaryKey = 'ID';  // Clave primaria si no es 'id'
    public $timestamps = false;    // Si no tienes los campos 'created_at' y 'updated_at'

    protected $fillable = [
        'Nombre', 'Telefono', 'Estado', 'Rol', 'Contraseña',
    ];

    // Mutador para encriptar la contraseña
    public function setContraseñaAttribute($value)
    {
        // Encriptamos la contraseña solo si no es nula o vacía
        if (!empty($value)) {
            $this->attributes['Contraseña'] = Hash::make($value);
        }
    }

    public function getAuthPassword()
    {
        return $this->Contraseña; // Devuelve la contraseña encriptada
    }

}
