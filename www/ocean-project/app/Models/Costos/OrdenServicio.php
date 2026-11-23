<?php

namespace App\Models\Costos;

use Illuminate\Database\Eloquent\Model;

class OrdenServicio extends Model
{
    protected $table = 'ordenes_servicio';

    protected $guarded = ['id'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function costos()
    {
        return $this->hasMany(CostoServicio::class, 'orden_servicio_id');
    }

}
