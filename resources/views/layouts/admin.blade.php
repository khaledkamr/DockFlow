<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouse - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <style>
        ::selection {
            color: #ffffff;
            background: #10d5bf;
        }
        :root {
            --primary-color: #10d5bf;
            --secondary-color: #0ec5a9;
        }
        .btn-1 {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #ffffff;
        }
        .btn-1:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: #ffffff;
        }
        body {
            font-family: "Cairo", sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
            font-variation-settings: "slnt" 0;
            min-height: 100vh;
            background-color: #eeeeee;
        }
        .sidebar {
            height: 100vh;
            background-color: #f8f9fa;
            border-left: 1px solid #ddd;
            padding-top: 1rem;
            position: fixed;
            right: 0;
            top: 0;
            width: 250px;
            overflow-y: auto;
        }
        .sidebar .nav-link {
            display: block;
            padding: 12px 15px;
            margin-bottom: 2px;
            color: black;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover {
            color: #10d5bf;
            background-color: rgba(16, 213, 191, 0.1);
        }
        .sidebar .nav-link.active {
            background-color: #10d5bf;
            color: white;
        }
        .sidebar .nav-link.active:hover {
            background-color: #0ec5a9;
            color: white;
        }
        .sidebar .parent-link {
            position: relative;
        }
        .sidebar .parent-link::after {
            content: '\f078';
            font-family: "Font Awesome 6 Free";
            font-weight: 900;
            position: absolute;
            left: 15px;
            transition: transform 0.3s ease;
        }
        .sidebar .parent-link.expanded::after {
            transform: rotate(180deg);
        }
        .sidebar .sub-menu {
            background-color: rgba(0,0,0,0.05);
            border-radius: 5px;
            margin: 2px 0;
        }
        .sidebar .sub-menu .nav-link {
            padding: 8px 15px 8px 30px;
            font-size: 0.9em;
            margin-bottom: 1px;
        }
        .main-content {
            margin-right: 250px;
            min-height: 100vh;
        }
        .navbar {
            background-color: white !important;
            border-bottom: 1px solid #ddd;
        }
        .content-area {
            padding: 2rem;
        }
        .logo-section {
            border-bottom: 1px solid #ddd;
            padding-bottom: 1rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar p-2">
        <div class="logo-section d-flex align-items-center mb-3">
            <img class="ms-2" src="https://erp.wazen.sa/images/wazen-logo-2024.png" width="50px" alt="">
            <span class="ms-2 fs-5 fw-bold">ســـاحـــة تخزيـــــن</span>
        </div>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link fw-bold {{ request()->routeIs('admin.home') ? 'active' : '' }}" href="{{ route('admin.home') }}">
                    <i class="fa-solid fa-house ms-2 me-2"></i> الصفحة الرئيسيـــة
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link parent-link fw-bold {{ request()->routeIs(['admin.users*']) ? 'expanded' : '' }}" 
                   data-bs-toggle="collapse" 
                   href="#users-management" 
                   role="button" 
                   aria-expanded="{{ request()->routeIs(['admin.users*']) ? 'true' : 'false' }}" 
                   aria-controls="users-management"
                   onclick="toggleSubmenu(this, event)">
                    <i class="fa-solid fa-users ms-2 me-2"></i> إدارة المستخدمون
                </a>
                <div class="collapse sub-menu {{ request()->routeIs(['admin.users*']) ? 'show' : '' }}" id="users-management">
                    <a class="nav-link fw-bold {{ request()->routeIs('admin.users') ? 'active' : '' }}" href="{{ route('admin.users') }}">
                        <i class="fa-solid fa-user ms-4 me-2"></i> العمــــلاء
                    </a>
                    <a class="nav-link fw-bold {{ request()->routeIs('admin.users.admins') ? 'active' : '' }}" href="{{ route('admin.users.admins') }}">
                        <i class="fa-solid fa-user-tie ms-4 me-2"></i>  المشرفين
                    </a>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link parent-link fw-bold {{ request()->routeIs(['admin.yard*']) ? 'expanded' : '' }}" 
                   data-bs-toggle="collapse" 
                   href="#yard-management" 
                   role="button" 
                   aria-expanded="{{ request()->routeIs(['admin.yard*']) ? 'true' : 'false' }}" 
                   aria-controls="yard-management"
                   onclick="toggleSubmenu(this, event)">
                    <i class="fa-solid fa-warehouse ms-2 me-2"></i> الســـــــاحــــــــــــة
                </a>
                <div class="collapse sub-menu {{ request()->routeIs(['admin.yard*']) ? 'show' : '' }}" id="yard-management">
                    <a class="nav-link fw-bold {{ request()->routeIs('admin.yard') ? 'active' : '' }}" href="{{ route('admin.yard') }}">
                        <i class="fa-solid fa-box ms-4 me-2"></i> الكونتينـــــرات
                    </a>
                    <a class="nav-link fw-bold {{ request()->routeIs('admin.yard.add') ? 'active' : '' }}" href="{{ route('admin.yard.add') }}">
                        <i class="fa-solid fa-plus ms-4 me-2"></i> إضافة كونتينر
                    </a>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link parent-link fw-bold {{ request()->routeIs(['admin.contracts*']) ? 'expanded' : '' }}" 
                   data-bs-toggle="collapse" 
                   href="#contract-management" 
                   role="button" 
                   aria-expanded="{{ request()->routeIs(['admin.contracts*']) ? 'true' : 'false' }}" 
                   aria-controls="contract-management"
                   onclick="toggleSubmenu(this, event)">
                    <i class="fa-solid fa-file ms-2 me-2"></i> إدارة العقــــــــــــود
                </a>
                <div class="collapse sub-menu {{ request()->routeIs(['admin.contracts*']) ? 'show' : '' }}" id="contract-management">
                    <a class="nav-link fw-bold {{ request()->routeIs('admin.contracts') ? 'active' : '' }}" href="{{ route('admin.contracts') }}">
                        <i class="fa-solid fa-file-contract ms-4 me-2"></i> العقــــــود
                    </a>
                    <a class="nav-link fw-bold {{ request()->routeIs('admin.contracts.create') ? 'active' : '' }}" href="{{ route('admin.contracts.create') }}">
                        <i class="fa-solid fa-file-circle-plus ms-3 me-2"></i> إضافة عقد
                    </a>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link fw-bold {{ request()->routeIs('admin.invoices') ? 'active' : '' }}" href="{{ route('admin.invoices') }}">
                    <i class="fa-solid fa-scroll ms-2 me-2"></i> الفــــــــواتيــــــــــر
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link fw-bold {{ request()->routeIs('admin.payments') ? 'active' : '' }}" href="{{ route('admin.payments') }}">
                    <i class="fa-solid fa-money-check-dollar ms-2 me-2"></i> المدفـــوعـــــــــات
                </a>
            </li>
        </ul>
    </div>
    
    <div class="main-content">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white">
            <div class="container-fluid">
                <a class="navbar-brand text-dark fw-bold" href="#">Warehouse System</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="ms-auto">
                    <span class="me-3">مرحباً، خالد</span>
                    <button class="btn btn-outline-danger btn-sm">تسجيل خروج</button>
                </div>
            </div>
        </nav>
        
        <div class="content-area">
            @yield('content')
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSubmenu(element, event) {
            event.preventDefault();
            
            // Get all parent links
            const allParentLinks = document.querySelectorAll('.parent-link');
            const targetId = element.getAttribute('href').substring(1);
            const targetCollapse = document.getElementById(targetId);
            
            // Close all other submenus (except the current one)
            allParentLinks.forEach(link => {
                if (link !== element && !link.classList.contains('expanded')) {
                    const otherTargetId = link.getAttribute('href').substring(1);
                    const otherCollapse = document.getElementById(otherTargetId);
                    if (otherCollapse && otherCollapse.classList.contains('show')) {
                        bootstrap.Collapse.getInstance(otherCollapse).hide();
                        link.classList.remove('expanded');
                    }
                }
            });
            
            // Toggle current submenu only if it's not already expanded due to active route
            if (!element.classList.contains('expanded')) {
                const bsCollapse = bootstrap.Collapse.getOrCreateInstance(targetCollapse);
                bsCollapse.toggle();
                
                // Add expanded class when opening
                targetCollapse.addEventListener('shown.bs.collapse', () => {
                    element.classList.add('expanded');
                });
                
                // Remove expanded class when closing
                targetCollapse.addEventListener('hidden.bs.collapse', () => {
                    element.classList.remove('expanded');
                });
            }
        }
    </script>
</body>
</html>