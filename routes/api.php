<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::post('user/login', [UserController::class, 'login']);
// Route::group(['middleware' => ['auth:sanctum']], function () {
//     Route::prefix('user')->group(function () {
//         Route::post('/register', [UserController::class, 'register']);
//         Route::put('/password/{id}', [UserController::class, 'password'])->where('id', '[0-9]+');
//         Route::put('/editar/{id}', [UserController::class, 'update'])->where('id', '[0-9]+');
//         Route::get('/user-profile', [UserController::class, 'userprofile']);
//         Route::get('/logout', [UserController::class, 'logout']);
//     });

// });
Route::prefix('productos')->group(function () {
    Route::post('', [ProductoController::class, 'getProductos2']);
    Route::get('/{pagina}/{limit}', [ProductoController::class, 'getProductos'])->where('pagina', '[0-9]+')->where('limit', '[0-9]+');
    Route::get('/{producto_id}', [ProductoController::class, 'getProductoById'])->where('producto_id', '[0-9]+');
    Route::put('', [ProductoController::class, 'insertProducto']);
    Route::post('/update', [ProductoController::class, 'updateProducto']);
    Route::delete('/{id}', [ProductoController::class, 'deleteProducto'])->where('id', '[0-9]+');
});
