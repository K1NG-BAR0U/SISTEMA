<?php

namespace App\Http\Controllers;

use App\Models\Bitacora;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    public function index(Request $request)
    {
        $registros = Bitacora::query()
            ->when($request->accion, fn ($q, $a) => $q->where('accion', $a))
            ->when($request->q, fn ($q, $b) => $q->where('usuario', 'like', "%$b%"))
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        $acciones = ['crear', 'editar', 'eliminar', 'inicio_sesion', 'cierre_sesion', 'intento_fallido'];

        return view('bitacora.index', compact('registros', 'acciones'));
    }
}