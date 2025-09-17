<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelsDescripcion extends Model
{
    use HasFactory;

    protected $table = 'DESCRIPCION';

    protected $primaryKey = 'Material';

    protected $fillable = [
        'Material',
        'Descripcion',
        'LTxCJ',
        'UM'
    ];


    public function paletizadoras()
    {
        return $this->hasMany(ModelsPaletizadoras::class, 'material_orden', 'material');
    }

}
