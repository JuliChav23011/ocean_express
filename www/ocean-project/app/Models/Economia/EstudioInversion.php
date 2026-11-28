<?php

namespace App\Models\Economia;

use App\Models\Empresa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstudioInversion extends Model
{
    protected $table = 'estudios_inversion';

    protected $fillable = [
        'empresa_id',
        'codigo',
        'nombre',
        'descripcion',
        'objetivo',
        'fecha_estudio',
        'horizonte_anios',
        'tasa_descuento',
        'tasa_impuesto',
        'tasa_inflacion',
        'moneda',
        'estado',
        'supuestos',
        'observaciones',
    ];

    protected $casts = [
        'fecha_estudio' => 'date',
        'horizonte_anios' => 'integer',
        'tasa_descuento' => 'decimal:4',
        'tasa_impuesto' => 'decimal:4',
        'tasa_inflacion' => 'decimal:4',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(
            Empresa::class,
            'empresa_id'
        );
    }

    public function alternativas(): HasMany
    {
        return $this->hasMany(
            AlternativaInversion::class,
            'estudio_inversion_id'
        );
    }
}

