<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dockflow - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <style>
        :root {
            --blue-1: #0b56a9;
            --blue-2: #218bab;
            --blue-3: #2cacbd;
            --blue-4: #42b3af;
            --blue-5: #52d6cb;
            --gradient: linear-gradient(135deg, #42b3af 0%, #0b56a9 100%);
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

        /* Sidebar width */
        .sidebar {
            width: 300px;
        }
        .sidebar .nav-link:hover:not(.bg-primary){
            background-color: rgba(0, 38, 255, 0.1);
            color: #0b56a9 !important;
            transition: 0.3s;
        }
        .main-content {
            margin-right: 300px;
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

        /* table styling */
        .table-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
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
        .table .status-waiting {
            background-color: #ffe590;
            color: #856404;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
        }
        .table .status-available {
            background-color: #d4d7ed;
            color: #151657;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
        }
        .table .status-delivered {
            background-color: #c1eccb;
            color: #155724;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
        }
        .table .status-danger {
            background-color: #f8d7da;
            color: #721c24;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
        }
        
        /* paginator styling */
        .pagination {
            margin: 0;
            padding: 0;
        }
        .pagination .page-item .page-link {
            color: #0d6efd;
            border: 1px solid #0d6efd;
            padding: 8px 16px;
            margin: 0 2px;
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

        /* nav link styles */
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
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar bg-white shadow-sm p-3 position-fixed top-0 start-0 vh-100 overflow-auto" style="z-index: 100;">
        <div class="text-center mb-4">
            <img class="" src="{{ asset('img/logo.png') }}" width="150px" alt="logo">
            <h2 class="fw-bold logo-gradient">DockFlow</h2>
        </div>

        <ul class="nav flex-column">
            <li class="nav-item mb-1">
                <a class="nav-link fw-bold rounded px-3 py-2 {{ request()->routeIs('dashboard') ? 'bg-primary text-white' : 'text-dark' }}" 
                   href="{{ route('dashboard') }}">
                    <i class="fa-solid fa-house ms-2 me-2"></i> الصفحة الرئيسيـــة
                </a>
            </li>

            @can('إدارة المستخدمين')
                <li class="nav-item mb-1">
                    <a class="nav-link parent-link fw-bold rounded px-3 py-2 text-dark position-relative {{ request()->routeIs(['relation*']) ? '' : 'collapsed' }}" 
                    data-bs-toggle="collapse" 
                    href="#relation-management" 
                    role="button" 
                    aria-expanded="{{ request()->routeIs(['relation*']) ? 'true' : 'false' }}" 
                    aria-controls="relation-management">
                        <i class="fa-solid fa-handshake ms-2 me-2"></i> إدارة العلاقــــــــات
                    </a>
                    <div class="collapse bg-body-secondary rounded mx-2 mt-1 {{ request()->routeIs(['relation*']) ? 'show' : '' }}" id="relation-management">
                        <a class="nav-link fw-bold rounded m-1 px-3 py-2 {{ request()->routeIs('relation.customers') ? 'bg-primary text-white' : 'text-dark' }}" 
                            href="{{ route('relation.customers') }}">
                            <i class="fa-solid fa-users ms-2 me-2"></i> العمــلاء
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-3 py-2 {{ request()->routeIs('relation.suppliers') ? 'bg-primary text-white' : 'text-dark' }}" 
                            href="{{ route('relation.suppliers') }}">
                            <i class="fa-solid fa-truck-loading ms-2 me-2"></i> الموردين
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2 {{ request()->routeIs('relation.drivers.vehicles') ? 'bg-primary text-white' : 'text-dark' }}" 
                            href="{{ route('relation.drivers.vehicles') }}">
                            <i class="fa-solid fa-truck me-2"></i> النقــــل
                        </a>
                    </div>
                </li>
            @endcan

            @can('إدارة المستخدمين')
                <li class="nav-item mb-1">
                    <a class="nav-link parent-link fw-bold rounded px-3 py-2 text-dark position-relative {{ request()->routeIs(['admin*']) ? '' : 'collapsed' }}" 
                    data-bs-toggle="collapse" 
                    href="#admin-management" 
                    role="button" 
                    aria-expanded="{{ request()->routeIs(['admin*']) ? 'true' : 'false' }}" 
                    aria-controls="admin-management">
                        <i class="fa-solid fa-users-gear ms-2 me-2"></i> إدارة المستخدمين
                    </a>
                    <div class="collapse bg-body-secondary rounded mx-2 mt-1 {{ request()->routeIs(['admin*']) ? 'show' : '' }}" id="admin-management">
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2 {{ request()->routeIs('admin.users') ? 'bg-primary text-white' : 'text-dark' }}" 
                            href="{{ route('admin.users') }}">
                            <i class="fa-solid fa-user-tie ms-2 me-2"></i> الموظفين
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2 {{ request()->routeIs('admin.roles') ? 'bg-primary text-white' : 'text-dark' }}" 
                            href="{{ route('admin.roles') }}">
                            <i class="fa-solid fa-shield-halved ms-2 me-2"></i> الصلاحيات
                        </a>
                    </div>
                </li>
            @endcan
            
            @can('عرض العقود')
                <li class="nav-item mb-1">
                    <a class="nav-link parent-link fw-bold rounded px-3 py-2 text-dark position-relative {{ request()->routeIs(['contracts*']) ? '' : 'collapsed' }}" 
                    data-bs-toggle="collapse" 
                    href="#contract-management" 
                    role="button" 
                    aria-expanded="{{ request()->routeIs(['contracts*']) ? 'true' : 'false' }}" 
                    aria-controls="contract-management">
                        <i class="fa-solid fa-file ms-2 me-2"></i> إدارة العقــــــــــــود
                    </a>
                    <div class="collapse bg-body-secondary rounded mx-2 mt-1 {{ request()->routeIs(['contracts*']) ? 'show' : '' }}" id="contract-management">
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2 {{ request()->routeIs('contracts') ? 'bg-primary text-white' : 'text-dark' }}" 
                        href="{{ route('contracts') }}">
                            <i class="fa-solid fa-file-contract ms-2 me-2"></i> العقــــــود
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2 {{ request()->routeIs('contracts.create') ? 'bg-primary text-white' : 'text-dark' }}" 
                        href="{{ route('contracts.create') }}">
                            <i class="fa-solid fa-file-circle-plus ms-2 me-2"></i> إضافة عقــــد
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2 {{ request()->routeIs('contracts.services') ? 'bg-primary text-white' : 'text-dark' }}" 
                        href="{{ route('contracts.services') }}">
                            <i class="fa-solid fa-screwdriver-wrench ms-2 me-2"></i> الخدمـــــات
                        </a>
                    </div>
                </li>
            @endcan

            @can('عرض الحاويات')
                <li class="nav-item mb-1">
                    <a class="nav-link parent-link fw-bold rounded px-3 py-2 text-dark position-relative {{ request()->routeIs(['yard*']) ? '' : 'collapsed' }}" 
                    data-bs-toggle="collapse" 
                    href="#yard-management" 
                    role="button" 
                    aria-expanded="{{ request()->routeIs(['yard*']) ? 'true' : 'false' }}" 
                    aria-controls="yard-management">
                        <i class="fa-solid fa-warehouse ms-2 me-2"></i> الســـــــاحــــــــــــة
                    </a>
                    <div class="collapse bg-body-secondary rounded mx-2 mt-1 {{ request()->routeIs(['yard*']) ? 'show' : '' }}" id="yard-management">
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2 {{ request()->routeIs('yard.containers') ? 'bg-primary text-white' : 'text-dark' }}" 
                        href="{{ route('yard.containers') }}">
                            <i class="fa-solid fa-boxes-stacked ms-2 me-2"></i> الحــــــاويـــات
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2 {{ request()->routeIs('yard.containers.types') ? 'bg-primary text-white' : 'text-dark' }}" 
                        href="{{ route('yard.containers.types') }}">
                            <i class="fa-solid fa-sitemap ms-2 me-2"></i> أنواع الحاويات
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2 {{ request()->routeIs('yard.containers.reports') ? 'bg-primary text-white' : 'text-dark' }}" 
                        href="{{ route('yard.containers.reports') }}">
                            <i class="fa-solid fa-file-lines ms-2 me-2"></i> تقارير الحاويات
                        </a>
                    </div>
                </li>
            @endcan

            @can('عرض الإتفاقيات')
                <li class="nav-item mb-1">
                    <a class="nav-link parent-link fw-bold rounded px-3 py-2 text-dark position-relative {{ request()->routeIs(['policies*']) ? '' : 'collapsed' }}" 
                    data-bs-toggle="collapse" 
                    href="#policy-management" 
                    role="button" 
                    aria-expanded="{{ request()->routeIs(['policies*']) ? 'true' : 'false' }}" 
                    aria-controls="policy-management">
                        <i class="fa-solid fa-file ms-2 me-2"></i> إدارة الإتفاقيــــــات
                    </a>
                    <div class="collapse bg-body-secondary rounded mx-2 mt-1 {{ request()->routeIs(['policies*']) ? 'show' : '' }}" id="policy-management">
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2 {{ request()->routeIs('policies') ? 'bg-primary text-white' : 'text-dark' }}" 
                            href="{{ route('policies') }}">
                            <i class="fa-solid fa-file-contract ms-2 me-2"></i> الإتفاقيـــــات
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2 {{ request()->routeIs('policies.storage.create') ? 'bg-primary text-white' : 'text-dark' }}" 
                            href="{{ route('policies.storage.create') }}">
                            <i class="fa-solid fa-file-circle-plus ms-2 me-2"></i> إضافة إتفاقية تخزين
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2 {{ request()->routeIs('policies.receive.create') ? 'bg-primary text-white' : 'text-dark' }}" 
                            href="{{ route('policies.receive.create') }}">
                            <i class="fa-solid fa-file-circle-plus ms-2 me-2"></i> إضافة إتفاقية تسليم
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2 {{ request()->routeIs('policies.services.create') ? 'bg-primary text-white' : 'text-dark' }}" 
                            href="{{ route('policies.services.create') }}">
                            <i class="fa-solid fa-file-circle-plus ms-2 me-2"></i> إضافة إتفاقية خدمات
                        </a>
                    </div>
                </li>
            @endcan

            @can('عرض الإتفاقيات')
                <li class="nav-item mb-1">
                    <a class="nav-link parent-link fw-bold rounded px-3 py-2 text-dark position-relative {{ request()->routeIs(['transactions*']) ? '' : 'collapsed' }}" 
                    data-bs-toggle="collapse" 
                    href="#transaction-management" 
                    role="button" 
                    aria-expanded="{{ request()->routeIs(['transactions*']) ? 'true' : 'false' }}" 
                    aria-controls="transaction-management">
                        <i class="fa-solid fa-file ms-2 me-2"></i> معاملات التخليـــص
                    </a>
                    <div class="collapse bg-body-secondary rounded mx-2 mt-1 {{ request()->routeIs(['transactions*']) ? 'show' : '' }}" id="transaction-management">
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2 {{ request()->routeIs('transactions') ? 'bg-primary text-white' : 'text-dark' }}" 
                            href="{{ route('transactions') }}">
                            <i class="fa-solid fa-file-contract ms-2 me-2"></i> المعامـــــــــلات
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2 {{ request()->routeIs('transactions.create') ? 'bg-primary text-white' : 'text-dark' }}" 
                            href="{{ route('transactions.create') }}">
                            <i class="fa-solid fa-file-circle-plus ms-2 me-2"></i> إضافة معاملة
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2 {{ request()->routeIs('transactions.transportOrders') ? 'bg-primary text-white' : 'text-dark' }}" 
                            href="{{ route('transactions.transportOrders') }}">
                            <i class="fa-solid fa-copy ms-2 me-2"></i> إشعارات النقل
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2 {{ request()->routeIs('transactions.transportOrders.create') ? 'bg-primary text-white' : 'text-dark' }}" 
                            href="{{ route('transactions.transportOrders.create') }}">
                            <i class="fa-solid fa-truck ms-2 me-2"></i> إضافة إشعار نقل
                        </a>
                    </div>
                </li>
            @endcan

            @can('عرض الفواتير')
                <li class="nav-item mb-1">
                    <a class="nav-link parent-link fw-bold rounded px-3 py-2 text-dark position-relative {{ request()->routeIs(['invoices*']) ? '' : 'collapsed' }}" 
                        data-bs-toggle="collapse" 
                        href="#invoice-management" 
                        role="button" 
                        aria-expanded="{{ request()->routeIs(['invoices*']) ? 'true' : 'false' }}" 
                        aria-controls="invoice-management">
                        <i class="fa-solid fa-receipt ms-2 me-2"></i> إدارة الفــــواتيــــــر
                    </a>
                    <div class="collapse bg-body-secondary rounded mx-2 mt-1 {{ request()->routeIs(['invoices*']) ? 'show' : '' }}" id="invoice-management">
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2 {{ request()->routeIs('invoices') ? 'bg-primary text-white' : 'text-dark' }}" 
                            href="{{ route('invoices') }}">
                            <i class="fa-solid fa-scroll ms-2 me-2"></i> الفــــواتيـــــر
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2 {{ request()->routeIs('invoices.create') ? 'bg-primary text-white' : 'text-dark' }}" 
                            href="{{ route('invoices.create') }}">
                            <i class="fa-solid fa-circle-plus ms-2 me-2"></i> إنشاء فاتورة
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2 {{ request()->routeIs('invoices.statements') ? 'bg-primary text-white' : 'text-dark' }}" 
                            href="{{ route('invoices.statements') }}">
                            <i class="fa-solid fa-layer-group ms-2 me-2"></i> المطالبــــــات
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2 {{ request()->routeIs('invoices.statements.create') ? 'bg-primary text-white' : 'text-dark' }}" 
                            href="{{ route('invoices.statements.create') }}">
                            <i class="fa-solid fa-circle-plus ms-2 me-2"></i> إنشاء مطالبة
                        </a>
                    </div>
                </li>
            @endcan

            @can('الإدارة المالية')
                <li class="nav-item mb-1">
                    <a class="nav-link parent-link fw-bold rounded px-3 py-2 text-dark position-relative {{ request()->routeIs(['admin.money*']) ? '' : 'collapsed' }}" 
                    data-bs-toggle="collapse" 
                    href="#money-management" 
                    role="button" 
                    aria-expanded="{{ request()->routeIs(['admin.money*']) ? 'true' : 'false' }}" 
                    aria-controls="money-management">
                        <i class="fa-solid fa-money-check-dollar ms-2 me-2"></i> الإدارة المـــاليــــة 
                    </a>
                    <div class="collapse bg-body-secondary rounded mx-2 mt-1 {{ request()->routeIs(['admin.money*']) ? 'show' : '' }}" id="money-management">
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2 {{ request()->routeIs('admin.money.entries') ? 'bg-primary text-white' : 'text-dark' }}" 
                        href="{{ route('admin.money.entries') }}">
                            <i class="fa-solid fa-money-bill-transfer ms-2 me-2"></i> القيود والسنـدات
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2 {{ request()->routeIs('admin.money.tree') ? 'bg-primary text-white' : 'text-dark' }}" 
                        href="{{ route('admin.money.tree') }}">
                            <i class="fa-solid fa-folder-tree ms-2 me-2"></i> دلـــيل الحسابـــات
                        </a>
                        <a class="nav-link fw-bold rounded m-1 px-4 py-2 {{ request()->routeIs('admin.money.reports') ? 'bg-primary text-white' : 'text-dark' }}" 
                        href="{{ route('admin.money.reports') }}">
                            <i class="fa-solid fa-receipt ms-2 me-2"></i> تقاريـــــر القيـــــود
                        </a>
                    </div>
                </li>
            @endcan
        </ul>
    </div>
    
    <div class="main-content">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg bg-white shadow-sm" style="height: 70px;">
            <div class="container-fluid">
                <a class="navbar-brand text-dark fw-bold" href="#">{{ auth()->user()->company->name }}</a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" 
                        aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarContent">
                    <form class="d-flex mx-auto" style="width: 40%;">
                        <div class="input-group">
                            <input class="form-control border-primary" type="search" placeholder="بحث..." aria-label="Search">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>

                    <div class="d-flex align-items-center gap-3 ms-auto">
                        <i class="fa-solid fa-globe fa-xl text-secondary"></i>
                        <i class="fa-solid fa-bell fa-xl text-secondary"></i>
                        
                        <!-- Settings Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-link p-0 border-0" type="button" id="settingsDropdown" 
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-gear fa-xl text-secondary"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsDropdown">
                                <li><a class="dropdown-item" href="#">الإعدادات</a></li>
                                @can('عرض بيانات الشركة')
                                    <li><a class="dropdown-item" href="{{ route('company', auth()->user()->company) }}">بيانات الشركة</a></li>
                                @endcan 
                                <li><a class="dropdown-item" href="{{ route('admin.user.profile', auth()->user()) }}">الملف الشخصي</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fa-solid fa-arrow-right-from-bracket me-1"></i>
                                            تسجيل خروج
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="d-flex align-items-center text-dark me-5">
                            <img src="{{ Auth::user()->avatar ?? asset('img/user-profile.jpg') }}" alt="Profile Photo" class="rounded-circle me-2" style="width: 40px; height: 40px;">
                            <div class="d-flex flex-column">
                                <span class="fw-bold" style="font-size: 14px;">{{ Auth::user()->name }}</span>
                                <span class="text-secondary" style="font-size: 12px;">{{ Auth::user()->roles->first()->name }}</span>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <button class="go-to-top btn btn-primary rounded-circle border-0" id="goToTopBtn" 
                onclick="scrollToTop()" title="العودة إلى الأعلى">
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
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Listen for scroll events
            window.addEventListener('scroll', toggleGoToTopButton);
            
            // Initial check for go to top button
            toggleGoToTopButton();

            // Handle sidebar collapse events to update arrow rotation
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
            
            // Remove toast after it's hidden
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

        // Show/hide go to top button based on scroll position
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