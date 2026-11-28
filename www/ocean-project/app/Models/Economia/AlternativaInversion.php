<?php

namespace App\Models\Economia;

use Illuminate\Database\Eloquent\Model;

class AlternativaInversion extends Model
{
    protected $table = 'alternativas_inversion';

    protected $guarded = ['id'];

    public function estudioInversion()
    {
        return $this->belongsTo(EstudioInversion::class, 'estudio_inversion_id');
    }

    public function evidencia()
    {
        return $this->belongsTo(Evidencia::class, 'evidencia_id');
    }

    public function flujosEfectivo()
    {
        return $this->hasMany(FlujoEfectivo::class, 'alternativa_inversion_id');
    }

    public function evaluaciones()
    {
        return $this->hasMany(EvaluacionEconomica::class, 'alternativa_inversion_id');
    }

}
