@extends('layouts.app')

@section('title', 'Balance de Comprobacion - Ocean Express')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
        <div>
            <h1 class="page-title">Balance de Comprobacion</h1>
            <p class="page-subtitle mb-0">Sumas de Debe y Haber y saldos deudores o acreedores por cuenta.</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('contabilidad.libros.diario', request()->except('page')) }}" class="btn btn-outline-primary">
                <i class="bi bi-journal-text me-1"></i> Libro Diario
            </a>
            <a href="{{ route('contabilidad.libros.mayor', request()->except('page')) }}" class="btn btn-outline-primary">
                <i class="bi bi-book-half me-1"></i> Libro Mayor
            </a>
            <a href="{{ route('contabilidad.libros.balance', request()->except('page')) }}" class="btn btn-ocean">
                <i class="bi bi-clipboard-check me-1"></i> Balance Comprobacion
            </a>
        </div>
    </div>

    @include('contabilidad.partials.nav')

    <div class="ocean-card mb-4">
        <form method="GET" action="{{ route('contabilidad.libros.balance') }}" class="row g-3 align-items-end">
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
                <button type="submit" class="btn btn-ocean w-100"><i class="bi bi-search me-1"></i> Consultar</button>
                <a href="{{ route('contabilidad.libros.balance') }}" class="btn btn-outline-secondary" title="Limpiar">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="ocean-card p-0 overflow-hidden">
        <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
            <table class="table ocean-table table-hover mb-0">
                <thead class="position-sticky top-0" style="z-index: 2;">
                    <tr>
                        <th>Codigo</th>
                        <th>Cuenta</th>
                        <th class="text-end">Debe ($)</th>
                        <th class="text-end">Haber ($)</th>
                        <th class="text-end">Saldo Deudor ($)</th>
                        <th class="text-end">Saldo Acreedor ($)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($filas as $fila)
                        <tr>
                            <td class="font-monospace">{{ $fila['cuenta']->codigo }}</td>
                            <td>{{ $fila['cuenta']->nombre }}</td>
                            <td class="text-end font-monospace">${{ number_format($fila['debe'], 2) }}</td>
                            <td class="text-end font-monospace">${{ number_format($fila['haber'], 2) }}</td>
                            <td class="text-end font-monospace">{{ $fila['saldo_deudor'] > 0 ? '$' . number_format($fila['saldo_deudor'], 2) : '-' }}</td>
                            <td class="text-end font-monospace">{{ $fila['saldo_acreedor'] > 0 ? '$' . number_format($fila['saldo_acreedor'], 2) : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No hay movimientos contabilizados para mostrar.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="2" class="text-end">TOTALES:</td>
                        <td class="text-end font-monospace">${{ number_format($totales['debe'], 2) }}</td>
                        <td class="text-end font-monospace">${{ number_format($totales['haber'], 2) }}</td>
                        <td class="text-end font-monospace">${{ number_format($totales['saldo_deudor'], 2) }}</td>
                        <td class="text-end font-monospace">${{ number_format($totales['saldo_acreedor'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection