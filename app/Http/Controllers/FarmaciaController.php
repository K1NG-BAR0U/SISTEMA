<?php

namespace App\Http\Controllers;

use App\Models\Farmacia;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FarmaciaController extends Controller
{
    public function index(Request $request)
    {
        $registros = Farmacia::query()
            ->when($request->q, function ($consulta, $b) {
                $consulta->where(function ($c) use ($b) {
                    $c->where('clave_insumo', 'like', "%$b%")
                      ->orWhere('descripcion', 'like', "%$b%")
                      ->orWhere('proveedor', 'like', "%$b%")
                      ->orWhere('numero_pedido', 'like', "%$b%");
                });
            })
            ->orderBy('id')
            ->paginate(25)
            ->withQueryString();

        return view('farmacia.index', compact('registros'));
    }

    public function create()
    {
        return view('farmacia.create', ['registro' => new Farmacia]);
    }

    public function store(Request $request)
    {
        $registro = new Farmacia($this->validar($request));
        $registro->calcular();
        $registro->save();

        return redirect()->route('farmacia.show', $registro)
            ->with('exito', 'Registro guardado correctamente.');
    }

    public function show(Farmacia $registro)
    {
        return view('farmacia.show', compact('registro'));
    }

    public function edit(Farmacia $registro)
    {
        return view('farmacia.edit', compact('registro'));
    }

    public function update(Request $request, Farmacia $registro)
    {
        $registro->fill($this->validar($request));
        $registro->calcular();
        $registro->save();

        return redirect()->route('farmacia.show', $registro)
            ->with('exito', 'Registro actualizado correctamente.');
    }

    public function destroy(Farmacia $registro)
    {
        $registro->delete();

        return redirect()->route('farmacia.index')
            ->with('exito', 'Registro eliminado.');
    }

    /** Valida solo los campos que se capturan; los calculados los obtiene calcular(). */
    private function validar(Request $request): array
    {
        $this->completarCatalogo($request);

        $datos = $request->validate([
            'id_anexo' => ['required', 'string', 'max:20'],
            'partida' => ['nullable', 'integer', 'min:0'],
            'clas' => ['required', Rule::in(['MED', 'MAT CURACION'])],
            'cucop' => ['nullable', 'string', 'max:8'],
            'clave_insumo' => ['required', 'string', 'max:20'],
            'descripcion' => ['required', 'string'],
            'unidad' => ['nullable', 'string', 'max:20'],
            'temperatura_almacenamiento' => ['nullable', 'numeric', 'between:-50,100'],

            'carga_aamates' => ['nullable', 'integer', 'min:0'],
            'adj_min_2526' => ['nullable', 'integer', 'min:0'],
            'adj_max_2526' => ['nullable', 'integer', 'min:0'],
            'resto_contrato' => ['nullable', 'integer', 'min:0'],
            'min_26' => ['nullable', 'integer', 'min:0'],
            'max_26' => ['nullable', 'integer', 'min:0'],

            'proveedor' => ['nullable', 'string', 'max:255'],
            'numero_pedido' => ['nullable', 'string', 'max:255'],
            'numero_procedimiento' => ['nullable', 'string', 'max:255'],
            'precio_unitario' => ['nullable', 'numeric', 'min:0'],
            'volumen_requerir' => ['nullable', 'integer', 'min:0'],
            'fecha_pedido' => ['nullable', 'date'],
            'nombre_os' => ['nullable', 'string', 'max:255'],

            'numero_entrada' => ['nullable', 'string', 'max:20'],
            'fecha_entrega' => ['nullable', 'date', 'after_or_equal:fecha_pedido'],
            'numero_remision' => ['nullable', 'string', 'max:50'],
            'volumen_entregado' => ['nullable', 'integer', 'min:0'],
            'laboratorio_marca' => ['nullable', 'string', 'max:255'],
            'pais_origen' => ['nullable', 'string', 'max:60'],
            'lote' => ['nullable', 'string', 'max:50'],
            'caducidad' => ['nullable', 'date'],

            'estatus_instrumento' => ['nullable', 'string', 'max:30'],
            'codigo_contrato_compranet' => ['nullable', 'string', 'max:60'],
            'fecha_inicio_contrato' => ['nullable', 'date'],
            'fecha_fin_contrato' => ['nullable', 'date', 'after_or_equal:fecha_inicio_contrato'],
            'monto_minimo_contrato' => ['nullable', 'numeric', 'min:0'],
            'monto_maximo_contrato' => ['nullable', 'numeric', 'min:0'],
            'representante_legal' => ['nullable', 'string', 'max:255'],
            'fabricante' => ['nullable', 'string', 'max:255'],

            'contacto' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string'],
            'correos' => ['nullable', 'string'],
            'observaciones' => ['nullable', 'string'],
            'estatus' => ['nullable', 'string', 'max:255'],
        ]);

        // Estas columnas no aceptan vacío en la base de datos
        foreach (['carga_aamates', 'resto_contrato', 'min_26', 'max_26'] as $campo) {
            $datos[$campo] = $datos[$campo] ?? 0;
        }

        return $datos;
    }

    /** Sustituye la búsqueda en el catálogo externo de Excel (VLOOKUP por ID). */
    private function completarCatalogo(Request $request): void
    {
        if (! $request->filled('id_anexo') || ($request->filled('clas') && $request->filled('cucop'))) {
            return;
        }

        $base = Farmacia::where('id_anexo', $request->id_anexo)->whereNotNull('clas')->first();

        if ($base) {
            $request->merge([
                'clas' => $request->clas ?: $base->clas,
                'cucop' => $request->cucop ?: $base->cucop,
            ]);
        }
    }
}