<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CostoServicio extends Model
{
    protected $table = 'costos_servicio';

    protected $guarded = ['id'];

    public function ordenServicio()
    {
        return $this->belongsTo(OrdenServicio::class, 'orden_servicio_id');
    }

    public function evidencia()
    {
        return $this->belongsTo(Evidencia::class, 'evidencia_id');
    }

}
