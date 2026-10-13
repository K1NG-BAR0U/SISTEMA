@extends('layouts.app')

@section('titulo', 'Bitácora')

@section('contenido')
<h1 class="mb-4 text-2xl font-semibold">Bitácora de auditoría</h1>

<form method="GET" action="{{ route('bitacora.index') }}" class="mb-4 flex flex-wrap gap-2">
    <input type="text" name="q" value="{{ request('q') }}" placeholder="Buscar por usuario"
           class="rounded border border-slate-300 px-3 py-2">
    <select name="accion" class="rounded border border-slate-300 px-3 py-2">
        <option value="">Todas las acciones</option>
        @foreach ($acciones as $a)
            <option value="{{ $a }}" @selected(request('accion') === $a)>{{ $a }}</option>
        @endforeach
    </select>
    <button class="rounded bg-slate-700 px-4 py-2 text-white hover:bg-slate-800">Filtrar</button>
</form>

<div class="overflow-x-auto rounded bg-white shadow">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-200 text-left">
            <tr>
                <th class="px-3 py-2">Fecha y hora</th>
                <th class="px-3 py-2">Usuario</th>
                <th class="px-3 py-2">Acción</th>
                <th class="px-3 py-2">Registro</th>
                <th class="px-3 py-2">IP</th>
                <th class="px-3 py-2">Detalle</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($registros as $b)
                <tr class="border-t align-top">
                    <td class="px-3 py-2 whitespace-nowrap">{{ $b->created_at?->format('d/m/Y H:i:s') }}</td>
                    <td class="px-3 py-2">{{ $b->usuario }}</td>
                    <td class="px-3 py-2">{{ $b->accion }}</td>
                    <td class="px-3 py-2">{{ $b->tabla ? $b->tabla . ' #' . $b->registro_id : '—' }}</td>
                    <td class="px-3 py-2">{{ $b->ip }}</td>
                    <td class="px-3 py-2">
                        @if ($b->detalle)
                            <details>
                                <summary class="cursor-pointer text-teal-700">Ver</summary>
                                <pre class="mt-2 max-w-xl overflow-x-auto rounded bg-slate-50 p-2 text-xs">{{ json_encode($b->detalle, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                            </details>
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-3 py-6 text-center text-slate-500">Sin movimientos.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $registros->links() }}</div>
@endsection