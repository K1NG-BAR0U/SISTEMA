<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Bitacora;

class SesionController extends Controller
{
    public function formulario()
    {
        return view('auth.login');
    }

    public function entrar(Request $request)
    {
        $credenciales = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credenciales)) {
         Bitacora::registrar('intento_fallido', null, ['correo' => $request->email]);

            return back()
            ->withErrors(['email' => 'Correo o contraseña incorrectos.'])
            ->onlyInput('email');
        }

            $request->session()->regenerate();
            Bitacora::registrar('inicio_sesion');

        return redirect()->intended('/farmacia');
    }

    public function salir(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}