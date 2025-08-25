<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dockflow - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <style>
        /* ::selection {
            color: #ffffff;
            background: #10d5bf;
        } */
        ::-webkit-scrollbar {
            width: 10px;
            height: 0; 
        }
        ::-webkit-scrollbar-track {
            background: #121212 !important;
        }
        ::-webkit-scrollbar-thumb {
            background: #0d6efd !important;
            /* color: #00246171; */
            cursor: grab;
            transition: 0.3s;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #0a57ca;
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
            background-color: white;
            /* border-left: 1px solid #ddd; */
            padding-top: 1rem;
            position: fixed;
            right: 0;
            top: 0;
            width: 270px;
            overflow-y: auto;
            z-index: 100;
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
            color: #0d6efd;
            background-color: rgba(16, 46, 213, 0.1);
        }
        .sidebar .nav-link.active {
            background-color: #0d6efd;
            color: white;
        }
        .sidebar .nav-link.active:hover {
            background-color: #0d5dd6;
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
            margin-right: 270px;
            min-height: 100vh;
        }
        .navbar {
            background-color: white !important;
            /* border-bottom: 1px solid #ddd; */
            height: 70px;
        }
        .content-area {
            padding: 2rem;
        }
        .logo-section {
            /* border-bottom: 1px solid #ddd; */
            /* padding-bottom: 1rem; */
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar shadow-sm p-2 relative">
        <div class="logo-section d-flex flex-column justify-content-center align-items-center">
            <img class="" src="{{ asset('img/logo.png') }}" width="150px" alt="logo">
            <h2 class="text-center fw-bold" style="background: linear-gradient(45deg, #42b3af, #0b56a9); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">DockFlow</h2>
        </div>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link fw-bold {{ request()->routeIs('admin.home') ? 'active' : '' }}" href="{{ route('admin.home') }}">
                    <i class="fa-solid fa-house ms-2 me-2"></i> الصفحة الرئيسيـــة
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link parent-link fw-bold {{ request()->routeIs(['users*']) ? 'expanded' : '' }}" 
                   data-bs-toggle="collapse" 
                   href="#users-management" 
                   role="button" 
                   aria-expanded="{{ request()->routeIs(['users*']) ? 'true' : 'false' }}" 
                   aria-controls="users-management"
                   onclick="toggleSubmenu(this, event)">
                    <i class="fa-solid fa-users ms-2 me-2"></i> إدارة المستخدمون
                </a>
                <div class="collapse sub-menu {{ request()->routeIs(['users*']) ? 'show' : '' }}" id="users-management">
                    <a class="nav-link fw-bold {{ request()->routeIs('users.customers') ? 'active' : '' }}" href="{{ route('users.customers') }}">
                        <i class="fa-solid fa-user ms-4 me-2"></i> العمــــلاء
                    </a>
                    <a class="nav-link fw-bold " href="">
                        <i class="fa-solid fa-user-tie ms-4 me-2"></i>  الموظفين
                    </a>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link parent-link fw-bold {{ request()->routeIs(['contracts*']) ? 'expanded' : '' }}" 
                   data-bs-toggle="collapse" 
                   href="#contract-management" 
                   role="button" 
                   aria-expanded="{{ request()->routeIs(['contracts*']) ? 'true' : 'false' }}" 
                   aria-controls="contract-management"
                   onclick="toggleSubmenu(this, event)">
                    <i class="fa-solid fa-file ms-2 me-2"></i> إدارة العقــــــــــــود
                </a>
                <div class="collapse sub-menu {{ request()->routeIs(['contracts*']) ? 'show' : '' }}" id="contract-management">
                    <a class="nav-link fw-bold {{ request()->routeIs('contracts') ? 'active' : '' }}" href="{{ route('contracts') }}">
                        <i class="fa-solid fa-file-contract ms-4 me-2"></i> العقــــــود
                    </a>
                    <a class="nav-link fw-bold {{ request()->routeIs('contracts.create') ? 'active' : '' }}" href="{{ route('contracts.create') }}">
                        <i class="fa-solid fa-file-circle-plus ms-3 me-2"></i> إضافة عقــــد
                    </a>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link parent-link fw-bold {{ request()->routeIs(['yard*']) ? 'expanded' : '' }}" 
                   data-bs-toggle="collapse" 
                   href="#yard-management" 
                   role="button" 
                   aria-expanded="{{ request()->routeIs(['yard*']) ? 'true' : 'false' }}" 
                   aria-controls="yard-management"
                   onclick="toggleSubmenu(this, event)">
                    <i class="fa-solid fa-warehouse ms-2 me-2"></i> الســـــــاحــــــــــــة
                </a>
                <div class="collapse sub-menu {{ request()->routeIs(['yard*']) ? 'show' : '' }}" id="yard-management">
                    <a class="nav-link fw-bold {{ request()->routeIs('yard.containers') ? 'active' : '' }}" href="{{ route('yard.containers') }}">
                        <i class="fa-solid fa-boxes-stacked ms-4 me-2"></i> الحــــــاويـــات
                    </a>
                    <a class="nav-link fw-bold {{ request()->routeIs('yard.containers.create') ? 'active' : '' }}" href="{{ route('yard.containers.create') }}">
                        <i class="fa-solid fa-square-plus ms-4 me-2"></i> إضافة حــاويات
                    </a>
                    <a class="nav-link fw-bold {{ request()->routeIs('yard.containers.types') ? 'active' : '' }}" href="{{ route('yard.containers.types') }}">
                        <i class="fa-solid fa-sitemap ms-4 me-2"></i> أنواع الحاويات
                    </a>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link parent-link fw-bold {{ request()->routeIs(['policies*']) ? 'expanded' : '' }}" 
                   data-bs-toggle="collapse" 
                   href="#policy-management" 
                   role="button" 
                   aria-expanded="{{ request()->routeIs(['policies*']) ? 'true' : 'false' }}" 
                   aria-controls="policy-management"
                   onclick="toggleSubmenu(this, event)">
                    <i class="fa-solid fa-file ms-2 me-2"></i> إدارة الإتفاقيــــــات
                </a>
                <div class="collapse sub-menu {{ request()->routeIs(['policies*']) ? 'show' : '' }}" id="policy-management">
                    <a class="nav-link fw-bold {{ request()->routeIs('policies') ? 'active' : '' }}" href="{{ route('policies') }}">
                        <i class="fa-solid fa-file-contract ms-4 me-2"></i> الإتفاقيـــــات
                    </a>
                    <a class="nav-link fw-bold {{ request()->routeIs('policies.storage.create') ? 'active' : '' }}" href="{{ route('policies.storage.create') }}">
                        <i class="fa-solid fa-file-circle-plus ms-3 me-2"></i> إضافة إتفاقية تخزين
                    </a>
                    <a class="nav-link fw-bold {{ request()->routeIs('policies.receive.create') ? 'active' : '' }}" href="{{ route('policies.receive.create') }}">
                        <i class="fa-solid fa-file-circle-plus ms-3 me-2"></i> إضافة إتفاقية إستلام
                    </a>
                </div>
            </li>
            
            <li class="nav-item">
                <a class="nav-link fw-bold {{ request()->routeIs('invoices') ? 'active' : '' }}" href="{{ route('invoices') }}">
                    <i class="fa-solid fa-scroll ms-2 me-2"></i> الفــــــــواتيــــــــــر
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link parent-link fw-bold {{ request()->routeIs(['admin.money*']) ? 'expanded' : '' }}" 
                   data-bs-toggle="collapse" 
                   href="#money-management" 
                   role="button" 
                   aria-expanded="{{ request()->routeIs(['admin.money*']) ? 'true' : 'false' }}" 
                   aria-controls="money-management"
                   onclick="toggleSubmenu(this, event)">
                    <i class="fa-solid fa-money-check-dollar ms-2 me-2"></i> الإدارة المـــاليــــة 
                </a>
                <div class="collapse sub-menu {{ request()->routeIs(['admin.money*']) ? 'show' : '' }}" id="money-management">
                    <a class="nav-link fw-bold {{ request()->routeIs('admin.money.entries') ? 'active' : '' }}" href="{{ route('admin.money.entries') }}">
                        <i class="fa-solid fa-money-bill-transfer ms-3 me-2"></i> القيــــــود
                    </a>
                    <a class="nav-link fw-bold {{ request()->routeIs('admin.money.tree') ? 'active' : '' }}" href="{{ route('admin.money.tree') }}">
                        <i class="fa-solid fa-folder-tree ms-3 me-2"></i> شجــــرة الحسابـــات
                    </a>
                </div>
            </li>
        </ul>
    </div>
    
    <div class="main-content">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand text-dark fw-bold" href="#">شركة تاج الأعمال للخدمات اللوجستية</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <form class="d-flex mx-auto" style="width: 40%;">
                    <div class="input-group">
                        <input class="form-control border-primary" type="search" placeholder="بحث..." aria-label="Search">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                <div class="ms-auto">
                    <span class="me-3 text-dark">
                        <i class="fa-solid fa-user-circle fa-2xl text-secondary me-2"></i>
                        خالد قمر
                    </span>
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