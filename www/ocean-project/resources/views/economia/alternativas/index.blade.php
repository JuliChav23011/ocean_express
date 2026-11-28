@extends('layouts.app')

@section('title', 'Alternativas de inversión')

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

        <div>
            <h2 class="fw-bold mb-1">
                <i class="bi bi-diagram-3"></i>
                Alternativas de inversión
            </h2>

            <p class="text-muted mb-0">
                Registro y administración de alternativas económicas.
            </p>
        </div>

        <div class="d-flex gap-2">

            <a href="{{ route('economia.index') }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
                Volver
            </a>

            <a href="{{ route('economia.alternativas.create', request()->only('estudio_id')) }}"
               class="btn btn-primary">
                <i class="bi bi-plus-circle"></i>
                Nueva alternativa
            </a>

        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i>
            {{ session('error') }}
        </div>
    @endif


    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <form method="GET"
                  action="{{ route('economia.alternativas.index') }}"
                  class="row g-3 align-items-end">

                <div class="col-md-8">

                    <label class="form-label fw-semibold">
                        Filtrar por estudio de inversión
                    </label>

                    <select name="estudio_id"
                            class="form-select">

                        <option value="">
                            Todos los estudios
                        </option>

                        @foreach($estudios as $estudio)

                            <option value="{{ $estudio->id }}"
                                @selected(request('estudio_id') == $estudio->id)>

                                {{ $estudio->codigo }} -
                                {{ $estudio->nombre }}

                            </option>

                        @endforeach

                    </select>

                </div>

                <div class="col-md-4 d-flex gap-2">

                    <button type="submit"
                            class="btn btn-primary">

                        <i class="bi bi-funnel"></i>
                        Filtrar

                    </button>

                    <a href="{{ route('economia.alternativas.index') }}"
                       class="btn btn-outline-secondary">

                        Limpiar

                    </a>

                </div>

            </form>

        </div>

    </div>


    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3">

            <h5 class="mb-0 fw-semibold">
                Alternativas registradas
            </h5>

        </div>

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>
                            <th>Código</th>
                            <th>Alternativa</th>
                            <th>Estudio</th>
                            <th>Tipo</th>
                            <th>Inversión inicial</th>
                            <th>Vida útil</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($alternativas as $alternativa)

                            <tr>

                                <td class="fw-semibold">
                                    {{ $alternativa->codigo }}
                                </td>

                                <td>
                                    {{ $alternativa->nombre }}
                                </td>

                                <td>
                                    {{ $alternativa->estudioInversion->nombre ?? 'Sin estudio' }}
                                </td>

                                <td>

                                    @switch($alternativa->tipo)

                                        @case('compra_activo')
                                            Compra de activo
                                            @break

                                        @case('contratacion_servicio')
                                            Contratación de servicio
                                            @break

                                        @default
                                            Otra

                                    @endswitch

                                </td>

                                <td class="fw-semibold">

                                    {{ $alternativa->estudioInversion->moneda ?? 'USD' }}

                                    {{ number_format($alternativa->inversion_inicial, 2) }}

                                </td>

                                <td>

                                    @if($alternativa->vida_util_anios)

                                        {{ $alternativa->vida_util_anios }} años

                                    @else

                                        No aplica

                                    @endif

                                </td>

                                <td>

                                    @if($alternativa->activa)

                                        <span class="badge bg-success">
                                            Activa
                                        </span>

                                    @else

                                        <span class="badge bg-secondary">
                                            Inactiva
                                        </span>

                                    @endif

                                </td>

                                <td>

                                    <div class="d-flex justify-content-center gap-2">

                                        <a href="{{ route('economia.alternativas.show', $alternativa) }}"
                                           class="btn btn-sm btn-outline-info"
                                           title="Ver detalles">

                                            <i class="bi bi-eye"></i>

                                        </a>

                                        <a href="{{ route('economia.alternativas.edit', $alternativa) }}"
                                           class="btn btn-sm btn-outline-warning"
                                           title="Editar">

                                            <i class="bi bi-pencil-square"></i>

                                        </a>

                                        <form action="{{ route('economia.alternativas.destroy', $alternativa) }}"
                                              method="POST"
                                              onsubmit="return confirm('¿Deseas eliminar esta alternativa?')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Eliminar">

                                                <i class="bi bi-trash"></i>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="8"
                                    class="text-center py-5">

                                    <i class="bi bi-folder2-open display-5 text-muted"></i>

                                    <h5 class="mt-3">
                                        No hay alternativas registradas
                                    </h5>

                                    <p class="text-muted">
                                        Registra una alternativa o modifica el filtro seleccionado.
                                    </p>

                                    <a href="{{ route('economia.alternativas.create', request()->only('estudio_id')) }}"
                                       class="btn btn-primary">

                                        <i class="bi bi-plus-circle"></i>
                                        Registrar alternativa

                                    </a>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        @if($alternativas->hasPages())

            <div class="card-footer bg-white">

                {{ $alternativas->links('pagination::bootstrap-5') }}

            </div>

        @endif

    </div>

</div>

@endsection