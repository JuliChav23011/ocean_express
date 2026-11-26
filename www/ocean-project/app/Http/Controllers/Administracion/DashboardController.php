<?php

namespace App\Http\Controllers\Administracion;

use App\Http\Controllers\Controller;
use App\Models\Costos\OrdenServicio;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // 1. ÓRDENES DE SERVICIO

        $ordenesPendientes = OrdenServicio::where(
            'estado', 'pendiente'
        )->count();

        $ordenesEnProceso = OrdenServicio::where(
            'estado', 'en_proceso'
        )->count();

        $ordenesFinalizadas = OrdenServicio::where(
            'estado', 'finalizada'
        )->count();

        $ordenesCanceladas = OrdenServicio::where(
            'estado', 'cancelada'
        )->count();


        // 2. EVALUACIONES ECONÓMICAS

        $evaluacionesRealizadas = Schema::hasTable(
            'evaluaciones_economicas'
        )
            ? DB::table('evaluaciones_economicas')->count()
            : null;


        // 3. ÚLTIMA ACTUALIZACIÓN

        $ultimaActualizacion = null;

        $tablas = [
            'ordenes_servicio',
            'asientos_contables',
            'evaluaciones_economicas'
        ];

        foreach ($tablas as $tabla) {

            if (
                !Schema::hasTable($tabla) ||
                !Schema::hasColumn($tabla, 'updated_at')
            ) {
                continue;
            }

            $fecha = DB::table($tabla)->max('updated_at');

            if (
                $fecha !== null &&
                (
                    $ultimaActualizacion === null ||
                    $fecha > $ultimaActualizacion
                )
            ) {
                $ultimaActualizacion = $fecha;
            }
        }


        // 4. GRÁFICO FINANCIERO

        $ingresos = collect();
        $costos = collect();
        $costosDisponibles = false;

        if (Schema::hasTable('ordenes_servicio')) {

            $ingresos = DB::table('ordenes_servicio')
                ->selectRaw(
                    "TO_CHAR(fecha, 'YYYY-MM') AS mes"
                )
                ->selectRaw(
                    'SUM(ingreso_total) AS total'
                )
                ->where('estado', 'finalizada')
                ->groupByRaw(
                    "TO_CHAR(fecha, 'YYYY-MM')"
                )
                ->pluck('total', 'mes');
        }

        if (
            Schema::hasTable('costos_servicio') &&
            Schema::hasColumn(
                'costos_servicio',
                'orden_servicio_id'
            ) &&
            Schema::hasColumn(
                'costos_servicio',
                'costo_total'
            )
        ) {
            $costosDisponibles = true;

            $costos = DB::table('costos_servicio as cs')
                ->join(
                    'ordenes_servicio as os',
                    'cs.orden_servicio_id',
                    '=',
                    'os.id'
                )
                ->selectRaw(
                    "TO_CHAR(os.fecha, 'YYYY-MM') AS mes"
                )
                ->selectRaw(
                    'SUM(cs.costo_total) AS total'
                )
                ->where('os.estado', 'finalizada')
                ->groupByRaw(
                    "TO_CHAR(os.fecha, 'YYYY-MM')"
                )
                ->pluck('total', 'mes');
        }

        $meses = $ingresos->keys()
            ->merge($costos->keys())
            ->unique()
            ->sort()
            ->values();

        $graficoFinanciero = [
            'meses' => $meses->all(),

            'ingresos' => $meses->map(
                fn ($mes) => (float) ($ingresos[$mes] ?? 0)
            )->all(),

            'costos' => $meses->map(
                fn ($mes) => (float) ($costos[$mes] ?? 0)
            )->all(),

            'costosDisponibles' => $costosDisponibles
        ];


        // 5. INGENIERÍA ECONÓMICA

        // No suponemos estados que todavía no
        // hemos confirmado en su migración.

        $resumenEconomico = [
            'disponible' => false,
            'viables' => null,
            'enAnalisis' => null,
            'noViables' => null
        ];


        // 6. ACTIVIDAD RECIENTE

        $actividadReciente = collect();

        foreach ($tablas as $tabla) {

            if (
                !Schema::hasTable($tabla) ||
                !Schema::hasColumn($tabla, 'updated_at')
            ) {
                continue;
            }

            $registros = DB::table($tabla)
                ->whereNotNull('updated_at')
                ->select('updated_at')
                ->orderByDesc('updated_at')
                ->limit(3)
                ->get();

            $nombreModulo = match ($tabla) {
                'ordenes_servicio' => 'Orden de servicio',
                'asientos_contables' => 'Asiento contable',
                'evaluaciones_economicas' => 'Evaluación económica',
                default => 'Registro'
            };

            foreach ($registros as $registro) {

                $actividadReciente->push([
                    'modulo' => $nombreModulo,
                    'fecha' => $registro->updated_at
                ]);
            }
        }

        $actividadReciente = $actividadReciente
            ->sortByDesc('fecha')
            ->take(5)
            ->values();


        // 7. ENVIAR DATOS A LA VISTA

        return view('dashboard.index', compact(
            'ordenesPendientes',
            'ordenesEnProceso',
            'ordenesFinalizadas',
            'ordenesCanceladas',
            'evaluacionesRealizadas',
            'ultimaActualizacion',
            'graficoFinanciero',
            'resumenEconomico',
            'actividadReciente'
        ));
    }
}
