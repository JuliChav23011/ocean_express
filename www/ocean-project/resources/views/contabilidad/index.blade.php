@extends('layouts.app')

@section('title', 'Contabilidad - Ocean Express')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
        <div>
            <h1 class="page-title">Módulo de Contabilidad</h1>
            <p class="page-subtitle mb-0">
                {{ $empresa->nombre ?? 'Ocean Express' }} · NIT: {{ $empresa->nit ?? '1123-220994-101-5' }}
                @if($periodoActivo)
                    · Período activo: <strong>{{ $periodoActivo->nombre }}</strong>
                    <span class="badge {{ $periodoActivo->estaAbierto() ? 'bg-success' : 'bg-secondary' }}">
                        {{ strtoupper($periodoActivo->estado) }}
                    </span>
                @endif
            </p>
        </div>
        <a href="{{ route('contabilidad.asientos.create') }}" class="btn btn-ocean">
            <i class="bi bi-plus-circle me-1"></i> Nuevo Asiento
        </a>
    </div>

    @include('contabilidad.partials.nav')

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="ocean-card h-100">
                <span class="text-muted small text-uppercase fw-bold">Catálogo de Cuentas</span>
                <h2 class="mt-2 mb-1 fw-bold text-primary">{{ $cuentasActivas }} <small class="fs-6 text-muted">/ {{ $totalCuentas }}</small></h2>
                <p class="mb-0">Cuentas activas disponibles</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ocean-card h-100">
                <span class="text-muted small text-uppercase fw-bold">Asientos Contabilizados</span>
                <h2 class="mt-2 mb-1 fw-bold" style="color: var(--ocean-dark);">{{ $totalAsientos }}</h2>
                <p class="mb-0">Partidas validadas en libro</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ocean-card h-100">
                <span class="text-muted small text-uppercase fw-bold">Total Activos</span>
                <h2 class="mt-2 mb-1 fw-bold text-success">${{ number_format($activos, 2) }}</h2>
                <p class="mb-0">Pasivo + Pat.: ${{ number_format($pasivos + $patrimonio + $utilidad, 2) }}</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ocean-card h-100">
                <span class="text-muted small text-uppercase fw-bold">Utilidad Neta Período</span>
                <h2 class="mt-2 mb-1 fw-bold {{ $utilidad >= 0 ? 'text-primary' : 'text-danger' }}">
                    ${{ number_format($utilidad, 2) }}
                </h2>
                <p class="mb-0">Resultado operativo acumulado</p>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="ocean-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Últimos Asientos Contables</h3>
                    <a href="{{ route('contabilidad.asientos.index') }}" class="btn btn-sm btn-ocean-outline">Ver todos</a>
                </div>
                <div class="table-responsive">
                    <table class="table ocean-table mb-0">
                        <thead>
                            <tr>
                                <th>N° Asiento</th>
                                <th>Fecha</th>
                                <th>Concepto</th>
                                <th class="text-end">Total</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ultimosAsientos as $asiento)
                                <tr>
                                    <td class="fw-bold">
                                        <a href="{{ route('contabilidad.asientos.show', $asiento) }}" class="text-decoration-none">
                                            {{ $asiento->numero_asiento }}
                                        </a>
                                    </td>
                                    <td>{{ $asiento->fecha->format('d/m/Y') }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($asiento->concepto, 55) }}</td>
                                    <td class="text-end fw-semibold">${{ number_format($asiento->total_debe, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $asiento->estado === 'contabilizado' ? 'bg-success' : ($asiento->estado === 'anulado' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                            {{ ucfirst($asiento->estado) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No hay asientos registrados aún.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ocean-card h-100">
                <h3><i class="bi bi-grid-check me-2 text-primary"></i>Accesos Rápidos</h3>
                <div class="list-group list-group-flush mt-3">
                    <a href="{{ route('contabilidad.libros.diario') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-journal-bookmark me-2 text-primary"></i>Libro Diario</span>
                        <i class="bi bi-chevron-right small"></i>
                    </a>
                    <a href="{{ route('contabilidad.libros.mayor') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-journal-check me-2 text-primary"></i>Libro Mayor (Cuentas T)</span>
                        <i class="bi bi-chevron-right small"></i>
                    </a>
                    <a href="{{ route('contabilidad.libros.balance') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-scale me-2 text-primary"></i>Balance de Comprobación</span>
                        <i class="bi bi-chevron-right small"></i>
                    </a>
                    <a href="{{ route('contabilidad.reportes.resultados') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-graph-up me-2 text-success"></i>Estado de Resultados</span>
                        <i class="bi bi-chevron-right small"></i>
                    </a>
                    <a href="{{ route('contabilidad.reportes.balance') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-bank me-2 text-success"></i>Balance General</span>
                        <i class="bi bi-chevron-right small"></i>
                    </a>
                    <a href="{{ route('contabilidad.reportes.patrimonio') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-pie-chart me-2 text-info"></i>Cambios en el Patrimonio</span>
                        <i class="bi bi-chevron-right small"></i>
                    </a>
                    <a href="{{ route('contabilidad.reportes.flujo') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-cash-coin me-2 text-info"></i>Estado de Flujos de Efectivo</span>
                        <i class="bi bi-chevron-right small"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection