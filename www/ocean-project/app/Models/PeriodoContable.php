<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodoContable extends Model
{
    protected $table = 'periodos_contables';

    protected $guarded = ['id'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function asientosContables()
    {
        return $this->hasMany(AsientoContable::class, 'periodo_contable_id');
    }

    public function depreciaciones()
    {
        return $this->hasMany(DepreciacionActivo::class, 'periodo_contable_id');
    }

}
