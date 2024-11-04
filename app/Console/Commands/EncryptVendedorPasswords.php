<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use App\Models\Vendedor; // Asegúrate de que esta es la ubicación correcta del modelo

class EncryptVendedorPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'encrypt:vendedor-passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Encripta las contraseñas de los vendedores en la base de datos';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Obtén todos los vendedores
        $vendedores = Vendedor::all();

        foreach ($vendedores as $vendedor) {
            // Si la contraseña no está encriptada, encriptarla
            if (!Hash::needsRehash($vendedor->Contraseña)) {
                $vendedor->Contraseña = Hash::make($vendedor->Contraseña);
                $vendedor->save();
                $this->info("Contraseña de {$vendedor->Nombre} encriptada.");
            }
        }

        $this->info('Todas las contraseñas han sido encriptadas.');
        return 0;
    }
}
