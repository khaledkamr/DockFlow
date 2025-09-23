<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'تقرير الطباعة')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        .no-print {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
        }
        .no-print .btn {
            background: linear-gradient(135deg, #42b3af 0%, #0b56a9 100%);
            transition: 0.3s;
        }
        .no-print .btn:hover {
            transform: scale(1.1);
        }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body onload="initPrint()">
    <div class="d-flex justify-content-between mx-3 mt-3">
        <div class="d-flex flex-column align-items-start">
            <span class="small">تاريخ الطباعة</span>
            <span class="small fw-bold">{{ Carbon\Carbon::now()->format('Y/m/d') }}</span>
        </div>
        <div class="text-center">
            <img src="{{ asset('img/logo.png') }}" alt="Logo" style="width: 100px;">
            <h6 class="fw-bold">{{ $company->name }}</h6>
            <div style="font-size: 10px;">
                CR: {{ $company->CR }} | 
                TIN: {{ $company->TIN }} | 
                phone: <span class="text-primary">{{ $company->phone }}</span> | 
                email: <span class="text-primary">{{ $company->email }} </span>
            </div>
        </div>
        <div class="d-flex flex-column align-items-end">
            <span class="small">أعد بواسطة</span>
            <span class="small fw-semibold">{{ Auth::user()->name }}</span>
        </div>
    </div>
    <hr class="mt-1">

    @yield('content')

    {{-- <div class="text-center no-print my-3">
        <button class="btn text-white border-0 shadow-lg fw-bold" onclick="window.print()">
            طباعة <i class="fa-solid fa-print ps-1"></i>
        </button>
    </div> --}}

    <script>
        function initPrint() {
            window.print();

            window.onafterprint = () => {
                window.close();
            };
        }
    </script>
</body>
</html>
