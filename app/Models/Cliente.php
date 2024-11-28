<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    // Especificamos la tabla si el nombre no sigue la convención plural
    protected $table = 'cliente';

    // Campos asignables en masa
    protected $fillable = [
        'DNI',
        'Nombre',
        'Apellido_Pat',
        'Apellido_Mat',
    ];

    // Definir la relación de un cliente con muchas ventas
    public function ventas()
    {
        return $this->hasMany(Salida::class, 'cliente_id'); // Asumiendo que 'cliente_id' es la clave foránea
    }
}
