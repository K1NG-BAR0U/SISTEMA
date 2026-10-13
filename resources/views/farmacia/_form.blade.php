@if ($errors->any())
    <div class="mb-4 rounded bg-red-100 px-4 py-3 text-red-800">
        Revisa los campos marcados en rojo ({{ $errors->count() }} errores).
    </div>
@endif

<p class="mb-4 rounded bg-sky-50 px-4 py-2 text-sm text-sky-800">
    Subtotal, IVA, costo total, periodo de entrega, costo por pedido, volumen pendiente y
    porcentaje se calculan automáticamente al guardar.
</p>

@foreach (App\Models\Farmacia::secciones() as $titulo => $campos)
    <fieldset class="mb-6 rounded bg-white p-4 shadow">
        <legend class="px-2 text-lg font-semibold text-teal-700">{{ $titulo }}</legend>
        <div class="grid gap-4 md:grid-cols-3">
            @foreach ($campos as $campo => $def)
                @continue($def[2] ?? false)
                @php
                    $tipo = $def[1];
                    $valor = old($campo, $registro->$campo);
                    if ($valor instanceof \Carbon\CarbonInterface) {
                        $valor = $valor->format('Y-m-d');
                    }
                    $clase = 'mt-1 w-full rounded border px-3 py-2 '
                        . ($errors->has($campo) ? 'border-red-500' : 'border-slate-300');
                @endphp
                <div class="{{ $tipo === 'area' ? 'md:col-span-3' : '' }}">
                    <label for="{{ $campo }}" class="block text-sm font-medium">{{ $def[0] }}</label>

                    @if ($tipo === 'area')
                        <textarea id="{{ $campo }}" name="{{ $campo }}" rows="3" class="{{ $clase }}">{{ $valor }}</textarea>
                    @elseif ($tipo === 'clas')
                        <select id="{{ $campo }}" name="{{ $campo }}" class="{{ $clase }}">
                            <option value="">Selecciona…</option>
                            @foreach (['MED', 'MAT CURACION'] as $opcion)
                                <option value="{{ $opcion }}" @selected($valor === $opcion)>{{ $opcion }}</option>
                            @endforeach
                        </select>
                    @else
                        <input id="{{ $campo }}" name="{{ $campo }}" value="{{ $valor }}" class="{{ $clase }}"
                            type="{{ ['entero' => 'number', 'decimal' => 'number', 'dinero' => 'number', 'fecha' => 'date'][$tipo] ?? 'text' }}"
                            @if ($tipo === 'entero') step="1" min="0" @endif
                            @if ($tipo === 'dinero') step="0.01" min="0" @endif
                            @if ($tipo === 'decimal') step="0.1" @endif>
                    @endif

                    @error($campo)
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach
        </div>
    </fieldset>
@endforeach