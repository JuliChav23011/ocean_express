<?php

namespace App\Models\Económica;

use Illuminate\Database\Eloquent\Model;

class FlujoEfectivo extends Model
{
    protected $table = 'flujos_efectivo';

    protected $guarded = ['id'];

    public function alternativaInversion()
    {
        return $this->belongsTo(AlternativaInversion::class, 'alternativa_inversion_id');
    }

    public function evidencia()
    {
        return $this->belongsTo(Evidencia::class, 'evidencia_id');
    }

}
