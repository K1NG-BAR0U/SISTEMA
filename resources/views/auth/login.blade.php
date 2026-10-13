@extends('layouts.app')

@section('titulo', 'Iniciar sesión')

@section('contenido')
<div class="mx-auto mt-10 max-w-sm rounded bg-white p-6 shadow">
    <h1 class="mb-4 text-xl font-semibold">Iniciar sesión</h1>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <label for="email" class="block text-sm font-medium">Correo</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
               class="mb-3 mt-1 w-full rounded border border-slate-300 px-3 py-2">

        <label for="password" class="block text-sm font-medium">Contraseña</label>
        <input id="password" type="password" name="password" required
               class="mb-3 mt-1 w-full rounded border border-slate-300 px-3 py-2">

        @error('email')
            <p class="mb-3 text-sm text-red-600">{{ $message }}</p>
        @enderror

        <button class="w-full rounded bg-teal-700 px-4 py-2 text-white hover:bg-teal-800">Entrar</button>
    </form>
</div>
@endsection