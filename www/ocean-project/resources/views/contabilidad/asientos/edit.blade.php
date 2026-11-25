@extends('layouts.app')

@section('title', 'Editar Asiento ' . $asiento->numero_asiento . ' - Ocean Express')

@section('content')
    <h1 class="page-title">Editar Asiento Borrador: {{ $asiento->numero_asiento }}</h1>
    <p class="page-subtitle">Solo los asientos en estado borrador pueden modificarse antes de contabilizarse.</p>

    @include('contabilidad.partials.nav')

    <form action="{{ route('contabilidad.asientos.update', $asiento) }}" method="POST" id="formAsiento">
        @csrf
        @method('PUT')
        <div class="ocean-card mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Período Contable *</label>
                    <select name="periodo_contable_id" class="form-select" required>
                        @foreach($periodosAbiertos as $periodo)
                            <option value="{{ $periodo->id }}" {{ old('periodo_contable_id', $asiento->periodo_contable_id) == $periodo->id ? 'selected' : '' }}>
                                {{ $periodo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">N° Asiento *</label>
                    <input type="text" name="numero_asiento" class="form-control font-monospace" value="{{ old('numero_asiento', $asiento->numero_asiento) }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Fecha *</label>
                    <input type="date" name="fecha" class="form-control" value="{{ old('fecha', $asiento->fecha->format('Y-m-d')) }}" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tipo *</label>
                    <select name="tipo" class="form-select" required>
                        @foreach(['normal' => 'Normal', 'ajuste' => 'Ajuste', 'apertura' => 'Apertura', 'cierre' => 'Cierre'] as $val => $label)
                            <option value="{{ $val }}" {{ old('tipo', $asiento->tipo) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-8">
                    <label class="form-label fw-semibold">Concepto *</label>
                    <input type="text" name="concepto" class="form-control" value="{{ old('concepto', $asiento->concepto) }}" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Referencia / Evidencia</label>
                    <input type="text" name="referencia_documento" list="listaEvidencias" class="form-control font-monospace" value="{{ old('referencia_documento', $asiento->referencia_documento) }}">
                    <datalist id="listaEvidencias">
                        @foreach($evidencias as $ev)
                            <option value="{{ $ev->codigo }}">{{ $ev->titulo }}</option>
                        @endforeach
                    </datalist>
                </div>
            </div>
        </div>

        <div class="ocean-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="mb-0">Líneas de Detalle</h3>
                <button type="button" class="btn btn-sm btn-ocean-outline" id="btnAgregarLinea">
                    <i class="bi bi-plus-lg me-1"></i> Agregar Línea
                </button>
            </div>

            <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                <table class="table ocean-table mb-0">
                    <thead class="position-sticky top-0" style="z-index: 2;">
                        <tr>
                            <th style="width: 34%;">Cuenta Contable *</th>
                            <th style="width: 28%;">Descripción</th>
                            <th style="width: 15%;" class="text-end">Debe ($)</th>
                            <th style="width: 15%;" class="text-end">Haber ($)</th>
                            <th style="width: 8%;" class="text-center">Quitar</th>
                        </tr>
                    </thead>
                    <tbody id="cuerpoDetalles">
                        @php
                            $lineasActuales = old('detalles', $asiento->detalles->map(fn($d) => [
                                'cuenta_contable_id' => $d->cuenta_contable_id,
                                'descripcion' => $d->descripcion,
                                'debe' => $d->debe > 0 ? $d->debe : '',
                                'haber' => $d->haber > 0 ? $d->haber : '',
                            ])->toArray());
                        @endphp
                        @foreach($lineasActuales as $i => $linea)
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
                                    <input type="text" name="detalles[{{ $i }}][descripcion]" class="form-control form-control-sm" value="{{ $linea['descripcion'] ?? '' }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" name="detalles[{{ $i }}][debe]" class="form-control form-control-sm text-end input-debe" value="{{ $linea['debe'] ?? '' }}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" min="0" name="detalles[{{ $i }}][haber]" class="form-control form-control-sm text-end input-haber" value="{{ $linea['haber'] ?? '' }}">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-quitar">
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
                            <td class="text-center" id="badgeCuadre"><span class="badge bg-secondary">0.00</span></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" name="accion" value="borrador" class="btn btn-outline-secondary">
                    <i class="bi bi-save me-1"></i> Guardar Borrador
                </button>
                <button type="submit" name="accion" value="contabilizado" class="btn btn-ocean">
                    <i class="bi bi-check2-all me-1"></i> Guardar y Contabilizar
                </button>
                <a href="{{ route('contabilidad.asientos.show', $asiento) }}" class="btn btn-outline-secondary">Cancelar</a>
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
            <td><input type="text" name="detalles[__INDEX__][descripcion]" class="form-control form-control-sm"></td>
            <td><input type="number" step="0.01" min="0" name="detalles[__INDEX__][debe]" class="form-control form-control-sm text-end input-debe" placeholder="0.00"></td>
            <td><input type="number" step="0.01" min="0" name="detalles[__INDEX__][haber]" class="form-control form-control-sm text-end input-haber" placeholder="0.00"></td>
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger btn-quitar"><i class="bi bi-trash"></i></button></td>
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
                    badge.innerHTML = `<span class="badge bg-danger">Dif: ${diff.toFixed(2)}</span>`;
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