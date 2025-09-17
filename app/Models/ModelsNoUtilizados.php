<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelsNoUtilizados extends Model
{
    use HasFactory;


    protected $table = 'NO_UTILIZADO';


    protected $fillable = [
        'uma',
        'material',
        'lote',
        'centro',
        'almacen',
        'orden_prev',
        'cantidad',
        'paletizadora',
        'fecha',
        'hora',
        'embala',
        'n_doc_mov',
        'n_doc_trasp',
        'actualiza',
        'utilizado'
    ];

    public $timestamps = false;

}
