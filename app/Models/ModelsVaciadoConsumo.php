<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelsVaciadoConsumo extends Model
{
    use HasFactory;


    protected $table = 'VACIADO_CONSUMO';

    protected $fillable = [
        'uma',
        'paletizadora',
        'material',
        'lote',
        'fecha',
        'hora',
        'versionf',
        'NordPrev',
        'cantidad',
        'um',
        'Lote_orig',
        'mat_orden',
        'doc_cons',
        'consumo',
        'uma_prod'
    ];

    public $timestamps = false;


}
