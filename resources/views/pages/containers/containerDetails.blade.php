@extends('layouts.app')

@section('title', 'تفاصيل الحاوية - ' . $container->code)

@section('content')
    <style>
        /* Minimal custom styles */
        .timeline-state {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
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

        .info-item {
            margin-bottom: 15px;
            padding: 5px 0;
        }

        .info-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        @media (max-width: 768px) {
            .timeline-state {
                gap: 0.75rem;
            }

            .state-connector {
                right: 18px;
            }

            .timeline-icon {
                width: 36px !important;
                height: 36px !important;
                font-size: 1rem !important;
            }

            .state-pulse {
                width: 36px;
                height: 36px;
            }

            .info-item {
                font-size: 14px;
            }
        }

        @media (max-width: 576px) {
            .timeline-state {
                gap: 0.5rem;
            }
        }
    </style>

    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mb-3 mb-md-4">
            <div class="col-12">
                <h1 class="h3 h-md-2 mb-0">
                    تفاصيل الحاوية {{ $container->code }}
                </h1>
            </div>
        </div>

        <!-- Services and Notes -->
        <div class="row g-3 mb-3 mb-md-4">
            <!-- Customer -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0 fs-6 fs-md-5">
                            <i class="fas fa-user-tie me-2"></i>
                            معلومات العميل
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <strong class="text-muted">اسم العميل:</strong>
                            <a href="{{ route('users.customer.profile', $container->customer) }}"
                                class="text-decoration-none fw-bold">
                                {{ $container->customer->name }}
                            </a>
                        </div>
                        <div class="info-item">
                            <strong class="text-muted">رقم الحساب:</strong>
                            <span class="text-dark">{{ $container->customer->account->code }}</span>
                        </div>
                        <div class="info-item">
                            <strong class="text-muted">الرقم الضريبي:</strong>
                            <span class="text-dark">{{ $container->customer->vatNumber }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0 fs-6 fs-md-5">
                            <i class="fas fa-tools me-2"></i>
                            الخدمات المضافة ({{ count($container->services) }})
                        </h5>
                    </div>
                    <div
                        class="card-body {{ count($container->services) > 0 ? '' : 'd-flex justify-content-center align-items-center' }}">
                        @if (count($container->services) > 0)
                            <div class="list-group list-group-flush">
                                @foreach ($container->services as $service)
                                    <div class="list-group-item px-0 border-bottom">
                                        <div
                                            class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-2">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fs-6">{{ $service->description }}</h6>
                                                @if ($service->pivot->notes)
                                                    <p class="mb-0 text-muted small">
                                                        <i class="fas fa-sticky-note me-1"></i>
                                                        {{ $service->pivot->notes }}
                                                    </p>
                                                @endif
                                            </div>
                                            <div>
                                                <span class="badge bg-primary">
                                                    {{ number_format($service->pivot->price, 2) }} ريال
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-3 pt-2 border-top">
                                <div class="d-flex justify-content-between fs-5 fs-md-4">
                                    <strong>إجمالي الخدمات:</strong>
                                    <strong class="text-primary">
                                        {{ number_format($container->services->sum('pivot.price'), 2) }} ريال
                                    </strong>
                                </div>
                            </div>
                        @else
                            <div class="text-center text-secondary py-4">
                                <i class="fas fa-tools fa-2x mb-2"></i>
                                <p class="fw-bold mb-0">لا توجد خدمات مضافة لهذه الحاوية</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline Section -->
        <div class="card shadow-sm border-0 mb-3 mb-md-4">
            <div class="card-header bg-dark text-white border-0">
                <div
                    class="d-flex flex-row align-items-start align-items-sm-center justify-content-between gap-2">
                    <h5 class="card-title mb-0 fs-6 fs-md-5 d-flex align-items-center">
                        <i class="fas fa-route me-2"></i>
                        <span>الخط الزمني للحاوية</span>
                    </h5>
                    <div class="d-flex align-items-center gap-2">
                        <h4 class="fw-bold m-0">
                            {{ (int) \Carbon\Carbon::parse($container->date)->diffInDays($container->exit_date ?? now()) }}
                        </h4>
                        <small class="text-light">يوم</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="position-relative px-2 px-md-3 py-3 py-md-4">
                    <div class="d-flex flex-column gap-3 position-relative">

                        <!-- Container Creation -->
                        <div class="timeline-state">
                            <div class="state-connector"></div>
                            <div class="position-relative" style="z-index: 3;">
                                <div class="timeline-icon d-flex align-items-center justify-content-center position-relative rounded-circle text-white fs-5 bg-primary"
                                    style="width: 48px; height: 48px;">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <div class="state-pulse"></div>
                            </div>
                            <div class="flex-grow-1 p-2 p-md-3 rounded-3 shadow-sm border-start border-4 border-primary">
                                <div
                                    class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-2 mb-2">
                                    <h5 class="mb-0 fs-6">إنشاء الحاوية</h5>
                                    <small class="d-flex flex-wrap align-items-center text-secondary">
                                        <i class="far fa-calendar-alt me-1"></i>
                                        <span>{{ \Carbon\Carbon::parse($container->created_at)->format('Y/m/d') }}</span>
                                        <span class="rounded-5 px-2 ms-1" style="background-color: #e9ecef;">
                                            {{ \Carbon\Carbon::parse($container->created_at)->format('h:i A') }}
                                        </span>
                                    </small>
                                </div>
                                <p class="text-muted mb-0 small mb-2">
                                    تم إنشاء الحاوية في النظام برقم <strong>{{ $container->code }}</strong> من نوع
                                    <strong>{{ $container->containerType->name ?? 'N/A' }}</strong> في النظام بواسطة
                                    <strong>{{ $container->made_by->name }}</strong>
                                </p>
                                @if($container->notes)
                                    <div class="bg-light p-2 p-md-3 rounded-3 d-flex flex-column flex-md-row gap-3">
                                        <div class="small">
                                            <i class="fas fa-sticky-note text-muted me-1"></i>
                                            <span>الملاحظات: <strong>{{ $container->notes }}</strong></span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if ($servicePolicy)
                            <div class="timeline-state">
                                <div class="state-connector"></div>
                                <div class="position-relative" style="z-index: 3;">
                                    <div class="timeline-icon d-flex align-items-center justify-content-center position-relative rounded-circle text-white fs-5 bg-primary"
                                        style="width: 48px; height: 48px;">
                                        <i class="fas fa-file-contract"></i>
                                    </div>
                                    <div class="state-pulse"></div>
                                </div>
                                <div
                                    class="flex-grow-1 p-2 p-md-3 rounded-3 shadow-sm border-start border-4 border-primary">
                                    <div
                                        class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-2 mb-2">
                                        <h5 class="mb-0 fs-6">بوليصة خدمات</h5>
                                        <small class="d-flex flex-wrap align-items-center text-secondary">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            <span>{{ \Carbon\Carbon::parse($servicePolicy->created_at)->format('d M Y') }}</span>
                                            <span class="rounded-5 px-2 ms-1" style="background-color: #e9ecef;">
                                                {{ \Carbon\Carbon::parse($servicePolicy->created_at)->format('h:i A') }}
                                            </span>
                                        </small>
                                    </div>
                                    <p class="text-muted mb-0 small">
                                        تم إضافة الحاوية إلى بوليصة خدمات رقم <a
                                            href="{{ route('policies.services.details', $servicePolicy) }}"
                                            class="text-decoration-none fw-bold">{{ $servicePolicy->code }}</a> بواسطة
                                        <strong>{{ $servicePolicy->made_by->name }}</strong>
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if ($serviceInvoice)
                            <div class="timeline-state">
                                <div class="state-connector"></div>
                                <div class="position-relative" style="z-index: 3;">
                                    <div class="timeline-icon d-flex align-items-center justify-content-center position-relative rounded-circle text-white fs-5 bg-primary"
                                        style="width: 48px; height: 48px;">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </div>
                                    <div class="state-pulse"></div>
                                </div>
                                <div
                                    class="flex-grow-1 p-2 p-md-3 rounded-3 shadow-sm border-start border-4 border-primary">
                                    <div
                                        class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-2 mb-2">
                                        <h5 class="mb-0 fs-6">فاتورة خدمات</h5>
                                        <small class="d-flex flex-wrap align-items-center text-secondary">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            <span>{{ \Carbon\Carbon::parse($serviceInvoice->created_at)->format('d M Y') }}</span>
                                            <span class="rounded-5 px-2 ms-1" style="background-color: #e9ecef;">
                                                {{ \Carbon\Carbon::parse($serviceInvoice->created_at)->format('h:i A') }}
                                            </span>
                                        </small>
                                    </div>
                                    <p class="text-muted mb-0 small">
                                        تم فوترة الحاوية بموجب فاتورة رقم <a
                                            href="{{ route('invoices.services.details', $serviceInvoice) }}"
                                            class="text-decoration-none fw-bold">{{ $serviceInvoice->code }}</a> بواسطة
                                        <strong>{{ $serviceInvoice->made_by->name }}</strong>
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if ($transaction)
                            <div class="timeline-state">
                                <div class="state-connector"></div>
                                <div class="position-relative" style="z-index: 3;">
                                    <div class="timeline-icon d-flex align-items-center justify-content-center position-relative rounded-circle text-white fs-5 bg-primary"
                                        style="width: 48px; height: 48px;">
                                        <i class="fas fa-file-contract"></i>
                                    </div>
                                    <div class="state-pulse"></div>
                                </div>
                                <div
                                    class="flex-grow-1 p-2 p-md-3 rounded-3 shadow-sm border-start border-4 border-primary">
                                    <div
                                        class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-2 mb-2">
                                        <h5 class="mb-0 fs-6">معاملة تخليص</h5>
                                        <small class="d-flex flex-wrap align-items-center text-secondary">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            <span>{{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y') }}</span>
                                            <span class="rounded-5 px-2 ms-1" style="background-color: #e9ecef;">
                                                {{ \Carbon\Carbon::parse($transaction->created_at)->format('h:i A') }}
                                            </span>
                                        </small>
                                    </div>
                                    <div class="text-muted small mb-2">
                                        تم إنشاء معاملة تخليص جمركي بموجب معاملة رقم <a
                                            class="fw-bold text-decoration-none"
                                            href="{{ route('transactions.details', $transaction) }}">{{ $transaction->code }}</a>
                                        بواسطة <strong>{{ $transaction->made_by->name }}</strong>
                                    </div>
                                    <div class="bg-light p-2 p-md-3 rounded-3 d-flex flex-column flex-md-row gap-3">
                                        <div class="small">
                                            <i class="fas fa-file text-muted me-1"></i>
                                            <span>رقم البوليصة: <strong>{{ $transaction->policy_number }}</strong></span>
                                        </div>
                                        <div class="small">
                                            <i class="fas fa-file-alt text-muted me-1"></i>
                                            <span>البيان الجمركي: {{ $transaction->customs_declaration ?? 'N/A' }}</span>
                                        </div>
                                        <div class="small">
                                            <i class="fas fa-calendar text-muted me-1"></i>
                                            <span>تاريخ البيان الجمركي:
                                                {{ $transaction->customs_declaration_date ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($clearanceInvoice)
                            <div class="timeline-state">
                                <div class="state-connector"></div>
                                <div class="position-relative" style="z-index: 3;">
                                    <div class="timeline-icon d-flex align-items-center justify-content-center position-relative rounded-circle text-white fs-5 bg-primary"
                                        style="width: 48px; height: 48px;">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </div>
                                    <div class="state-pulse"></div>
                                </div>
                                <div
                                    class="flex-grow-1 p-2 p-md-3 rounded-3 shadow-sm border-start border-4 border-primary">
                                    <div
                                        class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-2 mb-2">
                                        <h5 class="mb-0 fs-6">إنشاء فاتورة تخليص جمركي</h5>
                                        <small class="d-flex flex-wrap align-items-center text-secondary">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            <span>{{ \Carbon\Carbon::parse($clearanceInvoice->created_at)->format('d M Y') }}</span>
                                            <span class="rounded-5 px-2 ms-1" style="background-color: #e9ecef;">
                                                {{ \Carbon\Carbon::parse($clearanceInvoice->created_at)->format('h:i A') }}
                                            </span>
                                        </small>
                                    </div>
                                    <p class="text-muted mb-0 small">
                                        تم فوترة الحاوية بموجب فاتورة رقم <a
                                            href="{{ route('invoices.clearance.details', $clearanceInvoice) }}"
                                            class="text-decoration-none fw-bold">{{ $clearanceInvoice->code }}</a> بواسطة
                                        <strong>{{ $clearanceInvoice->made_by->name ?? 'N/A' }}</strong>
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if ($storagePolicy)
                            <div class="timeline-state">
                                <div class="state-connector"></div>
                                <div class="position-relative" style="z-index: 3;">
                                    <div class="timeline-icon d-flex align-items-center justify-content-center position-relative rounded-circle text-white fs-5 bg-primary"
                                        style="width: 48px; height: 48px;">
                                        <i class="fas fa-warehouse"></i>
                                    </div>
                                    <div class="state-pulse"></div>
                                </div>
                                <div
                                    class="flex-grow-1 p-2 p-md-3 rounded-3 shadow-sm border-start border-4 border-primary">
                                    <div
                                        class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-2 mb-2">
                                        <h5 class="mb-0 fs-6">بوليصة تخزين</h5>
                                        <small class="d-flex flex-wrap align-items-center text-secondary">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            <span>{{ \Carbon\Carbon::parse($storagePolicy->created_at)->format('d M Y') }}</span>
                                            <span class="rounded-5 px-2 ms-1" style="background-color: #e9ecef;">
                                                {{ \Carbon\Carbon::parse($storagePolicy->created_at)->format('h:i A') }}
                                            </span>
                                        </small>
                                    </div>
                                    <div class="text-muted small mb-2">
                                        تم تخزين الحاوية بموجب بوليصة رقم <a class="fw-bold text-decoration-none"
                                            href="{{ route('policies.storage.details', $storagePolicy) }}">{{ $storagePolicy->code }}</a>
                                        بواسطة <strong>{{ $storagePolicy->made_by->name }}</strong> و موقعها في الساحه <i
                                            class="fas fa-map-marker-alt text-muted me-1 ms-1"></i>
                                        <strong>{{ $container->location }}</strong>
                                    </div>
                                    <div class="bg-light p-2 p-md-3 rounded-3 d-flex flex-column flex-md-row gap-3">
                                        <div class="small">
                                            <i class="fas fa-user text-muted me-1"></i>
                                            <span>السائق: <strong>{{ $storagePolicy->driver_name }}</strong></span>
                                        </div>
                                        <div class="small">
                                            <i class="fas fa-id-card text-muted me-1"></i>
                                            <span>هوية السائق: {{ $storagePolicy->driver_NID }}</span>
                                        </div>
                                        <div class="small">
                                            <i class="fas fa-car text-muted me-1"></i>
                                            <span>{{ $storagePolicy->driver_car }} - {{ $storagePolicy->car_code }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($receivePolicy)
                            <div class="timeline-state">
                                <div class="state-connector"></div>
                                <div class="position-relative" style="z-index: 3;">
                                    <div class="timeline-icon d-flex align-items-center justify-content-center position-relative rounded-circle text-white fs-5 bg-primary"
                                        style="width: 48px; height: 48px;">
                                        <i class="fas fa-truck-fast"></i>
                                    </div>
                                    <div class="state-pulse"></div>
                                </div>
                                <div
                                    class="flex-grow-1 p-2 p-md-3 rounded-3 shadow-sm border-start border-4 border-primary">
                                    <div
                                        class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-2 mb-2">
                                        <h5 class="mb-0 fs-6">بوليصة تسليم</h5>
                                        <small class="d-flex flex-wrap align-items-center text-secondary">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            <span>{{ \Carbon\Carbon::parse($receivePolicy->created_at)->format('d M Y') }}</span>
                                            <span class="rounded-5 px-2 ms-1" style="background-color: #e9ecef;">
                                                {{ \Carbon\Carbon::parse($receivePolicy->created_at)->format('h:i A') }}
                                            </span>
                                        </small>
                                    </div>
                                    <div class="text-muted small mb-2">
                                        تم تسليم الحاوية للعميل <strong>{{ $receivePolicy->customer->name }}</strong> بموجب
                                        بوليصة رقم <a class="fw-bold text-decoration-none"
                                            href="{{ route('policies.receive.details', $receivePolicy) }}">{{ $receivePolicy->code }}</a>
                                        بواسطة <strong>{{ $receivePolicy->made_by->name }}</strong>
                                    </div>
                                    <div class="bg-light p-2 p-md-3 rounded-3 d-flex flex-column flex-md-row gap-3">
                                        <div class="small">
                                            <i class="fas fa-user text-muted me-1"></i>
                                            <span>السائق: <strong>{{ $receivePolicy->driver_name }}</strong></span>
                                        </div>
                                        <div class="small">
                                            <i class="fas fa-id-card text-muted me-1"></i>
                                            <span>هوية السائق: {{ $receivePolicy->driver_NID }}</span>
                                        </div>
                                        <div class="small">
                                            <i class="fas fa-car text-muted me-1"></i>
                                            <span>{{ $receivePolicy->driver_car }} - {{ $receivePolicy->car_code }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($storageInvoice)
                            <div class="timeline-state">
                                <div class="state-connector"></div>
                                <div class="position-relative" style="z-index: 3;">
                                    <div class="timeline-icon d-flex align-items-center justify-content-center position-relative rounded-circle text-white fs-5 bg-primary"
                                        style="width: 48px; height: 48px;">
                                        <i class="fas fa-file-invoice-dollar"></i>
                                    </div>
                                    <div class="state-pulse"></div>
                                </div>
                                <div
                                    class="flex-grow-1 p-2 p-md-3 rounded-3 shadow-sm border-start border-4 border-primary">
                                    <div
                                        class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-2 mb-2">
                                        <h5 class="mb-0 fs-6">إنشاء فاتورة تخزين</h5>
                                        <small class="d-flex flex-wrap align-items-center text-secondary">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            <span>{{ $storageInvoice->created_at->format('d M Y') }}</span>
                                            <span class="rounded-5 px-2 ms-1" style="background-color: #e9ecef;">
                                                {{ $storageInvoice->created_at->timezone(auth()->user()->timezone)->format('h:i A') }}
                                            </span>
                                        </small>
                                    </div>
                                    <p class="text-muted mb-0 small">
                                        تم فوترة الحاوية بموجب فاتورة رقم <a
                                            href="{{ route('invoices.details', $storageInvoice) }}"
                                            class="text-decoration-none fw-bold">{{ $storageInvoice->code }}</a> بواسطة
                                        <strong>{{ $storageInvoice->made_by->name }}</strong>
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
