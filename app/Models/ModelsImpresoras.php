<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelsImpresoras extends Model
{
    use HasFactory;

    protected $table = 'Lineas'; // Nombre exacto de la tabla en SQL Server

    public $timestamps = false; // La tabla no tiene campos created_at ni updated_at

    protected $primaryKey = 'orden'; // Clave primaria definida en la tabla
    public $incrementing = false; // Asumimos que 'orden' no es autoincremental
    protected $keyType = 'int'; // El tipo de la clave primaria

    protected $fillable = [
        'orden',
        'linea',
        'Producto',
        'activa',
        'impresora',
        'tipo_imp',
        'paletizadora',
        'impresorac',
        'tipo_imp2',
        'num_imp',
    ];

    protected $casts = [
        'orden'        => 'integer',
        'linea'        => 'string',
        'Producto'     => 'string',
        'activa'       => 'string',
        'impresora'    => 'string',
        'tipo_imp'     => 'string',
        'paletizadora' => 'string',
        'impresorac'   => 'string',
        'tipo_imp2'    => 'string',
        'num_imp'      => 'integer',
    ];
}
