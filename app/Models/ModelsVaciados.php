<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelsVaciados extends Model
{
    use HasFactory;

    protected $table = 'VACIADO';


    protected $fillable = [
        'uma',
        'material',
        'lote',
        'cantidad',
        'centro',
        'almacen',
        'version',
        'orden_prev',
        'paletizadora',
        'fecha',
        'hora',
        'n_doc_asig',
        'n_doc_des',
        'n_doc_trasp',
        'desembala',
        'cant_uma',
        'material_orden',
        'cant_consumo',
        'lote_consumo',
        'cant_recuperada',
        'fecha_uma'
    ];

    public $timestamps = false;

    public function descripcion()
    {
        return $this->belongsTo(ModelsDescripcion::class, 'descripcion');
    }


}
