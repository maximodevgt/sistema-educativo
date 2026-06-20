<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /** Iniciar sesión: valida credenciales y entrega un token de acceso. */
    public function login(Request $request)
    {
        $datos = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $usuario = User::where('email', $datos['email'])->first();

        if (! $usuario || ! Hash::check($datos['password'], $usuario->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales no son correctas.'],
            ]);
        }

        $token = $usuario->createToken('sesion-' . now()->timestamp)->plainTextToken;

        return response()->json([
            'token' => $token,
            'usuario' => [
                'id' => $usuario->id,
                'nombre' => $usuario->name,
                'cargo' => $usuario->cargo,
                'email' => $usuario->email,
            ],
        ]);
    }

    /** Cerrar sesión: revoca el token actual. */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['mensaje' => 'Sesión cerrada.']);
    }

    /** Devuelve el usuario autenticado (para validar la sesión). */
    public function me(Request $request)
    {
        $usuario = $request->user();

        return response()->json([
            'id' => $usuario->id,
            'nombre' => $usuario->name,
            'cargo' => $usuario->cargo,
            'email' => $usuario->email,
        ]);
    }
}
