<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permiso;
use App\Models\PermisoUsuario;
use Illuminate\Http\Request;

class PermisoController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function insertPermiso(Request $request)
    {
        $data = $request->only(['nombre', 'clave', 'accion']);
        if (Permiso::upsert($data, ['clave'], ['activo' => 1])) {
            return response()->json(['estatus' => true]);
        }
        return response()->json(['estatus' => false]);
    }

    /**
     * Display the specified resource.
     */
    public function showDisponiblesUsuario(Permiso $idUsuario)
    {
        // $permisos = Permiso::select('nombre', 'clave', 'accion')
        //     ->join('permiso_usuario', 'permiso_usuario.id_permiso', '=', 'permiso.id')
        //     ->except(PermisoUsuario::select('nombre', 'clave', 'accion')
        //         ->join('permiso', 'permiso_user.id_permiso', '=', 'permiso.id')
        //         ->where('permiso_user.id_user', $idUsuario)
        //         ->get());

        $permisos = Permiso::select('nombre', 'clave', 'accion')
            ->leftJoin('permiso_user', function ($join) use ($idUsuario) {
                $join->on('permiso.id', '=', 'permiso_user.id_permiso')
                    ->where('permiso_user.id_user', '=', $idUsuario);
            })
            ->whereNull('permiso_user.id_user') // Exclude already assigned permissions
            ->orWhere('permiso_user.estatus', 0)
            ->select('permiso.*')
            ->get();

        $permisos = Permiso::whereDoesntHave('users', function ($query) use ($idUsuario) {
            $query->where('id_user', $idUsuario);
        })->get();
        return response()->json(['estatus' => true, 'data' => $permisos]);
    }

    public function show(Request $request)
    {
        $valores = $request->input();
        $productos = [];
        $total = 0;
        if (isset($valores['first'])) {
            $sort = isset($valores['sortOrder']) && $valores['sortOrder'] == 1 ? 'asc' : 'desc';
            $sortField = isset($valores['sortField']) ? $valores['sortField'] : 'nombre';
            $condicion = [];
            if (!empty($valores['globalFilter'])) {
                $filtro =  '%' . $valores['globalFilter'] . '%';
                $condicion = function ($query) use ($filtro) {
                    $query->where('nombre', 'like', $filtro)
                        ->orWhere('clave', 'like', $filtro)
                        ->orWhere('accion', 'like', $filtro);
                };
            }
            $productos = Permiso::where($condicion)
                ->where('activo', 1)
                ->orderBy($sortField, $sort)
                ->offset($valores['first'])
                ->limit($valores['rows'])
                ->get()
                ->toArray();

            $total = Permiso::where($condicion)->count();
            return response()->json(['data' => $productos, 'count' => $total, 'parametros' => $valores]);
        }
        return response()->json(['data' => $productos, 'count' => 0, 'parametros' => $valores]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updatePermiso(Request $request)
    {
        $data = $request->only(['id', 'nombre', 'clave', 'accion']);

        $producto = Permiso::find($data['id']);

        if (!$producto) {
            return response()->json(['estatus' => false]);
        }

        $producto->update($data);

        return response()->json(['estatus' => true]);
    }

    public function deletePermiso($id)
    {
        $producto = Permiso::find($id);

        if (!$producto) {
            return response()->json(['estatus' => false]);
        }

        $producto->update(['activo' => 0]);

        return response()->json(['estatus' => true]);
    }

    public function deletePermisos(Request $request)
    {
        $valores = $request->input();
        $ids = [];
        foreach ($valores as $producto) {
            $ids[] = $producto['id'];
        }
        $producto = Permiso::whereIn('id', $ids)->update(['activo' => 0]);

        if (!$producto) {
            return response()->json(['estatus' => false]);
        }

        return response()->json(['estatus' => true]);
    }
}
