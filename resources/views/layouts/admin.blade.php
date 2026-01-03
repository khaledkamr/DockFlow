<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta property="og:title" content="DockFlow">
    <meta property="og:description" content="نظام شامل لإدارة التخزين، النقل، والتخليص الجمركي، مصمم لتحسين العمليات اللوجستية وزيادة الكفاءة.">
    <meta property="og:image" content="{{ asset('img/logo.png') }}">
    <meta property="og:type" content="website">

    <title>Dockflow - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
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
                <a class="nav-link fw-semibold rounded px-3 py-2"
                    style="{{ request()->routeIs('admin.dashboard') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                    href="{{ route('admin.dashboard') }}">
                    <i class="fa-solid fa-house ms-2 me-2"></i> الصفحة الرئيسيـــة
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link fw-semibold rounded px-3 py-2"
                    style="{{ request()->routeIs('admin.companies') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                    href="{{ route('admin.companies') }}">
                    <i class="fa-solid fa-building ms-2 me-2"></i> الشركات
                </a>
            </li>
            <li class="nav-item mb-1">
                <a class="nav-link fw-semibold rounded px-3 py-2"
                    style="{{ request()->routeIs('admin.dashboard.users') ? 'background: var(--gradient); color: white;' : 'color: #000;' }}"
                    href="{{ route('admin.dashboard.users') }}">
                    <i class="fa-solid fa-users ms-2 me-2"></i> المستخدمون
                </a>
            </li>
        </ul>
    </div>

    <!-- Mobile Menu Toggle Button -->
    <button class="mobile-menu-toggle" id="mobileMenuToggle">
        <i class="fa-solid fa-bars"></i>
    </button>

    <div class="main-content">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg bg-white shadow-sm" style="min-height: 70px;">
            <div class="container-fluid">
               

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarContent">
                    <form class="d-flex" style="width: 40%; max-width: 500px;">
                        <div class="input-group">
                            <input class="form-control border-primary" type="search" placeholder="بحث..."
                                aria-label="Search">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>

                    <div class="d-flex align-items-center gap-3 ms-auto">
                        <!-- Language Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-link p-0 border-0" type="button" id="languageDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-globe fa-xl text-secondary"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                                <li>
                                    <h6 class="dropdown-header">اختر اللغة</h6>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="#">
                                        <img src="https://flagcdn.com/w20/sa.png" alt="Arabic" class="me-2"
                                            style="width: 20px;">
                                        العربية
                                        <i class="fa-solid fa-check text-success ms-auto"
                                            style="display: {{ app()->getLocale() == 'ar' ? 'inline' : 'none' }}"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Notifications Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-link p-0 border-0 position-relative" type="button"
                                id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-bell fa-xl text-secondary"></i>
                                <!-- Notification Badge -->
                                {{-- <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                                    5
                                </span> --}}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown"
                                style="width: 350px; max-height: 400px; overflow-y: auto;">
                                <li>
                                    <h6 class="dropdown-header d-flex justify-content-between align-items-center">
                                        الإشعارات
                                        <span class="badge bg-primary rounded-pill">0</span>
                                    </h6>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>

                                <li>
                                    <div class="dropdown-item text-center py-4">
                                        <i class="fa-solid fa-bell-slash text-muted fa-2x mb-2"></i>
                                        <div class="text-muted">لا توجد إشعارات</div>
                                    </div>
                                </li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item text-center py-2 fw-bold text-primary" href="#">
                                        <i class="fa-solid fa-eye me-1"></i>
                                        عرض جميع الإشعارات
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Settings Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-link p-0 border-0" type="button" id="settingsDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-gear fa-xl text-secondary"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsDropdown">
                                <li class="dropdown-hover"><a class="dropdown-item" href="#">الإعدادات</a></li>
                                <li class="dropdown-hover"><a class="dropdown-item"
                                        href="{{ route('user.profile', auth()->user()) }}">الملف الشخصي</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li class="dropdown-hover">
                                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger"
                                            style="border: none; background: transparent;">
                                            <i class="fa-solid fa-arrow-right-from-bracket me-1"></i>
                                            تسجيل خروج
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>

                        <div class="d-flex align-items-center text-dark me-0 me-md-3">
                            <a href="{{ route('user.profile', Auth::user()) }}">
                                <img src="{{ Auth::user()->avatar ?? asset('img/user-profile.jpg') }}"
                                    alt="Profile Photo" class="rounded-circle me-2"
                                    style="width: 40px; height: 40px;">
                            </a>
                            <div class="d-flex flex-column">
                                <span class="fw-bold" style="font-size: 14px;">{{ Auth::user()->name }}</span>
                                <span class="text-secondary"
                                    style="font-size: 12px;">{{ Auth::user()->roles->first()->name }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

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
