@extends('layouts.app')

@section('title', 'Períodos Contables - Ocean Express')

@section('content')
    <h1 class="page-title">Administración de Períodos Contables</h1>
    <p class="page-subtitle">Control de apertura, fechas válidas y cierre histórico de períodos de registro.</p>

    @include('contabilidad.partials.nav')

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="ocean-card">
                <h3><i class="bi bi-calendar-plus me-2 text-primary"></i>Nuevo Período Contable</h3>
                <form action="{{ route('contabilidad.periodos.store') }}" method="POST" class="mt-3">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nombre del Período *</label>
                        <input type="text" name="nombre" class="form-control" placeholder="Ej. Octubre 2026" value="{{ old('nombre') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Fecha de Inicio *</label>
                        <input type="date" name="fecha_inicio" class="form-control" value="{{ old('fecha_inicio') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Fecha de Finalización *</label>
                        <input type="date" name="fecha_fin" class="form-control" value="{{ old('fecha_fin') }}" required>
                    </div>
                    <button type="submit" class="btn btn-ocean w-100">
                        <i class="bi bi-save me-1"></i> Registrar Período
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="ocean-card p-0 overflow-hidden">
                <div class="table-responsive">
                    <table class="table ocean-table mb-0">
                        <thead>
                            <tr>
                                <th>Período</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th class="text-center">Asientos</th>
                                <th>Estado</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($periodos as $periodo)
                                <tr>
                                    <td class="fw-bold">{{ $periodo->nombre }}</td>
                                    <td>{{ $periodo->fecha_inicio->format('d/m/Y') }}</td>
                                    <td>{{ $periodo->fecha_fin->format('d/m/Y') }}</td>
                                    <td class="text-center"><span class="badge bg-light text-dark border">{{ $periodo->asientos_contables_count }}</span></td>
                                    <td>
                                        @if($periodo->estaAbierto())
                                            <span class="badge bg-success">Abierto</span>
                                        @else
                                            <span class="badge bg-secondary"><i class="bi bi-lock-fill me-1"></i>Cerrado</span>
                                        @endif
                                    </td>
                                    <td class="text-end text-nowrap">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPeriodo{{ $periodo->id }}" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <form action="{{ route('contabilidad.periodos.estado', $periodo) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('¿Deseas cambiar el estado de este período contable?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $periodo->estaAbierto() ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                                {{ $periodo->estaAbierto() ? 'Cerrar' : 'Reabrir' }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editPeriodo{{ $periodo->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form action="{{ route('contabilidad.periodos.update', $periodo) }}" method="POST" class="modal-content">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Editar Período: {{ $periodo->nombre }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Nombre</label>
                                                    <input type="text" name="nombre" class="form-control" value="{{ $periodo->nombre }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Fecha Inicio</label>
                                                    <input type="date" name="fecha_inicio" class="form-control" value="{{ $periodo->fecha_inicio->format('Y-m-d') }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Fecha Fin</label>
                                                    <input type="date" name="fecha_fin" class="form-control" value="{{ $periodo->fecha_fin->format('Y-m-d') }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" class="btn btn-ocean">Guardar Cambios</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No hay períodos registrados.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection