<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'FARMACIA-HJC')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-800">
    <header class="bg-teal-700 text-white shadow">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3">
        <a href="{{ url('/') }}" class="text-xl font-bold">Integra-HJC</a>
        @auth
            <nav class="flex items-center gap-4 text-sm">
                <a href="{{ route('farmacia.index') }}" class="hover:underline">Farmacia</a>
                <a href="{{ route('bitacora.index') }}" class="hover:underline">Bitácora</a>
                <span class="opacity-80">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="rounded bg-teal-900 px-3 py-1 hover:bg-teal-950">Cerrar sesión</button>
                </form>
            </nav>
        @endauth
    </div>
</header>

    <main class="mx-auto max-w-7xl px-4 py-6">
        @if (session('exito'))
            <div class="mb-4 rounded bg-green-100 px-4 py-3 text-green-800">
                {{ session('exito') }}
            </div>
        @endif

        @yield('contenido')
    </main>
</body>
</html>