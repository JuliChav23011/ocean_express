@extends('layouts.app')

@section('title', 'Ajustes Contables - Ocean Express')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
        <div>
            <h1 class="page-title">Asientos de Ajuste Contable</h1>
            <p class="page-subtitle mb-0">Depreciaciones, amortizaciones, devengos y reclasificaciones del período.</p>
        </div>
        <a href="{{ route('contabilidad.asientos.create', ['tipo' => 'ajuste']) }}" class="btn btn-ocean">
            <i class="bi bi-sliders me-1"></i> Nuevo Ajuste Contable
        </a>
    </div>

    @include('contabilidad.partials.nav')

    <div class="ocean-card mb-4">
        <form method="GET" action="{{ route('contabilidad.ajustes.index') }}" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label small fw-semibold">Filtrar por Período</label>
                <select name="periodo_id" class="form-select">
                    <option value="">Todos los períodos</option>
                    @foreach($periodos as $periodo)
                        <option value="{{ $periodo->id }}" {{ request('periodo_id') == $periodo->id ? 'selected' : '' }}>
                            {{ $periodo->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-ocean w-100"><i class="bi bi-search me-1"></i> Filtrar</button>
            </div>
        </form>
    </div>

    <div class="ocean-card p-0 overflow-hidden">
        <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
            <table class="table ocean-table table-hover mb-0">
                <thead class="position-sticky top-0" style="z-index: 2;">
                    <tr>
                        <th>N° Ajuste</th>
                        <th>Fecha</th>
                        <th>Período</th>
                        <th>Concepto</th>
                        <th>Evidencia</th>
                        <th class="text-end">Monto</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ajustes as $ajuste)
                        <tr>
                            <td class="fw-bold font-monospace">{{ $ajuste->numero_asiento }}</td>
                            <td>{{ $ajuste->fecha->format('d/m/Y') }}</td>
                            <td>{{ $ajuste->periodoContable->nombre ?? '-' }}</td>
                            <td>{{ $ajuste->concepto }}</td>
                            <td>
                                @if($ajuste->referencia_documento)
                                    <span class="badge bg-info text-dark font-monospace">{{ $ajuste->referencia_documento }}</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-end fw-semibold">${{ number_format($ajuste->total_debe, 2) }}</td>
                            <td>
                                <span class="badge {{ $ajuste->estado === 'contabilizado' ? 'bg-success' : ($ajuste->estado === 'anulado' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                    {{ ucfirst($ajuste->estado) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('contabilidad.asientos.show', $ajuste) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> Ver
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">No hay asientos de ajuste registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection