<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            //'email' => 'required|email|unique:users',
            //'password' => 'required'
        ]);
        $user = new User();
        $user->name = $request->name;
        //$user->email = $request->email;
        $user->type = 2;
        //$user->password = Hash::make($request->password);

        $user->save();

        return response()->json([
            'estatus' => 1,
            'mensaje' => 'Registrado'
        ]);
    }

    public function password(Request $request, $id)
    {
        User::where("id", $id)->update([
            'password' => $request->password
        ]);

        return response()->json([
            'estatus' => 1,
            'mensaje' => 'Actualizado'
        ]);
    }

    public function update(Request $request, $id)
    {
        User::where("id", $id)->update([
            'name' => $request->name,
            'email' => $request->email
        ]);

        return response()->json([
            'estatus' => 1,
            'mensaje' => 'Actualizado'
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $user = User::where('email', '=', $request->email)->first();
        if (isset($user->id)) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('auth_token')->plainTextToken;
                return response()->json([
                    'estatus' => 1,
                    'mensaje' => 'Usuario correcto',
                    'name' => $user->name,
                    'type' => $user->type,
                    'access_token' => $token
                ]);
            } else {
                return response()->json([
                    'estatus' => 0,
                    'mensaje' => 'Contraseña incorrecta'
                ], 404);
            }
        } else {
            return response()->json([
                'estatus' => 0,
                'mensaje' => 'Usuario inexistente'
            ], 404);
        }
    }

    public function userProfile()
    {
        return response()->json([
            'estatus' => 1,
            'mensaje' => 'Perfil Usuario',
            'name' => auth()->user()->name,
            'type' => auth()->user()->type
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return response()->json([
            'estatus' => 1,
            'mensaje' => 'Cierre de Sesión'
        ]);
    }

    public function show(Request $request) {
        $valores = $request->input();
        $users = [];
        if (isset($valores['first'])) {
            $users = User::select('id', 'name', 'email')
                ->offset($valores['first'])
                ->limit($valores['last'])
                ->get();
            return response()->json(['estatus' => true, 'data' => $users]);
        }
        return response()->json(['estatus' => false, 'data' => $users]);
    }
}
