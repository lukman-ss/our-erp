@extends('layouts.adminlte.admin.main')
@section('title', 'Sales Order Create')

@section('style')
<style>
  .table-items th, .table-items td { vertical-align: middle; }
  .w-90  { width: 90px; }
  .w-110 { width: 110px; }
  .w-140 { width: 140px; }
  .w-160 { width: 160px; }
</style>
@endsection

@section('content')
<div class="container-fluid" id="sales-order-create">
  {{-- Top fields --}}
  <div class="row g-3">
    <div class="col-md-4">
      <label class="form-label">Code</label>
      <input type="text" id="code" class="form-control" placeholder="SO-YYYY-00001"
             value="SO-{{ now()->year }}-00001">
      <small id="error_code" class="text-danger"></small>
    </div>

    <div class="col-md-4">
      <label class="form-label">Customer ID</label>
      <input type="text" id="customer_id" class="form-control" placeholder="UUID / ID Customer">
      <small id="error_customer_id" class="text-danger"></small>
    </div>

    <div class="col-md-4">
      <label class="form-label">Sale Date</label>
      <input type="date" id="sale_date" class="form-control" value="{{ now()->toDateString() }}">
      <small id="error_sale_date" class="text-danger"></small>
    </div>

    <div class="col-md-4">
      <label class="form-label">Status</label>
      <select id="status" class="form-select">
        <option value="draft">draft</option>
        <option value="confirmed" selected>confirmed</option>
      </select>
      <small id="error_status" class="text-danger"></small>
    </div>

    <div class="col-md-4 d-flex align-items-end">
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="affect_stock" checked>
        <label class="form-check-label" for="affect_stock">Affect Stock</label>
      </div>
    </div>
  </div>

  <hr class="my-4">

  {{-- Items --}}
  <div class="d-flex justify-content-between align-items-center mb-2">
    <h5 class="mb-0">Items</h5>
    <button type="button" id="btnAddItem" class="btn btn-primary btn-sm">
      <i class="fas fa-plus me-1"></i> Add Item
    </button>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered table-items">
      <thead class="table-light">
        <tr>
          <th style="min-width:260px">Product ID</th>
          <th class="w-110">Qty</th>
          <th class="w-140">Unit Price</th>
          <th class="w-110">Disc %</th>
          <th class="w-140">Disc (Amt)</th>
          <th class="w-160 text-end">Subtotal</th>
          <th class="w-90"></th>
        </tr>
      </thead>
      <tbody id="items_tbody"></tbody>
      <tfoot>
        <tr>
          <th colspan="5" class="text-end">Grand Total</th>
          <th class="text-end"><span id="grand_total">Rp0</span></th>
          <th></th>
        </tr>
      </tfoot>
    </table>
  </div>

  <div class="d-flex justify-content-end gap-2 mt-3">
    <a href="{{ route('penjualan.produk.index') }}" class="btn btn-outline-secondary">Cancel</a>
    <button type="button" id="btn-submit" class="btn btn-success">
      <i class="fas fa-save me-1"></i><span class="btn-text">Save</span>
      <i class="fas fa-spinner fa-spin ms-2 d-none" id="btn-spinner"></i>
    </button>
  </div>
</div>
@endsection

@section('script')
<script>
$(function () {
  // ---- guard: must be logged in ----
  var token = sessionStorage.getItem('access_token');
  if (!token) {
    if (typeof showToast === 'function') showToast('Sesi habis. Silakan login lagi.', 'danger');
    window.location.href = "{{ url('login') }}";
    return;
  }

  // ---------- helpers ----------
  function toFloat(v){ var n=parseFloat(String(v).replace(/[^0-9.-]/g,'')); return isNaN(n)?0:n; }
  function fmt(n){ return new Intl.NumberFormat('id-ID',{style:'currency',currency:'IDR',maximumFractionDigits:0}).format(n); }
  function toast(msg, type){ if (typeof showToast==='function'){ showToast(msg, type==='error'?'danger':(type||'success')); } else { alert(msg); } }

  // ---------- items calc ----------
  function recalcRow($tr){
    const qty   = toFloat($tr.find('.itm-qty').val());
    const price = toFloat($tr.find('.itm-price').val());
    const dPct  = toFloat($tr.find('.itm-disc-pct').val());
    const dAmt  = toFloat($tr.find('.itm-disc-amt').val());
    const gross = qty * price;
    const pctAmt= (dPct/100) * gross;
    const sub   = Math.max(0, gross - pctAmt - dAmt);
    $tr.find('.itm-subtotal').text(fmt(sub)).data('val', sub);
  }

  function recalcTotals(){
    let total = 0;
    $('#items_tbody .itm-subtotal').each(function(){ total += toFloat($(this).data('val')); });
    $('#grand_total').text(fmt(total));
  }

  function buildRow(d){
    const _d = Object.assign({
      product_id:'', qty:1, unit_price:0, discount_percentage:0, discount_amount:0
    }, d || {});
    const html = `
      <tr>
        <td>
          <input type="text" class="form-control itm-product" placeholder="UUID Product" value="${_d.product_id}">
          <small class="text-danger itm-err-product"></small>
        </td>
        <td><input type="number" min="0" step="1" class="form-control itm-qty" value="${_d.qty}"></td>
        <td><input type="number" min="0" step="100" class="form-control itm-price" value="${_d.unit_price}"></td>
        <td><input type="number" min="0" step="0.01" class="form-control itm-disc-pct" value="${_d.discount_percentage}"></td>
        <td><input type="number" min="0" step="100" class="form-control itm-disc-amt" value="${_d.discount_amount}"></td>
        <td class="text-end"><span class="itm-subtotal" data-val="0">Rp0</span></td>
        <td class="text-center">
          <button type="button" class="btn btn-outline-danger btn-sm btn-remove">
            <i class="fas fa-trash"></i>
          </button>
        </td>
      </tr>`;
    const $row = $(html);
    recalcRow($row);
    return $row;
  }

  // ---------- bind ----------
  $(document).on('click', '#btnAddItem', function(){
    $('#items_tbody').append(buildRow({ qty:1 }));
    recalcTotals();
  });

  $(document).on('input', '#items_tbody .itm-qty, #items_tbody .itm-price, #items_tbody .itm-disc-pct, #items_tbody .itm-disc-amt', function(){
    const $tr = $(this).closest('tr'); recalcRow($tr); recalcTotals();
  });

  $(document).on('click', '#items_tbody .btn-remove', function(){
    $(this).closest('tr').remove(); recalcTotals();
  });

  // ---------- seed initial row (same as your cURL example) ----------
  $('#items_tbody').append(buildRow({
    product_id:'bedbbb8d-7796-4569-af03-3d0ff98d4b2e',
    qty:2, unit_price:295000, discount_percentage:10, discount_amount:5000
  }));
  recalcTotals();

  // ---------- submit ----------
  $('#btn-submit').on('click', function(){
    $('[id^="error_"]').text(''); $('.itm-err-product').text('');

    const payload = {
      code: $('#code').val() || null,
      customer_id: $('#customer_id').val() || null,
      sale_date: $('#sale_date').val(),
      status: $('#status').val(),
      affect_stock: $('#affect_stock').is(':checked'),
      items: []
    };

    $('#items_tbody tr').each(function(){
      const $tr = $(this);
      payload.items.push({
        product_id: $tr.find('.itm-product').val(),
        qty: toFloat($tr.find('.itm-qty').val()),
        unit_price: toFloat($tr.find('.itm-price').val()),
        discount_percentage: toFloat($tr.find('.itm-disc-pct').val()),
        discount_amount: toFloat($tr.find('.itm-disc-amt').val())
      });
    });

    const $btn = $('#btn-submit');
    $('#btn-spinner').removeClass('d-none'); $btn.prop('disabled', true);

    $.ajax({
      url: "{{ url('api/sales') }}", // <== endpoint per cURL
      method: 'POST',
      contentType: 'application/json',
      dataType: 'json',
      headers: { 'Authorization': 'Bearer ' + (sessionStorage.getItem('access_token') || '') },
      data: JSON.stringify(payload),

      complete: function(){
        $('#btn-spinner').addClass('d-none'); $btn.prop('disabled', false);
      },

      success: function(){
        toast('Sales order berhasil dibuat', 'success');
        setTimeout(function(){ window.location.href = "{{ route('penjualan.produk.index') }}"; }, 900);
      },

      statusCode: {
        401: function(xhr){
          toast((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unauthorized', 'danger');
        },
        422: function(xhr){
          const r = xhr.responseJSON || {};
          const bag = r.errors || r.error || {};
          if(bag.code) $('#error_code').text(Array.isArray(bag.code)?bag.code[0]:bag.code);
          if(bag.customer_id) $('#error_customer_id').text(Array.isArray(bag.customer_id)?bag.customer_id[0]:bag.customer_id);
          if(bag.sale_date) $('#error_sale_date').text(Array.isArray(bag.sale_date)?bag.sale_date[0]:bag.sale_date);
          if(bag.status) $('#error_status').text(Array.isArray(bag.status)?bag.status[0]:bag.status);
          Object.keys(bag||{}).forEach(function(k){
            const m = k.match(/^items\.(\d+)\.(.+)$/);
            if(m){
              const idx = +m[1], field = m[2];
              const $tr = $('#items_tbody tr').eq(idx);
              if(field === 'product_id'){
                $tr.find('.itm-err-product').text(Array.isArray(bag[k]) ? bag[k][0] : bag[k]);
              }
            }
          });
          toast(r.message ? String(r.message) : 'Validasi gagal', 'danger');
        }
      },

      error: function(){
        toast('Terjadi kesalahan pada server atau jaringan.', 'danger');
      }
    });
  });
});
</script>
@endsection
