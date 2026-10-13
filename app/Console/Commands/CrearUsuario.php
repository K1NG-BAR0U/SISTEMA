<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CrearUsuario extends Command
{
    protected $signature = 'usuario:crear';
    protected $description = 'Crea un usuario autorizado del sistema';

    public function handle(): int
    {
        $nombre = $this->ask('Nombre completo');
        $email = $this->ask('Correo');
        $password = $this->secret('Contraseña (mínimo 10 caracteres)');

        $validador = Validator::make(
            compact('nombre', 'email', 'password'),
            [
                'nombre' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:users,email'],
                'password' => ['required', 'string', 'min:10'],
            ]
        );

        if ($validador->fails()) {
            foreach ($validador->errors()->all() as $error) {
                $this->error($error);
            }
            return self::FAILURE;
        }

        User::create([
            'name' => $nombre,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $this->info("Usuario creado: $email");
        return self::SUCCESS;
    }
}