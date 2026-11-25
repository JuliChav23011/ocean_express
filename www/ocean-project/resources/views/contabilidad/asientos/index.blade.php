@extends('layouts.app')

@section('title', 'Asientos Contables - Ocean Express')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
        <div>
            <h1 class="page-title">Libro de Asientos Contables</h1>
            <p class="page-subtitle mb-0">Registro de partidas, borradores, contabilización y trazabilidad de evidencias.</p>
        </div>
        <a href="{{ route('contabilidad.asientos.create') }}" class="btn btn-ocean">
            <i class="bi bi-plus-circle me-1"></i> Nuevo Asiento
        </a>
    </div>

    @include('contabilidad.partials.nav')

    <div class="ocean-card mb-4">
        <form method="GET" action="{{ route('contabilidad.asientos.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Período Contable</label>
                <select name="periodo_id" class="form-select">
                    <option value="">Todos los períodos</option>
                    @foreach($periodos as $periodo)
                        <option value="{{ $periodo->id }}" {{ request('periodo_id') == $periodo->id ? 'selected' : '' }}>
                            {{ $periodo->nombre }} ({{ strtoupper($periodo->estado) }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Tipo de Asiento</label>
                <select name="tipo" class="form-select">
                    <option value="">Todos</option>
                    <option value="apertura" {{ request('tipo') === 'apertura' ? 'selected' : '' }}>Apertura</option>
                    <option value="normal" {{ request('tipo') === 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="ajuste" {{ request('tipo') === 'ajuste' ? 'selected' : '' }}>Ajuste</option>
                    <option value="cierre" {{ request('tipo') === 'cierre' ? 'selected' : '' }}>Cierre</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Estado</label>
                <select name="estado" class="form-select">
                    <option value="">Todos</option>
                    <option value="borrador" {{ request('estado') === 'borrador' ? 'selected' : '' }}>Borrador</option>
                    <option value="contabilizado" {{ request('estado') === 'contabilizado' ? 'selected' : '' }}>Contabilizado</option>
                    <option value="anulado" {{ request('estado') === 'anulado' ? 'selected' : '' }}>Anulado</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-ocean w-100"><i class="bi bi-filter"></i> Filtrar</button>
                <a href="{{ route('contabilidad.asientos.index') }}" class="btn btn-outline-secondary" title="Limpiar"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>

    <div class="ocean-card p-0 overflow-hidden">
        <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
            <table class="table ocean-table table-hover mb-0">
                <thead class="position-sticky top-0" style="z-index: 2;">
                    <tr>
                        <th>N° Asiento</th>
                        <th>Fecha</th>
                        <th>Período</th>
                        <th>Tipo</th>
                        <th>Concepto</th>
                        <th>Evidencia</th>
                        <th class="text-end">Total Debe</th>
                        <th class="text-end">Total Haber</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($asientos as $asiento)
                        <tr>
                            <td class="fw-bold font-monospace">{{ $asiento->numero_asiento }}</td>
                            <td>{{ $asiento->fecha->format('d/m/Y') }}</td>
                            <td>{{ $asiento->periodoContable->nombre ?? '-' }}</td>
                            <td><span class="badge bg-light text-dark border text-uppercase">{{ $asiento->tipo }}</span></td>
                            <td>{{ \Illuminate\Support\Str::limit($asiento->concepto, 45) }}</td>
                            <td>
                                @if($asiento->referencia_documento)
                                    <span class="badge bg-info text-dark font-monospace">{{ $asiento->referencia_documento }}</span>
                                @else
                                    <span class="text-muted small">Sin ref.</span>
                                @endif
                            </td>
                            <td class="text-end fw-semibold">${{ number_format($asiento->total_debe, 2) }}</td>
                            <td class="text-end fw-semibold">${{ number_format($asiento->total_haber, 2) }}</td>
                            <td>
                                <span class="badge {{ $asiento->estado === 'contabilizado' ? 'bg-success' : ($asiento->estado === 'anulado' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                    {{ ucfirst($asiento->estado) }}
                                </span>
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('contabilidad.asientos.show', $asiento) }}" class="btn btn-sm btn-outline-primary" title="Ver detalle">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($asiento->estado === 'borrador')
                                    <a href="{{ route('contabilidad.asientos.edit', $asiento) }}" class="btn btn-sm btn-outline-secondary" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">No hay asientos contables con los filtros aplicados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection