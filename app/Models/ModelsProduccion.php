<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelsProduccion extends Model
{
    use HasFactory;

    protected $table = 'produccion';
    public $timestamps = false;

    protected $primaryKey = 'uma';
    public $incrementing = false; // 'uma' es tipo nchar y no auto-incremental
    protected $keyType = 'string';

    protected $fillable = [
        'uma',
        'material',
        'lote',
        'centro',
        'almacen',
        'NOrdPrev',
        'VersionF',
        'fecha',
        'hora',
        'fecha_semi',
        'cantidad',
        'paletizadora',
        'n_doc',
        'li_mb',
        'li_fq',
        'corre_linea',
        'Exp_sap',
    ];



    protected $casts = [
        'uma'             => 'string',
        'material'        => 'string',
        'lote'            => 'string',
        'centro'          => 'string',
        'almacen'         => 'string',
        'NOrdPrev'        => 'string',
        'VersionF'        => 'string',
        'fecha'           => 'datetime',
        'hora'            => 'string',
        'fecha_semi'      => 'datetime',
        'cantidad'        => 'integer',
        'paletizadora'    => 'integer',
        'n_doc'           => 'string',
        'li_mb'           => 'string',
        'li_fq'           => 'string',
        'corre_linea'     => 'integer',
        'Exp_sap'         => 'string',
    ];



}
