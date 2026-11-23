<?php

namespace App\Models\Económica;

use Illuminate\Database\Eloquent\Model;

class ActivoFijo extends Model
{
    protected $table = 'activos_fijos';

    protected $guarded = ['id'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function evidencia()
    {
        return $this->belongsTo(Evidencia::class, 'evidencia_id');
    }

    public function depreciaciones()
    {
        return $this->hasMany(DepreciacionActivo::class, 'activo_fijo_id');
    }

}
