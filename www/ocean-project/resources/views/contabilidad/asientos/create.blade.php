@extends('layouts.app')

@section('title', 'Registrar Asiento Contable - Ocean Express')

@section('content')
    <h1 class="page-title">Registrar Asiento Contable</h1>
    <p class="page-subtitle">Partida doble en tiempo real: el Total Debe debe igualar exactamente al Total Haber.</p>

    @include('contabilidad.partials.nav')

    <form action="{{ route('contabilidad.asientos.store') }}" method="POST" id="formAsiento">
        @csrf
        <div class="ocean-card mb-4">
            <h3 class="mb-3"><i class="bi bi-file-earmark-plus me-2 text-primary"></i>Datos Generales de la Partida</h3>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Período Contable *</label>
                    <select name="periodo_contable_id" class="form-select" required>
                        @forelse($periodosAbiertos as $periodo)
                            <option value="{{ $periodo->id }}" {{ old('periodo_contable_id') == $periodo->id ? 'selected' : '' }}>
                                {{ $periodo->nombre }} ({{ $periodo->fecha_inicio->format('d/m/Y') }} - {{ $periodo->fecha_fin->format('d/m/Y') }})
                            </option>
                        @empty
                            <option value="">No hay períodos abiertos</option>
                        @endforelse
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">N° Asiento *</label>
                    <input type="text" name="numero_asiento" class="form-control font-monospace" value="{{ old('numero_asiento', $numeroSugerido) }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fecha *</label>
                    <input type="date" name="fecha" class="form-control" value="{{ old('fecha', '2026-09-30') }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tipo de Asiento *</label>
                    <select name="tipo" class="form-select" required>
                        @foreach(['normal' => 'Normal / Operativo', 'ajuste' => 'Ajuste Contable', 'apertura' => 'Apertura', 'cierre' => 'Cierre'] as $val => $label)
                            <option value="{{ $val }}" {{ old('tipo', $tipoPreseleccionado) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-8">
                    <label class="form-label fw-semibold">Concepto / Glosa *</label>
                    <input type="text" name="concepto" class="form-control" value="{{ old('concepto') }}" placeholder="Descripción clara de la operación contable..." required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Código de Evidencia / Documento</label>
                    <input type="text" name="referencia_documento" list="listaEvidencias" class="form-control font-monospace" value="{{ old('referencia_documento') }}" placeholder="Ej. EV-001 o Factura #...">
                    <datalist id="listaEvidencias">
                        @foreach($evidencias as $ev)
                            <option value="{{ $ev->codigo }}">{{ $ev->titulo }}</option>
                        @endforeach
                    </datalist>
                </div>
            </div>
        </div>

        <div class="ocean-card mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0"><i class="bi bi-list-ol me-2 text-primary"></i>Detalle de Cargos y Abonos</h3>
                <button type="button" class="btn btn-sm btn-ocean-outline" id="btnAgregarLinea">
                    <i class="bi bi-plus-lg me-1"></i> Agregar Línea
                </button>
            </div>

            <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                <table class="table ocean-table mb-0" id="tablaDetalles">
                    <thead class="position-sticky top-0" style="z-index: 2;">
                        <tr>
                            <th style="width: 34%;">Cuenta Contable *</th>
                            <th style="width: 28%;">Descripción de Línea</th>
                            <th style="width: 15%;" class="text-end">Debe ($)</th>
                            <th style="width: 15%;" class="text-end">Haber ($)</th>
                            <th style="width: 8%;" class="text-center">Quitar</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoDetalles">
                        @php $oldDetalles = old('detalles', [['cuenta_contable_id' => '', 'descripcion' => '', 'debe' => '', 'haber' => ''], ['cuenta_contable_id' => '', 'descripcion' => '', 'debe' => '', 'haber' => '']]); @endphp
                        @foreach($oldDetalles as $i => $linea)
                            <tr>
                                <td>
                                    <select name="detalles[{{ $i }}][cuenta_contable_id]" class="form-select form-select-sm" required>
                                        <option value="">-- Seleccione cuenta --</option>
                                        @foreach($cuentas as $cta)
                                            <option value="{{ $cta->id }}" {{ ($linea['cuenta_contable_id'] ?? '') == $cta->id ? 'selected' : '' }}>
                                                {{ $cta->codigo }} - {{ $cta->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <input type="text" name="detalles[{{ $i }}][descripcion]" class="form-control form-control-sm" value="{{ $linea['descripcion'] ?? '' }}" placeholder="Opcional">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" name="detalles[{{ $i }}][debe]" class="form-control form-control-sm text-end input-debe" value="{{ $linea['debe'] ?? '' }}" placeholder="0.00">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" name="detalles[{{ $i }}][haber]" class="form-control form-control-sm text-end input-haber" value="{{ $linea['haber'] ?? '' }}" placeholder="0.00">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-quitar" title="Eliminar línea">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="2" class="text-end">TOTALES:</td>
                            <td class="text-end font-monospace" id="totalDebe">$0.00</td>
                            <td class="text-end font-monospace" id="totalHaber">$0.00</td>
                            <td class="text-center" id="badgeCuadre">
                                <span class="badge bg-secondary">0.00</span>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mt-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex gap-2">
                    <button type="submit" name="accion" value="borrador" class="btn btn-outline-secondary">
                        <i class="bi bi-file-earmark me-1"></i> Guardar Borrador
                    </button>
                    <button type="submit" name="accion" value="contabilizado" class="btn btn-ocean" id="btnContabilizar">
                        <i class="bi bi-check2-all me-1"></i> Guardar y Contabilizar
                    </button>
                </div>
                <a href="{{ route('contabilidad.asientos.index') }}" class="btn btn-link text-muted">Cancelar y volver</a>
            </div>
        </div>
    </form>

    <template id="plantillaFila">
        <tr>
            <td>
                <select name="detalles[__INDEX__][cuenta_contable_id]" class="form-select form-select-sm" required>
                    <option value="">-- Seleccione cuenta --</option>
                    @foreach($cuentas as $cta)
                        <option value="{{ $cta->id }}">{{ $cta->codigo }} - {{ $cta->nombre }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="text" name="detalles[__INDEX__][descripcion]" class="form-control form-control-sm" placeholder="Opcional">
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="detalles[__INDEX__][debe]" class="form-control form-control-sm text-end input-debe" placeholder="0.00">
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="detalles[__INDEX__][haber]" class="form-control form-control-sm text-end input-haber" placeholder="0.00">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-quitar" title="Eliminar línea">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    </template>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let indice = document.querySelectorAll('#cuerpoDetalles tr').length;
            const cuerpo = document.getElementById('cuerpoDetalles');
            const plantilla = document.getElementById('plantillaFila').innerHTML;

            function recalcular() {
                let debe = 0, haber = 0;
                document.querySelectorAll('.input-debe').forEach(el => debe += parseFloat(el.value) || 0);
                document.querySelectorAll('.input-haber').forEach(el => haber += parseFloat(el.value) || 0);
                debe = Math.round(debe * 100) / 100;
                haber = Math.round(haber * 100) / 100;
                const diff = Math.round((debe - haber) * 100) / 100;

                document.getElementById('totalDebe').textContent = '$' + debe.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById('totalHaber').textContent = '$' + haber.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                const badge = document.getElementById('badgeCuadre');
                if (debe > 0 && diff === 0) {
                    badge.innerHTML = '<span class="badge bg-success"><i class="bi bi-check-lg"></i> Cuadrado</span>';
                } else {
                    badge.innerHTML = `<span class="badge bg-danger" title="Diferencia">Dif: ${diff.toFixed(2)}</span>`;
                }
            }

            document.getElementById('btnAgregarLinea').addEventListener('click', () => {
                cuerpo.insertAdjacentHTML('beforeend', plantilla.replaceAll('__INDEX__', indice++));
            });

            cuerpo.addEventListener('click', (e) => {
                if (e.target.closest('.btn-quitar') && cuerpo.querySelectorAll('tr').length > 2) {
                    e.target.closest('tr').remove();
                    recalcular();
                }
            });

            cuerpo.addEventListener('input', (e) => {
                if (e.target.classList.contains('input-debe') || e.target.classList.contains('input-haber')) {
                    recalcular();
                }
            });

            recalcular();
        });
    </script>
@endsection