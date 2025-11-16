@extends('layouts.app')

@section('title', 'تفاصيل الإتفاقية #' . $policy->id)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <h2 class="h3 text-primary mb-1">
                    <i class="fas fa-clipboard-list me-2"></i>
                    تفاصيل إتفاقية التخزين #{{ $policy->code }}
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        {{-- <li class="breadcrumb-item">
                            <a href="{{ route('contracts.details', $policy->customer->contract) }}" class="text-decoration-none">العقد #{{ $policy->contract_id }}</a>
                        </li> --}}
                        <li class="breadcrumb-item active" aria-current="page">الإتفاقية #{{ $policy->id }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <form action="{{ route('print', 'entry_permission') }}" method="POST" target="_blank">
                    @csrf
                    @foreach($policy->containers as $container)
                        <input type="hidden" name="containers[]" value="{{ $container->id }}">
                    @endforeach
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-print me-1"></i>
                        طباعة اذن دخول
                    </button>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 d-flex flex-column gap-3 mb-4">
                <!-- معلومات الإتفاقية -->
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            معلومات الإتفاقية
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col">
                                <label class="form-label text-muted small">تاريخ الإتفاقية</label>
                                <div class="fw-bold">{{ $policy->created_at->format('Y/m/d') }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">البيان الضريبي</label>
                                <div class="fw-bold">{{ $policy->tax_statement ?? 'N/A' }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">تم الإنشاء بواسطة</label>
                                <div class="fw-bold">{{ $policy->made_by->name }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- معلومات السائق والمركبة -->
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
                                    <div class="row">
                                        <div class="col">
                                            <label class="form-label text-muted small">اسم السائق</label>
                                            <div class="fw-bold">{{ $policy->driver_name }}</div>
                                        </div>
                                        <div class="col">
                                            <label class="form-label text-muted small">الرقم القومي</label>
                                            <div class="fw-bold">{{ $policy->driver_NID }}</div>
                                        </div>
                                        <div class="col">
                                            <label class="form-label text-muted small">نوع المركبة</label>
                                            <div class="fw-bold">{{ $policy->driver_car }}</div>
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

            <div class="col-lg-6 d-flex flex-column gap-3 mb-4">
                <!-- بيانات العميل -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-money-bill-wave me-2"></i>
                            بيانات العميل
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col">
                                <label class="form-label text-muted small">الاسم</label>
                                <div class="fw-bold">{{ $policy->customer->name ?? 'N/A' }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">السجل التجاري</label>
                                <div class="fw-bold">{{ $policy->customer->CR ?? 'N/A' }}</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">الرقم الضريبي</label>
                                <div class="fw-bold">{{ $policy->customer->vatNumber ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- المعلومات المالية -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-money-bill-wave me-2"></i>
                            المعلومات المالية
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col">
                                <label class="form-label text-muted small">سعر التخزين</label>
                                <div class="fw-bold">{{ $policy->storage_price }} <i data-lucide="saudi-riyal"></i></div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">مدة التخزين</label>
                                <div class="fw-bold">{{ $policy->storage_duration }} يوم</div>
                            </div>
                            <div class="col">
                                <label class="form-label text-muted small">غرامة التأخير (لليوم)</label>
                                <div class="fw-bold">{{ $policy->late_fee }} <i data-lucide="saudi-riyal"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-5">
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
                                <tr class="">
                                    <th class="text-center fw-bold">#</th>
                                    <th class="text-center fw-bold">كود الحاوية</th>
                                    <th class="text-center fw-bold">نوع الحاوية</th>
                                    <th class="text-center fw-bold">صاحب الحاوية</th>
                                    <th class="text-center fw-bold">الحالة</th>
                                    <th class="text-center fw-bold">الموقع</th>
                                    <th class="text-center fw-bold">تم الإستلام بواسطة</th>
                                    <th class="text-center fw-bold">تم التسليم بواسطة</th>
                                    <th class="text-center fw-bold">الإجرائات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($policy->containers as $index => $container)
                                <tr class="text-center">
                                    <td class="text-center">{{ $container->id}}</td>
                                    <td>
                                        <a href="{{ route('container.details', $container) }}" class="fw-bold text-decoration-none">
                                            {{ $container->code }}
                                        </a>
                                    </td>
                                    <td><div class="fw-bold">{{ $container->containerType->name }}</div></td>
                                    <td><div>{{ $container->customer->name }}</div></td>
                                    <td>
                                        @if($container->status == 'في الساحة')
                                            <div class="status-available">{{ $container->status }}</div>
                                        @elseif($container->status == 'تم التسليم')
                                            <div class="status-delivered">
                                                {{ $container->status }}
                                                <i class="fa-solid fa-check"></i>
                                            </div>
                                        @elseif($container->status == 'متأخر')
                                            <div class="status-danger">{{ $container->status }}</div>
                                        @endif
                                    </td>
                                    <td class="{{ $container->location ? 'fw-bold' : 'text-muted' }}">
                                        {{ $container->location ?? 'لم يحدد بعد' }}
                                    </td>
                                    <td class="{{ $container->received_by ? 'text-dark' : 'text-muted' }}">
                                        {{ $container->received_by ?? 'لم يتم الأستلام بعد' }}
                                    </td>
                                    <td class="{{ $container->delivered_by ? 'text-dark' : 'text-muted' }}">
                                        {{ $container->delivered_by ?? 'لم يتم التسليم بعد' }}
                                    </td>
                                    <td>
                                       <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal-{{ $container->id }}" {{ $container->status == 'تم التسليم' ? 'disabled' : '' }}>
                                            إضافة خدمة
                                        </button> 
                                    </td>
                                </tr>

                                <div class="modal fade" id="addServiceModal-{{ $container->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form action="{{ route('containers.add.service', $container->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold">إضافة خدمة للحاوية {{ $container->code }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">الخدمة</label>
                                                        <select name="service_id" class="form-select border-primary" required>
                                                            <option value="" disabled selected>اختر خدمة</option>
                                                            @foreach($services as $service)
                                                                <option value="{{ $service->id }}">{{ $service->description }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">السعر</label>
                                                        <input type="number" name="price" class="form-control border-primary" step="0.01">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">ملاحظات</label>
                                                        <textarea name="notes" class="form-control border-primary"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit" class="btn btn-primary fw-bold">حفظ</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">لا توجد حاويات مرتبطة بهذه البوليصة</h5>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
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