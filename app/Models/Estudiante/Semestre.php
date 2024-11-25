<?php

namespace App\Models\Estudiante;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Semestre extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;
    public $timestamps = false;
    protected $table = 'SEMESTRE';
    protected $primarykey = 'id';
    protected $fillable = [
        'cadena','semestre','anio','Eliminado'
    ];
}