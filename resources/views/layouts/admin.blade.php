<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warehouse - @yield('title')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <style>
        ::selection {
            color: #ffffff;
            background: #10d5bf;
        }
        body {
            font-family: "Cairo", sans-serif;
            font-optical-sizing: auto;
            font-weight: <weight>;
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
        }
        .sidebar a {
            display: block;
            padding: 10px 15px;
            margin-bottom: 5px;
            color: black;
            text-decoration: none;
            border-radius: 5px;
        }
        .sidebar a:hover {
            color: #10d5bf;
            transition: color 0.3s ease;
        }
        .sidebar .active {
            background-color: #10d5bf;
            color: white;
        }
        .sidebar .active:hover {
            background-color: #10d5bf;
            color: white;
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
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar p-2">
        <div class="d-flex align-items-center mb-5">
            <img class="ms-2" src="https://erp.wazen.sa/images/wazen-logo-2024.png" width="50px" alt="">
            <span class="ms-2 fs-5 fw-bold">ســـاحـــة تخزيـــــن</span>
        </div>
        <ul class="nav flex-column">
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.home') ? 'active' : '' }} fw-bold" href="{{ route('admin.home') }}">
                    <i class="fa-solid fa-house ms-2 me-2"></i> الرئــيــســيــــــــة
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }} fw-bold" href="{{ route('admin.users') }}">
                    <i class="fa-solid fa-users ms-2 me-2"></i> المستخدمـــــون
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.yard') ? 'active' : '' }} fw-bold" href="{{ route('admin.yard') }}">
                    <i class="fa-solid fa-warehouse ms-2 me-2"></i> الســـــــاحـــــــــة
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.contracts') ? 'active' : '' }} fw-bold" href="{{ route('admin.contracts') }}">
                    <i class="fa-solid fa-file ms-2 me-2"></i> العــــــــقــــــــود
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.invoices') ? 'active' : '' }} fw-bold" href="{{ route('admin.invoices') }}">
                    <i class="fa-solid fa-scroll ms-2 me-2"></i> الفــــــــواتيـــــــر
                </a>
            </div>
            <div class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.payments') ? 'active' : '' }} fw-bold" href="{{ route('admin.payments') }}">
                    <i class="fa-solid fa-money-check-dollar ms-2 me-2"></i> المدفـــوعــــــات
                </a>
            </div>
        </ul>
        
    </div>
    
    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white">
            <div class="container-fluid">
                <a class="navbar-brand text-dark fw-bold" href="#">Warehouse System</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>
        </nav>
        
        <!-- Content Area -->
        <div class="content-area">
            @yield('content')
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    {{-- <script>
        // Ensure only one sub-menu is open at a time
        document.querySelectorAll('.toggle').forEach(function(toggle) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Get the target collapse element
                const collapseId = this.getAttribute('href');
                const collapseElement = document.querySelector(collapseId);
                
                // Toggle the collapse state
                const bsCollapse = new bootstrap.Collapse(collapseElement, {
                    toggle: true
                });

                // Close other open collapses
                document.querySelectorAll('.sub-menu.collapse.show').forEach(function(otherCollapse) {
                    if (otherCollapse !== collapseElement) {
                        new bootstrap.Collapse(otherCollapse, {
                            toggle: false
                        }).hide();
                    }
                });
            });
        });

        // Prevent sub-menu collapse when clicking links
        document.querySelectorAll('.sub-menu a').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.stopPropagation(); // Prevent event from bubbling up to toggle
            });
        });

        // Auto-expand sub-menu based on current route
        document.addEventListener('DOMContentLoaded', function() {
            const currentRoute = window.location.pathname;
            const userRoutes = ['admin/users', 'users'];
            const courseRoutes = ['courses/create', 'courses'];
            const aiModelsRoutes = ['feedbacks'];

            if (userRoutes.some(route => currentRoute.includes(route))) {
                const userCollapse = document.querySelector('#users');
                if (userCollapse) {
                    new bootstrap.Collapse(userCollapse, {
                        show: true
                    });
                }
            } else if (courseRoutes.some(route => currentRoute.includes(route))) {
                const courseCollapse = document.querySelector('#courses');
                if (courseCollapse) {
                    new bootstrap.Collapse(courseCollapse, {
                        show: true
                    });
                }
            } else if (aiModelsRoutes.some(route => currentRoute.includes(route))) {
                const aiModelsCollapse = document.querySelector('#AImodels');
                if (aiModelsCollapse) {
                    new bootstrap.Collapse(aiModelsCollapse, {
                        show: true
                    });
                }
            }
        });
    </script> --}}
</body>
</html>