@extends('layouts.adminlte.admin.main')
@section('title', 'Dashboard')
@section('content')
<!--begin::App Main-->
<main class="app-main">
    <!--begin::App Content Header-->
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Dashboard Our-ERP</h3>
                    <h6 class="mb-0">Rekap Bulan Ini</h6>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#"></a></li>
                        <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                    </ol>
                </div>
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
           
        </div>
        <!--end::Container-->
    </div>
    <!--end::App Content-->
</main>
<!--end::App Main-->
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"
    integrity="sha256-+vh8GkaU7C9/wbSLIcwq82tQ2wTf44aOHA8HlBMwRI8=" crossorigin="anonymous"></script>
<script>
    $(document).ready(function () {
        $.ajax({
            url: "{{ route('dashboard.get_realisasi') }}",
            type: 'GET',
            success: function (response) {
                if (response.success) {
                    $('#realisasi-count').html(response.count);
                } else {
                    $('#realisasi-count').html('<span class="text-danger">Error</span>');
                }
            },
            error: function () {
                $('#realisasi-count').html('<span class="text-danger">Error</span>');
            }
        });
    });

</script>
<script>
    $(document).ready(function () {
        $.ajax({
            url: "{{ route('dashboard.get_perencanaan') }}",
            type: 'GET',
            success: function (response) {
                if (response.success) {
                    $('#perencanaan-count').text(response.count.toLocaleString());
                } else {
                    $('#perencanaan-count').html('<span class="text-danger">Error</span>');
                }
            },
            error: function () {
                $('#perencanaan-count').html('<span class="text-danger">Error</span>');
            }
        });
    });

</script>
<script>
    $(document).ready(function () {
        // AJAX for Total Perencanaan Bulan Ini
        $.ajax({
            url: "{{ route('dashboard.get_total_budget') }}",
            type: 'GET',
            success: function (response) {
                if (response.success) {
                    const formattedBudget = new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(response.total_budget);

                    $('#perencanaan-total').text(formattedBudget);
                    $('#perencanaan-loading').hide();
                } else {
                    $('#perencanaan-total').html('<span class="text-danger">Error</span>');
                }
            },
            error: function () {
                $('#perencanaan-total').html('<span class="text-danger">Error</span>');
            }
        });

        // AJAX for Total Realisasi Bulan Ini
        $.ajax({
            url: "{{ route('dashboard.get_total_realisasi') }}",
            type: 'GET',
            success: function (response) {
                if (response.success) {
                    const formattedRealisasi = new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(response.total_realisasi);
                    $('#realisasi-total').text(formattedRealisasi);
                    $('#realisasi-loading').hide();
                } else {
                    $('#realisasi-total').html('<span class="text-danger">Error</span>');
                }
            },
            error: function () {
                $('#realisasi-total').html('<span class="text-danger">Error</span>');
            }
        });
    });

</script>

<script>
    $(document).ready(function () {
        $.ajax({
            url: "{{ route('dashboard.get_monthly_realisasi') }}",
            type: 'GET',
            success: function (response) {
                if (response.success) {
                    const data = response.data;
                    const labels = response.labels;
                    const total = data.reduce((a, b) => a + b, 0);

                    // Format total as Rupiah
                    const formattedTotal = new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(total);

                    // Update footer
                    $('#total-realisasi-footer').text(formattedTotal);

                    // Draw Chart.js
                    const ctx = document.getElementById('monthly-realisasi-chart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Realisasi (Rp)',
                                data: data,
                                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false, // Allow custom height
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function (value) {
                                            return new Intl.NumberFormat('id-ID', {
                                                style: 'currency',
                                                currency: 'IDR',
                                                minimumFractionDigits: 0
                                            }).format(value);
                                        }
                                    }
                                }
                            }
                        }
                    });
                } else {
                    $('#total-realisasi-footer').text('Error');
                }
            },
            error: function () {
                $('#total-realisasi-footer').text('Error');
            }
        });
    });

</script>
<script>
$(document).ready(function () {
    $.ajax({
        url: "{{ route('dashboard.get_ranking') }}",
        type: 'GET',
        success: function (response) {
            if (!response.success) return;

            // extract labels and values
            const labels = response.ranking.map(item => item.perencanaan_name);
            const values = response.ranking.map(item => item.total_realization);

            // get the canvas context
            const ctx = document.getElementById('ranking-chart').getContext('2d');

            // render pie chart
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        // you can omit backgroundColor to use defaults,
                        // or provide your own array of colors here:
                        // backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed;
                                    return context.label + ': ' +
                                        new Intl.NumberFormat('id-ID', {
                                            style: 'currency',
                                            currency: 'IDR',
                                            minimumFractionDigits: 0
                                        }).format(value);
                                }
                            }
                        }
                    }
                }
            });
        },
        error: function () {
            console.error('Gagal mengambil data ranking.');
        }
    });
});
</script>


@endsection
@section('style')
<style>
    #monthly-realisasi-chart {
        height: 500px !important;
        /* Adjust height as needed */
    }

</style>
@endsection
