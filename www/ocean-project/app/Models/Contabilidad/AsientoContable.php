<?php

namespace App\Models\Contabilidad;

use App\Models\Administracion\Evidencia;
use App\Models\Empresa;
use Illuminate\Database\Eloquent\Model;

class AsientoContable extends Model
{
    protected $table = 'asientos_contables';
    protected $guarded = ['id'];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function periodoContable()
    {
        return $this->belongsTo(PeriodoContable::class, 'periodo_contable_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleAsiento::class, 'asiento_contable_id');
    }

    public function evidencia()
    {
        return $this->belongsTo(Evidencia::class, 'referencia_documento', 'codigo');
    }

    public function getTotalDebeAttribute(): float
    {
        return (float) $this->detalles->sum('debe');
    }

    public function getTotalHaberAttribute(): float
    {
        return (float) $this->detalles->sum('haber');
    }

    public function estaCuadrado(): bool
    {
        return round($this->total_debe, 2) === round($this->total_haber, 2) && $this->total_debe > 0;
    }
}