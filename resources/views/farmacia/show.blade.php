@extends('layouts.app')

@section('titulo', 'Detalle')

@section('contenido')
<div class="mb-4 flex flex-wrap items-center justify-between gap-3">
    <h1 class="text-2xl font-semibold">{{ $registro->clave_insumo }} · {{ $registro->proveedor ?? 'Sin proveedor' }}</h1>
    <div class="flex gap-2">
        <a href="{{ route('farmacia.edit', $registro) }}" class="rounded bg-amber-500 px-4 py-2 text-white hover:bg-amber-600">Editar</a>
        <a href="{{ route('farmacia.index') }}" class="rounded border border-slate-300 bg-white px-4 py-2">Volver al listado</a>
    </div>
</div>

@foreach (App\Models\Farmacia::secciones() as $titulo => $campos)
    <section class="mb-6 rounded bg-white p-4 shadow">
        <h2 class="mb-3 text-lg font-semibold text-teal-700">{{ $titulo }}</h2>
        <dl class="grid gap-4 md:grid-cols-3">
            @foreach ($campos as $campo => $def)
                @php
                    $v = $registro->$campo;
                    $texto = match ($def[1]) {
                        'dinero' => $v !== null ? '$' . number_format($v, 2) : '—',
                        'porcentaje' => $v !== null ? number_format($v, 1) . '%' : '—',
                        'fecha' => $v?->format('d/m/Y') ?? '—',
                        'entero' => $v !== null ? number_format($v) : '—',
                        default => ($v === null || $v === '') ? '—' : $v,
                    };
                @endphp
                <div class="{{ $def[1] === 'area' ? 'md:col-span-3' : '' }}">
                    <dt class="text-xs font-medium uppercase text-slate-500">
                        {{ $def[0] }}
                        @if ($def[2] ?? false)
                            <span class="normal-case text-teal-600">· automático</span>
                        @endif
                    </dt>
                    <dd class="mt-1 whitespace-pre-line">{{ $texto }}</dd>
                </div>
            @endforeach
        </dl>
    </section>
@endforeach
@endsection