@extends('layouts.adminlte.admin.main')
@section('title', 'Sales Product')

{{-- ===== Styles (DataTables) ===== --}}
@section('style')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"/>
@endsection

{{-- ===== Page Content ===== --}}
@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h3 class="card-title mb-0">Sales List</h3>
    <a href="{{ route('penjualan.produk.create') }}" class="btn btn-primary btn-sm">
      <i class="fas fa-plus me-1"></i> Create
    </a>
  </div>

  <div class="card-body">
    <table id="salesTable" class="table table-bordered table-striped w-100">
      <thead>
      <tr>
        <th style="width:60px">#</th>
        <th>Code</th>
        <th>Customer</th>
        <th>Sale Date</th>
        <th>Total Amount</th>
        <th>Status</th>
        <th>Items</th>
        <th style="width:140px">Action</th>
      </tr>
      </thead>
      <tbody></tbody>
    </table>
  </div>
</div>
@endsection

{{-- ===== Scripts ===== --}}
@section('script')
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(function () {
  // ===== Guard: token from sessionStorage (NOT Laravel session) =====
  const token = sessionStorage.getItem('access_token');
  if (!token) {
    if (typeof showToast === 'function') showToast('Sesi habis. Silakan login lagi.', 'danger');
    window.location.href = "{{ url('login') }}";
    return;
  }

  // ===== Set default headers for ALL AJAX calls =====
  $.ajaxSetup({
    headers: {
      'Authorization': 'Bearer ' + token,
      'Accept': 'application/json'
    }
  });

  // ===== Helpers =====
  function idr(n){
    return new Intl.NumberFormat('id-ID', {
      style: 'currency', currency: 'IDR', maximumFractionDigits: 0
    }).format(Number(n || 0));
  }
  function toast(msg, type){
    if (typeof showToast === 'function') showToast(msg, type || 'success');
    else alert(msg);
  }

  // ===== DataTable =====
  const table = $('#salesTable').DataTable({
    processing: true,
    serverSide: true,
    order: [[3, 'desc']], // default: sale_date desc
    searchDelay: 300,
    ajax: {
      url: "{{ url('/api/sales/datatable') }}",
      type: "GET",
      dataSrc: function (json) {
        // Support {data:[...]} or [...]
        return Array.isArray(json) ? json : (json.data || []);
      },
      error: function (xhr) {
        if (xhr.status === 401) {
          toast('Unauthorized. Silakan login ulang.', 'danger');
          sessionStorage.removeItem('access_token');
          window.location.href = "{{ url('login') }}";
        } else {
          toast('Gagal memuat data.', 'danger');
          console.error(xhr.responseText || xhr.statusText);
        }
      }
    },
    columns: [
      { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, defaultContent: '' },
      // IMPORTANT: map "name" to real DB columns so server-side order/search works
      { data: 'code',          name: 'sales.code',         defaultContent: '-' },
      { data: 'customer_name', name: 'customers.name',     defaultContent: '-' },
      { data: 'sale_date',     name: 'sales.sale_date',    defaultContent: '-' },
      {
        data: 'total_amount',  name: 'sales.total_amount', defaultContent: 0,
        render: function (data) { return idr(data); }
      },
      {
        data: 'status',        name: 'sales.status',       defaultContent: '-',
        render: function (data) {
          const status = (data || '').toString().toLowerCase();
          const cls = status === 'confirmed' ? 'bg-success'
                     : status === 'draft' ? 'bg-secondary'
                     : 'bg-info';
          return `<span class="badge ${cls} text-uppercase">${data || '-'}</span>`;
        }
      },
      { data: 'items_count',   name: 'items_count',        defaultContent: 0 },
      {
        data: 'action', name: 'action', orderable: false, searchable: false, defaultContent: '',
        render: function (data, type, row) {
          if (data) return data; // backend already provides action html
          const id = row.id || row.code;
          return `
            <div class="btn-group btn-group-sm" role="group">
              <a href="{{ url('sales') }}/${encodeURIComponent(id)}" class="btn btn-outline-primary" title="Detail">
                <i class="fas fa-eye"></i>
              </a>
              <a href="{{ url('sales') }}/${encodeURIComponent(id)}/edit" class="btn btn-outline-warning" title="Edit">
                <i class="fas fa-edit"></i>
              </a>
              <button type="button" class="btn btn-outline-danger btn-delete" data-id="${id}" title="Delete">
                <i class="fas fa-trash"></i>
              </button>
            </div>`;
        }
      }
    ]
  });

  // ===== Delete (API) =====
  $(document).on('click', '.btn-delete', function(){
    const id = $(this).data('id');
    if (!id) return;

    if (!confirm('Hapus data ini?')) return;

    $.ajax({
      url: "{{ url('/api/sales') }}/" + encodeURIComponent(id),
      type: "DELETE",
      success: function(){
        toast('Berhasil dihapus', 'success');
        table.ajax.reload(null, false);
      },
      statusCode: {
        401: function(){
          toast('Unauthorized. Silakan login ulang.', 'danger');
          sessionStorage.removeItem('access_token');
          window.location.href = "{{ url('login') }}";
        }
      },
      error: function(xhr){
        toast('Gagal menghapus data.', 'danger');
        console.error(xhr.responseText || xhr.statusText);
      }
    });
  });
});
</script>
@endsection
