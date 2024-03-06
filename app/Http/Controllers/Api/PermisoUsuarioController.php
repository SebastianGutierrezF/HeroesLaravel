<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permiso;
use App\Models\PermisoUsuario;
use Illuminate\Http\Request;

class PermisoUsuarioController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->only(['id_user', 'id_permiso', 'estatus']);
        $permisoUsuario = PermisoUsuario::upsert($data, ['id_user', 'id_permiso'], ['estatus']);
        if (!$permisoUsuario) {
            return response()->json(['estatus' => false]);
        }
        return response()->json(['estatus' => true]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $idUsuario)
    {
        // Primero obtenemos todos los permisos disponibles
        $permisos = Permiso::where('activo', 1)->get();
        // Luego obtenemos los permisos que el usuario tiene asignados y que estén activos
        $permisosUsuario = PermisoUsuario::select('permiso.id', 'nombre', 'clave', 'accion')
            ->join('permiso', 'permiso_user.id_permiso', '=', 'permiso.id')
            ->where('permiso_user.id_user', $idUsuario)
            ->where('permiso_user.estatus', 1)
            ->where('permiso.activo', 1)
            ->get();

        // Por último, con la función diff, quitarmos los permisos que ya tiene asignados el usuario
        $disponibles = $permisos->diff($permisosUsuario);

        // Enviamos asignados y disponibles en la misma respuesta
        return response()->json(['estatus' => true, 'asignados' => $permisosUsuario, 'disponibles' => $disponibles]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     */
    public function quitarPermisoUsuario(string $idUsuario, string $idPermiso)
    {
        if (PermisoUsuario::where('id_user', $idUsuario)->where('id_permiso', $idPermiso)->update(['estatus' => 0])) {
            return response()->json(['estatus' => true]);
        }
        return response()->json(['estatus' => false]);
    }
}
