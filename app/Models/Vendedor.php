<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Vendedor extends Authenticatable
{
    use Notifiable;

    protected $table = 'vendedor';

    protected $fillable = [
        'Nombre', 'Telefono', 'Estado', 'Rol', 'Contraseña',
    ];

    protected $hidden = [
        'Contraseña',
    ];

    public function getAuthPassword()
    {
        return $this->Contraseña;
    }
}
