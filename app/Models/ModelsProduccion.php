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

    protected static function booted()
    {
        static::updated(function ($model) {
            \App\Models\LogRegistro::create([
                'usuario' => auth()->user()->name ?? 'sistema',
                'accion' => 'actualización',
                'modelo' => 'Produccion',
                'registro_id' => $model->uma,
                'datos_anteriores' => json_encode($model->getOriginal()),
                'datos_nuevos' => json_encode($model->getChanges()),
            ]);
        });
        // 🔹 Registro de creación
        /*static::created(function ($model) {
            \App\Models\LogRegistro::create([
                'usuario' => auth()->user()->name ?? 'sistema',
                'accion' => 'creación',
                'modelo' => 'Produccion',
                'registro_id' => $model->id,
                'datos_nuevos' => json_encode($model->toArray()),
            ]);
        });*/

        // 🔹 Registro de eliminación
        static::deleted(function ($model) {
            \App\Models\LogRegistro::create([
                'usuario' => auth()->user()->name ?? 'sistema',
                'accion' => 'eliminación',
                'modelo' => 'Produccion',
                'registro_id' => $model->uma,
                'datos_anteriores' => json_encode($model->getOriginal()),
            ]);
        });        
    }

}
