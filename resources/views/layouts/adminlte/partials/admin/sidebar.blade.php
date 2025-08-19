  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="../../index3.html" class="brand-link">
      <img src="../../dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">AdminLTE 3</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="../../dist/img/user2-160x160.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">Alexander Pierce</a>
        </div>
      </div>

      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>

      <!-- Sidebar Menu -->
    <!-- Sidebar Menu -->
<nav class="mt-2">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

    {{-- Master --}}
    <li class="nav-item {{ request()->routeIs('materials.*') || request()->routeIs('products.*') || request()->routeIs('suppliers.*') || request()->routeIs('customers.*') ? 'menu-open' : '' }}">
      <a href="#" class="nav-link {{ request()->routeIs('materials.*') || request()->routeIs('products.*') || request()->routeIs('suppliers.*') || request()->routeIs('customers.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-folder"></i>
        <p>Master <i class="right fas fa-angle-left"></i></p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="{{ route('materials.index') }}" class="nav-link {{ request()->routeIs('materials.*') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i><p>Materials</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('products.index') }}" class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i><p>Product</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i><p>Supplier</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i><p>Customer</p>
          </a>
        </li>
      </ul>
    </li>

    {{-- Pembelian --}}
    <li class="nav-item {{ request()->routeIs('pembelian.material.*') ? 'menu-open' : '' }}">
      <a href="#" class="nav-link {{ request()->routeIs('pembelian.material.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-folder"></i>
        <p>Pembelian <i class="right fas fa-angle-left"></i></p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="{{ route('pembelian.material.index') }}" class="nav-link {{ request()->routeIs('pembelian.material.*') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i><p>Pembelian Material</p>
          </a>
        </li>
      </ul>
    </li>

    {{-- Manufactur --}}
    <li class="nav-item {{ request()->routeIs('produksi.produk.*') ? 'menu-open' : '' }}">
      <a href="#" class="nav-link {{ request()->routeIs('produksi.produk.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-folder"></i>
        <p>Manufactur <i class="right fas fa-angle-left"></i></p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="{{ route('produksi.produk.index') }}" class="nav-link {{ request()->routeIs('produksi.produk.*') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i><p>Produksi Produk</p>
          </a>
        </li>
      </ul>
    </li>

    {{-- Penjualan --}}
    <li class="nav-item {{ request()->routeIs('penjualan.produk.*') ? 'menu-open' : '' }}">
      <a href="#" class="nav-link {{ request()->routeIs('penjualan.produk.*') ? 'active' : '' }}">
        <i class="nav-icon fas fa-folder"></i>
        <p>Penjualan <i class="right fas fa-angle-left"></i></p>
      </a>
      <ul class="nav nav-treeview">
        <li class="nav-item">
          <a href="{{ route('penjualan.produk.index') }}" class="nav-link {{ request()->routeIs('penjualan.produk.*') ? 'active' : '' }}">
            <i class="far fa-circle nav-icon"></i><p>Penjualan Produk</p>
          </a>
        </li>
      </ul>
    </li>

  </ul>
</nav>
<!-- /.sidebar-menu -->

      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
