<?php

namespace App\Models\Económica;

use Illuminate\Database\Eloquent\Model;

class EvaluacionEconomica extends Model
{
    protected $table = 'evaluaciones_economicas';

    protected $guarded = ['id'];

    public function alternativaInversion()
    {
        return $this->belongsTo(AlternativaInversion::class, 'alternativa_inversion_id');
    }

}
