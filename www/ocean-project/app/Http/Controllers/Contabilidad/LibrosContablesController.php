<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\Contabilidad\AsientoContable;
use App\Models\Contabilidad\CuentaContable;
use App\Models\Contabilidad\DetalleAsiento;
use App\Models\Contabilidad\PeriodoContable;
use App\Models\Empresa;
use Illuminate\Http\Request;

class LibrosContablesController extends Controller
{
    public function index(Request $request)
    {
        return $this->diario($request);
    }

    public function diario(Request $request)
    {
        $empresa = Empresa::first();
        $periodos = PeriodoContable::where('empresa_id', $empresa->id)->orderByDesc('fecha_inicio')->get();
        $cuentas = CuentaContable::where('empresa_id', $empresa->id)->activas()->orderBy('codigo')->get();

        $periodoId = $request->get('periodo_id');
        $cuentaId = $request->get('cuenta_id');

        $queryDiario = AsientoContable::with(['periodoContable', 'detalles.cuentaContable'])
            ->where('empresa_id', $empresa->id)
            ->where('estado', 'contabilizado');

        if ($periodoId) {
            $queryDiario->where('periodo_contable_id', $periodoId);
        }

        if ($cuentaId) {
            $queryDiario->whereHas('detalles', fn($q) => $q->where('cuenta_contable_id', $cuentaId));
        }

        $asientosDiario = $queryDiario->orderBy('fecha')->orderBy('id')->paginate(15)->withQueryString();

        return view('contabilidad.libros.diario', compact(
            'empresa',
            'periodos',
            'cuentas',
            'periodoId',
            'cuentaId',
            'asientosDiario'
        ));
    }

    public function mayor(Request $request)
    {
        $empresa = Empresa::first();
        $periodos = PeriodoContable::where('empresa_id', $empresa->id)->orderByDesc('fecha_inicio')->get();
        $cuentas = CuentaContable::where('empresa_id', $empresa->id)->activas()->orderBy('codigo')->get();

        $periodoId = $request->get('periodo_id');
        $cuentaId = $request->get('cuenta_id');

        $cuentaSeleccionada = $cuentaId
            ? $cuentas->firstWhere('id', (int) $cuentaId)
            : $cuentas->firstWhere('permite_movimientos', true);

        $movimientosMayor = collect();
        $saldoInicialCuenta = 0.0;

        if ($cuentaSeleccionada) {
            $queryMov = DetalleAsiento::with(['asientoContable.periodoContable', 'cuentaContable'])
                ->where('cuenta_contable_id', $cuentaSeleccionada->id)
                ->whereHas('asientoContable', function ($q) use ($empresa, $periodoId) {
                    $q->where('empresa_id', $empresa->id)
                      ->where('estado', 'contabilizado');

                    if ($periodoId) {
                        $q->where('periodo_contable_id', $periodoId);
                    }
                });

            $movimientosMayor = $queryMov->get()->sortBy(function ($det) {
                return ($det->asientoContable->fecha?->format('Y-m-d') ?? '') . '-' . str_pad((string) $det->asiento_contable_id, 8, '0', STR_PAD_LEFT) . '-' . str_pad((string) $det->id, 8, '0', STR_PAD_LEFT);
            })->values();
        }

        return view('contabilidad.libros.mayor', compact(
            'empresa',
            'periodos',
            'cuentas',
            'periodoId',
            'cuentaId',
            'cuentaSeleccionada',
            'movimientosMayor',
            'saldoInicialCuenta'
        ));
    }

    public function balanceComprobacion(Request $request)
    {
        $empresa = Empresa::first();
        $periodos = PeriodoContable::where('empresa_id', $empresa->id)->orderByDesc('fecha_inicio')->get();
        $periodoId = $request->get('periodo_id');

        $cuentas = CuentaContable::where('empresa_id', $empresa->id)
            ->activas()
            ->deMovimiento()
            ->orderBy('codigo')
            ->get();

        $detalles = DetalleAsiento::with('cuentaContable')
            ->whereHas('asientoContable', function ($q) use ($empresa, $periodoId) {
                $q->where('empresa_id', $empresa->id)
                  ->where('estado', 'contabilizado');

                if ($periodoId) {
                    $q->where('periodo_contable_id', $periodoId);
                }
            })
            ->get()
            ->groupBy('cuenta_contable_id');

        $filas = $cuentas->map(function ($cta) use ($detalles) {
            $movs = $detalles->get($cta->id, collect());
            $debe = round((float) $movs->sum('debe'), 2);
            $haber = round((float) $movs->sum('haber'), 2);
            $esDeudora = $cta->naturaleza === 'deudora';

            $saldoDeudor = 0.0;
            $saldoAcreedor = 0.0;

            if ($esDeudora) {
                $diff = round($debe - $haber, 2);
                if ($diff >= 0) {
                    $saldoDeudor = $diff;
                } else {
                    $saldoAcreedor = abs($diff);
                }
            } else {
                $diff = round($haber - $debe, 2);
                if ($diff >= 0) {
                    $saldoAcreedor = $diff;
                } else {
                    $saldoDeudor = abs($diff);
                }
            }

            return [
                'cuenta' => $cta,
                'debe' => $debe,
                'haber' => $haber,
                'saldo_deudor' => $saldoDeudor,
                'saldo_acreedor' => $saldoAcreedor,
            ];
        })->filter(fn($f) => $f['debe'] > 0 || $f['haber'] > 0)->values();

        $totales = [
            'debe' => round($filas->sum('debe'), 2),
            'haber' => round($filas->sum('haber'), 2),
            'saldo_deudor' => round($filas->sum('saldo_deudor'), 2),
            'saldo_acreedor' => round($filas->sum('saldo_acreedor'), 2),
        ];

        return view('contabilidad.libros.balance', compact(
            'empresa',
            'periodos',
            'periodoId',
            'filas',
            'totales'
        ));
    }
}