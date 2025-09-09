@extends('layouts.admin')

@section('title', 'تفاصيل الحاوية - ' . $container->code)

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2 mb-0">
                        تفاصيل الحاوية {{ $container->code }}
                    </h1>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="window.print()">
                        <i class="fas fa-print me-1"></i>
                        طباعة
                    </button>
                    <a href="{{ route('yard.containers.update', $container->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i>
                        تعديل
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Information -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-user-tie me-2"></i>
                معلومات العميل
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-item">
                        <strong class="text-muted">اسم العميل:</strong>
                        <a href="{{ route('users.customer.profile', $container->customer->id) }}" 
                            class="text-decoration-none fw-bold">
                            {{ $container->customer->name }}
                        </a>
                    </div>
                    <div class="info-item">
                        <strong class="text-muted">رقم السجل التجاري:</strong>
                        <span class="text-dark">{{ $container->customer->CR }}</span>
                    </div>
                    <div class="info-item">
                        <strong class="text-muted">الرقم الضريبي:</strong>
                        <span class="text-dark">{{ $container->customer->TIN }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <strong class="text-muted">رقم الهاتف:</strong>
                        <a href="tel:{{ $container->customer->phone }}" class="text-decoration-none">
                            {{ $container->customer->phone }}
                        </a>
                    </div>
                    <div class="info-item">
                        <strong class="text-muted">البريد الإلكتروني:</strong>
                        <a href="mailto:{{ $container->customer->email }}" class="text-decoration-none">
                            {{ $container->customer->email }}
                        </a>
                    </div>
                    <div class="info-item">
                        <strong class="text-muted">العنوان الوطني:</strong>
                        <span class="text-dark">{{ $container->customer->national_address }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Policies -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-contract me-2"></i>
                        إتفاقيات الحاوية ({{ count($container->policies) }})
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($container->policies) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($container->policies as $policy)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1">
                                            <a href="{{ $policy->type == 'تخزين' ? route('policies.storage.details', $policy->id) : route('policies.receive.details', $policy->id) }}" 
                                                class="badge bg-primary text-decoration-none me-2">{{ $policy->code }}</a>
                                            {{ $policy->type }}
                                        </h6>
                                        <p class="mb-1 text-muted small">
                                            <i class="fas fa-user me-1"></i>
                                            السائق: {{ $policy->driver_name }} ({{ $policy->driver_NID }})
                                        </p>
                                        <p class="mb-0 text-muted small">
                                            <i class="fas fa-car me-1"></i>
                                            {{ $policy->driver_car }} - {{ $policy->car_code }}
                                        </p>
                                    </div>
                                    <small class="text-muted">
                                        {{ Carbon\Carbon::parse($policy->date)->format('Y/m/d') }} 
                                        <i class="fas fa-calendar-alt ms-1"></i> 
                                    </small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>لا توجد إتفاقيات مرتبطة بهذه الحاوية</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Services -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tools me-2"></i>
                        الخدمات المضافة ({{ count($container->services) }})
                    </h5>
                </div>
                <div class="card-body">
                    @if(count($container->services) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($container->services as $service)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $service->description }}</h6>
                                        @if($service->pivot->notes)
                                        <p class="mb-0 text-muted small">
                                            <i class="fas fa-sticky-note me-1"></i>
                                            {{ $service->pivot->notes }}
                                        </p>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-success">
                                            {{ number_format($service->pivot->price, 2) }} ريال
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-3 pt-2 border-top">
                            <div class="d-flex justify-content-between fs-4">
                                <strong>إجمالي الخدمات:</strong>
                                <strong class="text-success">
                                    {{ number_format($container->services->sum('pivot.price'), 2) }} ريال
                                </strong>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-tools fa-2x mb-2"></i>
                            <p>لا توجد خدمات مضافة لهذه الحاوية</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline Section -->
    <div class="card shadow-lg border-0 mb-4">
        <div class="card-header bg-dark text-white border-0">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-route"></i>
                        <span class="ms-3">الخط الزمني للحاوية</span>
                    </h5>
                </div>
                <div>
                    <div class="text-center">
                        <h4 class="fw-bold m-0">
                            {{ \Carbon\Carbon::parse($container->date)->diffInDays($container->exit_date ?? now()) }}
                        </h4>
                        <small class="text-light">يوم</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="relative px-3 py-4">
                <div class="d-flex flex-column gap-3 relative">
                    <div class="timeline-state" data-aos="fade-up" data-aos-delay="100">
                        <div class="state-connector"></div>
                        <div class="relative z-3">
                            <div class="d-flex align-items-center justify-content-center relative rounded-circle text-white fs-5 bg-primary" style="width: 48px; height: 48px;">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="state-pulse"></div>
                        </div>
                        <div class="flex-grow-1 p-3 rounded-3 shadow-sm border-start border-4 border-primary">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5>إنشاء الحاوية</h5>
                                <small class="d-flex align-items-center text-secondary mb-0">
                                    <i class="far fa-calendar-alt me-1"></i>
                                    {{ \Carbon\Carbon::parse($container->created_at)->format('Y/m/d') }}
                                    <span class="rounded-5 px-2 ms-1" style="background-color: #e9ecef;">
                                        {{ \Carbon\Carbon::parse($container->created_at)->format('H:i') }}
                                    </span>
                                </small>
                            </div>
                            <p class="text-muted mb-0">
                                تم إنشاء الحاوية برقم <strong>{{ $container->code }}</strong> في النظام بواسطة 
                                <strong>{{ $container->made_by ?? 'خالد قمر' }}</strong>
                            </p>
                        </div>
                    </div>

                    @foreach($container->policies as $index => $policy)
                        <div class="timeline-state" data-aos="fade-up" data-aos-delay="{{ 200 + ($index * 100) }}">
                            <div class="state-connector"></div>
                            <div class="relative z-3">
                                <div class="d-flex align-items-center justify-content-center relative rounded-circle text-white fs-5 bg-primary" style="width: 48px; height: 48px;">
                                    <i class="fas {{ $policy->type === 'تخزين' ? 'fa-warehouse' : 'fa-truck-fast' }}"></i>
                                </div>
                                <div class="state-pulse"></div>
                            </div>
                            <div class="flex-grow-1 p-3 rounded-3 shadow-sm border-start border-4 border-primary">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5>{{ $policy->type }}</h5>
                                    <small class="d-flex align-items-center text-secondary">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        {{ \Carbon\Carbon::parse($policy->date)->format('d M Y') }}
                                    </small>
                                </div>
                                <div class="state-details">
                                    <div class="detail-row">
                                        <i class="fas fa-user text-muted me-1"></i>
                                        <span>السائق: <strong>{{ $policy->driver_name }}</strong></span>
                                    </div>
                                    <div class="detail-row">
                                        <i class="fas fa-car text-muted me-1"></i>
                                        <span>{{ $policy->driver_car }} - {{ $policy->car_code }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <i class="fas fa-id-card text-muted me-1"></i>
                                        <span>هوية السائق: {{ $policy->driver_NID }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if($container->exit_date)
                        <div class="timeline-state" data-aos="fade-up" data-aos-delay="{{ 300 + (count($container->policies) * 100) }}">
                            <div class="state-connector"></div>
                            <div class="relative z-3">
                                <div class="d-flex align-items-center justify-content-center relative rounded-circle text-white fs-5 bg-primary" style="width: 48px; height: 48px;">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="state-pulse"></div>
                            </div>
                            <div class="flex-grow-1 p-3 rounded-3 shadow-sm border-start border-4 border-primary">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5>تم التسليم</h5>
                                    <small class="d-flex align-items-center text-secondary">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        {{ \Carbon\Carbon::parse($container->exit_date)->format('d M Y') }}
                                        <span class="rounded-5 px-2 ms-1" style="background-color: #e9ecef;">
                                            {{ \Carbon\Carbon::parse($container->exit_date)->format('H:i') }}
                                        </span>
                                    </small>
                                </div>
                                <div class="state-details">
                                    <div class="detail-row">
                                        <i class="fas fa-user-check text-success me-1"></i>
                                        <span>مُسلم بواسطة: <strong>{{ $container->delivered_by }}</strong></span>
                                    </div>
                                    @if($container->location)
                                    <div class="detail-row">
                                        <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                        <span>الموقع: {{ $container->location }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="timeline-state" data-aos="fade-up" data-aos-delay="{{ 300 + (count($container->policies) * 100) }}">
                            <div class="state-connector"></div>
                            <div class="relative z-3">
                                <div class="d-flex align-items-center justify-content-center relative rounded-circle text-white fs-5 bg-secondary" style="width: 48px; height: 48px;">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="state-pulse pending-pulse"></div>
                            </div>
                            <div class="state-content">
                                <div class="state-header">
                                    <h6 class="state-title">في انتظار التسليم</h6>
                                    <span class="state-badge badge bg-secondary">قيد الانتظار</span>
                                </div>
                                <p class="state-description">
                                    الحاوية جاهزة للتسليم
                                </p>
                                <div class="state-details">
                                    <small class="text-muted">سيتم التحديث عند إتمام التسليم</small>
                                </div>
                            </div>
                        </div>
                    @endif

                    @foreach($container->invoices as $index => $invoice)
                        <div class="timeline-state completed invoice-state" data-aos="fade-up" data-aos-delay="{{ 400 + (count($container->policies) * 100) + ($index * 100) }}">
                            <div class="state-connector"></div>
                            <div class="relative z-3">
                                <div class="d-flex align-items-center justify-content-center relative rounded-circle text-white fs-5 bg-primary" style="width: 48px; height: 48px;">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </div>
                                <div class="state-pulse"></div>
                            </div>
                            <div class="flex-grow-1 p-3 rounded-3 shadow-sm border-start border-4 border-primary">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5>إنشاء فاتورة</h5>
                                    <small class="d-flex align-items-center text-secondary">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        {{ \Carbon\Carbon::parse($invoice->created_at)->format('d M Y') }}
                                        <span class="rounded-5 px-2 ms-1" style="background-color: #e9ecef;">
                                            {{ \Carbon\Carbon::parse($invoice->created_at)->format('H:i') }}
                                        </span>
                                    </small>
                                </div>
                                <p class="state-description ">
                                    تم فوترة الحاوية في فاتورة رقم <a href="{{ route('invoices.details', $invoice->code) }}" class="text-decoration-none fw-bold">{{ $invoice->code }}</a>
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
<style>
    .timeline-state {
        display: flex;
        align-items: flex-start;
        gap: 1.5rem;
        position: relative;
    }

    .state-connector {
        position: absolute;
        right: 24px;
        top: 48px;
        width: 2px;
        height: calc(100% + 2rem);
        background: linear-gradient(to bottom, #007bff, #28a745);
    }

    .timeline-state:last-child .state-connector {
        display: none;
    }

    .state-pulse {
        position: absolute;
        top: 0;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: rgba(0, 123, 255, 0.3);
        animation: pulse 2s ease-in-out infinite;
        pointer-events: none;
        z-index: -1;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.3);
            opacity: 0.7;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .state-content {
        flex: 1;
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        border-left: 4px solid #007bff;
    }

    .invoice-state .state-content {
        border-left-color: #667eea !important;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
    }

    .state-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .state-title {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
    }

    .state-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-weight: 500;
    }

    .state-date {
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        color: #6c757d;
        display: flex;
        align-items: center;
    }

    .time {
        margin-left: auto;
        background: #e9ecef;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .state-details {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 8px;
        margin-top: 1rem;
    }

    .detail-row {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .detail-row:last-child {
        margin-bottom: 0;
    }
</style>

@push('styles')
<style>
.info-item {
    margin-bottom: 15px;
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.info-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 0;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #007bff;
}

.timeline-title {
    color: #495057;
    margin-bottom: 5px;
    font-weight: 600;
}

.timeline-date {
    font-size: 0.875rem;
    margin-bottom: 8px;
}

.timeline-description {
    margin-bottom: 0;
    color: #6c757d;
}

.card {
    border: none;
    border-radius: 12px;
    overflow: hidden;
}

.card-header {
    border-bottom: none;
    font-weight: 600;
}

.badge {
    font-size: 0.875em;
}

.list-group-item {
    border: none;
    border-bottom: 1px solid #e9ecef;
}

.list-group-item:last-child {
    border-bottom: none;
}

@media print {
    .btn, .breadcrumb {
        display: none !important;
    }
    
    .card {
        break-inside: avoid;
        margin-bottom: 20px !important;
    }
}

/* Hover effects */
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
    transition: all 0.3s ease;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,0.075);
}
</style>
@endpush

@endsection