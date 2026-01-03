@extends('layouts.admin')

@section('title', 'dashboard')

@section('content')
    <style>
        :root {
            --color-primary: #0b56a9;
            --color-secondary: #218bab;
            --color-tertiary: #2cacbd;
            --color-quaternary: #42b3af;
            --color-accent: #52d6cb;
        }
        
        .bg-theme-1 { background-color: #0b56a9 !important; }
        .bg-theme-2 { background-color: #218bab !important; }
        .bg-theme-3 { background-color: #2cacbd !important; }
        .bg-theme-4 { background-color: #42b3af !important; }
        .bg-theme-5 { background-color: #52d6cb !important; }
        
        .bg-theme-1-light { background-color: rgba(11, 86, 169, 0.1) !important; }
        .bg-theme-2-light { background-color: rgba(33, 139, 171, 0.1) !important; }
        .bg-theme-3-light { background-color: rgba(44, 172, 189, 0.1) !important; }
        .bg-theme-4-light { background-color: rgba(66, 179, 175, 0.1) !important; }
        .bg-theme-5-light { background-color: rgba(82, 214, 203, 0.1) !important; }
        
        .text-theme-1 { color: #0b56a9 !important; }
        .text-theme-2 { color: #218bab !important; }
        .text-theme-3 { color: #2cacbd !important; }
        .text-theme-4 { color: #42b3af !important; }
        .text-theme-5 { color: #52d6cb !important; }
        
        .btn-theme-1 { background-color: #0b56a9; border-color: #0b56a9; color: #fff; }
        .btn-theme-1:hover { background-color: #094a91; border-color: #094a91; color: #fff; }
        .btn-outline-theme-1 { border-color: #0b56a9; color: #0b56a9; }
        .btn-outline-theme-1:hover, .btn-outline-theme-1.active { background-color: #0b56a9; color: #fff; }
        
        .badge-theme-1 { background-color: #0b56a9; color: #fff; }
        .badge-theme-2 { background-color: #218bab; color: #fff; }
        .badge-theme-3 { background-color: #2cacbd; color: #fff; }
        .badge-theme-4 { background-color: #42b3af; color: #fff; }
        .badge-theme-5 { background-color: #52d6cb; color: #fff; }
    </style>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <!-- Total Companies Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-theme-1-light rounded-3 p-3">
                                <i class="fa-solid fa-building text-theme-1 fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">إجمالي الشركات</h6>
                            <h3 class="mb-0 fw-bold">{{ $totalCompanies ?? 24 }}</h3>
                            <small class="text-theme-4">
                                <i class="fa-solid fa-arrow-up me-1"></i>
                                12% من الشهر الماضي
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Companies Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-theme-2-light rounded-3 p-3">
                                <i class="fa-solid fa-building-circle-check text-theme-2 fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">الشركات النشطة</h6>
                            <h3 class="mb-0 fw-bold">{{ $activeCompanies ?? 18 }}</h3>
                            <small class="text-theme-4">
                                <i class="fa-solid fa-arrow-up me-1"></i>
                                8% من الشهر الماضي
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Users Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-theme-3-light rounded-3 p-3">
                                <i class="fa-solid fa-users text-theme-3 fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">إجمالي المستخدمين</h6>
                            <h3 class="mb-0 fw-bold">{{ $totalUsers ?? 156 }}</h3>
                            <small class="text-theme-4">
                                <i class="fa-solid fa-arrow-up me-1"></i>
                                23% من الشهر الماضي
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Users Card -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-theme-4-light rounded-3 p-3">
                                <i class="fa-solid fa-user-check text-theme-4 fa-2x"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">المستخدمين النشطين</h6>
                            <h3 class="mb-0 fw-bold">{{ $activeUsers ?? 89 }}</h3>
                            <small class="text-theme-1">
                                <i class="fa-solid fa-arrow-down me-1"></i>
                                3% من الشهر الماضي
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-4">
        <!-- Companies Growth Chart -->
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="fa-solid fa-chart-line text-theme-1 me-2"></i>
                            نمو الاشتراكات
                        </h5>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-theme-1 active"
                                data-period="monthly">شهري</button>
                            <button type="button" class="btn btn-outline-theme-1" data-period="yearly">سنوي</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="subscriptionsChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Companies by Plan Chart -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fa-solid fa-chart-pie text-theme-1 me-2"></i>
                        الشركات حسب الخطة
                    </h5>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <canvas id="companiesByPlanChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Charts Row -->
    <div class="row g-3 mb-4">
        <!-- Users Activity Chart -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fa-solid fa-chart-bar text-theme-1 me-2"></i>
                        نشاط المستخدمين (آخر 7 أيام)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="usersActivityChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Users by Company Chart -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="fa-solid fa-building-user text-theme-1 me-2"></i>
                        المستخدمين حسب الشركة
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="usersByCompanyChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Top Companies -->
    <div class="row g-3">
        <!-- Recent Companies -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="fa-solid fa-clock-rotate-left text-theme-1 me-2"></i>
                            أحدث الشركات المسجلة
                        </h5>
                        <a href="{{ route('admin.companies') }}" class="btn btn-sm btn-outline-theme-1">عرض الكل</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center border-0">الشركة</th>
                                    <th class="text-center border-0">الخطة</th>
                                    <th class="text-center border-0">تاريخ التسجيل</th>
                                    <th class="text-center border-0">الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center fw-bold">شركة الأمل للتجارة</td>
                                    <td class="text-center"><span class="badge badge-theme-1">احترافية</span></td>
                                    <td class="text-center text-muted">2026/01/02</td>
                                    <td class="text-center"><span class="badge badge-theme-4">نشط</span></td>
                                </tr>
                                <tr>
                                    <td class="text-center fw-bold">مؤسسة النور</td>
                                    <td class="text-center"><span class="badge badge-theme-2">أساسية</span></td>
                                    <td class="text-center text-muted">2026/01/01</td>
                                    <td class="text-center"><span class="badge badge-theme-4">نشط</span></td>
                                </tr>
                                <tr>
                                    <td class="text-center fw-bold">شركة الخليج</td>
                                    <td class="text-center"><span class="badge badge-theme-5">تجريبية</span></td>
                                    <td class="text-center text-muted">2025/12/28</td>
                                    <td class="text-center"><span class="badge badge-theme-3">تجريبي</span></td>
                                </tr>
                                <tr>
                                    <td class="text-center fw-bold">مجموعة السلام</td>
                                    <td class="text-center"><span class="badge badge-theme-1">احترافية</span></td>
                                    <td class="text-center text-muted">2025/12/25</td>
                                    <td class="text-center"><span class="badge badge-theme-4">نشط</span></td>
                                </tr>
                                <tr>
                                    <td class="text-center fw-bold">شركة الريادة</td>
                                    <td class="text-center"><span class="badge badge-theme-2">أساسية</span></td>
                                    <td class="text-center text-muted">2025/12/20</td>
                                    <td class="text-center"><span class="badge bg-danger">منتهي</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Companies by Users -->
        <div class="col-12 col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 fw-bold">
                            <i class="fa-solid fa-ranking-star text-theme-1 me-2"></i>
                            أكثر الشركات نشاطاً
                        </h5>
                        <a href="{{ route('admin.users') }}" class="btn btn-sm btn-outline-theme-1">عرض المستخدمين</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center border-0">#</th>
                                    <th class="text-center border-0">الشركة</th>
                                    <th class="text-center border-0">المستخدمين</th>
                                    <th class="text-center border-0">المعاملات</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge badge-theme-5 rounded-pill">1</span>
                                    </td>
                                    <td class="text-center fw-bold">شركة الأمل للتجارة</td>
                                    <td class="text-center">24</td>
                                    <td class="text-center text-theme-1 fw-bold">1,250</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge badge-theme-3 rounded-pill">2</span>
                                    </td>
                                    <td class="text-center fw-bold">مجموعة السلام</td>
                                    <td class="text-center">18</td>
                                    <td class="text-center text-theme-1 fw-bold">980</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge badge-theme-2 rounded-pill">3</span>
                                    </td>
                                    <td class="text-center fw-bold">مؤسسة النور</td>
                                    <td class="text-center">15</td>
                                    <td class="text-center text-theme-1 fw-bold">756</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill">4</span>
                                    </td>
                                    <td class="text-center fw-bold">شركة الخليج</td>
                                    <td class="text-center">12</td>
                                    <td class="text-center text-theme-1 fw-bold">543</td>
                                </tr>
                                <tr>
                                    <td class="text-center">
                                        <span class="badge bg-secondary rounded-pill">5</span>
                                    </td>
                                    <td class="text-center fw-bold">شركة الريادة</td>
                                    <td class="text-center">8</td>
                                    <td class="text-center text-theme-1 fw-bold">321</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Theme colors
            const themeColors = {
                color1: '#0b56a9',
                color2: '#218bab',
                color3: '#2cacbd',
                color4: '#42b3af',
                color5: '#52d6cb'
            };

            // Chart.js default configuration for RTL
            Chart.defaults.font.family = 'Tajawal, sans-serif';

            // Subscriptions Growth Chart (Line Chart)
            const subscriptionsCtx = document.getElementById('subscriptionsChart').getContext('2d');
            new Chart(subscriptionsCtx, {
                type: 'line',
                data: {
                    labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس',
                        'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
                    ],
                    datasets: [{
                        label: 'الشركات الجديدة',
                        data: [3, 5, 4, 6, 8, 7, 9, 11, 10, 12, 15, 18],
                        borderColor: themeColors.color1,
                        backgroundColor: 'rgba(11, 86, 169, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: themeColors.color1,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5
                    }, {
                        label: 'المستخدمين الجدد',
                        data: [12, 19, 15, 25, 32, 28, 35, 42, 38, 48, 55, 62],
                        borderColor: themeColors.color4,
                        backgroundColor: 'rgba(66, 179, 175, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: themeColors.color4,
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            rtl: true,
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Companies by Plan Chart (Doughnut Chart)
            const planCtx = document.getElementById('companiesByPlanChart').getContext('2d');
            new Chart(planCtx, {
                type: 'doughnut',
                data: {
                    labels: ['احترافية', 'أساسية', 'تجريبية', 'مؤسسات'],
                    datasets: [{
                        data: [10, 8, 4, 2],
                        backgroundColor: [
                            themeColors.color1,
                            themeColors.color2,
                            themeColors.color3,
                            themeColors.color4
                        ],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            rtl: true,
                            labels: {
                                usePointStyle: true,
                                padding: 15
                            }
                        }
                    },
                    cutout: '60%'
                }
            });

            // Users Activity Chart (Bar Chart)
            const activityCtx = document.getElementById('usersActivityChart').getContext('2d');
            new Chart(activityCtx, {
                type: 'bar',
                data: {
                    labels: ['السبت', 'الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'],
                    datasets: [{
                        label: 'تسجيلات الدخول',
                        data: [65, 89, 80, 81, 56, 55, 40],
                        backgroundColor: 'rgba(11, 86, 169, 0.8)',
                        borderRadius: 5
                    }, {
                        label: 'المعاملات',
                        data: [28, 48, 40, 42, 26, 30, 18],
                        backgroundColor: 'rgba(44, 172, 189, 0.8)',
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            rtl: true,
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Users by Company Chart (Horizontal Bar Chart)
            const companyUsersCtx = document.getElementById('usersByCompanyChart').getContext('2d');
            new Chart(companyUsersCtx, {
                type: 'bar',
                data: {
                    labels: ['شركة الأمل', 'مجموعة السلام', 'مؤسسة النور', 'شركة الخليج', 'شركة الريادة'],
                    datasets: [{
                        label: 'عدد المستخدمين',
                        data: [24, 18, 15, 12, 8],
                        backgroundColor: [
                            'rgba(11, 86, 169, 0.8)',
                            'rgba(33, 139, 171, 0.8)',
                            'rgba(44, 172, 189, 0.8)',
                            'rgba(66, 179, 175, 0.8)',
                            'rgba(82, 214, 203, 0.8)'
                        ],
                        borderRadius: 5
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection
