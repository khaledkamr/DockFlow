@extends('layouts.app')

@section('title', 'dashboard')

@section('content')
    <style>
        /* Responsive cards adjustments */
        @media (max-width: 768px) {
            .stats-card {
                margin-bottom: 15px;
            }

            .card-body h6.card-title {
                font-size: 14px;
            }

            .card-body h6.fw-bold {
                font-size: 1.2rem !important;
            }

            .card-body i.fa-xl {
                font-size: 1.2rem !important;
            }
        }

        @media (max-width: 576px) {
            .card-body {
                padding: 1rem !important;
            }

            .card-body h6.card-title {
                font-size: 13px;
            }

            .card-body h6.fw-bold {
                font-size: 1.1rem !important;
            }
        }

        /* Chart containers responsive */
        @media (max-width: 768px) {
            .chart-container {
                height: 200px !important;
            }

            .chart-container canvas {
                max-height: 200px;
            }
        }

        /* Events container responsive */
        @media (max-width: 768px) {
            .event-card {
                padding: 0.75rem !important;
            }

            .event-card h6 {
                font-size: 13px;
            }

            .event-card p {
                font-size: 14px;
            }
        }

        /* Button responsive */
        @media (max-width: 576px) {
            .btn-sm {
                font-size: 12px;
                padding: 0.35rem 0.7rem;
            }

            .card-title {
                font-size: 15px !important;
            }
        }

        /* Date input responsive */
        @media (max-width: 768px) {
            .form-control[type="date"] {
                font-size: 12px;
                padding: 0.4rem;
            }
        }

        /* Spacing adjustments for mobile */
        @media (max-width: 768px) {
            .row.mb-4 {
                margin-bottom: 1.5rem !important;
            }
        }
    </style>

    <!-- Statistics Cards Row -->
    <div class="row mb-4">
        <div class="col-12 col-sm-6 col-lg-3 stats-card">
            <a href="{{ route('relation.customers') }}" class="text-decoration-none">
                <div class="card rounded-3 border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">إجمالي العمـــلاء</h6>
                            <h6 class="text-primary fw-bold mb-0" style="font-size: 1.4rem;">{{ $customers }}</h6>
                        </div>
                        <div>
                            <i class="fa-solid fa-users fa-xl"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-lg-3 stats-card">
            <a href="{{ route('admin.users') }}" class="text-decoration-none">
                <div class="card rounded-3 border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">إجمالي المستخدمين</h6>
                            <h6 class="text-primary fw-bold mb-0" style="font-size: 1.4rem;">{{ $users }}</h6>
                        </div>
                        <div>
                            <i class="fa-solid fa-users fa-xl"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-lg-3 stats-card">
            <a href="{{ route('contracts') }}" class="text-decoration-none">
                <div class="card rounded-3 border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">إجمالي العقود</h6>
                            <h6 class="text-primary fw-bold mb-0" style="font-size: 1.4rem;">{{ $contracts }}</h6>
                        </div>
                        <div>
                            <i class="fa-solid fa-file-circle-check fa-xl"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-lg-3 stats-card">
            <a href="{{ route('invoices') }}" class="text-decoration-none">
                <div class="card rounded-3 border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">إجمالي الفواتير</h6>
                            <h6 class="text-primary fw-bold mb-0" style="font-size: 1.4rem;">{{ $invoices }}</h6>
                        </div>
                        <div>
                            <i class="fa-solid fa-scroll fa-xl"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <a href="{{ route('money.entries') }}?view=سندات%20صرف" class="text-decoration-none">
                <div class="card rounded-3 border-0 shadow-sm animate__animated animate__fadeInUp"
                    style="background: var(--gradient);">
                    <div class="card-body d-flex justify-content-between align-items-center text-white">
                        <div>
                            <h6 class="card-title text-white mb-2">سندات الصرف</h6>
                            <h6 class="text-white fw-bold mb-0" style="font-size: 1.4rem;">{{ $vouchers ?? 0 }}</h6>
                        </div>
                        <div>
                            <i class="fa-solid fa-money-bill-wave fa-xl text-white opacity-75"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <a href="{{ route('money.entries') }}?view=سندات%20قبض" class="text-decoration-none">
                <div class="card rounded-3 border-0 shadow-sm animate__animated animate__fadeInUp"
                    style="background: var(--gradient);">
                    <div class="card-body d-flex justify-content-between align-items-center text-white">
                        <div>
                            <h6 class="card-title text-white mb-2">سندات القبض</h6>
                            <h6 class="text-white fw-bold mb-0" style="font-size: 1.4rem;">{{ $vouchers ?? 0 }}</h6>
                        </div>
                        <div>
                            <i class="fa-solid fa-hand-holding-dollar fa-xl text-white opacity-75"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <a href="{{ route('money.entries') }}" class="text-decoration-none">
                <div class="card rounded-3 border-0 shadow-sm animate__animated animate__fadeInUp"
                    style="background: var(--gradient);">
                    <div class="card-body d-flex justify-content-between align-items-center text-white">
                        <div>
                            <h6 class="card-title text-white mb-2">قيود يومية</h6>
                            <h6 class="text-white fw-bold mb-0" style="font-size: 1.4rem;">{{ $journals ?? 0 }}</h6>
                        </div>
                        <div>
                            <i class="fa-solid fa-money-bill-transfer fa-xl text-white opacity-75"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <a href="{{ route('money.reports') }}?view=كشف%20حساب" class="text-decoration-none">
                <div class="card rounded-3 border-0 shadow-sm animate__animated animate__fadeInUp"
                    style="background: var(--gradient);">
                    <div class="card-body d-flex justify-content-between align-items-center text-white">
                        <div>
                            <h6 class="card-title text-white mb-2">كشف الحساب</h6>
                            <p class="mb-1 opacity-75" style="font-size: 0.9rem;">عرض كافة الحسابات</p>
                        </div>
                        <div>
                            <i class="fa-solid fa-receipt fa-xl text-white opacity-75"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Dashboard Charts Section -->
    <div class="row mb-4">
        <!-- Monthly Revenue Chart -->
        <div class="col-12 col-xl-8">
            <div class="card rounded-3 border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">الإيرادات الشهرية</h5>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="revenueFilter"
                            data-bs-toggle="dropdown">
                            2024
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">2024</a></li>
                            <li><a class="dropdown-item" href="#">2023</a></li>
                            <li><a class="dropdown-item" href="#">2022</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 400px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customer Growth Chart -->
        <div class="col-12 col-xl-4">
            <div class="card rounded-3 border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">نمو العملاء</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 400px;">
                        <canvas id="customerGrowthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Invoice Status Distribution -->
        <div class="col-12 col-lg-6">
            <div class="card rounded-3 border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">توزيع حالة الفواتير</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="invoiceStatusChart"></canvas>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div class="me-2"
                                    style="width: 12px; height: 12px; background-color: #28a745; border-radius: 50%;">
                                </div>
                                <small>مدفوعة</small>
                            </div>
                            <small class="fw-bold">75%</small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <div class="me-2"
                                    style="width: 12px; height: 12px; background-color: #ffc107; border-radius: 50%;">
                                </div>
                                <small>معلقة</small>
                            </div>
                            <small class="fw-bold">15%</small>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <div class="me-2"
                                    style="width: 12px; height: 12px; background-color: #dc3545; border-radius: 50%;">
                                </div>
                                <small>متأخرة</small>
                            </div>
                            <small class="fw-bold">10%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Services -->
        <div class="col-12 col-lg-6">
            <div class="card rounded-3 border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">أفضل الخدمات</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 300px;">
                        <canvas id="topServicesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Financial Overview -->
        <div class="col-12">
            <div class="card rounded-3 border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">نظرة عامة مالية</h5>
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" name="financeFilter" id="finance7days" checked>
                        <label class="btn btn-sm btn-outline-primary" for="finance7days">7 أيام</label>

                        <input type="radio" class="btn-check" name="financeFilter" id="finance30days">
                        <label class="btn btn-sm btn-outline-primary" for="finance30days">30 يوم</label>

                        <input type="radio" class="btn-check" name="financeFilter" id="finance90days">
                        <label class="btn btn-sm btn-outline-primary" for="finance90days">90 يوم</label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="height: 350px;">
                        <canvas id="financialOverviewChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row mb-4">
        <div class="col-12 col-lg-8">
            <div class="card rounded-3 border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">الأنشطة الأخيرة</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="rounded-circle bg-success"
                                            style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-plus text-white small"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">عميل جديد</h6>
                                        <p class="mb-1 text-muted small">تم إضافة عميل جديد: شركة النقل السريع</p>
                                        <small class="text-muted">منذ 5 دقائق</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="rounded-circle bg-primary"
                                            style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-file-invoice text-white small"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">فاتورة جديدة</h6>
                                        <p class="mb-1 text-muted small">تم إنشاء فاتورة رقم INV-2024-001</p>
                                        <small class="text-muted">منذ 15 دقيقة</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="rounded-circle bg-warning"
                                            style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-exclamation-triangle text-white small"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">تنبيه مخزون</h6>
                                        <p class="mb-1 text-muted small">مخزون الحاويات منخفض</p>
                                        <small class="text-muted">منذ ساعة</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-12 col-lg-4">
            <div class="card rounded-3 border-0 shadow-sm">
                <div class="card-header bg-transparent border-0">
                    <h5 class="card-title mb-0">إحصائيات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-muted">معدل الدفع</span>
                            <span class="small fw-bold">94%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: 94%"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-muted">رضا العملاء</span>
                            <span class="small fw-bold">87%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" style="width: 87%"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-muted">استخدام الحاويات</span>
                            <span class="small fw-bold">76%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-warning" style="width: 76%"></div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <div class="text-center">
                        <h3 class="text-primary mb-1">2,847</h3>
                        <small class="text-muted">إجمالي المعاملات</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Chart.js Configuration
            Chart.defaults.font.family = 'Cairo, sans-serif';
            Chart.defaults.color = '#6c757d';

            // Revenue Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            const revenueChart = new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس',
                        'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
                    ],
                    datasets: [{
                        label: 'الإيرادات',
                        data: [65000, 78000, 85000, 92000, 88000, 95000, 102000, 98000, 105000,
                            112000, 108000, 125000
                        ],
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3
                    }, {
                        label: 'المصروفات',
                        data: [45000, 52000, 58000, 62000, 59000, 65000, 68000, 66000, 70000, 75000,
                            72000, 80000
                        ],
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString() + ' ر.س';
                                }
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });

            // Customer Growth Chart
            const customerGrowthCtx = document.getElementById('customerGrowthChart').getContext('2d');
            const customerGrowthChart = new Chart(customerGrowthCtx, {
                type: 'doughnut',
                data: {
                    labels: ['عملاء جدد', 'عملاء حاليين', 'عملاء غير نشطين'],
                    datasets: [{
                        data: [30, 45, 25],
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.8)',
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 193, 7, 0.8)'
                        ],
                        borderColor: [
                            'rgba(40, 167, 69, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 193, 7, 1)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 15
                            }
                        }
                    }
                }
            });

            // Invoice Status Chart
            const invoiceStatusCtx = document.getElementById('invoiceStatusChart').getContext('2d');
            const invoiceStatusChart = new Chart(invoiceStatusCtx, {
                type: 'pie',
                data: {
                    labels: ['مدفوعة', 'معلقة', 'متأخرة'],
                    datasets: [{
                        data: [75, 15, 10],
                        backgroundColor: [
                            'rgba(40, 167, 69, 0.8)',
                            'rgba(255, 193, 7, 0.8)',
                            'rgba(220, 53, 69, 0.8)'
                        ],
                        borderColor: [
                            'rgba(40, 167, 69, 1)',
                            'rgba(255, 193, 7, 1)',
                            'rgba(220, 53, 69, 1)'
                        ],
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });

            // Top Services Chart
            const topServicesCtx = document.getElementById('topServicesChart').getContext('2d');
            const topServicesChart = new Chart(topServicesCtx, {
                type: 'bar',
                data: {
                    labels: ['شحن بحري', 'شحن جوي', 'شحن بري', 'تخزين', 'تخليص جمركي'],
                    datasets: [{
                        label: 'عدد الطلبات',
                        data: [45, 32, 28, 25, 18],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(255, 205, 86, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(153, 102, 255, 0.8)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 99, 132, 1)',
                            'rgba(255, 205, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 2,
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Financial Overview Chart
            const financialOverviewCtx = document.getElementById('financialOverviewChart').getContext('2d');
            const financialOverviewChart = new Chart(financialOverviewCtx, {
                type: 'bar',
                data: {
                    labels: ['الأسبوع الأول', 'الأسبوع الثاني', 'الأسبوع الثالث', 'الأسبوع الرابع'],
                    datasets: [{
                        label: 'الإيرادات',
                        data: [25000, 32000, 28000, 35000],
                        backgroundColor: 'rgba(40, 167, 69, 0.8)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 2,
                        borderRadius: 5
                    }, {
                        label: 'المصروفات',
                        data: [18000, 22000, 19000, 24000],
                        backgroundColor: 'rgba(220, 53, 69, 0.8)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 2,
                        borderRadius: 5
                    }, {
                        label: 'الربح',
                        data: [7000, 10000, 9000, 11000],
                        backgroundColor: 'rgba(54, 162, 235, 0.8)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        borderRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString() + ' ر.س';
                                }
                            }
                        }
                    }
                }
            });

            // Finance Filter Event Listeners
            document.querySelectorAll('input[name="financeFilter"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    // Update chart data based on selected period
                    let newData, newLabels;

                    if (this.id === 'finance7days') {
                        newLabels = ['الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت',
                            'الأحد'
                        ];
                        newData = [
                            [5000, 6200, 4800, 7500, 6800, 5500, 6000],
                            [3200, 4100, 3500, 4800, 4200, 3800, 4000],
                            [1800, 2100, 1300, 2700, 2600, 1700, 2000]
                        ];
                    } else if (this.id === 'finance30days') {
                        newLabels = ['الأسبوع 1', 'الأسبوع 2', 'الأسبوع 3', 'الأسبوع 4'];
                        newData = [
                            [25000, 32000, 28000, 35000],
                            [18000, 22000, 19000, 24000],
                            [7000, 10000, 9000, 11000]
                        ];
                    } else {
                        newLabels = ['الشهر الأول', 'الشهر الثاني', 'الشهر الثالث'];
                        newData = [
                            [95000, 110000, 125000],
                            [68000, 75000, 85000],
                            [27000, 35000, 40000]
                        ];
                    }

                    financialOverviewChart.data.labels = newLabels;
                    financialOverviewChart.data.datasets[0].data = newData[0];
                    financialOverviewChart.data.datasets[1].data = newData[1];
                    financialOverviewChart.data.datasets[2].data = newData[2];
                    financialOverviewChart.update();
                });
            });
        });
    </script>

@endsection
