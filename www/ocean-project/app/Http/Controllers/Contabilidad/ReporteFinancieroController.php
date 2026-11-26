<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\Contabilidad\AsientoContable;
use App\Models\Contabilidad\CuentaContable;
use App\Models\Contabilidad\DetalleAsiento;
use App\Models\Contabilidad\PeriodoContable;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class ReporteFinancieroController extends Controller
{
    public function estadoResultados(Request $request)
    {
        $empresa = Empresa::first();
        $periodos = PeriodoContable::where('empresa_id', $empresa->id)->orderByDesc('fecha_inicio')->get();
        $periodoId = $request->get('periodo_id');

        $saldos = $this->calcularSaldosPorCuenta($empresa->id, $periodoId);

        $ingresos = $saldos->filter(fn($f) => $f['cuenta']->tipo === 'ingreso')->values();
        $costos = $saldos->filter(fn($f) => $f['cuenta']->tipo === 'costo')->values();
        $gastos = $saldos->filter(fn($f) => $f['cuenta']->tipo === 'gasto')->values();

        $totalIngresos = round($ingresos->sum('saldo_neto'), 2);
        $totalCostos = round($costos->sum('saldo_neto'), 2);
        $utilidadBruta = round($totalIngresos - $totalCostos, 2);
        $totalGastos = round($gastos->sum('saldo_neto'), 2);
        $utilidadNeta = round($utilidadBruta - $totalGastos, 2);

        return view('contabilidad.reportes.resultados', compact(
            'empresa',
            'periodos',
            'periodoId',
            'ingresos',
            'costos',
            'gastos',
            'totalIngresos',
            'totalCostos',
            'utilidadBruta',
            'totalGastos',
            'utilidadNeta'
        ));
    }

    public function balanceGeneral(Request $request)
    {
        $empresa = Empresa::first();
        $periodos = PeriodoContable::where('empresa_id', $empresa->id)->orderByDesc('fecha_inicio')->get();
        $periodoId = $request->get('periodo_id');

        $saldos = $this->calcularSaldosPorCuenta($empresa->id, $periodoId);

        $activos = $saldos->filter(fn($f) => $f['cuenta']->tipo === 'activo')->values();
        $pasivos = $saldos->filter(fn($f) => $f['cuenta']->tipo === 'pasivo')->values();
        $patrimonio = $saldos->filter(fn($f) => $f['cuenta']->tipo === 'patrimonio')->values();

        $totalActivos = round($activos->sum('saldo_neto'), 2);
        $totalPasivos = round($pasivos->sum('saldo_neto'), 2);
        $totalPatrimonio = round($patrimonio->sum('saldo_neto'), 2);
        $totalPasivoPatrimonio = round($totalPasivos + $totalPatrimonio, 2);

        return view('contabilidad.reportes.balance', compact(
            'empresa',
            'periodos',
            'periodoId',
            'activos',
            'pasivos',
            'patrimonio',
            'totalActivos',
            'totalPasivos',
            'totalPatrimonio',
            'totalPasivoPatrimonio'
        ));
    }

    public function cambiosPatrimonio(Request $request)
    {
        $empresa = Empresa::first();
        $periodos = PeriodoContable::where('empresa_id', $empresa->id)->orderByDesc('fecha_inicio')->get();
        $periodoId = $request->get('periodo_id');

        $cuentasPatrimonio = CuentaContable::where('empresa_id', $empresa->id)
            ->activas()
            ->deMovimiento()
            ->where('tipo', 'patrimonio')
            ->orderBy('codigo')
            ->get();

        $detalles = DetalleAsiento::with('asientoContable')
            ->whereIn('cuenta_contable_id', $cuentasPatrimonio->pluck('id'))
            ->whereHas('asientoContable', function ($q) use ($empresa, $periodoId) {
                $q->where('empresa_id', $empresa->id)
                  ->where('estado', 'contabilizado');

                if ($periodoId) {
                    $q->where('periodo_contable_id', $periodoId);
                }
            })
            ->get()
            ->groupBy('cuenta_contable_id');

        $filas = $cuentasPatrimonio->map(function ($cta) use ($detalles) {
            $movs = $detalles->get($cta->id, collect());
            $apertura = round((float) $movs->filter(fn($m) => ($m->asientoContable->tipo ?? '') === 'apertura')
                ->sum(fn($m) => ($m->haber ?? 0) - ($m->debe ?? 0)), 2);

            $movimientos = round((float) $movs->filter(fn($m) => ($m->asientoContable->tipo ?? '') !== 'apertura')
                ->sum(fn($m) => ($m->haber ?? 0) - ($m->debe ?? 0)), 2);

            $saldoFinal = round($apertura + $movimientos, 2);

            return [
                'cuenta' => $cta,
                'apertura' => $apertura,
                'movimientos' => $movimientos,
                'saldo_final' => $saldoFinal,
            ];
        })->filter(fn($f) => $f['apertura'] != 0 || $f['movimientos'] != 0 || $f['saldo_final'] != 0)->values();

        $totales = [
            'apertura' => round($filas->sum('apertura'), 2),
            'movimientos' => round($filas->sum('movimientos'), 2),
            'saldo_final' => round($filas->sum('saldo_final'), 2),
        ];

        return view('contabilidad.reportes.patrimonio', compact(
            'empresa',
            'periodos',
            'periodoId',
            'filas',
            'totales'
        ));
    }

    public function flujoEfectivo(Request $request)
    {
        $empresa = Empresa::first();
        $periodos = PeriodoContable::where('empresa_id', $empresa->id)->orderByDesc('fecha_inicio')->get();
        $periodoId = $request->get('periodo_id');

        $asientosEfectivo = AsientoContable::with(['periodoContable', 'detalles.cuentaContable'])
            ->where('empresa_id', $empresa->id)
            ->where('estado', 'contabilizado')
            ->when($periodoId, fn($q) => $q->where('periodo_contable_id', $periodoId))
            ->whereHas('detalles.cuentaContable', function ($q) {
                $q->where('codigo', 'like', '1101%')
                  ->orWhere('codigo', 'like', '1102%')
                  ->orWhere('nombre', 'ilike', '%caja%')
                  ->orWhere('nombre', 'ilike', '%banco%');
            })
            ->orderBy('fecha')
            ->orderBy('id')
            ->get();

        $movimientosEfectivo = collect();
        $totalEntradas = 0.0;
        $totalSalidas = 0.0;

        foreach ($asientosEfectivo as $asiento) {
            foreach ($asiento->detalles as $det) {
                $cta = $det->cuentaContable;
                $esEfectivo = $cta && (
                    str_starts_with((string) $cta->codigo, '1101') ||
                    str_starts_with((string) $cta->codigo, '1102') ||
                    stripos((string) $cta->nombre, 'caja') !== false ||
                    stripos((string) $cta->nombre, 'banco') !== false
                );

                if (!$esEfectivo) {
                    continue;
                }

                $entrada = round((float) $det->debe, 2);
                $salida = round((float) $det->haber, 2);
                $totalEntradas += $entrada;
                $totalSalidas += $salida;

                $movimientosEfectivo->push([
                    'fecha' => $asiento->fecha,
                    'asiento' => $asiento,
                    'cuenta' => $cta,
                    'concepto' => $det->descripcion ?: $asiento->concepto,
                    'entrada' => $entrada,
                    'salida' => $salida,
                ]);
            }
        }

        $totalEntradas = round($totalEntradas, 2);
        $totalSalidas = round($totalSalidas, 2);
        $flujoNeto = round($totalEntradas - $totalSalidas, 2);

        return view('contabilidad.reportes.flujo', compact(
            'empresa',
            'periodos',
            'periodoId',
            'movimientosEfectivo',
            'totalEntradas',
            'totalSalidas',
            'flujoNeto'
        ));
    }

    private function calcularSaldosPorCuenta(int $empresaId, ?string $periodoId): Collection
    {
        $cuentas = CuentaContable::where('empresa_id', $empresaId)
            ->activas()
            ->deMovimiento()
            ->orderBy('codigo')
            ->get();

        $detalles = DetalleAsiento::whereHas('asientoContable', function ($q) use ($empresaId, $periodoId) {
                $q->where('empresa_id', $empresaId)
                  ->where('estado', 'contabilizado');

                if ($periodoId) {
                    $q->where('periodo_contable_id', $periodoId);
                }
            })
            ->get()
            ->groupBy('cuenta_contable_id');

        return $cuentas->map(function ($cta) use ($detalles) {
            $movs = $detalles->get($cta->id, collect());
            $debe = round((float) $movs->sum('debe'), 2);
            $haber = round((float) $movs->sum('haber'), 2);

            $saldoNeto = $cta->naturaleza === 'deudora'
                ? round($debe - $haber, 2)
                : round($haber - $debe, 2);

            return [
                'cuenta' => $cta,
                'debe' => $debe,
                'haber' => $haber,
                'saldo_neto' => $saldoNeto,
            ];
        })->filter(fn($f) => $f['debe'] > 0 || $f['haber'] > 0)->values();
    }
}