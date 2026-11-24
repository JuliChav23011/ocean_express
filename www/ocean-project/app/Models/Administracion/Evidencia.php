<?php

namespace App\Models\Administracion;

use App\Models\Contabilidad\AsientoContable;
use App\Models\Costos\CostoServicio;
use App\Models\Economia\ActivoFijo;
use App\Models\Economia\AlternativaInversion;
use App\Models\Economia\FlujoEfectivo;
use App\Models\Empresa;
use Illuminate\Database\Eloquent\Model;

class Evidencia extends Model
{
    protected $table = 'evidencias';
    protected $guarded = ['id'];

    protected $casts = [
        'fecha_documento' => 'date',
        'verificada' => 'boolean',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function asientosContables()
    {
        return $this->hasMany(AsientoContable::class, 'referencia_documento', 'codigo');
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