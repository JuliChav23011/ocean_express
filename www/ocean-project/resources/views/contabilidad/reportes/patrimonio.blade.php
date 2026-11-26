@extends('layouts.app')

@section('title', 'Cambios en el Patrimonio - Ocean Express')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
        <div>
            <h1 class="page-title">Estado de Cambios en el Patrimonio</h1>
            <p class="page-subtitle mb-0">Saldos iniciales, variaciones del periodo y saldo final patrimonial.</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('contabilidad.reportes.resultados', request()->query()) }}" class="btn btn-outline-primary">Resultados</a>
            <a href="{{ route('contabilidad.reportes.balance', request()->query()) }}" class="btn btn-outline-primary">Balance General</a>
            <a href="{{ route('contabilidad.reportes.patrimonio', request()->query()) }}" class="btn btn-ocean">Patrimonio</a>
            <a href="{{ route('contabilidad.reportes.flujo', request()->query()) }}" class="btn btn-outline-primary">Flujo de Efectivo</a>
        </div>
    </div>

    @include('contabilidad.partials.nav')

    <div class="ocean-card mb-4">
        <form method="GET" action="{{ route('contabilidad.reportes.patrimonio') }}" class="row g-3 align-items-end">
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
                <a href="{{ route('contabilidad.reportes.patrimonio') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>

    <div class="ocean-card p-0 overflow-hidden">
        <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
            <table class="table ocean-table table-hover mb-0">
                <thead class="position-sticky top-0" style="z-index: 2;">
                    <tr>
                        <th>Codigo</th>
                        <th>Cuenta Patrimonial</th>
                        <th class="text-end">Saldo Inicial ($)</th>
                        <th class="text-end">Variacion Periodo ($)</th>
                        <th class="text-end">Saldo Final ($)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($filas as $fila)
                        <tr>
                            <td class="font-monospace">{{ $fila['cuenta']->codigo }}</td>
                            <td>{{ $fila['cuenta']->nombre }}</td>
                            <td class="text-end font-monospace">${{ number_format($fila['apertura'], 2) }}</td>
                            <td class="text-end font-monospace">${{ number_format($fila['movimientos'], 2) }}</td>
                            <td class="text-end font-monospace fw-bold">${{ number_format($fila['saldo_final'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No hay movimientos patrimoniales registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="2" class="text-end">TOTAL PATRIMONIO:</td>
                        <td class="text-end font-monospace">${{ number_format($totales['apertura'], 2) }}</td>
                        <td class="text-end font-monospace">${{ number_format($totales['movimientos'], 2) }}</td>
                        <td class="text-end font-monospace">${{ number_format($totales['saldo_final'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection