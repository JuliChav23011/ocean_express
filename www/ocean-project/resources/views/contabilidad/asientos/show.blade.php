@extends('layouts.app')

@section('title', 'Asiento ' . $asiento->numero_asiento . ' - Ocean Express')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
        <div>
            <h1 class="page-title">Partida Contable {{ $asiento->numero_asiento }}</h1>
            <p class="page-subtitle mb-0">
                Fecha: <strong>{{ $asiento->fecha->format('d/m/Y') }}</strong> ·
                Período: <strong>{{ $asiento->periodoContable->nombre ?? '-' }}</strong> ·
                Tipo: <span class="badge bg-light text-dark border text-uppercase">{{ $asiento->tipo }}</span>
            </p>
        </div>
        <div class="d-flex gap-2">
            @if($asiento->estado === 'borrador')
                <a href="{{ route('contabilidad.asientos.edit', $asiento) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-pencil me-1"></i> Editar
                </a>
                <form action="{{ route('contabilidad.asientos.contabilizar', $asiento) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-ocean">
                        <i class="bi bi-check2-all me-1"></i> Contabilizar
                    </button>
                </form>
            @endif

            @if($asiento->estado !== 'anulado')
                <form action="{{ route('contabilidad.asientos.anular', $asiento) }}" method="POST"
                      onsubmit="return confirm('¿Anular este asiento contable?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-x-circle me-1"></i> Anular
                    </button>
                </form>
            @endif
        </div>
    </div>

    @include('contabilidad.partials.nav')

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="ocean-card p-0 overflow-hidden mb-4">
                <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small text-muted d-block">Concepto</span>
                        <strong>{{ $asiento->concepto }}</strong>
                    </div>
                    <span class="badge fs-6 {{ $asiento->estado === 'contabilizado' ? 'bg-success' : ($asiento->estado === 'anulado' ? 'bg-danger' : 'bg-warning text-dark') }}">
                        {{ strtoupper($asiento->estado) }}
                    </span>
                </div>

                <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                    <table class="table ocean-table mb-0">
                        <thead class="position-sticky top-0" style="z-index: 2;">
                            <tr>
                                <th>Código</th>
                                <th>Cuenta Contable</th>
                                <th>Descripción</th>
                                <th class="text-end">Debe ($)</th>
                                <th class="text-end">Haber ($)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($asiento->detalles as $detalle)
                                <tr>
                                    <td class="font-monospace">{{ $detalle->cuentaContable->codigo ?? '-' }}</td>
                                    <td>{{ $detalle->cuentaContable->nombre ?? '-' }}</td>
                                    <td class="text-muted small">{{ $detalle->descripcion ?: '-' }}</td>
                                    <td class="text-end font-monospace">{{ $detalle->debe > 0 ? '$' . number_format($detalle->debe, 2) : '-' }}</td>
                                    <td class="text-end font-monospace">{{ $detalle->haber > 0 ? '$' . number_format($detalle->haber, 2) : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="3" class="text-end">TOTALES PARTIDA:</td>
                                <td class="text-end font-monospace">${{ number_format($asiento->total_debe, 2) }}</td>
                                <td class="text-end font-monospace">${{ number_format($asiento->total_haber, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="ocean-card">
                <h3><i class="bi bi-paperclip me-2 text-primary"></i>Evidencia Documental</h3>

                @if($asiento->evidencia)
                    <div class="p-3 rounded-3 bg-light border mb-3">
                        <div class="fw-bold font-monospace text-primary">{{ $asiento->evidencia->codigo }}</div>
                        <div class="fw-semibold">{{ $asiento->evidencia->titulo }}</div>
                        <div class="small text-muted mt-1">
                            Tipo: {{ ucfirst(str_replace('_', ' ', $asiento->evidencia->tipo)) }}
                            @if($asiento->evidencia->emisor) · Emisor: {{ $asiento->evidencia->emisor }} @endif
                        </div>
                        @if($asiento->evidencia->ruta_archivo)
                            <a href="{{ route('contabilidad.evidencias.descargar', $asiento->evidencia) }}" class="btn btn-sm btn-ocean mt-2">
                                <i class="bi bi-download me-1"></i> Descargar {{ $asiento->evidencia->nombre_archivo }}
                            </a>
                        @endif
                    </div>
                @elseif($asiento->referencia_documento)
                    <div class="alert alert-info py-2 small">
                        Referencia registrada: <strong class="font-monospace">{{ $asiento->referencia_documento }}</strong>
                    </div>
                @else
                    <p class="text-muted small">Aún no hay evidencia vinculada a este asiento.</p>
                @endif

                <form action="{{ route('contabilidad.asientos.evidencia', $asiento) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label small fw-semibold">Vincular o Registrar</label>
                        <select name="modo" id="modoEvidencia" class="form-select form-select-sm">
                            <option value="existente">Seleccionar evidencia existente</option>
                            <option value="nueva">Registrar nueva evidencia</option>
                        </select>
                    </div>

                    <div id="bloqueExistente" class="mb-3">
                        <select name="codigo_existente" class="form-select form-select-sm">
                            <option value="">-- Elija una evidencia --</option>
                            @foreach($evidencias as $ev)
                                <option value="{{ $ev->codigo }}" {{ $asiento->referencia_documento === $ev->codigo ? 'selected' : '' }}>
                                    {{ $ev->codigo }} - {{ $ev->titulo }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="bloqueNueva" style="display: none;">
                        <div class="mb-2">
                            <input type="text" name="codigo_nuevo" class="form-control form-control-sm font-monospace" placeholder="Código (Ej. EV-005)">
                        </div>
                        <div class="mb-2">
                            <input type="text" name="titulo" class="form-control form-control-sm" placeholder="Título o descripción del soporte">
                        </div>
                        <div class="mb-2">
                            <select name="tipo_evidencia" class="form-select form-select-sm">
                                <option value="factura">Factura</option>
                                <option value="cotizacion">Cotización</option>
                                <option value="registro_contable">Registro contable</option>
                                <option value="autorizacion">Autorización</option>
                                <option value="estado_financiero">Estado financiero</option>
                                <option value="fotografia">Fotografía</option>
                                <option value="entrevista">Entrevista</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <input type="text" name="emisor" class="form-control form-control-sm" placeholder="Emisor / Proveedor (opcional)">
                        </div>
                        <div class="mb-2">
                            <input type="date" name="fecha_documento" class="form-control form-control-sm">
                        </div>
                        <div class="mb-3">
                            <input type="file" name="archivo" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png,.xlsx,.docx">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-sm btn-ocean w-100">
                        <i class="bi bi-link-45deg me-1"></i> Guardar Evidencia
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modo = document.getElementById('modoEvidencia');
            const existente = document.getElementById('bloqueExistente');
            const nueva = document.getElementById('bloqueNueva');

            modo.addEventListener('change', () => {
                const esNueva = modo.value === 'nueva';
                existente.style.display = esNueva ? 'none' : 'block';
                nueva.style.display = esNueva ? 'block' : 'none';
            });
        });
    </script>
@endsection