<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermisoUsuario extends Model
{
    protected $table = 'permiso_user';
    protected $fillable = ['id_user', 'id_permiso', 'estatus'];
    public $timestamps = false;
}
