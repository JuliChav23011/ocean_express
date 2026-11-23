
<aside class="ocean-sidebar">

    <p class="sidebar-title">Menú principal</p>

    <nav aria-label="Navegación principal">

        <a href="{{ url('/') }}"
           class="sidebar-link {{ request()->is('/') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Panel principal</span>
        </a>

        <a href="{{ url('/contabilidad') }}"
           class="sidebar-link {{ request()->is('contabilidad*') ? 'active' : '' }}">
            <i class="bi bi-journal-text"></i>
            <span>Contabilidad</span>
        </a>

        <a href="{{ url('/costos') }}"
           class="sidebar-link {{ request()->is('costos*') ? 'active' : '' }}">
            <i class="bi bi-calculator"></i>
            <span>Sistema de Costos</span>
        </a>

        <a href="{{ url('/economia') }}"
           class="sidebar-link {{ request()->is('economia*') ? 'active' : '' }}">
            <i class="bi bi-graph-up-arrow"></i>
            <span>Ingeniería Económica</span>
        </a>

        <div class="sidebar-divider"></div>

        <p class="sidebar-title">Configuración</p>

        <a href="{{ url('/administracion') }}"
           class="sidebar-link {{ request()->is('administracion*') ? 'active' : '' }}">
            <i class="bi bi-gear"></i>
            <span>Administración</span>
        </a>

    </nav>

</aside>