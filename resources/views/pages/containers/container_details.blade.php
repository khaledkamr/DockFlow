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
            <div class="col-12 d-flex flex-row align-items-center justify-content-between gap-3">
                <h1 class="h3 h-md-2 mb-0">
                    تفاصيل الحاوية {{ $container->code }}
                </h1>
                @if (auth()->user()->roles->contains('name', 'Admin'))
                    <div class="mt-2">
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                            data-bs-target="#deleteContainerModal">
                            <i class="fas fa-trash me-1"></i>
                            حذف الحاوية
                        </button>
                    </div>
                @endif

                <!-- Delete Confirmation Modal -->
                <div class="modal fade" id="deleteContainerModal" tabindex="-1" aria-labelledby="deleteContainerModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title" id="deleteContainerModalLabel">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    تأكيد حذف الحاوية
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="text-center mb-3">
                                    <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                                </div>
                                <p class="text-center mb-3">
                                    هل أنت متأكد من أنك تريد حذف الحاوية <strong>{{ $container->code }}</strong>؟
                                </p>
                                <div class="alert alert-warning">
                                    <i class="fas fa-info-circle me-2"></i>
                                    هذا الإجراء لا يمكن التراجع عنه وسيتم حذف جميع البيانات المرتبطة بهذه الحاوية نهائياً.
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i>
                                    إلغاء
                                </button>
                                <form action="{{ route('containers.delete', $container) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash me-1"></i>
                                        حذف الحاوية
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services and Notes -->
        <div class="row g-3 mb-3 mb-md-4">
            <!-- Details -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0 fs-6 fs-md-5">
                            <i class="fas fa-user-tie me-2"></i>
                            معلومات الحاوية
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <strong>العميل:</strong>
                                <a href="{{ route('users.customer.profile', $container->customer) }}"
                                    class="text-decoration-none fw-bold">
                                    {{ $container->customer->name ?? 'N/A' }}
                                </a>
                            </div>
                            <div class="col-6">
                                <strong>نوع الحاوية:</strong> {{ $container->containerType->name ?? 'N/A' }}
                            </div>
                            <div class="col-6">
                                <strong>تاريخ الدخول:</strong>
                                {{ \Carbon\Carbon::parse($container->date)->format('Y/m/d') }}
                            </div>
                            <div class="col-6">
                                <strong>تاريخ الخروج:</strong>
                                {{ $container->exit_date ? \Carbon\Carbon::parse($container->exit_date)->format('Y/m/d') : 'لم يتم الخروج بعد' }}
                            </div>
                            <div class="col-6">
                                <strong>الرقم المرجعي:</strong> {{ $container->reference_number ?? 'N/A' }}
                            </div>
                            <div class="col-6">
                                <strong>الحالة الحالية:</strong>
                                @if ($container->status == 'في الساحة')
                                    <div class="badge status-available">{{ $container->status }}</div>
                                @elseif($container->status == 'تم التسليم')
                                    <div class="badge status-delivered">{{ $container->status }} <i
                                            class="fa-solid fa-check"></i></div>
                                @elseif($container->status == 'متأخر')
                                    <div class="badge status-danger">{{ $container->status }}</div>
                                @elseif($container->status == 'خدمات')
                                    <div class="badge status-waiting">{{ $container->status }}</div>
                                @elseif($container->status == 'في الميناء')
                                    <div class="badge status-info">{{ $container->status }}</div>
                                @elseif($container->status == 'قيد النقل')
                                    <div class="badge status-purple">{{ $container->status }}</div>
                                @endif
                            </div>
                            <div class="col-6">
                                <strong>الموقع الحالي:</strong> {{ $container->location ?? 'N/A' }}
                            </div>
                            <div class="col-6">
                                <strong>الملاحظات:</strong>
                                {{ $container->notes ?? 'لا توجد ملاحظات' }}
                            </div>
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
                <div class="d-flex flex-row align-items-start align-items-sm-center justify-content-between gap-2">
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

                        @foreach ($timeline as $operation)
                            <div class="timeline-state">
                                <div class="state-connector"></div>
                                <div class="position-relative" style="z-index: 3;">
                                    <div class="timeline-icon d-flex align-items-center justify-content-center position-relative rounded-circle text-white fs-5 bg-primary"
                                        style="width: 48px; height: 48px;">
                                        <i class="fas {{ $operation['icon'] }}"></i>
                                    </div>
                                    <div class="state-pulse"></div>
                                </div>
                                <div class="flex-grow-1 p-2 p-md-3 rounded-3 shadow-sm border-start border-4 border-primary">
                                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-2 mb-2">
                                        <h5 class="mb-0 fs-6">{{ $operation['title'] }}</h5>
                                        <small class="d-flex flex-wrap align-items-center text-secondary">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            <span>{{ \Carbon\Carbon::parse($operation['date'])->format('Y/m/d') }}</span>
                                            <span class="rounded-5 px-2 ms-1" style="background-color: #e9ecef;">
                                                {{ \Carbon\Carbon::parse($operation['date'])->format('h:i A') }}
                                            </span>
                                        </small>
                                    </div>
                                    <p class="text-muted mb-0 small mb-2">
                                        {!! $operation['description'] !!}
                                    </p>
                                    @if ($operation['extra_info'])
                                        <div class="bg-light p-2 p-md-3 rounded-3 d-flex flex-column flex-md-row gap-3">
                                            @foreach ($operation['extra_info'] as $info)
                                                <div class="small">
                                                    <i class="fas {{ $info['icon'] }} text-muted me-1"></i>
                                                    @if ($info['label'])
                                                        <span>{{ $info['label'] }}:
                                                            <strong>{{ $info['value'] }}</strong>
                                                        </span>
                                                    @else
                                                        <span>{{ $info['value'] }}</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
