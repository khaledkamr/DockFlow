@extends('layouts.admin')

@section('title', 'تفاصيل الإتفاقية')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h2 class="h3 text-primary mb-1">
                    <i class="fas fa-clipboard-list me-2"></i>
                    تفاصيل إتفاقية التسليم #{{ $policy->id }}
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('contracts.details', $policy->contract_id) }}" class="text-decoration-none">العقد #{{ $policy->contract_id }}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">الإتفاقية #{{ $policy->id }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <form action="{{ route('print', 'exit_permission') }}" method="POST" target="_blank">
                    @csrf
                    @foreach($policy->containers as $container)
                        <input type="hidden" name="containers[]" value="{{ $container->id }}">
                    @endforeach
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-print me-1"></i>
                        طباعة اذن خروج
                    </button>
                </form>
                <button class="btn btn-primary me-2">
                    <i class="fas fa-print me-1"></i>
                    طباعة الإتفاقية
                </button>
                <button class="btn btn-secondary">
                    <i class="fas fa-download me-1"></i>
                    تحميل PDF
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 d-flex flex-column gap-3 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            معلومات الإتفاقية
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-12">
                                <div>
                                    
                                    <div class="row">
                                        <div class="col">
                                            <label class="form-label text-muted small">تاريخ الإتفاقية</label>
                                            <div class="fw-bold fs-5">{{ $policy->created_at->format('Y/m/d') }}</div>
                                        </div>
                                        <div class="col">
                                            <label class="form-label text-muted small">تم الإنشاء بواسطة</label>
                                            <div class="fw-bold">{{ $policy->made_by->name }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user me-2"></i>
                            معلومات السائق والمركبة
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-12">
                                <div>
                                    <h6 class="text-muted mb-2">
                                        <i class="fas fa-truck me-2"></i>
                                        بيانات السائق والمركبة
                                    </h6>
                                    <div class="row">
                                        <div class="col">
                                            <label class="form-label text-muted small">اسم السائق</label>
                                            <div class="fw-bold fs-5">{{ $policy->driver_name }}</div>
                                        </div>
                                        <div class="col">
                                            <label class="form-label text-muted small">الرقم القومي</label>
                                            <div class="fw-bold">{{ $policy->driver_NID }}</div>
                                        </div>
                                        <div class="col">
                                            <label class="form-label text-muted small">نوع المركبة</label>
                                            <div class="fw-bold fs-5">{{ $policy->driver_car }}</div>
                                        </div>
                                        <div class="col">
                                            <label class="form-label text-muted small">رقم اللوحة</label>
                                            <div class="fw-bold">{{ $policy->car_code }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-money-bill-wave me-2"></i>
                            اطراف الإتفاقية
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-building me-2"></i>
                                الطرف الأول
                            </h6>
                            <div class="row border-bottom pb-3">
                                <div class="col">
                                    <label class="form-label text-muted small">الاسم</label>
                                    <div class="fw-bold">{{ $policy->contract->company->name }}</div>
                                </div>
                                <div class="col">
                                    <label class="form-label text-muted small">السجل التجاري</label>
                                    <div class="fw-bold">{{ $policy->contract->company->CR }}</div>
                                </div>
                                <div class="col">
                                    <label class="form-label text-muted small">الرقم الضريبي</label>
                                    <div class="fw-bold">{{ $policy->contract->company->TIN }}</div>
                                </div>
                            </div>
                            
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-user-tie me-2"></i>
                                الطرف الثاني
                            </h6>
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label text-muted small">الاسم</label>
                                    <div class="fw-bold">{{ $policy->contract->customer->name }}</div>
                                </div>
                                <div class="col">
                                    <label class="form-label text-muted small">السجل التجاري</label>
                                    <div class="fw-bold">{{ $policy->contract->customer->CR }}</div>
                                </div>
                                <div class="col">
                                    <label class="form-label text-muted small">الرقم الضريبي</label>
                                    <div class="fw-bold">{{ $policy->contract->customer->TIN }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-dark text-white">
                <div class="d-flex justify-content-between align-items-center text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-boxes me-2"></i>
                        الحاويات المشمولة في الإتفاقية
                    </h5>
                    <span class="badge bg-light text-dark">{{ count($policy->containers) }} حاوية</span>
                </div>
            </div>
            <div class="card-body p-0">
                @if(count($policy->containers) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-primary">
                                <tr>
                                    <th class="border-0 text-center fw-bold">#</th>
                                    <th class="border-0 text-center fw-bold">كود الحاوية</th>
                                    <th class="border-0 text-center fw-bold">نوع الحاوية</th>
                                    <th class="border-0 text-center fw-bold">تاريخ الدخول</th>
                                    <th class="border-0 text-center fw-bold">تاريخ الخروج</th>
                                    <th class="border-0 text-center fw-bold">تم الإستلام بواسطة</th>
                                    <th class="border-0 text-center fw-bold">تم التسليم بواسطة</th>
                                    <th class="border-0 text-center fw-bold">ملاحظات</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach($policy->containers as $index => $container)
                                    <tr>
                                        <td>{{ $container->id }}</td>
                                        <td>
                                            <a href="{{ route('container.details', $container->id) }}" class="fw-bold text-decoration-none">
                                                {{ $container->code }}
                                            </a>
                                        </td>
                                        <td class="fw-bold">{{ $container->containerType->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($container->date)->format('Y/m/d') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($container->exit_date)->format('Y/m/d') }}</td>
                                        <td>{{ $container->received_by }}</td>
                                        <td>{{ $container->delivered_by }}</td>
                                        <td>{{ $container->notes ?? '---' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد حاويات مرتبطة بهذه الإتفاقية</h5>
                    </div>
                @endif
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

@if (session('errors'))
    @push('scripts')
        <script>
            showToast("حدث خطأ في العملية الرجاء مراجعة البيانات", "danger");
        </script>
    @endpush
@endif

<style>
    .bg-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0,123,255,0.05);
    }

    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    @media (max-width: 768px) {
        .d-flex.justify-content-between {
            flex-direction: column;
            gap: 1rem;
        }
        
        .btn-group {
            justify-content: center;
        }
    }
    .table .status-waiting {
        background-color: #ffe590;
        color: #745700;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    .table .status-available {
        background-color: #d4d7ed;
        color: #151657;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    .table .status-delivered {
        background-color: #c1eccb;
        color: #155724;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    .table .status-danger {
        background-color: #f8d7da;
        color: #721c24;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
</style>
@endsection