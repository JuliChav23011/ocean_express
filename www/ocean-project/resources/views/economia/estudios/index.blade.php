
@extends('layouts.app')

@section('title', 'Estudios de inversión')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">

        <div>
            <h1 class="fw-bold">Estudios de inversión</h1>

            <p class="text-muted mb-0">
                Administración de estudios de Ingeniería Económica.
            </p>
        </div>

        <div class="d-flex gap-2">

            <a href="{{ route('economia.index') }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i>
                Volver
            </a>

            <a href="{{ route('economia.estudios.create') }}"
               class="btn btn-primary">
                <i class="bi bi-plus-circle"></i>
                Nuevo estudio
            </a>

        </div>

    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="card shadow-sm border-0">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-clipboard-data"></i>
                Estudios registrados
            </h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Empresa</th>
                            <th>Horizonte</th>
                            <th>TMAR</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($estudios as $estudio)

                            <tr>

                                <td>{{ $estudio->codigo }}</td>

                                <td>{{ $estudio->nombre }}</td>

                                <td>
                                    {{ $estudio->empresa->nombre ?? 'Sin empresa' }}
                                </td>

                                <td>
                                    {{ $estudio->horizonte_anios }} años
                                </td>

                                <td>
                                    {{ number_format($estudio->tasa_descuento, 2) }}%
                                </td>

                                <td>

                                    @switch($estudio->estado)

                                        @case('borrador')
                                            <span class="badge bg-secondary">
                                                Borrador
                                            </span>
                                            @break

                                        @case('en_evaluacion')
                                            <span class="badge bg-warning text-dark">
                                                En evaluación
                                            </span>
                                            @break

                                        @case('finalizado')
                                            <span class="badge bg-success">
                                                Finalizado
                                            </span>
                                            @break

                                    @endswitch

                                </td>

                                <td>

                                    <div class="d-flex gap-2">

                                        <a href="{{ route('economia.estudios.show', $estudio) }}"
                                           class="btn btn-sm btn-outline-info"
                                           title="Ver estudio">

                                            <i class="bi bi-eye"></i>

                                        </a>

                                        <a href="{{ route('economia.estudios.edit', $estudio) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Editar estudio">

                                            <i class="bi bi-pencil-square"></i>

                                        </a>

                                        <form action="{{ route('economia.estudios.destroy', $estudio) }}"
                                              method="POST"
                                              onsubmit="return confirm('¿Desea eliminar este estudio?');">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Eliminar estudio">

                                                <i class="bi bi-trash"></i>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="text-center py-5">

                                    <i class="bi bi-folder2-open fs-1 text-muted"></i>

                                    <p class="text-muted mt-3">
                                        No hay estudios de inversión registrados.
                                    </p>

                                    <a href="{{ route('economia.estudios.create') }}"
                                       class="btn btn-primary">

                                        Registrar primer estudio

                                    </a>

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{ $estudios->links('pagination::bootstrap-5') }}

        </div>

    </div>

</div>

@endsection
