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
                <h5 class="card-title d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center text-dark fw-bold mb-4">
                    <div class="mb-2 mb-sm-0">
                        <i class="fa-solid fa-file-circle-question me-1"></i>
                        تقارير الحاويات
                    </div>
                    <div>
                        <form method="GET" action="">
                            <input type="date" name="date" class="form-control border-primary"
                                value="{{ request('date', Carbon\Carbon::now()->format('Y-m-d')) }}"
                                onchange="this.form.submit()">
                        </form>
                    </div>
                </h5>
                <div class="events-container">
                    <div class="event-card mb-3 p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-bold">إجمالي الحاويات في الساحة</h6>
                            </div>
                            <p class="mb-0 text-muted fw-bold">
                                {{ $availableContainers }} <i class="fa-solid fa-boxes-stacked text-primary ms-1"></i>
                            </p>
                        </div>
                    </div>
                    <div class="event-card mb-3 p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-bold">الحاويات التي تم إستلامها</h6>
                            </div>
                            <p class="mb-0 text-muted fw-bold">
                                {{ $receivedContainers  }} <i class="fa-solid fa-circle-check text-primary ms-1"></i>
                            </p>
                        </div>
                    </div>
                    <div class="event-card mb-3 p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-bold">الحاويات التي تم تسليمها</h6>
                            </div>
                            <p class="mb-0 text-muted fw-bold">
                                {{ $deliveredContainers  }} <i class="fa-solid fa-circle-check text-primary ms-1"></i>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-8">
        <div class="card rounded-3 shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center text-dark fw-bold mb-3">
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
    <div class="col-12 col-lg-3 mb-3 mb-lg-0 order-2 order-lg-1">
        <div class="card rounded-3 shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title text-center fw-bold mb-4">توزيع الحاويات في الساحة</h5>
                <div class="chart-container d-flex justify-content-center align-items-center" style="position: relative; height:257px;">
                    <canvas id="containersDistribution"></canvas>
                </div>
                <a href="{{ route('yard.containers') }}" class="btn btn-primary fw-bold w-100 mt-3">
                    عرض جميع الحاويــات
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-lg-6 mb-3 mb-lg-0 order-1 order-lg-2">
        <div class="card rounded-3 shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center fw-bold mb-4">
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

    <div class="col-12 col-lg-3 order-3">
        <div class="card rounded-3 shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center text-dark fw-bold mb-4">
                    <div class="mb-2 mb-sm-0">الصندوق</div>
                    <a href="{{ route('money.entries') }}" class="btn btn-sm btn-primary fw-bold">
                        تفاصيل
                    </a>
                </h5>
                <div class="events-container">
                    <div class="event-card mb-3 p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-bold">سندات الصرف</h6>
                            </div>
                            <p class="mb-0 text-muted fw-bold">
                                {{ $receipt_vouchers_amount }} <i class="fa-solid fa-dollar-sign ms-1"></i>
                            </p>
                        </div>
                    </div>
                    <div class="event-card mb-3 p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-bold">سندات القبض</h6>
                            </div>
                            <p class="mb-0 text-muted fw-bold">
                                {{ $payment_vouchers_amount }} <i class="fa-solid fa-dollar-sign ms-1"></i>
                            </p>
                        </div>
                    </div>
                    <div class="event-card mb-3 p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-bold">الصندوق</h6>
                            </div>
                            <p class="mb-0 text-muted fw-bold">
                                {{ $balanceBox }} <i class="fa-solid fa-dollar-sign ms-1"></i>
                            </p>
                        </div>
                    </div>
                    <div class="event-card mb-3 p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-bold">البنك</h6>
                            </div>
                            <p class="mb-0 text-muted fw-bold">
                                {{ 0 }} <i class="fa-solid fa-dollar-sign ms-1"></i>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Line Chart - Container Trend
        const containersLineChart = document.getElementById('containersLineChart').getContext('2d');
        new Chart(containersLineChart, {
            type: 'line',
            data: {
                labels: @json(array_keys($containersEnteredTrend)),
                datasets: [{
                    label: 'عدد الحاويات الداخلة',
                    data: @json(array_values($containersEnteredTrend)),
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
                    data: @json(array_values($containersExitTrend)),
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
                }]
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
                    data: @json(array_values($containersDistribution)),
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
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: window.innerWidth < 768 ? 'bottom' : 'bottom',
                        labels: {
                            color: '#212529',
                            padding: window.innerWidth < 768 ? 8 : 12,
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
                labels: ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul'],
                datasets: [{
                    label: 'students distribution',
                    data: [0, 0, 0, 0, 0, 0, 0],
                    backgroundColor: 'rgba(0, 123, 255, 0.7)',
                    borderColor: '#007bff',
                    borderWidth: 1,
                    borderRadius: window.innerWidth < 768 ? 8 : 15,
                    barThickness: window.innerWidth < 768 ? 30 : 50,
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
                                return `Total: ${context.raw} students`;
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
                        max: 16,
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