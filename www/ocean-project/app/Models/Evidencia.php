<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evidencia extends Model
{
    protected $table = 'evidencias';

    protected $guarded = ['id'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function costosServicio()
    {
        return $this->hasMany(CostoServicio::class, 'evidencia_id');
    }

    public function activosFijos()
    {
        return $this->hasMany(ActivoFijo::class, 'evidencia_id');
    }

    public function alternativasInversion()
    {
        return $this->hasMany(AlternativaInversion::class, 'evidencia_id');
    }

    public function flujosEfectivo()
    {
        return $this->hasMany(FlujoEfectivo::class, 'evidencia_id');
    }

}
