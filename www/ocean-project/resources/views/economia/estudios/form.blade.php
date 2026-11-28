
@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Revisa los siguientes errores:</strong>
        <ul class="mb-0 mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-3">

    <div class="col-12">
        <h5 class="fw-bold text-primary">
            <i class="bi bi-info-circle"></i>
            Información general
        </h5>
        <hr>
    </div>

    <div class="col-md-6">
        <label class="form-label">Empresa *</label>

        <select name="empresa_id" class="form-select" required>
            <option value="">Seleccione una empresa</option>

            @foreach($empresas as $empresa)
                <option value="{{ $empresa->id }}"
                    @selected(old('empresa_id', $estudio->empresa_id ?? '') == $empresa->id)>
                    {{ $empresa->nombre }}
                </option>
            @endforeach
        </select>

        @if($empresas->isEmpty())
            <small class="text-danger">
                No hay empresas registradas.
            </small>
        @endif
    </div>

    <div class="col-md-6">
        <label class="form-label">Código del estudio *</label>

        <input type="text"
               name="codigo"
               class="form-control"
               maxlength="30"
               placeholder="Ejemplo: EST-001"
               value="{{ old('codigo', $estudio->codigo ?? '') }}"
               required>
    </div>

    <div class="col-12">
        <label class="form-label">Nombre del estudio *</label>

        <input type="text"
               name="nombre"
               class="form-control"
               maxlength="200"
               value="{{ old('nombre', $estudio->nombre ?? '') }}"
               required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Fecha del estudio *</label>

        <input type="date"
               name="fecha_estudio"
               class="form-control"
               value="{{ old('fecha_estudio', isset($estudio) ? $estudio->fecha_estudio->format('Y-m-d') : now()->format('Y-m-d')) }}"
               required>
    </div>

    <div class="col-md-6">
        <label class="form-label">Estado *</label>

        <select name="estado" class="form-select" required>

            @foreach([
                'borrador' => 'Borrador',
                'en_evaluacion' => 'En evaluación',
                'finalizado' => 'Finalizado'
            ] as $valor => $nombre)

                <option value="{{ $valor }}"
                    @selected(old('estado', $estudio->estado ?? 'borrador') == $valor)>
                    {{ $nombre }}
                </option>

            @endforeach

        </select>
    </div>

    <div class="col-12">
        <label class="form-label">Descripción</label>

        <textarea name="descripcion"
                  class="form-control"
                  rows="3">{{ old('descripcion', $estudio->descripcion ?? '') }}</textarea>
    </div>

    <div class="col-12">
        <label class="form-label">Objetivo del estudio</label>

        <textarea name="objetivo"
                  class="form-control"
                  rows="3">{{ old('objetivo', $estudio->objetivo ?? '') }}</textarea>
    </div>

    <div class="col-12 mt-4">
        <h5 class="fw-bold text-primary">
            <i class="bi bi-calculator"></i>
            Parámetros económicos
        </h5>
        <hr>
    </div>

    <div class="col-md-4">
        <label class="form-label">Horizonte de evaluación *</label>

        <div class="input-group">
            <input type="number"
                   name="horizonte_anios"
                   class="form-control"
                   min="1"
                   step="1"
                   value="{{ old('horizonte_anios', $estudio->horizonte_anios ?? '') }}"
                   required>

            <span class="input-group-text">años</span>
        </div>
    </div>

    <div class="col-md-4">
        <label class="form-label">TMAR *</label>

        <div class="input-group">
            <input type="number"
                   name="tasa_descuento"
                   class="form-control"
                   min="0"
                   max="9999.9999"
                   step="0.0001"
                   value="{{ old('tasa_descuento', $estudio->tasa_descuento ?? '') }}"
                   required>

            <span class="input-group-text">%</span>
        </div>

        <small class="text-muted">
            Tasa mínima atractiva de rendimiento.
        </small>
    </div>

    <div class="col-md-4">
        <label class="form-label">Tasa de impuesto *</label>

        <div class="input-group">
            <input type="number"
                   name="tasa_impuesto"
                   class="form-control"
                   min="0"
                   max="100"
                   step="0.0001"
                   value="{{ old('tasa_impuesto', $estudio->tasa_impuesto ?? 0) }}"
                   required>

            <span class="input-group-text">%</span>
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label">Tasa de inflación *</label>

        <div class="input-group">
            <input type="number"
                   name="tasa_inflacion"
                   class="form-control"
                   min="0"
                   max="9999.9999"
                   step="0.0001"
                   value="{{ old('tasa_inflacion', $estudio->tasa_inflacion ?? 0) }}"
                   required>

            <span class="input-group-text">%</span>
        </div>
    </div>

    <div class="col-md-6">
        <label class="form-label">Moneda *</label>

        <input type="text"
               name="moneda"
               class="form-control text-uppercase"
               maxlength="3"
               minlength="3"
               value="{{ old('moneda', $estudio->moneda ?? 'USD') }}"
               required>
    </div>

    <div class="col-12 mt-4">
        <h5 class="fw-bold text-primary">
            <i class="bi bi-journal-text"></i>
            Información complementaria
        </h5>
        <hr>
    </div>

    <div class="col-12">
        <label class="form-label">Supuestos económicos</label>

        <textarea name="supuestos"
                  class="form-control"
                  rows="3"
                  placeholder="Indique los supuestos utilizados en la evaluación.">{{ old('supuestos', $estudio->supuestos ?? '') }}</textarea>
    </div>

    <div class="col-12">
        <label class="form-label">Observaciones</label>

        <textarea name="observaciones"
                  class="form-control"
                  rows="3">{{ old('observaciones', $estudio->observaciones ?? '') }}</textarea>
    </div>

</div>

<hr class="my-4">

<div class="d-flex justify-content-end gap-2">

    <a href="{{ route('economia.estudios.index') }}"
       class="btn btn-outline-secondary">
        Cancelar
    </a>

    <button type="submit"
            class="btn btn-primary"
            @disabled($empresas->isEmpty())>
        <i class="bi bi-floppy"></i>
        Guardar estudio
    </button>

</div>
