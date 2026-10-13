@extends('layouts.app')

@section('titulo', 'Nuevo registro')

@section('contenido')
<h1 class="mb-4 text-2xl font-semibold">Nuevo registro de Farmacia</h1>

<form method="POST" action="{{ route('farmacia.store') }}">
    @csrf
    @include('farmacia._form')

    <div class="flex gap-3">
        <button class="rounded bg-teal-700 px-5 py-2 text-white hover:bg-teal-800">Guardar</button>
        <a href="{{ route('farmacia.index') }}" class="rounded border border-slate-300 bg-white px-5 py-2">Cancelar</a>
    </div>
</form>
@endsection