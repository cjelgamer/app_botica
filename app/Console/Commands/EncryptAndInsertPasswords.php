<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class EncryptAndInsertPasswords extends Command
{
    protected $signature = 'encrypt:insert-passwords';
    protected $description = 'Inserta contraseñas encriptadas en la tabla Vendedor';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        DB::table('Vendedor')->insert([
            [
                'Nombre' => 'Cristian',
                'Telefono' => '123456789',
                'Estado' => 'Activo',
                'Rol' => 'admin',
                'Contraseña' => Hash::make('admin'),
            ],
            [
                'Nombre' => 'Ludwing',
                'Telefono' => '987654321',
                'Estado' => 'Activo',
                'Rol' => 'vendedor',
                'Contraseña' => Hash::make('vendedor1'),
            ],
            [
                'Nombre' => 'Diego',
                'Telefono' => '456789123',
                'Estado' => 'Activo',
                'Rol' => 'vendedor',
                'Contraseña' => Hash::make('vendedor2'),
            ],
            [
                'Nombre' => 'Daniela',
                'Telefono' => '789123456',
                'Estado' => 'Activo',
                'Rol' => 'vendedor',
                'Contraseña' => Hash::make('vendedor3'),
            ],
        ]);

        $this->info('Contraseñas encriptadas e insertadas correctamente.');
    }
}
