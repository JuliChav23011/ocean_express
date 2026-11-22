<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $table = 'empresas';

    protected $guarded = ['id'];

    public function periodosContables()
    {
        return $this->hasMany(PeriodoContable::class, 'empresa_id');
    }

    public function cuentasContables()
    {
        return $this->hasMany(CuentaContable::class, 'empresa_id');
    }

    public function asientosContables()
    {
        return $this->hasMany(AsientoContable::class, 'empresa_id');
    }

    public function ordenesServicio()
    {
        return $this->hasMany(OrdenServicio::class, 'empresa_id');
    }

    public function activosFijos()
    {
        return $this->hasMany(ActivoFijo::class, 'empresa_id');
    }

    public function estudiosInversion()
    {
        return $this->hasMany(EstudioInversion::class, 'empresa_id');
    }

    public function evidencias()
    {
        return $this->hasMany(Evidencia::class, 'empresa_id');
    }

}
