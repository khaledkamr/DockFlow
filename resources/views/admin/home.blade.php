@extends('layouts.admin')

@section('title', 'dashboard')

@section('content')
<div class="row mb-4">
    <div class="col">
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
    </div>
    <div class="col">
        <div class="card rounded-3 border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title">إجمالي الموظفين</h6>
                    <h6 class="text-primary fw-bold mb-0" style="font-size: 1.4rem;">{{ $invoices }}</h6>
                </div>
                <div>
                    <i class="fa-solid fa-users fa-xl"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
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
    </div>
    <div class="col">
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
    </div>
</div>

<div class="row mb-4">
    <div class="col-4">
        <div class="card rounded-3 shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title d-flex justify-content-between align-items-center text-dark fw-bold mb-4">
                    <div>
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
    <div class="col-md-8">
        <div class="card rounded-3 shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title d-flex justify-content-between align-items-center text-dark fw-bold">
                    <div>معدل دخول وخروج الحاويات</div>
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

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card rounded-3 shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title text-center fw-bold mb-4">توزيع الحاويات في الساحة</h5>
                <div class="chart-container" style="position: relative; height:257px;">
                    <canvas id="containersDistribution"></canvas>
                </div>
                <a href="{{ route('yard.containers') }}" class="btn btn-primary fw-bold w-100">
                    عرض جميع الحاويــات
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card rounded-3 shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title d-flex justify-content-between align-items-center fw-bold mb-4">
                    <div>
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
    <div class="col-md-3">
        <div class="card rounded-3 shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title d-flex justify-content-between align-items-center text-dark fw-bold mb-4">
                    <div>الصندوق</div>
                    <a href="{{ route('admin.money.entries') }}" class="btn btn-sm btn-primary fw-bold">
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
                                {{ $availableContainers }} <i class="fa-solid fa-dollar-sign ms-1"></i>
                            </p>
                        </div>
                    </div>
                    <div class="event-card mb-3 p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-bold">سندات القبض</h6>
                            </div>
                            <p class="mb-0 text-muted fw-bold">
                                {{ $waitingContainers  }} <i class="fa-solid fa-dollar-sign ms-1"></i>
                            </p>
                        </div>
                    </div>
                    <div class="event-card mb-3 p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-bold">الصندوق</h6>
                            </div>
                            <p class="mb-0 text-muted fw-bold">
                                {{ $receivedContainers  }} <i class="fa-solid fa-dollar-sign ms-1"></i>
                            </p>
                        </div>
                    </div>
                    <div class="event-card mb-3 p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-bold">البنك</h6>
                            </div>
                            <p class="mb-0 text-muted fw-bold">
                                {{ $deliveredContainers  }} <i class="fa-solid fa-dollar-sign ms-1"></i>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if (session('success'))
    @push('scripts')
        <script>
            showToast("{{ session('success') }}", "success");
        </script>
    @endpush
@endif

@if ($errors->any())
    @push('scripts')
        <script>
            @foreach ($errors->all() as $error)
                showToast("{{ $error }}", "danger");
            @endforeach
        </script>
    @endpush
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const containersLineChart = document.getElementById('containersLineChart').getContext('2d');
        new Chart(containersLineChart, {
            type: 'line',
            data: {
                labels: ['sat', 'sun', 'mon', 'tue', 'wed', 'thr', 'fri', 'sat'],
                datasets: [{
                    label: 'عدد الحاويات',
                    data: @json($containersTrend),
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#007bff',
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
                        },
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxRotation: 45,
                            minRotation: 0
                        },
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        const containersDistribution = document.getElementById('containersDistribution').getContext('2d');
        new Chart(containersDistribution, {
            type: 'pie',
            data: {
                labels: ['فئة 20', 'فئة 30', 'فئة 40', 'اخرى'],
                datasets: [{
                    data: @json($containersDistribution),
                    backgroundColor: [
                        '#0b56a9', 
                        '#2b87ac',
                        '#42b3af', 
                        'rgba(108, 117, 125, 0.65)', 
                    ],
                    borderColor: [
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
                        position: 'bottom',
                        labels: {
                            color: '#212529',
                            padding: 12,
                            usePointStyle: true,
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Total: ${context.raw}`;
                            }
                        }
                    }
                }
            }
        });

        const profitChart = document.getElementById('profitChart').getContext('2d');
        new Chart(profitChart, {
            type: 'bar',
            data: {
                labels: ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul'],
                datasets: [{
                    label: 'students distribution',
                    data: [1, 2, 3, 10, 5, 15, 7],
                    backgroundColor: 'rgba(0, 123, 255, 0.7)',
                    borderColor: '#007bff',
                    borderWidth: 1,
                    borderRadius: 15,
                    barThickness: 50,
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
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 16,
                        ticks: {
                            stepSize: 1
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
    });
</script>
@endsection