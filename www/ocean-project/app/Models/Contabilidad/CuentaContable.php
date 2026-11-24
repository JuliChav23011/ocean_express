<?php

namespace App\Models\Contabilidad;

use App\Models\Empresa;
use Illuminate\Database\Eloquent\Model;

class CuentaContable extends Model
{
    protected $table = 'cuentas_contables';
    protected $guarded = ['id'];

    protected $casts = [
        'permite_movimientos' => 'boolean',
        'activa' => 'boolean',
    ];

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

    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    public function scopeDeMovimiento($query)
    {
        return $query->where('permite_movimientos', true);
    }

    public function tieneMovimientos(): bool
    {
        return $this->detallesAsientos()->exists();
    }

    public function getNivelAttribute(): int
    {
        $nivel = 1;
        $padre = $this->cuentaPadre;
        while ($padre) {
            $nivel++;
            $padre = $padre->cuentaPadre;
        }
        return $nivel;
    }
}