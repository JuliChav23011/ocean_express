<?php

namespace App\Models\Economia;

use App\Models\Administracion\Evidencia;
use Illuminate\Database\Eloquent\Model;

class AlternativaInversion extends Model
{
    protected $table = 'alternativas_inversion';

    protected $fillable = [
        'estudio_inversion_id',
        'evidencia_id',
        'codigo',
        'nombre',
        'descripcion',
        'tipo',
        'inversion_inicial',
        'costo_operativo_anual',
        'ingreso_anual_estimado',
        'valor_residual',
        'vida_util_anios',
        'metodo_depreciacion',
        'clasificacion_datos',
        'justificacion',
        'activa',
    ];

    protected $casts = [
        'inversion_inicial' => 'decimal:2',
        'costo_operativo_anual' => 'decimal:2',
        'ingreso_anual_estimado' => 'decimal:2',
        'valor_residual' => 'decimal:2',
        'vida_util_anios' => 'integer',
        'activa' => 'boolean',
    ];

    public function estudioInversion()
    {
        return $this->belongsTo(
            EstudioInversion::class,
            'estudio_inversion_id'
        );
    }

    public function evidencia()
    {
        return $this->belongsTo(
            Evidencia::class,
            'evidencia_id'
        );
    }

    public function flujosEfectivo()
    {
        return $this->hasMany(
            FlujoEfectivo::class,
            'alternativa_inversion_id'
        );
    }

    public function evaluaciones()
    {
        return $this->hasMany(
            EvaluacionEconomica::class,
            'alternativa_inversion_id'
        );
    }
}