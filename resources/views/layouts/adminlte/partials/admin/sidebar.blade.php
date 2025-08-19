@php
    use Illuminate\Support\Str;
@endphp

<!--begin::Sidebar-->
<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <!--begin::Sidebar Brand-->
    <div class="sidebar-brand">
        <a href="{{ route('dashboard.index') }}" class="brand-link">
            <img src="{{ asset('adminlte/assets/img/AdminLTELogo1.png') }}"
                 alt="AdminLTE Logo"
                 class="brand-image opacity-75 shadow" />
            <span class="brand-text fw-light">e-Budgeting</span>
        </a>
    </div>
    <!--end::Sidebar Brand-->

    <!--begin::Sidebar Wrapper-->
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                @foreach (($sidebarMenus ?? collect())->filter(fn($menu) => Str::endsWith($menu->menu_route, '.index')) as $menu)
                    <li class="nav-item">
                        <a href="{{ route($menu->menu_route) }}"
                           class="nav-link {{ request()->routeIs($menu->menu_route) ? 'active' : '' }}">
                            <i class="nav-icon bi bi-circle"></i>
                            <p>{{ $menu->menu_name }}</p>
                        </a>
                    </li>
                @endforeach
            </ul>
            <!--end::Sidebar Menu-->
        </nav>
    </div>
    <!--end::Sidebar Wrapper-->
</aside>
<!--end::Sidebar-->
