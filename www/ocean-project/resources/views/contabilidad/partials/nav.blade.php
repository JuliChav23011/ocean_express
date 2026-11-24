<ul class="nav ocean-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('contabilidad.index') ? 'active' : '' }}"
           href="{{ route('contabilidad.index') }}">
            <i class="bi bi-speedometer2 me-1"></i> Resumen
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('contabilidad.cuentas.*') ? 'active' : '' }}"
           href="{{ route('contabilidad.cuentas.index') }}">
            <i class="bi bi-list-nested me-1"></i> Catálogo de Cuentas
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('contabilidad.periodos.*') ? 'active' : '' }}"
           href="{{ route('contabilidad.periodos.index') }}">
            <i class="bi bi-calendar-range me-1"></i> Períodos
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('contabilidad.asientos.*') ? 'active' : '' }}"
           href="{{ route('contabilidad.asientos.index') }}">
            <i class="bi bi-journal-plus me-1"></i> Asientos
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('contabilidad.ajustes.*') ? 'active' : '' }}"
           href="{{ route('contabilidad.ajustes.index') }}">
            <i class="bi bi-sliders me-1"></i> Ajustes
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('contabilidad.libros.*') ? 'active' : '' }}"
           href="{{ route('contabilidad.libros.diario') }}">
            <i class="bi bi-book me-1"></i> Libros Contables
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('contabilidad.reportes.*') ? 'active' : '' }}"
           href="{{ route('contabilidad.reportes.resultados') }}">
            <i class="bi bi-file-earmark-bar-graph me-1"></i> Estados Financieros
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('contabilidad.cierre.*') ? 'active' : '' }}"
           href="{{ route('contabilidad.cierre.index') }}">
            <i class="bi bi-lock me-1"></i> Cierre Contable
        </a>
    </li>
</ul>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm" role="alert">
        <strong><i class="bi bi-exclamation-circle-fill me-1"></i> Revisa los siguientes errores:</strong>
        <ul class="mb-0 mt-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
@endif