<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PerfilController extends Controller
{
    /** Subir o reemplazar la foto de perfil del usuario autenticado. */
    public function subirFoto(Request $request)
    {
        $request->validate([
            'foto' => ['required', 'image', 'max:3072'], // máx 3 MB
        ]);

        $usuario = $request->user();

        if ($usuario->foto) {
            Storage::disk('public')->delete($usuario->foto);
        }

        $ruta = $request->file('foto')->store('perfiles', 'public');
        $usuario->update(['foto' => $ruta]);

        return response()->json([
            'foto_url' => $usuario->foto_url,
        ]);
    }

    /** Quitar la foto de perfil (vuelve a mostrar iniciales). */
    public function quitarFoto(Request $request)
    {
        $usuario = $request->user();

        if ($usuario->foto) {
            Storage::disk('public')->delete($usuario->foto);
            $usuario->update(['foto' => null]);
        }

        return response()->json(['mensaje' => 'Foto eliminada.']);
    }
}
