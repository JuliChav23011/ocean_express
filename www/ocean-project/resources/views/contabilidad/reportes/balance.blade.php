@extends('layouts.app')

@section('title', 'Balance General - Ocean Express')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
        <div>
            <h1 class="page-title">Balance General</h1>
            <p class="page-subtitle mb-0">Situacion financiera: Activos, Pasivos y Patrimonio.</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('contabilidad.reportes.resultados', request()->query()) }}" class="btn btn-outline-primary">Resultados</a>
            <a href="{{ route('contabilidad.reportes.balance', request()->query()) }}" class="btn btn-ocean">Balance General</a>
            <a href="{{ route('contabilidad.reportes.patrimonio', request()->query()) }}" class="btn btn-outline-primary">Patrimonio</a>
            <a href="{{ route('contabilidad.reportes.flujo', request()->query()) }}" class="btn btn-outline-primary">Flujo de Efectivo</a>
        </div>
    </div>

    @include('contabilidad.partials.nav')

    <div class="ocean-card mb-4">
        <form method="GET" action="{{ route('contabilidad.reportes.balance') }}" class="row g-3 align-items-end">
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
                <a href="{{ route('contabilidad.reportes.balance') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="ocean-card">
                <small class="text-muted d-block">Total Activos</small>
                <div class="fs-4 fw-bold font-monospace text-primary">${{ number_format($totalActivos, 2) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ocean-card">
                <small class="text-muted d-block">Total Pasivos</small>
                <div class="fs-4 fw-bold font-monospace">${{ number_format($totalPasivos, 2) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="ocean-card">
                <small class="text-muted d-block">Pasivo + Patrimonio</small>
                <div class="fs-4 fw-bold font-monospace">${{ number_format($totalPasivoPatrimonio, 2) }}</div>
            </div>
        </div>
    </div>

    @php
        $lineasBalance = collect()
            ->concat($activos->map(fn($f) => ['tipo' => 'Activo', 'cuenta' => $f['cuenta'], 'saldo' => $f['saldo_neto']]))
            ->concat($pasivos->map(fn($f) => ['tipo' => 'Pasivo', 'cuenta' => $f['cuenta'], 'saldo' => $f['saldo_neto']]))
            ->concat($patrimonio->map(fn($f) => ['tipo' => 'Patrimonio', 'cuenta' => $f['cuenta'], 'saldo' => $f['saldo_neto']]));
    @endphp

    <div class="ocean-card p-0 overflow-hidden">
        <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
            <table class="table ocean-table table-hover mb-0">
                <thead class="position-sticky top-0" style="z-index: 2;">
                    <tr>
                        <th>Clasificacion</th>
                        <th>Codigo</th>
                        <th>Cuenta</th>
                        <th class="text-end">Saldo ($)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lineasBalance as $item)
                        <tr>
                            <td><span class="badge bg-light text-dark border">{{ $item['tipo'] }}</span></td>
                            <td class="font-monospace">{{ $item['cuenta']->codigo }}</td>
                            <td>{{ $item['cuenta']->nombre }}</td>
                            <td class="text-end font-monospace fw-semibold">${{ number_format($item['saldo'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No hay cuentas con saldo para este reporte.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="2" class="text-end">TOTAL ACTIVOS:</td>
                        <td class="text-end font-monospace">${{ number_format($totalActivos, 2) }}</td>
                        <td class="text-end font-monospace">P+Pat: ${{ number_format($totalPasivoPatrimonio, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection