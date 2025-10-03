<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelsCantidadMaterial extends Model
{
    use HasFactory;


  protected $table = 'Material';

    // Como la tabla no tiene clave primaria autoincremental, definimos la PK manual
    protected $primaryKey = 'Material';
    public $incrementing = false;

    // Tipo de clave primaria (nchar(10) => string)
    protected $keyType = 'string';

    // No tiene created_at ni updated_at
    public $timestamps = false;

    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'Material',
        'corr_actual',
        'nvo_lote',
        'cant_pro',
        'linea',
    ];



}
