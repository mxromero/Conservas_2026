<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogRegistro extends Model
{
    use HasFactory;

    protected $table = 'log_registros';

    protected $fillable = [
        'usuario',
        'accion',
        'modelo',
        'registro_id',
        'datos_anteriores',
        'datos_nuevos',
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos'     => 'array',
    ];
}
