<?php

namespace App\Observers;

use App\Models\Bitacora;
use App\Models\Farmacia;
use Illuminate\Support\Arr;

class FarmaciaObserver
{
    public function created(Farmacia $farmacia): void
    {
        Bitacora::registrar('crear', $farmacia, [
            'despues' => $this->limpio($farmacia->getAttributes()),
        ]);
    }

    public function updated(Farmacia $farmacia): void
    {
        $cambios = collect($farmacia->getChanges())->except('updated_at');

        if ($cambios->isEmpty()) {
            return;
        }

        Bitacora::registrar('editar', $farmacia, [
            'antes' => $cambios->map(fn ($valor, $campo) => $farmacia->getOriginal($campo))->all(),
            'despues' => $cambios->all(),
        ]);
    }

    public function deleted(Farmacia $farmacia): void
    {
        Bitacora::registrar('eliminar', $farmacia, [
            'antes' => $this->limpio($farmacia->getAttributes()),
        ]);
    }

    private function limpio(array $atributos): array
    {
        return Arr::except($atributos, ['created_at', 'updated_at']);
    }
}