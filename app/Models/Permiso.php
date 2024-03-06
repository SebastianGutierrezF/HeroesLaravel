<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class   Permiso extends Model
{
    protected $table = 'permiso';
    protected $fillable = ['nombre', 'clave', 'accion', 'activo'];
    public $timestamps = false;
}
