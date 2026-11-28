<?php

namespace App\Models\Economia;

use App\Models\Administracion\Evidencia;
use Illuminate\Database\Eloquent\Model;

class FlujoEfectivo extends Model
{
    protected $table = 'flujos_efectivo';

    protected $guarded = ['id'];

    public function alternativaInversion()
    {
        return $this->belongsTo(
            AlternativaInversion::class,
            'alternativa_inversion_id'
        );
    }

    public function evidencia()
    {
        return $this->belongsTo(
            Evidencia::class,
            'evidencia_id'
        );
    }
}