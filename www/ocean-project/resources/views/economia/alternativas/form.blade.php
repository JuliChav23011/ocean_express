@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Por favor, corrige los siguientes errores:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif



<h5 class="fw-bold mb-3">
    <i class="bi bi-info-circle"></i>
    Datos generales
</h5>

<div class="row g-3 mb-4">

    <div class="col-md-8">
        <label class="form-label">
            Estudio de inversión *
        </label>

        <select name="estudio_inversion_id"
                class="form-select" required>

            <option value="">Seleccione un estudio</option>

            @foreach ($estudios as $estudio)
                <option value="{{ $estudio->id }}"
                    @selected(old('estudio_inversion_id',
                        $alternativa->estudio_inversion_id
                        ?? $estudioSeleccionado ?? '') == $estudio->id)>

                    {{ $estudio->codigo }} -
                    {{ $estudio->nombre }} -
                    {{ $estudio->empresa->nombre }}

                </option>
            @endforeach

        </select>

        @if ($estudios->isEmpty())
            <small class="text-danger">
                Primero debe registrar un estudio de inversión.
            </small>

            <a href="{{ route('economia.estudios.create') }}">
                Registrar estudio
            </a>
        @endif
    </div>

    <div class="col-md-4">
        <label class="form-label">Código *</label>

        <input type="text"
               name="codigo"
               class="form-control"
               maxlength="20"
               placeholder="ALT-001"
               value="{{ old('codigo', $alternativa->codigo ?? '') }}"
               required>
    </div>

    <div class="col-md-8">
        <label class="form-label">
            Nombre de la alternativa *
        </label>

        <input type="text"
               name="nombre"
               class="form-control"
               maxlength="200"
               value="{{ old('nombre', $alternativa->nombre ?? '') }}"
               required>
    </div>

    <div class="col-md-4">
        <label class="form-label">Tipo *</label>

        <select name="tipo" class="form-select" required>

            @foreach ([
                'compra_activo' => 'Compra de activo',
                'contratacion_servicio' => 'Contratación de servicio',
                'otra' => 'Otra alternativa'
            ] as $valor => $texto)

                <option value="{{ $valor }}"
                    @selected(old('tipo',
                        $alternativa->tipo ?? 'compra_activo') == $valor)>
                    {{ $texto }}
                </option>

            @endforeach

        </select>
    </div>

    <div class="col-12">
        <label class="form-label">Descripción</label>

        <textarea name="descripcion"
                  class="form-control"
                  rows="3">{{ old('descripcion', $alternativa->descripcion ?? '') }}</textarea>
    </div>

</div>

<hr>


<h5 class="fw-bold mb-3">
    <i class="bi bi-cash-stack"></i>
    Información financiera
</h5>

<div class="row g-3 mb-4">

    <div class="col-md-6">
        <label class="form-label">
            Inversión inicial *
        </label>

        <input type="number"
               name="inversion_inicial"
               class="form-control"
               min="0"
               max="9999999999999.99"
               step="0.01"
               value="{{ old('inversion_inicial', $alternativa->inversion_inicial ?? 0) }}"
               required>

        <small class="text-muted">
            Desembolso inicial necesario para implementar la alternativa.
        </small>
    </div>

    <div class="col-md-6">
        <label class="form-label">
            Costo operativo anual *
        </label>

        <input type="number"
               name="costo_operativo_anual"
               class="form-control"
               min="0"
               max="9999999999999.99"
               step="0.01"
               value="{{ old('costo_operativo_anual', $alternativa->costo_operativo_anual ?? 0) }}"
               required>
    </div>

    <div class="col-md-6">
        <label class="form-label">
            Ingreso anual estimado *
        </label>

        <input type="number"
               name="ingreso_anual_estimado"
               class="form-control"
               min="0"
               max="9999999999999.99"
               step="0.01"
               value="{{ old('ingreso_anual_estimado', $alternativa->ingreso_anual_estimado ?? 0) }}"
               required>
    </div>

    <div class="col-md-6">
        <label class="form-label">
            Valor residual *
        </label>

        <input type="number"
               name="valor_residual"
               class="form-control"
               min="0"
               max="9999999999999.99"
               step="0.01"
               value="{{ old('valor_residual', $alternativa->valor_residual ?? 0) }}"
               required>

        <small class="text-muted">
            Valor estimado de recuperación al finalizar la vida útil.
        </small>
    </div>

    <div class="col-md-6">
        <label class="form-label">
            Vida útil (años)
        </label>

        <input type="number"
               name="vida_util_anios"
               class="form-control"
               min="1"
               max="2147483647"
               step="1"
               value="{{ old('vida_util_anios', $alternativa->vida_util_anios ?? '') }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">
            Método de depreciación *
        </label>

        <select name="metodo_depreciacion"
                class="form-select" required>

            @foreach ([
                'no_aplica' => 'No aplica',
                'linea_recta' => 'Línea recta',
                'suma_digitos' => 'Suma de dígitos de los años',
                'doble_saldo_decreciente' => 'Doble saldo decreciente'
            ] as $valor => $texto)

                <option value="{{ $valor }}"
                    @selected(old('metodo_depreciacion',
                        $alternativa->metodo_depreciacion ?? 'no_aplica') == $valor)>
                    {{ $texto }}
                </option>

            @endforeach

        </select>

        <small class="text-muted">
            La vida útil es obligatoria si se selecciona un método de depreciación.
        </small>
    </div>

</div>

<hr>



<h5 class="fw-bold mb-3">
    <i class="bi bi-file-earmark-check"></i>
    Evidencias y justificación
</h5>

<div class="row g-3 mb-4">

    <div class="col-md-6">
        <label class="form-label">
            Clasificación de los datos *
        </label>

        <select name="clasificacion_datos"
                class="form-select" required>

            @foreach ([
                'real' => 'Datos reales',
                'estimado' => 'Datos estimados',
                'mixto' => 'Datos reales y estimados'
            ] as $valor => $texto)

                <option value="{{ $valor }}"
                    @selected(old('clasificacion_datos',
                        $alternativa->clasificacion_datos ?? 'estimado') == $valor)>
                    {{ $texto }}
                </option>

            @endforeach

        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">
            Evidencia documental
        </label>

        <select name="evidencia_id" class="form-select">

            <option value="">Sin evidencia asociada</option>

            @foreach ($evidencias as $evidencia)

                <option value="{{ $evidencia->id }}"
                    @selected(old('evidencia_id',
                        $alternativa->evidencia_id ?? '') == $evidencia->id)>

                    {{ $evidencia->codigo }} -
                    {{ $evidencia->titulo }}

                </option>

            @endforeach

        </select>

        <small class="text-muted">
            La evidencia debe pertenecer a la empresa del estudio.
        </small>
    </div>

    <div class="col-12">
        <label class="form-label">
            Justificación de los datos
        </label>

        <textarea name="justificacion"
                  class="form-control"
                  rows="4"
                  placeholder="Explique el origen de los datos y los criterios utilizados para las estimaciones.">{{ old('justificacion', $alternativa->justificacion ?? '') }}</textarea>
    </div>

    <div class="col-12">

        <input type="hidden" name="activa" value="0">

        <div class="form-check form-switch">

            <input type="checkbox"
                   name="activa"
                   id="activa"
                   value="1"
                   class="form-check-input"
                   @checked(old('activa', $alternativa->activa ?? true))>

            <label class="form-check-label" for="activa">
                Alternativa activa
            </label>

        </div>

    </div>

</div>

<hr>



<div class="d-flex justify-content-end gap-2">

    <a href="{{ route('economia.alternativas.index') }}"
       class="btn btn-outline-secondary">

        <i class="bi bi-x-circle"></i>
        Cancelar

    </a>

    <button type="submit"
            class="btn btn-primary"
            @disabled($estudios->isEmpty())>

        <i class="bi bi-floppy"></i>
        Guardar alternativa

    </button>

</div>