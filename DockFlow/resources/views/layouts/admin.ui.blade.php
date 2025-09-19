<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dockflow - Fixed Layout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <style>
        ::-webkit-scrollbar {
            width: 10px;
            height: 0; 
        }
        .sidebar::-webkit-scrollbar {
            width: 5px;
            height: 0; 
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
            min-height: 100vh;
            background-color: #eeeeee;
        }
        .sidebar {
            height: 100vh;
            background-color: white;
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
            height: 70px;
        }
        .content-area {
            padding: 2rem;
        }
        .logo-section {
            margin-bottom: 1rem;
        }
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
        .dropdown-toggle::after {
            display: none !important;
        }
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
        .go-to-top {
            position: fixed;
            bottom: 30px;
            left: 30px;
            width: 50px;
            height: 50px;
            background-color: #0b56a9;
            cursor: pointer;
            font-size: 18px;
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

        /* Sample content styles for demo */
        .demo-card {
            background: white;
            border-radius: 8px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar shadow-sm p-2 relative">
        <div class="logo-section d-flex flex-column justify-content-center align-items-center">
            <div style="width: 150px; height: 80px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px;">
                <i class="fa-solid fa-anchor fa-2x text-primary"></i>
            </div>
            <h2 class="text-center fw-bold" style="background: linear-gradient(45deg, #42b3af, #0b56a9); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">DockFlow</h2>
        </div>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link fw-bold active" href="#home">
                    <i class="fa-solid fa-house ms-2 me-2"></i> الصفحة الرئيسيـــة
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link parent-link fw-bold" 
                   data-bs-toggle="collapse" 
                   href="#users-management" 
                   role="button" 
                   aria-expanded="false" 
                   aria-controls="users-management">
                    <i class="fa-solid fa-users ms-2 me-2"></i> إدارة المستخدمون
                </a>
                <div class="collapse sub-menu" id="users-management">
                    <a class="nav-link fw-bold" href="#customers">
                        <i class="fa-solid fa-user ms-4 me-2"></i> العمــــلاء
                    </a>
                    <a class="nav-link fw-bold" href="#employees">
                        <i class="fa-solid fa-user-tie ms-4 me-2"></i> الموظفين
                    </a>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link parent-link fw-bold" 
                   data-bs-toggle="collapse" 
                   href="#contract-management" 
                   role="button" 
                   aria-expanded="false" 
                   aria-controls="contract-management">
                    <i class="fa-solid fa-file ms-2 me-2"></i> إدارة العقــــــــــــود
                </a>
                <div class="collapse sub-menu" id="contract-management">
                    <a class="nav-link fw-bold" href="#contracts">
                        <i class="fa-solid fa-file-contract ms-4 me-2"></i> العقــــــود
                    </a>
                    <a class="nav-link fw-bold" href="#contracts-create">
                        <i class="fa-solid fa-file-circle-plus ms-3 me-2"></i> إضافة عقــــد
                    </a>
                    <a class="nav-link fw-bold" href="#contracts-services">
                        <i class="fa-solid fa-screwdriver-wrench ms-3 me-2"></i> الخدمـــــات
                    </a>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link parent-link fw-bold" 
                   data-bs-toggle="collapse" 
                   href="#yard-management" 
                   role="button" 
                   aria-expanded="false" 
                   aria-controls="yard-management">
                    <i class="fa-solid fa-warehouse ms-2 me-2"></i> الســـــــاحــــــــــــة
                </a>
                <div class="collapse sub-menu" id="yard-management">
                    <a class="nav-link fw-bold" href="#containers">
                        <i class="fa-solid fa-boxes-stacked ms-4 me-2"></i> الحــــــاويـــات
                    </a>
                    <a class="nav-link fw-bold" href="#container-types">
                        <i class="fa-solid fa-sitemap ms-4 me-2"></i> أنواع الحاويات
                    </a>
                    <a class="nav-link fw-bold" href="#container-reports">
                        <i class="fa-solid fa-file-lines ms-4 me-2"></i> تقارير الحاويات
                    </a>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link parent-link fw-bold" 
                   data-bs-toggle="collapse" 
                   href="#policy-management" 
                   role="button" 
                   aria-expanded="false" 
                   aria-controls="policy-management">
                    <i class="fa-solid fa-file ms-2 me-2"></i> إدارة الإتفاقيــــــات
                </a>
                <div class="collapse sub-menu" id="policy-management">
                    <a class="nav-link fw-bold" href="#policies">
                        <i class="fa-solid fa-file-contract ms-4 me-2"></i> الإتفاقيـــــات
                    </a>
                    <a class="nav-link fw-bold" href="#storage-policy">
                        <i class="fa-solid fa-file-circle-plus ms-3 me-2"></i> إضافة إتفاقية تخزين
                    </a>
                    <a class="nav-link fw-bold" href="#receive-policy">
                        <i class="fa-solid fa-file-circle-plus ms-3 me-2"></i> إضافة إتفاقية إستلام
                    </a>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link parent-link fw-bold" 
                   data-bs-toggle="collapse" 
                   href="#invoice-management" 
                   role="button" 
                   aria-expanded="false" 
                   aria-controls="invoice-management">
                    <i class="fa-solid fa-receipt ms-2 me-2"></i> إدارة الفــــواتيــــــر
                </a>
                <div class="collapse sub-menu" id="invoice-management">
                    <a class="nav-link fw-bold" href="#invoices">
                        <i class="fa-solid fa-scroll ms-4 me-2"></i> الفــــواتيـــــر
                    </a>
                    <a class="nav-link fw-bold" href="#invoice-create">
                        <i class="fa-solid fa-circle-plus ms-4 me-2"></i> إضافة فاتورة
                    </a>
                </div>
            </li>

            <li class="nav-item">
                <a class="nav-link parent-link fw-bold" 
                   data-bs-toggle="collapse" 
                   href="#money-management" 
                   role="button" 
                   aria-expanded="false" 
                   aria-controls="money-management">
                    <i class="fa-solid fa-money-check-dollar ms-2 me-2"></i> الإدارة المـــاليــــة 
                </a>
                <div class="collapse sub-menu" id="money-management">
                    <a class="nav-link fw-bold" href="#money-entries">
                        <i class="fa-solid fa-money-bill-transfer ms-3 me-2"></i> القيود والسنـدات
                    </a>
                    <a class="nav-link fw-bold" href="#money-tree">
                        <i class="fa-solid fa-folder-tree ms-3 me-2"></i> دلـــيل الحسابـــات
                    </a>
                    <a class="nav-link fw-bold" href="#money-reports">
                        <i class="fa-solid fa-receipt ms-3 me-2"></i> تقاريـــــر القيـــــود
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

                <div class="ms-auto d-flex align-items-center gap-3">
                    <div class="dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="settingsDropdown" role="button" 
                        data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-solid fa-gear fa-xl text-secondary"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsDropdown">
                            <li><a class="dropdown-item" href="#settings">الإعدادات</a></li>
                            <li><a class="dropdown-item" href="#company">بيانات الشركة</a></li>
                            <li><a class="dropdown-item" href="#profile">الملف الشخصي</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="#logout">
                                    <i class="fa-solid fa-arrow-right-from-bracket me-1"></i>
                                    تسجيل خروج
                                </a>
                            </li>
                        </ul>
                    </div>
                    <span class="me-3 text-dark d-flex align-items-center">
                        أحمد محمد
                        <i class="fa-solid fa-user-circle fa-2xl text-secondary ms-2"></i>
                    </span>
                </div>
            </div>
        </nav>

        <button class="go-to-top d-flex justify-content-center align-items-center border-0 rounded-circle text-white" 
            id="goToTopBtn" onclick="scrollToTop()" title="العودة إلى الأعلى">
            <i class="fa-solid fa-angles-up"></i>
        </button>
        
        <div class="content-area">
            <!-- Sample content for demonstration -->
            <div class="demo-card">
                <h3 class="mb-4">مرحباً بك في نظام DockFlow</h3>
                <p>هذا هو المحتوى الرئيسي للصفحة. يمكنك الآن اختبار:</p>
                <ul>
                    <li>قوائم الشريط الجانبي - يجب أن تفتح وتغلق بشكل صحيح</li>
                    <li>قائمة الإعدادات في شريط التنقل العلوي</li>
                    <li>زر العودة إلى الأعلى عند التمرير</li>
                </ul>
                <div style="height: 100vh; background: linear-gradient(45deg, #f8f9fa, #e9ecef); display: flex; align-items: center; justify-content: center; margin-top: 2rem; border-radius: 8px;">
                    <p class="text-muted">محتوى إضافي للتمرير واختبار زر العودة إلى الأعلى</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Initialize Bootstrap components when DOM is loaded
        document.addEventListener("DOMContentLoaded", function () {
            // Initialize all tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Initialize all dropdowns (this ensures the settings dropdown works)
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                return new bootstrap.Dropdown(dropdownToggleEl)
            });

            // Setup sidebar collapse functionality
            setupSidebarCollapse();
            
            // Listen for scroll events
            window.addEventListener('scroll', toggleGoToTopButton);
            
            // Initial check for go to top button
            toggleGoToTopButton();
        });

        // Enhanced sidebar collapse functionality
        function setupSidebarCollapse() {
            const parentLinks = document.querySelectorAll('.parent-link');
            
            parentLinks.forEach(parentLink => {
                // Get the target collapse element
                const targetId = parentLink.getAttribute('href').substring(1);
                const targetCollapse = document.getElementById(targetId);
                
                if (targetCollapse) {
                    // Create Bootstrap Collapse instance
                    const bsCollapse = new bootstrap.Collapse(targetCollapse, {
                        toggle: false
                    });
                    
                    // Add click event listener
                    parentLink.addEventListener('click', function(e) {
                        e.preventDefault();
                        
                        // Toggle the collapse
                        bsCollapse.toggle();
                    });
                    
                    // Add event listeners for state changes
                    targetCollapse.addEventListener('show.bs.collapse', function() {
                        parentLink.classList.add('expanded');
                        parentLink.setAttribute('aria-expanded', 'true');
                    });
                    
                    targetCollapse.addEventListener('hide.bs.collapse', function() {
                        parentLink.classList.remove('expanded');
                        parentLink.setAttribute('aria-expanded', 'false');
                    });
                }
            });
        }

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
</body>
</html>