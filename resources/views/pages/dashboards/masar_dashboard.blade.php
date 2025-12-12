@extends('layouts.app')

@section('title', 'dashboard')

@section('content')
    <style>
        .stats-card,
        .stats-card:hover .card,
        .stats-card:hover .card .card-body h6 {
            transition: all 0.5s ease;
        }

        .stats-card:hover {
            transform: scale(1.05);
        }

        .stats-card:hover .card {
            background: var(--gradient);
            color: #fff !important;
        }

        .stats-card:hover .card .card-body h6 {
            color: #fff !important;
        }

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
        <div class="col-12 col-sm-6 col-lg-3 stats-card">
            <a href="{{ route('money.entries') }}?view=سندات%20صرف" class="text-decoration-none">
                <div class="card rounded-3 border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">سندات الصرف</h6>
                            <h6 class="text-primary fw-bold mb-0" style="font-size: 1.4rem;">{{ $vouchers ?? 0 }}</h6>
                        </div>
                        <div>
                            <i class="fa-solid fa-money-bill-wave fa-xl"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-lg-3 stats-card">
            <a href="{{ route('money.entries') }}?view=سندات%20قبض" class="text-decoration-none">
                <div class="card rounded-3 border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">سندات القبض</h6>
                            <h6 class="text-primary fw-bold mb-0" style="font-size: 1.4rem;">{{ $vouchers ?? 0 }}</h6>
                        </div>
                        <div>
                            <i class="fa-solid fa-hand-holding-dollar fa-xl"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-lg-3 stats-card">
            <a href="{{ route('money.entries') }}" class="text-decoration-none">
                <div class="card rounded-3 border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">قيود يومية</h6>
                            <h6 class="text-primary fw-bold mb-0" style="font-size: 1.4rem;">{{ $journals ?? 0 }}</h6>
                        </div>
                        <div>
                            <i class="fa-solid fa-money-bill-transfer fa-xl"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-12 col-sm-6 col-lg-3 stats-card">
            <a href="{{ route('money.reports') }}?view=كشف%20حساب" class="text-decoration-none">
                <div class="card rounded-3 border-0 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">كشف الحساب</h6>
                            <p class="mb-1" style="font-size: 0.9rem;">عرض كافة الحسابات</p>
                        </div>
                        <div>
                            <i class="fa-solid fa-receipt fa-xl"></i>
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
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title fw-bold mb-0">الإيرادات الشهرية</h5>
                    </div>
                    <div class="chart-container" style="height: 400px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- invoices bie chart -->
        <div class="col-12 col-xl-4">
            <div class="card rounded-3 border-0 shadow-sm">
                <div class="card-body">
                    <!-- Sales Invoices Chart -->
                    <div class="mb-4 position-relative">
                        <h6 class="text-dark fw-bold mb-3">فواتير المبيعات</h6>
                        <button class="btn btn-sm btn-outline-primary position-absolute top-0 end-0 z-10">
                            عرض الفواتير
                        </button>
                        <div class="d-flex align-items-center">
                            <div class="chart-container flex-shrink-0" style="height: 150px; width: 150px;">
                                <canvas id="salesInvoicesChart"></canvas>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="me-2"
                                        style="width: 12px; height: 12px; background-color: #0b56a9; border-radius: 50%;">
                                    </div>
                                    <span class="text-muted small me-auto">مدفوعة</span>
                                    <span class="fw-bold small">68%</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="me-2"
                                        style="width: 12px; height: 12px; background-color: #218bab; border-radius: 50%;">
                                    </div>
                                    <span class="text-muted small me-auto">معلقة</span>
                                    <span class="fw-bold small">32%</span>
                                </div>
                                {{-- <div class="d-flex align-items-center">
                                    <div class="me-2"
                                        style="width: 12px; height: 12px; background-color: #52d6cb; border-radius: 50%;">
                                    </div>
                                    <span class="text-muted small me-auto">متأخرة</span>
                                    <span class="fw-bold small">10%</span>
                                </div> --}}
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Expense Invoices Chart -->
                    <div class="mb-1 position-relative">
                        <h6 class="text-dark fw-bold mb-3">فواتير المشتريات</h6>
                        <button class="btn btn-sm btn-outline-primary position-absolute top-0 end-0 z-10">
                            عرض الفواتير
                        </button>
                        <div class="d-flex align-items-center">
                            <div class="chart-container flex-shrink-0" style="height: 150px; width: 150px;">
                                <canvas id="expenseInvoicesChart"></canvas>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="me-2"
                                        style="width: 12px; height: 12px; background-color: #0b56a9; border-radius: 50%;">
                                    </div>
                                    <span class="text-muted small me-auto">مدفوعة</span>
                                    <span class="fw-bold small">75%</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="me-2"
                                        style="width: 12px; height: 12px; background-color: #218bab; border-radius: 50%;">
                                    </div>
                                    <span class="text-muted small me-auto">معلقة</span>
                                    <span class="fw-bold small">25%</span>
                                </div>
                                {{-- <div class="d-flex align-items-center">
                                    <div class="me-2"
                                        style="width: 12px; height: 12px; background-color: #52d6cb; border-radius: 50%;">
                                    </div>
                                    <span class="text-muted small me-auto">متأخرة</span>
                                    <span class="fw-bold small">7%</span>
                                </div> --}}
                            </div>
                        </div>
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
                        borderColor: '#52d6cb',
                        backgroundColor: 'rgba(44, 172, 189, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointBackgroundColor: '#52d6cb',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }, {
                        label: 'المصروفات',
                        data: [45000, 52000, 58000, 62000, 59000, 65000, 68000, 66000, 70000, 75000,
                            72000, 80000
                        ],
                        borderColor: '#218bab',
                        backgroundColor: 'rgba(33, 139, 171, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointBackgroundColor: '#218bab',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }, {
                        label: 'الربح',
                        data: [20000, 26000, 27000, 30000, 29000, 30000, 34000, 32000, 35000,
                            37000, 36000, 45000
                        ],
                        borderColor: '#0b56a9',
                        backgroundColor: 'rgba(11, 86, 169, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointBackgroundColor: '#0b56a9',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
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
                                    return value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });

            // Sales Invoices Pie Chart
            const salesInvoicesCtx = document.getElementById('salesInvoicesChart').getContext('2d');
            const salesInvoicesChart = new Chart(salesInvoicesCtx, {
                type: 'pie',
                data: {
                    labels: ['مدفوعة', 'معلقة', 'متأخرة'],
                    datasets: [{
                        data: [68, 32,],
                        backgroundColor: [
                            '#0b56a9',
                            '#218bab',
                            '#52d6cb',
                        ],
                        borderColor: [
                            '#fff',
                            '#fff',
                            '#fff'
                        ],
                        borderWidth: 3,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.parsed + '%';
                                }
                            }
                        }
                    }
                }
            });

            // Expense Invoices Pie Chart
            const expenseInvoicesCtx = document.getElementById('expenseInvoicesChart').getContext('2d');
            const expenseInvoicesChart = new Chart(expenseInvoicesCtx, {
                type: 'pie',
                data: {
                    labels: ['مدفوعة', 'معلقة', 'متأخرة'],
                    datasets: [{
                        data: [75, 25,],
                        backgroundColor: [
                            '#0b56a9',
                            '#218bab',
                            '#52d6cb',
                        ],
                        borderColor: [
                            '#fff',
                            '#fff',
                            '#fff'
                        ],
                        borderWidth: 3,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.parsed + '%';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>

@endsection
