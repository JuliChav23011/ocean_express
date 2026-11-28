<?php

namespace App\Models\Economia;

use Illuminate\Database\Eloquent\Model;

class DepreciacionActivo extends Model
{
    protected $table = 'depreciaciones_activos';

    protected $guarded = ['id'];

    public function activoFijo()
    {
        return $this->belongsTo(ActivoFijo::class, 'activo_fijo_id');
    }

    public function periodoContable()
    {
        return $this->belongsTo(PeriodoContable::class, 'periodo_contable_id');
    }

}
