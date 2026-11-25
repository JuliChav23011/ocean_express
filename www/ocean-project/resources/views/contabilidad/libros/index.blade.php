@extends('layouts.app')

@section('title', 'Libros Contables - Ocean Express')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
        <div>
            <h1 class="page-title">Libros Contables</h1>
            <p class="page-subtitle mb-0">Libro Diario cronologico y Libro Mayor por cuenta con partidas contabilizadas.</p>
        </div>
        <div class="btn-group">
            <a href="{{ route('contabilidad.libros.index', array_merge(request()->except('page'), ['libro' => 'diario'])) }}"
               class="btn {{ $vista === 'diario' ? 'btn-ocean' : 'btn-outline-primary' }}">
                <i class="bi bi-journal-text me-1"></i> Libro Diario
            </a>
            <a href="{{ route('contabilidad.libros.index', array_merge(request()->except('page'), ['libro' => 'mayor'])) }}"
               class="btn {{ $vista === 'mayor' ? 'btn-ocean' : 'btn-outline-primary' }}">
                <i class="bi bi-book-half me-1"></i> Libro Mayor
            </a>
        </div>
    </div>

    @include('contabilidad.partials.nav')

    <div class="ocean-card mb-4">
        <form method="GET" action="{{ route('contabilidad.libros.index') }}" class="row g-3 align-items-end">
            <input type="hidden" name="libro" value="{{ $vista }}">

            <div class="col-md-4">
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

            <div class="col-md-5">
                <label class="form-label small fw-semibold">Cuenta Contable</label>
                <select name="cuenta_id" class="form-select">
                    <option value="">{{ $vista === 'mayor' ? 'Seleccionar cuenta...' : 'Todas las cuentas' }}</option>
                    @foreach($cuentas as $cta)
                        <option value="{{ $cta->id }}" {{ (string) $cuentaId === (string) $cta->id ? 'selected' : '' }}>
                            {{ $cta->codigo }} - {{ $cta->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-ocean w-100"><i class="bi bi-search me-1"></i> Consultar</button>
                <a href="{{ route('contabilidad.libros.index', ['libro' => $vista]) }}" class="btn btn-outline-secondary" title="Limpiar">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>

    @if($vista === 'mayor')
        <div class="ocean-card p-0 overflow-hidden">
            <div class="p-3 border-bottom d-flex flex-wrap justify-content-between align-items-center bg-light">
                <div>
                    <span class="small text-muted d-block">Cuenta Mayorizada</span>
                    @if($cuentaSeleccionada)
                        <strong class="font-monospace">{{ $cuentaSeleccionada->codigo }}</strong> — <strong>{{ $cuentaSeleccionada->nombre }}</strong>
                        <span class="badge bg-light text-dark border ms-2 text-uppercase">{{ $cuentaSeleccionada->naturaleza }}</span>
                    @else
                        <span class="text-muted">Sin cuenta seleccionada</span>
                    @endif
                </div>
                @if($cuentaSeleccionada)
                    <small class="text-muted">Movimientos contabilizados: {{ $movimientosMayor->count() }}</small>
                @endif
            </div>

            <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                <table class="table ocean-table table-hover mb-0">
                    <thead class="position-sticky top-0" style="z-index: 2;">
                        <tr>
                            <th>Fecha</th>
                            <th>Asiento</th>
                            <th>Concepto / Detalle</th>
                            <th class="text-end">Debe ($)</th>
                            <th class="text-end">Haber ($)</th>
                            <th class="text-end">Saldo ($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $saldoAcum = $saldoInicialCuenta;
                            $esDeudora = ($cuentaSeleccionada->naturaleza ?? 'deudora') === 'deudora';
                        @endphp
                        @forelse($movimientosMayor as $mov)
                            @php
                                $debe = (float) $mov->debe;
                                $haber = (float) $mov->haber;
                                $saldoAcum += $esDeudora ? ($debe - $haber) : ($haber - $debe);
                            @endphp
                            <tr>
                                <td>{{ $mov->asientoContable->fecha?->format('d/m/Y') }}</td>
                                <td class="font-monospace fw-bold">
                                    <a href="{{ route('contabilidad.asientos.show', $mov->asientoContable) }}" class="text-decoration-none">
                                        {{ $mov->asientoContable->numero_asiento }}
                                    </a>
                                </td>
                                <td>{{ $mov->descripcion ?: $mov->asientoContable->concepto }}</td>
                                <td class="text-end font-monospace">{{ $debe > 0 ? '$' . number_format($debe, 2) : '-' }}</td>
                                <td class="text-end font-monospace">{{ $haber > 0 ? '$' . number_format($haber, 2) : '-' }}</td>
                                <td class="text-end font-monospace fw-semibold">${{ number_format($saldoAcum, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No hay movimientos contabilizados para esta consulta.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="ocean-card p-0 overflow-hidden">
            <div class="table-responsive" style="max-height: 480px; overflow-y: auto;">
                <table class="table ocean-table table-hover mb-0">
                    <thead class="position-sticky top-0" style="z-index: 2;">
                        <tr>
                            <th>Fecha</th>
                            <th>Asiento</th>
                            <th>Codigo</th>
                            <th>Cuenta</th>
                            <th>Concepto</th>
                            <th class="text-end">Debe ($)</th>
                            <th class="text-end">Haber ($)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($asientosDiario as $asiento)
                            @foreach($asiento->detalles as $det)
                                <tr>
                                    <td>{{ $asiento->fecha?->format('d/m/Y') }}</td>
                                    <td class="font-monospace fw-bold">
                                        <a href="{{ route('contabilidad.asientos.show', $asiento) }}" class="text-decoration-none">
                                            {{ $asiento->numero_asiento }}
                                        </a>
                                    </td>
                                    <td class="font-monospace">{{ $det->cuentaContable->codigo ?? '-' }}</td>
                                    <td>{{ $det->cuentaContable->nombre ?? '-' }}</td>
                                    <td>{{ $det->descripcion ?: $asiento->concepto }}</td>
                                    <td class="text-end font-monospace">{{ $det->debe > 0 ? '$' . number_format($det->debe, 2) : '-' }}</td>
                                    <td class="text-end font-monospace">{{ $det->haber > 0 ? '$' . number_format($det->haber, 2) : '-' }}</td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No hay asientos contabilizados en el periodo seleccionado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($asientosDiario->hasPages())
                <div class="d-flex flex-wrap justify-content-between align-items-center px-3 py-2 border-top bg-light">
                    <small class="text-muted">
                        Mostrando {{ $asientosDiario->firstItem() }}–{{ $asientosDiario->lastItem() }} de {{ $asientosDiario->total() }} asientos
                    </small>
                    {{ $asientosDiario->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    @endif
@endsection