@extends('layouts.app')

@section('title', 'Catálogo de Cuentas - Ocean Express')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
        <div>
            <h1 class="page-title">Catálogo de Cuentas Contables</h1>
            <p class="page-subtitle mb-0">Estructura jerárquica, naturaleza y control de cuentas operativas de Ocean Express.</p>
        </div>
        <a href="{{ route('contabilidad.cuentas.create') }}" class="btn btn-ocean">
            <i class="bi bi-plus-lg me-1"></i> Nueva Cuenta
        </a>
    </div>

    @include('contabilidad.partials.nav')

    <div class="ocean-card mb-4">
        <form method="GET" action="{{ route('contabilidad.cuentas.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Buscar por código o nombre</label>
                <input type="text" name="buscar" class="form-control" placeholder="Ej. 1101, Caja, Alquiler..." value="{{ request('buscar') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Naturaleza / Grupo</label>
                <select name="tipo" class="form-select">
                    <option value="">Todos los grupos</option>
                    @foreach(['activo' => 'Activo', 'pasivo' => 'Pasivo', 'patrimonio' => 'Patrimonio', 'ingreso' => 'Ingresos', 'costo' => 'Costos', 'gasto' => 'Gastos'] as $val => $label)
                        <option value="{{ $val }}" {{ request('tipo') === $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-semibold">Estado</label>
                <select name="estado" class="form-select">
                    <option value="">Todas</option>
                    <option value="activa" {{ request('estado') === 'activa' ? 'selected' : '' }}>Activas</option>
                    <option value="inactiva" {{ request('estado') === 'inactiva' ? 'selected' : '' }}>Inactivas</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-ocean w-100"><i class="bi bi-search"></i> Filtrar</button>
                <a href="{{ route('contabilidad.cuentas.index') }}" class="btn btn-outline-secondary" title="Limpiar"><i class="bi bi-x-lg"></i></a>
            </div>
        </form>
    </div>

    <div class="ocean-card p-0 overflow-hidden">
        <div class="table-responsive">
            <table class="table ocean-table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre de la Cuenta</th>
                        <th>Grupo</th>
                        <th>Naturaleza</th>
                        <th>Clasificación</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cuentas as $cuenta)
                        <tr class="{{ !$cuenta->permite_movimientos ? 'table-light fw-bold' : '' }}">
                            <td class="font-monospace">{{ $cuenta->codigo }}</td>
                            <td>
                                <span style="padding-left: {{ ($cuenta->nivel - 1) * 18 }}px;">
                                    @if(!$cuenta->permite_movimientos)
                                        <i class="bi bi-folder2-open text-primary me-1"></i>
                                    @else
                                        <i class="bi bi-file-earmark-text text-muted me-1"></i>
                                    @endif
                                    {{ $cuenta->nombre }}
                                </span>
                            </td>
                            <td><span class="badge bg-light text-dark border text-uppercase">{{ $cuenta->tipo }}</span></td>
                            <td>{{ ucfirst($cuenta->naturaleza) }}</td>
                            <td>
                                @if($cuenta->permite_movimientos)
                                    <span class="badge bg-info text-dark">Detalle (Movimiento)</span>
                                @else
                                    <span class="badge bg-secondary">Agrupadora</span>
                                @endif
                            </td>
                            <td>
                                @if($cuenta->activa)
                                    <span class="badge bg-success">Activa</span>
                                @else
                                    <span class="badge bg-danger">Inactiva</span>
                                @endif
                            </td>
                            <td class="text-end text-nowrap">
                                <a href="{{ route('contabilidad.cuentas.edit', $cuenta) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                <form action="{{ route('contabilidad.cuentas.toggle', $cuenta) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm {{ $cuenta->activa ? 'btn-outline-warning' : 'btn-outline-success' }}"
                                            title="{{ $cuenta->activa ? 'Desactivar' : 'Activar' }}">
                                        <i class="bi {{ $cuenta->activa ? 'bi-pause-circle' : 'bi-play-circle' }}"></i>
                                    </button>
                                </form>

                                @if($cuenta->detalles_asientos_count === 0)
                                    <form action="{{ route('contabilidad.cuentas.destroy', $cuenta) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('¿Eliminar definitivamente esta cuenta sin movimientos?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">No se encontraron cuentas con los criterios seleccionados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection