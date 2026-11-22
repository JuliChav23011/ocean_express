<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstudioInversion extends Model
{
    protected $table = 'estudios_inversion';

    protected $guarded = ['id'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function alternativas()
    {
        return $this->hasMany(AlternativaInversion::class, 'estudio_inversion_id');
    }

}
