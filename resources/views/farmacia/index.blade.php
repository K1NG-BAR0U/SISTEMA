@extends('layouts.app')

@section('titulo', 'Farmacia')

@section('contenido')
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-semibold">Farmacia – Anexo FG</h1>
    <a href="{{ route('farmacia.create') }}"
       class="rounded bg-teal-700 px-4 py-2 text-white hover:bg-teal-800">Nuevo registro</a>
</div>

<form method="GET" action="{{ route('farmacia.index') }}" class="mb-4 flex gap-2">
    <input type="text" name="q" value="{{ request('q') }}"
           placeholder="Buscar por clave, descripción, proveedor o pedido"
           class="w-full rounded border border-slate-300 px-3 py-2">
    <button class="rounded bg-slate-700 px-4 py-2 text-white hover:bg-slate-800">Buscar</button>
</form>

<div class="overflow-x-auto rounded bg-white shadow">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-200 text-left">
            <tr>
                <th class="px-3 py-2">ID</th>
                <th class="px-3 py-2">Clave</th>
                <th class="px-3 py-2">Descripción</th>
                <th class="px-3 py-2">Proveedor</th>
                <th class="px-3 py-2 text-right">Requerido</th>
                <th class="px-3 py-2 text-right">Entregado</th>
                <th class="px-3 py-2 text-right">Pendiente</th>
                <th class="px-3 py-2 text-right">%</th>
                <th class="px-3 py-2">Estatus</th>
                <th class="px-3 py-2">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($registros as $r)
                <tr class="border-t hover:bg-slate-50">
                    <td class="px-3 py-2">{{ $r->id_anexo }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">{{ $r->clave_insumo }}</td>
                    <td class="px-3 py-2" title="{{ $r->descripcion }}">{{ Str::limit($r->descripcion, 70) }}</td>
                    <td class="px-3 py-2">{{ $r->proveedor ?? '—' }}</td>
                    <td class="px-3 py-2 text-right">{{ $r->volumen_requerir ?? '—' }}</td>
                    <td class="px-3 py-2 text-right">{{ $r->volumen_entregado ?? '—' }}</td>
                    <td class="px-3 py-2 text-right">{{ $r->volumen_pendiente ?? '—' }}</td>
                    <td class="px-3 py-2 text-right">
                        {{ $r->porcentaje !== null ? number_format($r->porcentaje, 0) . '%' : '—' }}
                    </td>
                    <td class="px-3 py-2">{{ $r->estatus ?? '—' }}</td>
                    <td class="px-3 py-2 whitespace-nowrap">
                        <a href="{{ route('farmacia.show', $r) }}" class="text-teal-700 hover:underline">Ver</a>
                        <a href="{{ route('farmacia.edit', $r) }}" class="ml-2 text-amber-600 hover:underline">Editar</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="px-3 py-6 text-center text-slate-500">No se encontraron registros.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">{{ $registros->links() }}</div>
@endsection