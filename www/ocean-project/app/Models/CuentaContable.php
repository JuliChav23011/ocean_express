<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CuentaContable extends Model
{
    protected $table = 'cuentas_contables';

    protected $guarded = ['id'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function cuentaPadre()
    {
        return $this->belongsTo(CuentaContable::class, 'cuenta_padre_id');
    }

    public function subcuentas()
    {
        return $this->hasMany(CuentaContable::class, 'cuenta_padre_id');
    }

    public function detallesAsientos()
    {
        return $this->hasMany(DetalleAsiento::class, 'cuenta_contable_id');
    }

}
