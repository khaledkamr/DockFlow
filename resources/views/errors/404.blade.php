<!DOCTYPE html>
<html lang="ar" dir="rtl" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dockflow - الصفحة غير موجودة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: "Cairo", sans-serif;
        }

        .error-code {
            font-size: 150px;
            background: linear-gradient(135deg, #52d6cb 0%, #218bab 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
</head>

<body class="min-vh-100 d-flex align-items-center justify-content-center p-4">
    <div class="text-center" style="max-width: 600px;">
        <h1 class="error-code fw-bold lh-1 mb-0">404</h1>

        <h2 class="fs-3 fw-bold text-light mb-3">الصفحة غير موجودة</h2>

        <p class="fs-5 text-secondary mb-4 lh-lg">
            عذراً، الصفحة التي تبحث عنها غير موجودة.
            <br>
            يرجى التحقق من الرابط أو العودة إلى الصفحة الرئيسية.
        </p>

        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="javascript:history.back()"
                class="btn btn-outline-light rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2">
                <i class="fas fa-arrow-right"></i>
                العودة للخلف
            </a>
            <a href="{{ url('/') }}"
                class="btn btn-info rounded-pill px-4 py-2 fw-semibold d-inline-flex align-items-center gap-2 shadow">
                <i class="fas fa-home"></i>
                الصفحة الرئيسية
            </a>
        </div>

        {{-- <div class="mt-5 p-4 bg-white rounded-4 shadow-sm">
            <p class="text-muted mb-0 small">
                <i class="fas fa-lightbulb me-2"></i>
                إذا كنت تعتقد أن هذا خطأ، يرجى التواصل مع الدعم الفني
            </p>
        </div> --}}
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
