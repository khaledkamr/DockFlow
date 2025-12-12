<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dockflow - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css"
        rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        :root {
            --blue-1: #0b56a9;
            --blue-2: #218bab;
            --blue-3: #2cacbd;
            --blue-4: #42b3af;
            --blue-5: #52d6cb;
            --gradient: linear-gradient(135deg, #42b3af 0%, #0b56a9 100%);
            --sidebar-width: 300px;
        }

        body {
            font-family: "Cairo", sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
            font-variation-settings: "slnt" 0;
            background-color: #eeeeee;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
        }

        .sidebar::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-track {
            background: #121212 !important;
        }

        ::-webkit-scrollbar-thumb {
            background: #0d6efd !important;
            cursor: grab;
            transition: 0.3s;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #0a57ca;
        }

        ::selection {
            background-color: rgba(13, 110, 253, 0.2);
            color: #0056d8;
        }

        /* Sidebar styling */
        .sidebar {
            width: var(--sidebar-width);
            position: fixed;
            top: 0;
            right: 0;
            height: 100vh;
            overflow-y: auto;
            background-color: #fff;
            box-shadow: -2px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 1050;
            transition: transform 0.3s ease-in-out;
        }

        .sidebar .nav-link:hover {
            background: linear-gradient(135deg, #42b3af75 0%, #0b55a9d0 100%);
            color: #ffffff !important;
            transition: 0.5s;
        }

        /* Main content area */
        .main-content {
            margin-right: var(--sidebar-width);
            transition: margin-right 0.3s ease-in-out;
        }

        /* Mobile sidebar hidden by default */
        @media (max-width: 991px) {
            .sidebar {
                transform: translateX(100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-right: 0;
            }

            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1040;
                display: none;
            }

            .sidebar-overlay.show {
                display: block;
            }
        }

        /* Mobile menu toggle button */
        .mobile-menu-toggle {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--gradient);
            color: white;
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            z-index: 1030;
            font-size: 24px;
            transition: all 0.3s ease;
        }

        .mobile-menu-toggle:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        }

        @media (max-width: 991px) {
            .mobile-menu-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }

        /* Logo gradient text */
        .logo-gradient {
            background: linear-gradient(45deg, #42b3af, #0b56a9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Parent link arrow */
        .parent-link::after {
            content: '\f078';
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            left: 15px;
            transition: transform 0.3s ease;
        }

        .parent-link.collapsed::after {
            transform: rotate(0deg);
        }

        .parent-link:not(.collapsed)::after {
            transform: rotate(180deg);
        }

        /* Navbar responsive adjustments */
        @media (max-width: 991px) {
            .navbar .d-flex.mx-auto {
                width: 100% !important;
                margin: 10px 0 !important;
            }

            .navbar .d-flex.align-items-center.gap-3 {
                justify-content: space-between;
                width: 100%;
                margin-top: 10px;
            }

            .navbar .me-5 {
                margin-right: 0 !important;
            }
        }

        @media (max-width: 576px) {
            .navbar-brand {
                font-size: 16px;
            }

            .navbar .d-flex.align-items-center .rounded-circle {
                width: 35px !important;
                height: 35px !important;
            }

            .navbar .d-flex.flex-column span {
                font-size: 12px !important;
            }
        }

        /* Table responsive */
        .table-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow-x: auto;
        }

        @media (max-width: 768px) {
            .table {
                font-size: 12px;
            }

            .table th,
            .table td {
                padding: 10px 8px;
            }
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .table thead {
            background-color: #f8f9fa;
            color: #333;
        }

        .table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            border-bottom: 1px solid #e9ecef;
        }

        .table td {
            padding: 15px;
            font-size: 14px;
            color: #333;
            border-bottom: 1px solid #e9ecef;
        }

        .table tbody tr:hover {
            background-color: #f1f3f5;
        }

        /* Status badges */
        .status-waiting {
            background-color: #ffe590;
            color: #856404;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
        }

        .status-available {
            background-color: #d4d7ed;
            color: #151657;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
        }

        .status-delivered {
            background-color: #c1eccb;
            color: #155724;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
        }

        .status-danger {
            background-color: #f8d7da;
            color: #721c24;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
        }

        .status-info {
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
        }

        .status-purple {
            background-color: #e7d6f7;
            color: #5a2d7a;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
        }

        /* Pagination */
        .pagination {
            margin: 0;
            padding: 0;
            flex-wrap: wrap;
        }

        .pagination .page-item .page-link {
            color: #0d6efd;
            border: 1px solid #0d6efd;
            padding: 8px 16px;
            margin: 2px;
            border-radius: 4px;
            transition: 0.3s;
        }

        .pagination .page-item.active .page-link,
        .pagination .page-item .page-link:hover {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: white;
        }

        .pagination .page-item.disabled .page-link {
            color: #fff;
            pointer-events: none;
            background-color: #d5d7d8;
            border-color: #dee2e6;
        }

        /* Nav tabs */
        .nav-tabs .nav-link {
            color: #495057;
        }

        .nav-tabs .nav-link.active {
            background-color: #ffffff;
            border-color: #48a0ff #48a0ff #ffffff;
            color: #007bff;
            font-weight: bold;
        }

        /* Go to top button */
        .go-to-top {
            position: fixed;
            bottom: 30px;
            left: 30px;
            width: 50px;
            height: 50px;
            background-color: #0b56a9;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        @media (max-width: 991px) {
            .go-to-top {
                bottom: 90px;
                left: 20px;
                width: 45px;
                height: 45px;
            }
        }

        .go-to-top:hover {
            background-color: #217cab;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
        }

        .go-to-top.show {
            opacity: 1;
            visibility: visible;
        }

        .go-to-top:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        /* Content padding adjustment for mobile */
        @media (max-width: 768px) {
            .content-area {
                padding: 1rem !important;
            }
        }

        /* Sidebar close button */
        .sidebar-close {
            display: none;
            position: absolute;
            top: 15px;
            left: 15px;
            background: transparent;
            border: none;
            font-size: 24px;
            color: #333;
            z-index: 10;
        }

        @media (max-width: 991px) {
            .sidebar-close {
                display: block;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar p-3" id="sidebar">
        <button class="sidebar-close" id="sidebarClose">
            <i class="fa-solid fa-times"></i>
        </button>

        <div class="text-center mb-4">
            <img class="" src="{{ asset('img/logo.png') }}" width="150px" alt="logo">
            <h2 class="fw-bold logo-gradient">DockFlow</h2>
        </div>

        <ul class="nav flex-column">
            <li class="nav-item mb-1">
                <a class="nav-link fw-bold rounded px-3 py-2"
                    style="{{ request()->routeIs('dashboard') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                    href="{{ route('dashboard') }}">
                    <i class="fa-solid fa-house ms-2 me-2"></i> الصفحة الرئيسيـــة
                </a>
            </li>

            <li class="nav-item mb-1">
                <a class="nav-link parent-link fw-bold rounded px-3 py-2 text-dark position-relative {{ request()->routeIs(['relation*']) ? '' : 'collapsed' }}"
                    data-bs-toggle="collapse" href="#relation-management" role="button"
                    aria-expanded="{{ request()->routeIs(['relation*']) ? 'true' : 'false' }}"
                    aria-controls="relation-management">
                    <i class="fa-solid fa-handshake ms-2 me-2"></i> إدارة العلاقــــــــات
                </a>
                <div class="collapse bg-body-secondary rounded mx-2 mt-1 {{ request()->routeIs(['relation*']) ? 'show' : '' }}"
                    id="relation-management">
                    <a class="nav-link fw-bold rounded m-1 px-3 py-2"
                        style="{{ request()->routeIs('relation.customers') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                        href="{{ route('relation.customers') }}">
                        <i class="fa-solid fa-users ms-2 me-2"></i> العمــلاء
                    </a>
                    <a class="nav-link fw-bold rounded m-1 px-3 py-2"
                        style="{{ request()->routeIs('relation.suppliers') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                        href="{{ route('relation.suppliers') }}">
                        <i class="fa-solid fa-truck-loading ms-2 me-2"></i> الموردين
                    </a>
                    <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                        style="{{ request()->routeIs('relation.drivers.vehicles') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                        href="{{ route('relation.drivers.vehicles') }}">
                        <i class="fa-solid fa-truck me-2"></i> النقــــل
                    </a>
                </div>
            </li>

            @can('إدارة المستخدمين')
                <li class="nav-item mb-1">
                    <a class="nav-link parent-link fw-bold rounded px-3 py-2 text-dark position-relative {{ request()->routeIs(['admin*']) ? '' : 'collapsed' }}"
                        data-bs-toggle="collapse" href="#admin-management" role="button"
                        aria-expanded="{{ request()->routeIs(['admin*']) ? 'true' : 'false' }}"
                        aria-controls="admin-management">
                        <i class="fa-solid fa-users-gear ms-2 me-2"></i> إدارة المستخدمين
                    </a>
                    <div class="collapse bg-body-secondary rounded mx-2 mt-1 {{ request()->routeIs(['admin*']) ? 'show' : '' }}"
                        id="admin-management">
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                            style="{{ request()->routeIs('admin.users') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                            href="{{ route('admin.users') }}">
                            <i class="fa-solid fa-user-tie ms-2 me-2"></i> المستخدمين
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                            style="{{ request()->routeIs('admin.roles') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                            href="{{ route('admin.roles') }}">
                            <i class="fa-solid fa-shield-halved ms-2 me-2"></i> الصلاحيات
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                            style="{{ request()->routeIs('admin.logs') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                            href="{{ route('admin.logs') }}">
                            <i class="fa-solid fa-file-lines ms-2 me-2"></i> السجـــلات
                        </a>
                    </div>
                </li>
            @endcan

            @can('عرض العقود')
                <li class="nav-item mb-1">
                    <a class="nav-link parent-link fw-bold rounded px-3 py-2 text-dark position-relative {{ request()->routeIs(['contracts*']) ? '' : 'collapsed' }}"
                        data-bs-toggle="collapse" href="#contract-management" role="button"
                        aria-expanded="{{ request()->routeIs(['contracts*']) ? 'true' : 'false' }}"
                        aria-controls="contract-management">
                        <i class="fa-solid fa-file ms-2 me-2"></i> إدارة العقــــــــــــود
                    </a>
                    <div class="collapse bg-body-secondary rounded mx-2 mt-1 {{ request()->routeIs(['contracts*']) ? 'show' : '' }}"
                        id="contract-management">
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                            style="{{ request()->routeIs('contracts') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                            href="{{ route('contracts') }}">
                            <i class="fa-solid fa-file-contract ms-2 me-2"></i> العقــــــود
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                            style="{{ request()->routeIs('contracts.create') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                            href="{{ route('contracts.create') }}">
                            <i class="fa-solid fa-file-circle-plus ms-2 me-2"></i> إضافة عقــــد
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                            style="{{ request()->routeIs('contracts.services') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                            href="{{ route('contracts.services') }}">
                            <i class="fa-solid fa-screwdriver-wrench ms-2 me-2"></i> الخدمـــــات
                        </a>
                    </div>
                </li>
            @endcan

            @if (auth()->user()->company->hasModule('نقل'))
                <li class="nav-item mb-1">
                    <a class="nav-link parent-link fw-bold rounded px-3 py-2 text-dark position-relative {{ request()->routeIs(['shipping.policies*']) ? '' : 'collapsed' }}"
                        data-bs-toggle="collapse" href="#shipping-policy-management" role="button"
                        aria-expanded="{{ request()->routeIs(['shipping.policies*']) ? 'true' : 'false' }}"
                        aria-controls="shipping-policy-management">
                        <i class="fa-solid fa-shipping-fast ms-2 me-2"></i> بوالــــص الشحــــن
                    </a>
                    <div class="collapse bg-body-secondary rounded mx-2 mt-1 {{ request()->routeIs(['shipping.policies*']) ? 'show' : '' }}"
                        id="shipping-policy-management">
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                            style="{{ request()->routeIs('shipping.policies') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                            href="{{ route('shipping.policies') }}">
                            <i class="fa-solid fa-file-contract ms-2 me-2"></i> بوالص الشحن
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                            style="{{ request()->routeIs('shipping.policies.create') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                            href="{{ route('shipping.policies.create') }}">
                            <i class="fa-solid fa-file-circle-plus ms-1 me-2"></i> إضافة بوليصة شحن
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                            style="{{ request()->routeIs('shipping.policies.reports') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                            href="{{ route('shipping.policies.reports') }}">
                            <i class="fa-solid fa-file-lines ms-2 me-2"></i> تقارير بوالص الشحن
                        </a>
                    </div>
                </li>
            @endif

            @if (auth()->user()->company->hasModule('تخزين'))
                @can('عرض الحاويات')
                    <li class="nav-item mb-1">
                        <a class="nav-link parent-link fw-bold rounded px-3 py-2 text-dark position-relative {{ request()->routeIs(['yard*']) ? '' : 'collapsed' }}"
                            data-bs-toggle="collapse" href="#yard-management" role="button"
                            aria-expanded="{{ request()->routeIs(['yard*']) ? 'true' : 'false' }}"
                            aria-controls="yard-management">
                            <i class="fa-solid fa-boxes-stacked me-2 ms-2"></i> الســـــــاحــــــــــــة
                        </a>
                        <div class="collapse bg-body-secondary rounded mx-2 mt-1 {{ request()->routeIs(['yard*']) ? 'show' : '' }}"
                            id="yard-management">
                            <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                                style="{{ request()->routeIs('yard.containers') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                                href="{{ route('yard.containers') }}">
                                <i class="fa-solid fa-boxes-stacked ms-2 me-2"></i> الحــــــاويـــات
                            </a>
                            <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                                style="{{ request()->routeIs('yard.containers.types') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                                href="{{ route('yard.containers.types') }}">
                                <i class="fa-solid fa-sitemap ms-2 me-2"></i> أنواع الحاويات
                            </a>
                            <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                                style="{{ request()->routeIs('yard.containers.reports') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                                href="{{ route('yard.containers.reports') }}">
                                <i class="fa-solid fa-file-lines ms-2 me-2"></i> تقارير الحاويات
                            </a>
                        </div>
                    </li>
                @endcan
            @endif

            @if (auth()->user()->company->hasModule('تخزين'))
                @can('عرض الإتفاقيات')
                    <li class="nav-item mb-1">
                        <a class="nav-link parent-link fw-bold rounded px-3 py-2 text-dark position-relative {{ request()->routeIs(['policies*']) ? '' : 'collapsed' }}"
                            data-bs-toggle="collapse" href="#policy-management" role="button"
                            aria-expanded="{{ request()->routeIs(['policies*']) ? 'true' : 'false' }}"
                            aria-controls="policy-management">
                            <i class="fa-solid fa-file ms-2 me-2"></i> بوالـص التخزيـــــــن
                        </a>
                        <div class="collapse bg-body-secondary rounded mx-2 mt-1 {{ request()->routeIs(['policies*']) ? 'show' : '' }}"
                            id="policy-management">
                            <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                                style="{{ request()->routeIs('policies') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                                href="{{ route('policies') }}">
                                <i class="fa-solid fa-file-contract ms-2 me-2"></i> بوالص التخزين
                            </a>
                            <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                                style="{{ request()->routeIs('policies.storage.create') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                                href="{{ route('policies.storage.create') }}">
                                <i class="fa-solid fa-file-circle-plus ms-2 me-2"></i> إضافة بوليصة تخزين
                            </a>
                            <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                                style="{{ request()->routeIs('policies.receive.create') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                                href="{{ route('policies.receive.create') }}">
                                <i class="fa-solid fa-file-circle-plus ms-2 me-2"></i> إضافة بوليصة تسليم
                            </a>
                            <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                                style="{{ request()->routeIs('policies.services.create') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                                href="{{ route('policies.services.create') }}">
                                <i class="fa-solid fa-file-circle-plus ms-2 me-2"></i> إضافة بوليصة خدمات
                            </a>
                        </div>
                    </li>
                @endcan
            @endif

            @if (auth()->user()->company->hasModule('تخليص'))
                @can('عرض الإتفاقيات')
                    <li class="nav-item mb-1">
                        <a class="nav-link parent-link fw-bold rounded px-3 py-2 text-dark position-relative {{ request()->routeIs(['transactions*']) ? '' : 'collapsed' }}"
                            data-bs-toggle="collapse" href="#transaction-management" role="button"
                            aria-expanded="{{ request()->routeIs(['transactions*']) ? 'true' : 'false' }}"
                            aria-controls="transaction-management">
                            <i class="fa-solid fa-file ms-2 me-2"></i> معاملات التخليـــص
                        </a>
                        <div class="collapse bg-body-secondary rounded mx-2 mt-1 {{ request()->routeIs(['transactions*']) ? 'show' : '' }}"
                            id="transaction-management">
                            <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                                style="{{ request()->routeIs('transactions') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                                href="{{ route('transactions') }}">
                                <i class="fa-solid fa-file-contract ms-2 me-2"></i> المعامـــــــــلات
                            </a>
                            <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                                style="{{ request()->routeIs('transactions.create') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                                href="{{ route('transactions.create') }}">
                                <i class="fa-solid fa-file-circle-plus ms-2 me-2"></i> إضافة معاملة
                            </a>
                            <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                                style="{{ request()->routeIs('transactions.reports') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                                href="{{ route('transactions.reports') }}">
                                <i class="fa-solid fa-chart-line ms-2 me-2"></i> تقارير المعاملات
                            </a>
                            <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                                style="{{ request()->routeIs('transactions.transportOrders') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                                href="{{ route('transactions.transportOrders') }}">
                                <i class="fa-solid fa-copy ms-2 me-2"></i> إشعارات النقل
                            </a>
                            <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                                style="{{ request()->routeIs('transactions.transportOrders.create') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                                href="{{ route('transactions.transportOrders.create') }}">
                                <i class="fa-solid fa-truck ms-2 me-2"></i> إضافة إشعار نقل
                            </a>
                            <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                                style="{{ request()->routeIs('transactions.transportOrders.reports') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                                href="{{ route('transactions.transportOrders.reports') }}">
                                <i class="fa-solid fa-chart-line ms-2 me-2"></i> تقارير إشعارات النقل
                            </a>
                        </div>
                    </li>
                @endcan
            @endif

            @can('عرض الفواتير')
                <li class="nav-item mb-1">
                    <a class="nav-link parent-link fw-bold rounded px-3 py-2 text-dark position-relative {{ request()->routeIs(['invoices*']) ? '' : 'collapsed' }}"
                        data-bs-toggle="collapse" href="#invoice-management" role="button"
                        aria-expanded="{{ request()->routeIs(['invoices*']) ? 'true' : 'false' }}"
                        aria-controls="invoice-management">
                        <i class="fa-solid fa-receipt ms-2 me-2"></i> إدارة الفــــواتيــــــر
                    </a>
                    <div class="collapse bg-body-secondary rounded mx-2 mt-1 {{ request()->routeIs(['invoices*']) ? 'show' : '' }}"
                        id="invoice-management">
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                            style="{{ request()->routeIs('invoices') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                            href="{{ route('invoices') }}">
                            <i class="fa-solid fa-scroll ms-2 me-2"></i> الفــــواتيـــــر
                        </a>
                        @if (auth()->user()->company->hasModule('تخزين'))
                            <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                                style="{{ request()->routeIs('invoices.create') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                                href="{{ route('invoices.create') }}">
                                <i class="fa-solid fa-circle-plus ms-2 me-2"></i> إنشاء فاتورة تخزين
                            </a>
                        @endif
                        @if (auth()->user()->company->hasModule('نقل'))
                            <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                                style="{{ request()->routeIs('invoices.shipping.create') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                                href="{{ route('invoices.shipping.create') }}">
                                <i class="fa-solid fa-circle-plus ms-2 me-2"></i> إنشاء فاتورة شحن
                            </a>
                        @endif
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                            style="{{ request()->routeIs('invoices.reports') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                            href="{{ route('invoices.reports') }}">
                            <i class="fa-solid fa-chart-line ms-2 me-2"></i> تقارير الفواتير
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                            style="{{ request()->routeIs('invoices.statements') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                            href="{{ route('invoices.statements') }}">
                            <i class="fa-solid fa-layer-group ms-2 me-2"></i> المطالبــــــات
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                            style="{{ request()->routeIs('invoices.statements.create') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                            href="{{ route('invoices.statements.create') }}">
                            <i class="fa-solid fa-circle-plus ms-2 me-2"></i> إنشاء مطالبة
                        </a>
                    </div>
                </li>
            @endcan

            @can('عرض الفواتير')
                <li class="nav-item mb-1">
                    <a class="nav-link parent-link fw-bold rounded px-3 py-2 text-dark position-relative {{ request()->routeIs(['expense*']) ? '' : 'collapsed' }}"
                        data-bs-toggle="collapse" href="#expense-management" role="button"
                        aria-expanded="{{ request()->routeIs(['expense*']) ? 'true' : 'false' }}"
                        aria-controls="expense-management">
                        <i class="fa-solid fa-shopping-cart ms-2 me-2"></i> المشـــــتريــــــــات
                    </a>
                    <div class="collapse bg-body-secondary rounded mx-2 mt-1 {{ request()->routeIs(['expense*']) ? 'show' : '' }}"
                        id="expense-management">
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                            style="{{ request()->routeIs('expense.invoices') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                            href="{{ route('expense.invoices') }}">
                            <i class="fa-solid fa-file-invoice-dollar ms-2 me-2"></i> فواتير المصاريف
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                            style="{{ request()->routeIs('expense.invoices.create') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                            href="{{ route('expense.invoices.create') }}">
                            <i class="fa-solid fa-file-circle-plus  me-2"></i> إنشاء فاتورة مصاريف
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                            style="{{ request()->routeIs('expense.invoices.reports') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                            href="{{ route('expense.invoices.reports') }}">
                            <i class="fa-solid fa-chart-bar  me-2"></i> تقارير الفواتير
                        </a>
                    </div>
                </li>
            @endcan

            @can('الإدارة المالية')
                <li class="nav-item mb-1">
                    <a class="nav-link parent-link fw-bold rounded px-3 py-2 text-dark position-relative {{ request()->routeIs(['money*']) ? '' : 'collapsed' }}"
                        data-bs-toggle="collapse" href="#money-management" role="button"
                        aria-expanded="{{ request()->routeIs(['money*']) ? 'true' : 'false' }}"
                        aria-controls="money-management">
                        <i class="fa-solid fa-money-check-dollar ms-2 me-2"></i> الإدارة المـــاليــــة
                    </a>
                    <div class="collapse bg-body-secondary rounded mx-2 mt-1 {{ request()->routeIs(['money*']) ? 'show' : '' }}"
                        id="money-management">
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                            style="{{ request()->routeIs('money.entries') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                            href="{{ route('money.entries') }}">
                            <i class="fa-solid fa-money-bill-transfer ms-2 me-2"></i> القيود والسنـدات
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                            style="{{ request()->routeIs('money.tree') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                            href="{{ route('money.tree') }}">
                            <i class="fa-solid fa-folder-tree ms-2 me-2"></i> دلـــيل الحسابـــات
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                            style="{{ request()->routeIs('money.cost.centers') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                            href="{{ route('money.cost.centers') }}">
                            <i class="fa-solid fa-code-branch ms-2 me-2"></i> مــراكـــز التكلفـــة
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2"
                            style="{{ request()->routeIs('money.reports') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                            href="{{ route('money.reports') }}">
                            <i class="fa-solid fa-receipt ms-2 me-2"></i> التقاريـــــر الماليـــــة
                        </a>
                    </div>
                </li>
            @endcan
        </ul>
    </div>

    <!-- Mobile Menu Toggle Button -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">
        <i class="fa-solid fa-bars"></i>
    </button>

    <div class="main-content">
        <!-- Navbar -->
        @include('layouts.navbar')

        <button class="go-to-top btn btn-primary rounded-circle border-0" id="goToTopBtn" onclick="scrollToTop()"
            title="العودة إلى الأعلى">
            <i class="fa-solid fa-angles-up"></i>
        </button>

        <div class="content-area p-4">
            @yield('content')
        </div>

        @if (session('success'))
            @push('scripts')
                <script>
                    showToast(`{!! session('success') !!}`, "success");
                </script>
            @endpush
        @endif

        @if (session('error'))
            @push('scripts')
                <script>
                    showToast("{{ session('error') }}", "danger");
                </script>
            @endpush
        @endif

        @if (session('errors'))
            @push('scripts')
                <script>
                    showToast("حدث خطأ في العملية الرجاء مراجعة البيانات", "danger");
                </script>
            @endpush
        @endif

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                @push('scripts')
                    <script>
                        showToast("{{ $error }}", "danger");
                    </script>
                @endpush
            @endforeach
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        document.addEventListener("DOMContentLoaded", function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Listen for scroll events
            window.addEventListener('scroll', toggleGoToTopButton);

            // Initial check for go to top button
            toggleGoToTopButton();

            // Handle sidebar collapse events
            const collapseElements = document.querySelectorAll('.collapse');
            collapseElements.forEach(function(collapseEl) {
                const parentLink = document.querySelector(`[href="#${collapseEl.id}"]`);

                collapseEl.addEventListener('show.bs.collapse', function() {
                    parentLink.classList.remove('collapsed');
                });

                collapseEl.addEventListener('hide.bs.collapse', function() {
                    parentLink.classList.add('collapsed');
                });
            });

            // Mobile sidebar functionality
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const mobileMenuToggle = document.getElementById('mobileMenuToggle');
            const sidebarClose = document.getElementById('sidebarClose');

            function toggleSidebar() {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');

                // Change icon
                const icon = mobileMenuToggle.querySelector('i');
                if (sidebar.classList.contains('show')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }

            mobileMenuToggle.addEventListener('click', toggleSidebar);
            sidebarClose.addEventListener('click', toggleSidebar);
            sidebarOverlay.addEventListener('click', toggleSidebar);

            // Close sidebar when clicking on a link (on mobile)
            if (window.innerWidth <= 991) {
                const sidebarLinks = sidebar.querySelectorAll('.nav-link');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', function(e) {
                        // Don't close if it's a parent link (collapsible)
                        if (!this.classList.contains('parent-link')) {
                            setTimeout(toggleSidebar, 300);
                        }
                    });
                });
            }
        });

        // Toast notification function
        function showToast(message, type = 'info') {
            const toastContainer = document.getElementById('toastContainer') || createToastContainer();

            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type} border-0`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-3 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;

            toastContainer.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();

            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        }

        // Create toast container
        function createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container position-fixed top-0 end-0 p-4 pt-5 mt-5';
            container.style.zIndex = '1055';
            document.body.appendChild(container);
            return container;
        }

        // Go to Top functionality
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Show/hide go to top button
        function toggleGoToTopButton() {
            const goToTopBtn = document.getElementById('goToTopBtn');
            if (window.pageYOffset > 300) {
                goToTopBtn.classList.add('show');
            } else {
                goToTopBtn.classList.remove('show');
            }
        }
    </script>
    @stack('scripts')
</body>

</html>
