<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AsientoContable extends Model
{
    protected $table = 'asientos_contables';

    protected $guarded = ['id'];

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

}
