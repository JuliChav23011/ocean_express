@extends('layouts.app')

@section('title', 'Editar Cuenta Contable - Ocean Express')

@section('content')
    <h1 class="page-title">Editar Cuenta Contable</h1>
    <p class="page-subtitle">Modificando: <strong>{{ $cuenta->codigo }} - {{ $cuenta->nombre }}</strong></p>

    @include('contabilidad.partials.nav')

    <div class="ocean-card" style="max-width: 780px;">
        <form action="{{ route('contabilidad.cuentas.update', $cuenta) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Código Contable *</label>
                    <input type="text" name="codigo" class="form-control font-monospace" value="{{ old('codigo', $cuenta->codigo) }}" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">Nombre de la Cuenta *</label>
                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $cuenta->nombre) }}" required>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-semibold">Cuenta Padre (Jerarquía)</label>
                    <select name="cuenta_padre_id" class="form-select">
                        <option value="">-- Ninguna (Cuenta de nivel raíz) --</option>
                        @foreach($cuentasPadre as $padre)
                            <option value="{{ $padre->id }}" {{ old('cuenta_padre_id', $cuenta->cuenta_padre_id) == $padre->id ? 'selected' : '' }}>
                                {{ $padre->codigo }} - {{ $padre->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Grupo / Clasificación *</label>
                    <select name="tipo" class="form-select" required>
                        @foreach(['activo' => 'Activo', 'pasivo' => 'Pasivo', 'patrimonio' => 'Patrimonio', 'ingreso' => 'Ingresos', 'costo' => 'Costos', 'gasto' => 'Gastos'] as $val => $label)
                            <option value="{{ $val }}" {{ old('tipo', $cuenta->tipo) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Naturaleza del Saldo *</label>
                    <select name="naturaleza" class="form-select" required>
                        <option value="deudora" {{ old('naturaleza', $cuenta->naturaleza) === 'deudora' ? 'selected' : '' }}>Deudora</option>
                        <option value="acreedora" {{ old('naturaleza', $cuenta->naturaleza) === 'acreedora' ? 'selected' : '' }}>Acreedora</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tipo de Cuenta *</label>
                    <select name="permite_movimientos" class="form-select" required>
                        <option value="1" {{ old('permite_movimientos', $cuenta->permite_movimientos ? '1' : '0') == '1' ? 'selected' : '' }}>Subcuenta (Acepta movimientos)</option>
                        <option value="0" {{ old('permite_movimientos', $cuenta->permite_movimientos ? '1' : '0') == '0' ? 'selected' : '' }}>Agrupadora (Solo título/mayor)</option>
                    </select>
                </div>

                <div class="col-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="activa" value="1" id="activaSwitch" {{ old('activa', $cuenta->activa) ? 'checked' : '' }}>
                        <label class="form-check-label" for="activaSwitch">Cuenta activa</label>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-ocean"><i class="bi bi-check2-circle me-1"></i> Actualizar Cuenta</button>
                <a href="{{ route('contabilidad.cuentas.index') }}" class="btn btn-outline-secondary">Volver</a>
            </div>
        </form>
    </div>
@endsection