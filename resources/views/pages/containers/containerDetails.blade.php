@extends('layouts.app')

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
                {{-- <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary" onclick="window.print()">
                        <i class="fas fa-print me-1"></i>
                        طباعة
                    </button>
                    <a href="{{ route('yard.containers.update', $container->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i>
                        تعديل
                    </a>
                </div> --}}
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
                        <a href="{{ route('users.customer.profile', $container->customer) }}" 
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
        <!-- Services -->
        <div class="col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tools me-2"></i>
                        الخدمات المضافة ({{ count($container->services) }})
                    </h5>
                </div>
                <div class="card-body {{ count($container->services) > 0 ? '' : 'd-flex justify-content-center align-items-center' }} ">
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
                                        <span class="badge bg-primary">
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
                                <strong class="text-primary">
                                    {{ number_format($container->services->sum('pivot.price'), 2) }} ريال
                                </strong>
                            </div>
                        </div>
                    @else
                        <div class="text-center text-secondary">
                            <i class="fas fa-tools fa-2x mb-2"></i>
                            <p class="fw-bold">لا توجد خدمات مضافة لهذه الحاوية</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- notes -->
        <div class="col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-sticky-note me-2"></i>
                        ملاحظات الحاوية
                    </h5>
                </div>
                <div class="card-body">
                    @if($container->notes)
                       <p>{{ $container->notes }}</p>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>لا توجد ملاحظات على هذه الحاوية</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline Section -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-dark text-white border-0">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="card-title mb-0 d-flex align-items-center">
                        <i class="fas fa-route"></i>
                        <span class="ms-3">الخط الزمني للحاوية</span>
                    </h5>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2">
                        <h4 class="fw-bold m-0">
                            {{ (int) (\Carbon\Carbon::parse($container->date)->diffInDays($container->exit_date ?? now())) }}
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
                                        {{ \Carbon\Carbon::parse($container->created_at)->format('h:i A') }}
                                    </span>
                                </small>
                            </div>
                            <p class="text-muted mb-0">
                                تم إنشاء الحاوية في النظام برقم <strong>{{ $container->code }}</strong> من نوع <strong>{{ $container->containerType->name ?? 'N/A' }}</strong> في النظام بواسطة 
                                <strong>{{ $container->made_by->name }}</strong>
                            </p>
                        </div>
                    </div>

                    @if($serviceInvoice)
                        <div class="timeline-state completed invoice-state">
                            <div class="state-connector"></div>
                            <div class="relative z-3">
                                <div class="d-flex align-items-center justify-content-center relative rounded-circle text-white fs-5 bg-primary" style="width: 48px; height: 48px;">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </div>
                                <div class="state-pulse"></div>
                            </div>
                            <div class="flex-grow-1 p-3 rounded-3 shadow-sm border-start border-4 border-primary">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5>إنشاء فاتورة تخليص جمركي</h5>
                                    <small class="d-flex align-items-center text-secondary">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        {{ \Carbon\Carbon::parse($serviceInvoice->created_at)->format('d M Y') }}
                                        <span class="rounded-5 px-2 ms-1" style="background-color: #e9ecef;">
                                            {{ \Carbon\Carbon::parse($serviceInvoice->created_at)->format('h:i A') }}
                                        </span>
                                    </small>
                                </div>
                                <p class="text-muted">
                                    تم فوترة الحاوية بموجب فاتورة رقم <a href="{{ route('invoices.services.details', $serviceInvoice) }}" class="text-decoration-none fw-bold">{{ $serviceInvoice->code }}</a> بواسطة <strong>{{ $storageInvoice->made_by->name }}</strong>
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($transaction)
                        <div class="timeline-state">
                            <div class="state-connector"></div>
                            <div class="relative z-3">
                                <div class="d-flex align-items-center justify-content-center relative rounded-circle text-white fs-5 bg-primary" style="width: 48px; height: 48px;">
                                    <i class="fas fa-file-contract"></i>
                                </div>
                                <div class="state-pulse"></div>
                            </div>
                            <div class="flex-grow-1 p-3 rounded-3 shadow-sm border-start border-4 border-primary">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5>معاملة تخليص</h5>
                                    <small class="d-flex align-items-center text-secondary">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        {{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y') }}
                                        <span class="rounded-5 px-2 ms-1" style="background-color: #e9ecef;">
                                            {{ \Carbon\Carbon::parse($transaction->created_at)->format('h:i A') }}
                                        </span>
                                    </small>
                                </div>
                                <div class="text-muted">
                                    تم إنشاء معاملة تخليص جمركي بموجب معاملة رقم <a class="fw-bold text-decoration-none" href="{{ route('transactions.details', $transaction) }}">{{ $transaction->code }}</a> بواسطة <strong>{{ $transaction->made_by->name }}</strong>
                                </div>
                                <small class="bg-light p-3 rounded-3 mt-3 d-flex gap-5">
                                    <div>
                                        <i class="fas fa-file text-muted me-1"></i>
                                        <span>رقم البوليصة: <strong>{{ $transaction->policy_number }}</strong></span>
                                    </div>
                                    <div>
                                        <i class="fas fa-file-alt text-muted me-1"></i>
                                        <span>البيان الجمركي: {{ $transaction->customs_declaration ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <i class="fas fa-calendar text-muted me-1"></i>
                                        <span>تاريخ البيان الجمركي: {{ $transaction->customs_declaration_date ?? 'N/A' }}</span>
                                    </div>
                                </small>
                            </div>
                        </div>
                    @endif

                    @if($clearanceInvoice)
                        <div class="timeline-state completed invoice-state">
                            <div class="state-connector"></div>
                            <div class="relative z-3">
                                <div class="d-flex align-items-center justify-content-center relative rounded-circle text-white fs-5 bg-primary" style="width: 48px; height: 48px;">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </div>
                                <div class="state-pulse"></div>
                            </div>
                            <div class="flex-grow-1 p-3 rounded-3 shadow-sm border-start border-4 border-primary">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5>إنشاء فاتورة تخليص جمركي</h5>
                                    <small class="d-flex align-items-center text-secondary">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        {{ \Carbon\Carbon::parse($clearanceInvoice->created_at)->format('d M Y') }}
                                        <span class="rounded-5 px-2 ms-1" style="background-color: #e9ecef;">
                                            {{ \Carbon\Carbon::parse($clearanceInvoice->created_at)->format('h:i A') }}
                                        </span>
                                    </small>
                                </div>
                                <p class="text-muted">
                                    تم فوترة الحاوية بموجب فاتورة رقم <a href="{{ route('invoices.clearance.details', $clearanceInvoice) }}" class="text-decoration-none fw-bold">{{ $clearanceInvoice->code }}</a> بواسطة <strong>{{ $storageInvoice->made_by->name ?? 'N/A' }}</strong>
                                </p>
                            </div>
                        </div>
                    @endif

                    @if($storagePolicy)
                        <div class="timeline-state">
                            <div class="state-connector"></div>
                            <div class="relative z-3">
                                <div class="d-flex align-items-center justify-content-center relative rounded-circle text-white fs-5 bg-primary" style="width: 48px; height: 48px;">
                                    <i class="fas fa-warehouse"></i>
                                </div>
                                <div class="state-pulse"></div>
                            </div>
                            <div class="flex-grow-1 p-3 rounded-3 shadow-sm border-start border-4 border-primary">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5>إتفاقية تخزين</h5>
                                    <small class="d-flex align-items-center text-secondary">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        {{ \Carbon\Carbon::parse($storagePolicy->created_at)->format('d M Y') }}
                                        <span class="rounded-5 px-2 ms-1" style="background-color: #e9ecef;">
                                            {{ \Carbon\Carbon::parse($storagePolicy->created_at)->format('h:i A') }}
                                        </span>
                                    </small>
                                </div>
                                <div class="text-muted">
                                    تم تخزين الحاوية بموجب إتفاقية رقم <a class="fw-bold text-decoration-none" href="{{ route('policies.storage.details', $storagePolicy) }}">{{ $storagePolicy->code }}</a> بواسطة <strong>{{ $storagePolicy->made_by->name }}</strong> و موقعها في الساحه <i class="fas fa-map-marker-alt text-muted me-1 ms-1"></i> <strong>{{ $container->location }}</strong> 
                                </div>
                                <small class="bg-light p-3 rounded-3 mt-3 d-flex gap-5">
                                    <div>
                                        <i class="fas fa-user text-muted me-1"></i>
                                        <span>السائق: <strong>{{ $storagePolicy->driver_name }}</strong></span>
                                    </div>
                                    <div>
                                        <i class="fas fa-id-card text-muted me-1"></i>
                                        <span>هوية السائق: {{ $storagePolicy->driver_NID }}</span>
                                    </div>
                                    <div>
                                        <i class="fas fa-car text-muted me-1"></i>
                                        <span>{{ $storagePolicy->driver_car }} - {{ $storagePolicy->car_code }}</span>
                                    </div>
                                </small>
                            </div>
                        </div>
                    @endif

                    @if($receivePolicy)
                        <div class="timeline-state">
                            <div class="state-connector"></div>
                            <div class="relative z-3">
                                <div class="d-flex align-items-center justify-content-center relative rounded-circle text-white fs-5 bg-primary" style="width: 48px; height: 48px;">
                                    <i class="fas fa-truck-fast"></i>
                                </div>
                                <div class="state-pulse"></div>
                            </div>
                            <div class="flex-grow-1 p-3 rounded-3 shadow-sm border-start border-4 border-primary">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5>إتفاقية تسليم</h5>
                                    <small class="d-flex align-items-center text-secondary">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        {{ \Carbon\Carbon::parse($receivePolicy->created_at)->format('d M Y') }}
                                        <span class="rounded-5 px-2 ms-1" style="background-color: #e9ecef;">
                                            {{ \Carbon\Carbon::parse($receivePolicy->created_at)->format('h:i A') }}
                                        </span>
                                    </small>
                                </div>
                                <div class="text-muted">
                                    تم تسليم الحاوية للعميل <strong>{{ $receivePolicy->customer->name }}</strong> بموجب إتفاقية رقم <a class="fw-bold text-decoration-none" href="{{ route('policies.receive.details', $receivePolicy) }}">{{ $receivePolicy->code }}</a> بواسطة <strong>{{ $receivePolicy->made_by->name }}</strong>
                                </div>
                                <small class="bg-light p-3 rounded-3 mt-3 d-flex gap-5">
                                    <div>
                                        <i class="fas fa-user text-muted me-1"></i>
                                        <span>السائق: <strong>{{ $receivePolicy->driver_name }}</strong></span>
                                    </div>
                                    <div>
                                        <i class="fas fa-id-card text-muted me-1"></i>
                                        <span>هوية السائق: {{ $receivePolicy->driver_NID }}</span>
                                    </div>
                                    <div>
                                        <i class="fas fa-car text-muted me-1"></i>
                                        <span>{{ $receivePolicy->driver_car }} - {{ $receivePolicy->car_code }}</span>
                                    </div>
                                </small>
                            </div>
                        </div>
                    @endif

                    @if($storageInvoice)
                        <div class="timeline-state completed invoice-state">
                            <div class="state-connector"></div>
                            <div class="relative z-3">
                                <div class="d-flex align-items-center justify-content-center relative rounded-circle text-white fs-5 bg-primary" style="width: 48px; height: 48px;">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                </div>
                                <div class="state-pulse"></div>
                            </div>
                            <div class="flex-grow-1 p-3 rounded-3 shadow-sm border-start border-4 border-primary">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5>إنشاء فاتورة تخزين</h5>
                                    <small class="d-flex align-items-center text-secondary">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        {{ \Carbon\Carbon::parse($storageInvoice->created_at)->format('d M Y') }}
                                        <span class="rounded-5 px-2 ms-1" style="background-color: #e9ecef;">
                                            {{ \Carbon\Carbon::parse($storageInvoice->created_at)->format('h:i A') }}
                                        </span>
                                    </small>
                                </div>
                                <p class="text-muted">
                                    تم فوترة الحاوية بموجب فاتورة رقم <a href="{{ route('invoices.details', $storageInvoice) }}" class="text-decoration-none fw-bold">{{ $storageInvoice->code }}</a> بواسطة <strong>{{ $storageInvoice->made_by->name }}</strong>
                                </p>
                            </div>
                        </div>
                    @endif
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
        top: 30px;
        width: 2px;
        height: calc(100% + 2rem);
        background-color: #007bff;
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

    .state-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
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
</style>
@endpush

@endsection