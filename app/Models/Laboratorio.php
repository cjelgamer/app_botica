<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Laboratorio extends Authenticatable
{
    use Notifiable;

    protected $table = 'laboratorio';
    protected $primaryKey = 'ID';  // Clave primaria si no es 'id'
    public $timestamps = false;    // Si no tienes los campos 'created_at' y 'updated_at'

    protected $fillable = [
        'Nombre', 'Telefono','Estado', 'Direccion', 'RUC', 
    ];

}
