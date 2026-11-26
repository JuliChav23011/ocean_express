
@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">

    <!-- ENCABEZADO -->
    <div class="mb-4">
        <h2 class="fw-bold text-dark">
            Panel general
        </h2>

        <p class="text-muted mb-0">
            Visión general de Ocean Express
        </p>
    </div>


    <!-- INDICADORES GENERALES -->
    <div class="row g-3 mb-4">

        <!-- Órdenes en proceso -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">

                    <p class="text-muted mb-2">
                        Órdenes en proceso
                    </p>

                    <h3 class="fw-bold mb-0">
                        {{ $ordenesEnProceso }}
                    </h3>

                    <small class="text-muted">
                        Órdenes actualmente en proceso
                    </small>

                </div>
            </div>
        </div>


        <!-- Evaluaciones realizadas -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">

                    <p class="text-muted mb-2">
                        Evaluaciones realizadas
                    </p>

                    <h3 class="fw-bold mb-0">
                        {{ $evaluacionesRealizadas ?? '--' }}
                    </h3>

                    <small class="text-muted">
                        Evaluaciones económicas registradas
                    </small>

                </div>
            </div>
        </div>


        <!-- Última actualización -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">

                    <p class="text-muted mb-2">
                        Última actualización
                    </p>

                    <h5 class="fw-bold mb-0">

                        @if ($ultimaActualizacion)

                            {{ \Carbon\Carbon::parse(
                                $ultimaActualizacion
                            )->format('d/m/Y h:i A') }}

                        @else
                            Sin registros
                        @endif

                    </h5>

                    <small class="text-muted">
                        Último movimiento registrado
                    </small>

                </div>
            </div>
        </div>

    </div>


    <!-- GRÁFICOS PRINCIPALES -->
    <div class="row g-3 mb-4">

        <!-- Gráfico financiero -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">

                    <h5 class="fw-semibold mb-1">
                        Ingresos y costos
                    </h5>

                    <p class="text-muted small mb-4">
                        Resumen mensual de órdenes finalizadas
                    </p>

                    @if (count($graficoFinanciero['meses']) > 0)

                        <div class="dashboard-chart-container">
                            <canvas id="graficoFinanciero"></canvas>
                        </div>

                        @if (!$graficoFinanciero['costosDisponibles'])
                            <small class="text-muted">
                                Los costos aún no están disponibles
                                para este resumen.
                            </small>
                        @endif

                    @else

                        <div class="dashboard-chart-placeholder">
                            <i class="bi bi-bar-chart fs-1 text-secondary"></i>

                            <p class="text-muted mt-3 mb-0">
                                No hay información financiera disponible.
                            </p>
                        </div>

                    @endif

                </div>
            </div>
        </div>


        <!-- Gráfico de órdenes -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">

                    <h5 class="fw-semibold mb-1">
                        Órdenes por estado
                    </h5>

                    <p class="text-muted small mb-4">
                        Distribución de órdenes de servicio
                    </p>

                    @if (
                        $ordenesPendientes +
                        $ordenesEnProceso +
                        $ordenesFinalizadas +
                        $ordenesCanceladas > 0
                    )

                        <div class="dashboard-chart-container">
                            <canvas id="graficoOrdenes"></canvas>
                        </div>

                    @else

                        <div class="dashboard-chart-placeholder">
                            <i class="bi bi-pie-chart fs-1 text-secondary"></i>

                            <p class="text-muted mt-3 mb-0">
                                No hay órdenes registradas.
                            </p>
                        </div>

                    @endif

                </div>
            </div>
        </div>

    </div>


    <!-- SECCIÓN INFERIOR -->
    <div class="row g-3 mb-4">

        <!-- Ingeniería económica -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">

                    <h5 class="fw-semibold mb-2">
                        Ingeniería económica
                    </h5>

                    <p class="text-muted small mb-4">
                        Estado de las evaluaciones financieras
                    </p>

                    @if ($resumenEconomico['disponible'])

                        @php
                            $totalEconomico =
                                $resumenEconomico['viables'] +
                                $resumenEconomico['enAnalisis'] +
                                $resumenEconomico['noViables'];

                            $porcentajeViables = $totalEconomico > 0
                                ? $resumenEconomico['viables'] / $totalEconomico * 100
                                : 0;

                            $porcentajeAnalisis = $totalEconomico > 0
                                ? $resumenEconomico['enAnalisis'] / $totalEconomico * 100
                                : 0;

                            $porcentajeNoViables = $totalEconomico > 0
                                ? $resumenEconomico['noViables'] / $totalEconomico * 100
                                : 0;
                        @endphp

                        <div class="d-flex justify-content-between mb-2">
                            <span>Evaluaciones viables</span>
                            <span>{{ $resumenEconomico['viables'] }}</span>
                        </div>

                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar bg-success"
                                 style="width: {{ $porcentajeViables }}%;">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>En análisis</span>
                            <span>{{ $resumenEconomico['enAnalisis'] }}</span>
                        </div>

                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar bg-info"
                                 style="width: {{ $porcentajeAnalisis }}%;">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>No viables</span>
                            <span>{{ $resumenEconomico['noViables'] }}</span>
                        </div>

                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-secondary"
                                 style="width: {{ $porcentajeNoViables }}%;">
                            </div>
                        </div>

                    @else

                        <div class="dashboard-empty-state">
                            <i class="bi bi-graph-up-arrow fs-2 text-secondary"></i>

                            <p class="text-muted mt-3 mb-0">
                                Clasificación de evaluaciones
                                pendiente de integración.
                            </p>
                        </div>

                    @endif

                </div>
            </div>
        </div>


        <!-- Actividad reciente -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">

                    <h5 class="fw-semibold mb-2">
                        Actividad reciente
                    </h5>

                    <p class="text-muted small mb-4">
                        Últimos movimientos registrados en el sistema
                    </p>

                    @forelse ($actividadReciente as $actividad)

                        <div class="d-flex justify-content-between
                                    align-items-center border-bottom py-3">

                            <div class="d-flex align-items-center gap-3">

                                <i class="bi bi-clock-history text-primary"></i>

                                <span>
                                    {{ $actividad['modulo'] }}
                                </span>

                            </div>

                            <small class="text-muted">
                                {{ \Carbon\Carbon::parse(
                                    $actividad['fecha']
                                )->format('d/m/Y h:i A') }}
                            </small>

                        </div>

                    @empty

                        <div class="dashboard-empty-state">

                            <i class="bi bi-clock-history fs-2 text-secondary"></i>

                            <p class="text-muted mt-3 mb-0">
                                No hay actividad para mostrar.
                            </p>

                        </div>

                    @endforelse

                </div>
            </div>
        </div>

    </div>

</div>

@endsection


<!-- CHART.JS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // GRÁFICO FINANCIERO

    const financiero = document.getElementById('graficoFinanciero');

    if (financiero && typeof Chart !== 'undefined') {

        const datasets = [
            {
                label: 'Ingresos',
                data: @json($graficoFinanciero['ingresos']),
                backgroundColor: '#2563eb',
                borderRadius: 5
            }
        ];

        if (@json($graficoFinanciero['costosDisponibles'])) {
            datasets.push({
                label: 'Costos',
                data: @json($graficoFinanciero['costos']),
                backgroundColor: '#38bdf8',
                borderRadius: 5
            });
        }

        new Chart(financiero, {
            type: 'bar',

            data: {
                labels: @json($graficoFinanciero['meses']),
                datasets: datasets
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                scales: {
                    y: {
                        beginAtZero: true
                    }
                },

                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }


    // GRÁFICO DE ÓRDENES

    const ordenes = document.getElementById('graficoOrdenes');

    if (ordenes && typeof Chart !== 'undefined') {

        new Chart(ordenes, {
            type: 'doughnut',

            data: {
                labels: [
                    'Pendientes',
                    'En proceso',
                    'Finalizadas',
                    'Canceladas'
                ],

                datasets: [{
                    data: [
                        {{ $ordenesPendientes }},
                        {{ $ordenesEnProceso }},
                        {{ $ordenesFinalizadas }},
                        {{ $ordenesCanceladas }}
                    ],

                    backgroundColor: [
                        '#f59e0b',
                        '#38bdf8',
                        '#10b981',
                        '#94a3b8'
                    ],

                    borderWidth: 0
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',

                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

});
</script>
