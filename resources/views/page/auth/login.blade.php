<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Our-ERP | Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        crossorigin="anonymous" />

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="{{ asset('adminlte/css/adminlte.css') }}">
</head>

<body class="login-page bg-body-secondary">
    <!-- Toast container -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1100">
        <div id="liveToast"
     class="toast align-items-center text-bg-primary border-0"
     role="alert" aria-live="assertive" aria-atomic="true"
     data-bs-autohide="true" data-bs-delay="1000">

            <div class="d-flex">
                <div class="toast-body" id="toast-message">
                    <!-- Pesan toast -->
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                    aria-label="Close"></button>
            </div>
        </div>
    </div>


    <div class="login-box">
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <h2 class="h1">Our-ERP</h2>
            </div>

            <div class="card-body">
                <p class="login-box-msg">Sign in to start your session</p>

                {{-- Error messages --}}
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Logout success --}}
                @if (session('logout_success'))
                <div class="alert alert-success">{{ session('logout_success') }}</div>
                @endif

                <form method="POST" id="login_form">
                    @csrf

                    <div class="input-group mb-3">
                        <div class="form-floating flex-grow-1">
                            <label for="loginEmail">Email</label>
                            <input type="email" name="email" id="loginEmail" class="form-control" placeholder="Email"
                                value="{{ old('email') }}" required autofocus>
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <div class="form-floating flex-grow-1">
                            <label for="loginPassword">Password</label>
                            <input type="password" name="password" id="loginPassword" class="form-control"
                                placeholder="Password" required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                    </div>

                    <button type="button" id="btn-submit" class="btn btn-primary w-100">Sign In</button>
                </form>
            </div>
        </div>
    </div>

    <!-- AdminLTE JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" crossorigin="anonymous">
    </script>
    <script src="{{ asset('adminlte/js/adminlte.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        function showToast(message, type = 'primary') {
            // type: primary, success, danger, warning, info
            const toastEl = $('#liveToast');
            const bodyEl = $('#toast-message');

            // Reset kelas warna dulu
            toastEl.removeClass(function (index, className) {
                return (className.match(/(^|\s)text-bg-\S+/g) || []).join(' ');
            });

            // Set warna baru sesuai type
            toastEl.addClass('text-bg-' + type);

            // Set pesan
            bodyEl.text(message);

            // Tampilkan toast (pakai Bootstrap API)
            const toast = new bootstrap.Toast(toastEl[0]);
            toast.show();
        }

    </script>


</body>
<script>
    $(document).ready(function () {
        $('#btn-submit').on('click', function (e) {
            e.preventDefault();

            const $btn = $(this); // langsung ambil tombol
            const originalBtnText = $btn.text();

            $.ajax({
                url: "{{ url('api/auth/login') }}", // ✅ otomatis sesuai base_url Laravel
                type: "POST",
                data: JSON.stringify({
                    email: $('#loginEmail').val(),
                    password: $('#loginPassword').val(),
                    remember: $('#remember').is(':checked') ? 1 : 0
                }),
                contentType: "application/json",
                dataType: "json",

                beforeSend: function () {
                    // $btn.prop('disabled', true).html(
                    //     '<div class="spinner-border spinner-border-sm" role="status"><span class="visually-hidden">Loading...</span></div>'
                    // );
                },

                complete: function () {
                    $btn.prop('disabled', false).text(originalBtnText);
                },

                success: function (response) {
                    if (response && response.access_token) {
                        sessionStorage.setItem("access_token", response.access_token);
                        sessionStorage.setItem("user", JSON.stringify(response.user || {}));
                        sessionStorage.setItem("logged_in", "1");

                        showToast("Login berhasil, selamat datang " + (response.user?.name || "User"), "success");

                        setTimeout(function () {
                            window.location.href = "{{ url('dashboard') }}";
                        }, 1500);
                    } else {
                        showToast("Login gagal. Respons tidak valid.", "danger");
                    }
                },

                statusCode: {
                401: function (xhr) {
                    var msg = (xhr && xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : "Email atau password salah.";
                    showToast(msg, "danger");
                },
                422: function (xhr) {
                    var res = (xhr && xhr.responseJSON) ? xhr.responseJSON : {};
                    // Laravel bisa return { errors: {field:[msg]} } atau { error: {field:msg} }
                    var bag = res.errors || res.error || {};
                    $.each(bag, function (key, value) {
                    var msg = Array.isArray(value) ? value[0] : value;
                    $("#error_" + key).text(msg);
                    });
                    showToast(res.message ? String(res.message) : "Validasi gagal. Periksa input Anda.", "warning");
                }
                },

                error: function () {
                    showToast("Terjadi kesalahan pada server atau jaringan.", "danger");
                }

            });
        });
    });

</script>


</html>
