<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ModelsPaletizadoras extends Model
{
    use HasFactory;

    protected $table = 'PALETIZADORAS';

    protected $primaryKey = 'paletizadora';

    protected $fillable = [
        'paletizadora',
        'NOrdPrev',
        'fecha',
        'VersionF',
        'centro',
        'almacen',
        'ult_uma',
        'material_orden',
        'ult_fecha',
        'pedido',
        'pos',
        'lote_vac',
        'eliminada',
    ];

    public $timestamps = false;


    public function descripcion()
    {
        return $this->belongsTo(ModelsDescripcion::class, 'material_orden', 'material');
    }

}
