<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Bitacora extends Model
{
    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'detalle' => 'array',
        'created_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        // Segunda barrera: el modelo tampoco permite editar ni borrar
        static::updating(fn () => false);
        static::deleting(fn () => false);
    }

    public static function registrar(string $accion, ?Model $modelo = null, array $detalle = []): void
    {
        $usuario = Auth::user();

        static::create([
            'user_id' => $usuario?->id,
            'usuario' => $usuario
                ? "{$usuario->name} <{$usuario->email}>"
                : (app()->runningInConsole() ? 'Sistema' : 'Sin sesión'),
            'accion' => $accion,
            'tabla' => $modelo?->getTable(),
            'registro_id' => $modelo?->getKey(),
            'detalle' => $detalle ?: null,
            'ip' => request()->ip(),
            'created_at' => now(),
        ]);
    }
}