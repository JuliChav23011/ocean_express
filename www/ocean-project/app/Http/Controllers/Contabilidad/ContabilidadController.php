<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\Contabilidad\AsientoContable;
use App\Models\Contabilidad\CuentaContable;
use App\Models\Contabilidad\DetalleAsiento;
use App\Models\Contabilidad\PeriodoContable;
use App\Models\Empresa;

class ContabilidadController extends Controller
{
    public function index()
    {
        $empresa = Empresa::first();
        $periodoActivo = PeriodoContable::where('empresa_id', $empresa?->id)
            ->orderByDesc('fecha_inicio')
            ->first();

        $totalCuentas = CuentaContable::where('empresa_id', $empresa?->id)->count();
        $cuentasActivas = CuentaContable::where('empresa_id', $empresa?->id)->where('activa', true)->count();
        $totalAsientos = AsientoContable::where('empresa_id', $empresa?->id)
            ->where('estado', 'contabilizado')
            ->count();

        $movimientos = DetalleAsiento::selectRaw('cuentas_contables.tipo, SUM(detalles_asientos.debe) as total_debe, SUM(detalles_asientos.haber) as total_haber')
            ->join('asientos_contables', 'detalles_asientos.asiento_contable_id', '=', 'asientos_contables.id')
            ->join('cuentas_contables', 'detalles_asientos.cuenta_contable_id', '=', 'cuentas_contables.id')
            ->where('asientos_contables.estado', 'contabilizado')
            ->when($periodoActivo, fn($q) => $q->where('asientos_contables.periodo_contable_id', $periodoActivo->id))
            ->groupBy('cuentas_contables.tipo')
            ->get()
            ->keyBy('tipo');

        $activos = ($movimientos['activo']->total_debe ?? 0) - ($movimientos['activo']->total_haber ?? 0);
        $pasivos = ($movimientos['pasivo']->total_haber ?? 0) - ($movimientos['pasivo']->total_debe ?? 0);
        $patrimonio = ($movimientos['patrimonio']->total_haber ?? 0) - ($movimientos['patrimonio']->total_debe ?? 0);
        $ingresos = ($movimientos['ingreso']->total_haber ?? 0) - ($movimientos['ingreso']->total_debe ?? 0);
        $costos = ($movimientos['costo']->total_debe ?? 0) - ($movimientos['costo']->total_haber ?? 0);
        $gastos = ($movimientos['gasto']->total_debe ?? 0) - ($movimientos['gasto']->total_haber ?? 0);
        $utilidad = $ingresos - ($costos + $gastos);

        $ultimosAsientos = AsientoContable::with(['periodoContable', 'detalles'])
            ->where('empresa_id', $empresa?->id)
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        return view('contabilidad.index', compact(
            'empresa', 'periodoActivo', 'totalCuentas', 'cuentasActivas',
            'totalAsientos', 'activos', 'pasivos', 'patrimonio', 'utilidad', 'ultimosAsientos'
        ));
    }
}