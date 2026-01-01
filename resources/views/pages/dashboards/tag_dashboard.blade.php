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

    <!-- Container Reports Row -->
    <div class="row mb-4">
        <div class="col-12 col-lg-4 mb-3 mb-lg-0">
            <div class="card rounded-3 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title text-center fw-bold mb-4">توزيع الحاويات في الساحة</h5>
                    <div class="chart-container d-flex justify-content-around align-items-center" style="position: relative; height:176px;">
                        <canvas id="containersDistribution"></canvas>
                        <div class="ms-3">
                            @foreach ($containerTypes as $type)
                                <div class="d-flex align-items-center mb-2">
                                    <div class="me-2"
                                        style="width: 12px; height: 12px; background-color: var(--blue-{{ $loop->iteration }}); border-radius: 50%;">
                                    </div>
                                    <span class="text-muted small me-auto">{{ $type->name }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <a href="{{ route('yard.containers') }}" class="btn btn-primary fw-bold w-100 mt-4">
                        عرض جميع الحاويــات
                    </a>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-8">
            <div class="card rounded-3 shadow-sm border-0">
                <div class="card-body">
                    <h5
                        class="card-title d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center text-dark fw-bold mb-3">
                        <div class="mb-2 mb-sm-0">معدل دخول وخروج الحاويات</div>
                        <a href="{{ route('yard.containers.reports') }}" class="btn btn-sm btn-primary fw-bold">
                            عرض تقارير الحاويات
                        </a>
                    </h5>
                    <div class="chart-container" style="position: relative; height:240px;">
                        <canvas id="containersLineChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Finance Row -->
    <div class="row mb-4">
        <div class="col-12 col-lg-8 mb-3 mb-lg-0 order-1 order-lg-2">
            <div class="card rounded-3 border-0 shadow-sm">
                <div class="card-body">
                    <div class="border-0">
                        <h5 class="card-title fw-bold mb-3">أفضل الخدمات</h5>
                    </div>
                    <div class="chart-container" style="height: 283px;">
                        <canvas id="topServicesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4 order-3">
            <div class="card rounded-3 border-0 shadow-sm">
                <div class="card-body">
                    <!-- Sales Invoices Chart -->
                    <div class="mb-4 position-relative">
                        <h6 class="text-dark fw-bold mb-3">فواتير المبيعات</h6>
                        <button class="btn btn-sm btn-primary position-absolute top-0 end-0 z-10">
                            عرض الفواتير
                        </button>
                        <div class="d-flex align-items-center">
                            <div class="chart-container flex-shrink-0" style="height: 100px; width: 100px;">
                                <canvas id="salesInvoicesChart"></canvas>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="me-2" style="width: 12px; height: 12px; background-color: #0b56a9; border-radius: 50%;"></div>
                                    <span class="text-muted small me-auto">مدفوعة</span>
                                    <span class="fw-bold small">68%</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <div class="me-2" style="width: 12px; height: 12px; background-color: #218bab; border-radius: 50%;"></div>
                                    <span class="text-muted small me-auto">معلقة</span>
                                    <span class="fw-bold small">32%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Expense Invoices Chart -->
                    <div class="mb-1 position-relative">
                        <h6 class="text-dark fw-bold mb-3">فواتير المشتريات</h6>
                        <button class="btn btn-sm btn-primary position-absolute top-0 end-0 z-10">
                            عرض الفواتير
                        </button>
                        <div class="d-flex align-items-center">
                            <div class="chart-container flex-shrink-0" style="height: 100px; width: 100px;">
                                <canvas id="expenseInvoicesChart"></canvas>
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="me-2" style="width: 12px; height: 12px; background-color: #0b56a9; border-radius: 50%;"></div>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12 col-lg-12 mb-3 mb-lg-0 order-1 order-lg-2">
            <div class="card rounded-3 shadow-sm border-0">
                <div class="card-body">
                    <h5
                        class="card-title d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center fw-bold mb-4">
                        <div class="mb-2 mb-sm-0">
                            تقارير الإيرادات الشهرية
                            <i class="fa-solid fa-money-bill-trend-up"></i>
                        </div>
                        <button class="btn btn-sm btn-primary fw-bold">
                            عرض تقارير الإيرادات
                        </button>
                    </h5>
                    <div class="chart-container" style="position: relative; height:288px;">
                        <canvas id="profitChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Chart.defaults.font.family = 'Cairo, sans-serif';
            Chart.defaults.color = '#6c757d';

            // Line Chart - Container Trend
            const containersLineChart = document.getElementById('containersLineChart').getContext('2d');
            new Chart(containersLineChart, {
                type: 'line',
                data: {
                    labels: @json(array_keys($containersEnteredTrend)),
                    datasets: [{
                            label: 'عدد الحاويات الداخلة',
                            data: [5, 8, 4, 10, 6, 9, 3],
                            borderColor: '#0b56a9',
                            backgroundColor: 'rgba(11, 86, 169, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#0b56a9',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'عدد الحاويات الخارجة',
                            data: [3, 5, 7, 4, 4, 8, 2],
                            borderColor: '#2cacbd',
                            backgroundColor: 'rgba(44, 172, 189, 0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#2cacbd',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: {
                                    size: window.innerWidth < 768 ? 10 : 12
                                }
                            },
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                maxRotation: window.innerWidth < 768 ? 45 : 0,
                                minRotation: window.innerWidth < 768 ? 45 : 0,
                                font: {
                                    size: window.innerWidth < 768 ? 10 : 12
                                }
                            },
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            titleFont: {
                                size: window.innerWidth < 768 ? 12 : 14
                            },
                            bodyFont: {
                                size: window.innerWidth < 768 ? 11 : 13
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    }
                }
            });

            // Pie Chart - Container Distribution
            const containersDistribution = document.getElementById('containersDistribution').getContext('2d');
            new Chart(containersDistribution, {
                type: 'pie',
                data: {
                    labels: @json(array_keys($containersDistribution)),
                    datasets: [{
                        data: [20, 15, 3, 6, 5],
                        backgroundColor: [
                            '#0b56a9',
                            '#218bab',
                            '#2cacbd',
                            '#42b3af',
                            '#52d6cb',
                        ],
                        borderColor: [
                            '#fff',
                            '#fff',
                            '#fff',
                            '#fff',
                            '#fff',
                        ],
                        borderWidth: 2,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: false,
                            position: window.innerWidth < 768 ? 'bottom' : 'left',
                            labels: {
                                color: '#212529',
                                padding: 15,
                                usePointStyle: true,
                                font: {
                                    size: window.innerWidth < 768 ? 11 : 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Total: ${context.raw}`;
                                }
                            },
                            titleFont: {
                                size: window.innerWidth < 768 ? 12 : 14
                            },
                            bodyFont: {
                                size: window.innerWidth < 768 ? 11 : 13
                            }
                        }
                    }
                }
            });

            // Bar Chart - Profit Chart
            const profitChart = document.getElementById('profitChart').getContext('2d');
            new Chart(profitChart, {
                type: 'bar',
                data: {
                    labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس',
                        'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
                    ],
                    datasets: [{
                            label: 'المصروفات',
                            data: [32000, 54000, 28000, 76000, 41000, 63000, 29500, 52000, 36000, 47000,
                                69000, 58500
                            ],
                            backgroundColor: 'rgba(44, 172, 189, 0.85)',
                            borderColor: '#2cacbd',
                            borderWidth: 1,
                            borderRadius: window.innerWidth < 768 ? 6 : 12,
                            barThickness: window.innerWidth < 768 ? 10 : 18,
                        },
                        {
                            label: 'الايرادات',
                            data: [68000, 92000, 75000, 120000, 88000, 135000, 79000, 110000, 95000,
                                102000, 148000, 160000
                            ],
                            backgroundColor: 'rgba(33, 139, 171, 0.85)',
                            borderColor: '#218bab',
                            borderWidth: 1,
                            borderRadius: window.innerWidth < 768 ? 6 : 12,
                            barThickness: window.innerWidth < 768 ? 10 : 18,
                        },
                        {
                            label: 'الربح',
                            data: [36000, 38000, 47000, 44000, 47000, 72000, 49500, 58000, 59000, 55000,
                                79000, 101500
                            ],
                            backgroundColor: 'rgba(11, 86, 169, 0.85)',
                            borderColor: '#0b56a9',
                            borderWidth: 1,
                            borderRadius: window.innerWidth < 768 ? 6 : 12,
                            barThickness: window.innerWidth < 768 ? 10 : 18,
                        }
                    ]
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
                                    return `${context.dataset.label}: ${context.raw}`;
                                }
                            },
                            titleFont: {
                                size: window.innerWidth < 768 ? 12 : 14
                            },
                            bodyFont: {
                                size: window.innerWidth < 768 ? 11 : 13
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 160000,
                            ticks: {
                                stepSize: 1,
                                font: {
                                    size: window.innerWidth < 768 ? 10 : 12
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: window.innerWidth < 768 ? 10 : 12
                                }
                            }
                        }
                    }
                }
            });

            // Top Services Chart
            const topServicesCtx = document.getElementById('topServicesChart').getContext('2d');
            const topServicesChart = new Chart(topServicesCtx, {
                type: 'bar',
                data: {
                    labels: @json(array_keys($topServices)),
                    datasets: [{
                        label: 'عدد الطلبات',
                        data: [50, 99, 90, 80, 123],
                        backgroundColor: [
                            'rgba(82, 214, 203, 0.8)',
                            'rgba(66, 179, 175, 0.8)',
                            'rgba(44, 172, 189, 0.8)',
                            'rgba(33, 139, 171, 0.8)',
                            'rgba(11, 86, 169, 0.8)',
                        ],
                        borderColor: [
                            'rgba(82, 214, 203)',
                            'rgba(66, 179, 175)',
                            'rgba(44, 172, 189)',
                            'rgba(33, 139, 171)',
                            'rgba(11, 86, 169)',
                        ],
                        borderWidth: 2,
                        borderRadius: 20
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
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
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
                        data: [68, 32, ],
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
                        data: [75, 25, ],
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

            // Update charts on window resize
            let resizeTimer;
            window.addEventListener('resize', function() {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(function() {
                    // Charts will automatically resize due to responsive: true
                    Chart.instances.forEach(chart => chart.resize());
                }, 250);
            });
        });
    </script>
@endsection
