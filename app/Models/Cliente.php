<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'Cliente';
    
    // Desactivar timestamps
    public $timestamps = false;
    
    protected $fillable = [
        'DNI',
        'Nombre',
        'Apellido_Pat',
        'Apellido_Mat'
    ];

    public function salidas()
    {
        return $this->hasMany(Salida::class, 'Cliente_ID');
    }
}