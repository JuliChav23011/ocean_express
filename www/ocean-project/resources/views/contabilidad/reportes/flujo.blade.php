@extends('layouts.app')

@section('title', 'Flujo de Efectivo - Ocean Express')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
        <div>
            <h1 class="page-title">Flujo de Efectivo</h1>
            <p class="page-subtitle mb-0">Entradas y salidas registradas en cuentas de caja y bancos contabilizadas.</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('contabilidad.reportes.resultados', request()->query()) }}" class="btn btn-outline-primary">Resultados</a>
            <a href="{{ route('contabilidad.reportes.balance', request()->query()) }}" class="btn btn-outline-primary">Balance General</a>
            <a href="{{ route('contabilidad.reportes.patrimonio', request()->query()) }}" class="btn btn-outline-primary">Patrimonio</a>
            <a href="{{ route('contabilidad.reportes.flujo', request()->query()) }}" class="btn btn-ocean">Flujo de Efectivo</a>
        </div>
    </div>

    @include('contabilidad.partials.nav')

    <div class="ocean-card mb-4">
        <form method="GET" action="{{ route('contabilidad.reportes.flujo') }}" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Periodo Contable</label>
                <select name="periodo_id" class="form-select">
                    <option value="">Todos los periodos</option>
                    @foreach($periodos as $periodo)
                        <option value="{{ $periodo->id }}" {{ (string) $periodoId === (string) $periodo->id ? 'selected' : '' }}>
                            {{ $periodo->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-ocean w-100"><i class="bi bi-search me-1"></i> Filtrar</button>
                <a href="{{ route('contabilidad.reportes.flujo') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="ocean-card">
                <small class="text-muted d-block">Total Entradas</small>
                <div class="fs-4 fw-bold font-monospace text-success">${{ number_format($totalEntradas, 2) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ocean-card">
                <small class="text-muted d-block">Total Salidas</small>
                <div class="fs-4 fw-bold font-monospace text-danger">${{ number_format($totalSalidas, 2) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ocean-card">
                <small class="text-muted d-block">Flujo Neto de Efectivo</small>
                <div class="fs-4 fw-bold font-monospace {{ $flujoNeto >= 0 ? 'text-success' : 'text-danger' }}">
                    ${{ number_format($flujoNeto, 2) }}
                </div>
            </div>
        </div>
    </div>

    <div class="ocean-card p-0 overflow-hidden">
        <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
            <table class="table ocean-table table-hover mb-0">
                <thead class="position-sticky top-0" style="z-index: 2;">
                    <tr>
                        <th>Fecha</th>
                        <th>Asiento</th>
                        <th>Cuenta</th>
                        <th>Concepto</th>
                        <th class="text-end">Entrada ($)</th>
                        <th class="text-end">Salida ($)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($movimientosEfectivo as $mov)
                        <tr>
                            <td>{{ $mov['fecha']?->format('d/m/Y') }}</td>
                            <td class="font-monospace fw-bold">
                                <a href="{{ route('contabilidad.asientos.show', $mov['asiento']) }}" class="text-decoration-none">
                                    {{ $mov['asiento']->numero_asiento }}
                                </a>
                            </td>
                            <td>{{ $mov['cuenta']->codigo }} - {{ $mov['cuenta']->nombre }}</td>
                            <td>{{ $mov['concepto'] }}</td>
                            <td class="text-end font-monospace text-success">{{ $mov['entrada'] > 0 ? '$' . number_format($mov['entrada'], 2) : '-' }}</td>
                            <td class="text-end font-monospace text-danger">{{ $mov['salida'] > 0 ? '$' . number_format($mov['salida'], 2) : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No hay movimientos de efectivo contabilizados.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="4" class="text-end">TOTALES / FLUJO NETO:</td>
                        <td class="text-end font-monospace">${{ number_format($totalEntradas, 2) }}</td>
                        <td class="text-end font-monospace">${{ number_format($totalSalidas, 2) }} (${{ number_format($flujoNeto, 2) }})</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection