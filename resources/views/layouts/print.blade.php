<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'تقرير الطباعة')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Cairo", sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
            font-variation-settings: "slnt" 0;
            min-height: 100vh;
            background-color: #fff;
            color: #000;
        }
        .print-header, .print-footer {
            text-align: center;
            padding: 10px;
        }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="print-header text-center mb-4">
        <img src="{{ asset('img/logo.png') }}" alt="Logo" style="width: 100px;">
        <h2>{{ $company->name }}</h2>
        <hr>
    </div>

    @yield('content')

    <div class="print-footer text-center mt-4">
        <p>تاريخ الطباعة: <span id="printDate">({{ Carbon\Carbon::now()->format('Y-m-d') }})</span> اعد بواسطة {{ Auth::user()->name }}</p>
    </div>

    <div class="text-center no-print my-3">
        <button class="btn btn-primary fw-bold" onclick="window.print()">
            طباعة
            <i class="fa-solid fa-print"></i>
        </button>
    </div>
</body>
</html>
