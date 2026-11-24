<?php

namespace App\Models\Contabilidad;

use App\Models\Economia\DepreciacionActivo;
use App\Models\Empresa;
use Illuminate\Database\Eloquent\Model;

class PeriodoContable extends Model
{
    protected $table = 'periodos_contables';
    protected $guarded = ['id'];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

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

    public function estaAbierto(): bool
    {
        return $this->estado === 'abierto';
    }

    public function contieneFecha(string $fecha): bool
    {
        return $fecha >= $this->fecha_inicio->format('Y-m-d')
            && $fecha <= $this->fecha_fin->format('Y-m-d');
    }
}