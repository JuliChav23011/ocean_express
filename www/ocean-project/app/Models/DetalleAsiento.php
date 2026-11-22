<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleAsiento extends Model
{
    protected $table = 'detalles_asientos';

    protected $guarded = ['id'];

    public function asientoContable()
    {
        return $this->belongsTo(AsientoContable::class, 'asiento_contable_id');
    }

    public function cuentaContable()
    {
        return $this->belongsTo(CuentaContable::class, 'cuenta_contable_id');
    }

}
