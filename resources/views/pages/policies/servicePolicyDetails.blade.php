@extends('layouts.app')

@section('title', 'تفاصيل الإتفاقية')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="mb-4">
                <h2 class="h3 text-primary mb-1">
                    <i class="fas fa-clipboard-list me-2"></i>
                    تفاصيل إتفاقية الخدمات #{{ $policy->id }}
                </h2>
                @if($policy->customer_id && $policy->customer && $policy->customer->contract)
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('contracts.details', $policy->customer->contract) }}" class="text-decoration-none">العقد #{{ $policy->customer->contract->id }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">الإتفاقية #{{ $policy->id }}</li>
                        </ol>
                    </nav>
                @endif
            </div>
            <div class="d-flex gap-2">
                <form action="{{ route('print', 'exit_permission') }}" method="POST" target="_blank">
                    @csrf
                    @foreach($policy->containers as $container)
                        <input type="hidden" name="containers[]" value="{{ $container->id }}">
                    @endforeach
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-print me-1"></i>
                        طباعة اذن خدمة
                    </button>
                </form>
                @if($policy->containers->first()->invoices->isEmpty())
                    <button class="btn btn-outline-primary me-2" type="button" data-bs-toggle="modal" data-bs-target="#createInvoice">
                        <i class="fas fa-scroll me-1"></i>
                        إنشاء فاتورة
                    </button>
                @endif
            </div>
        </div>

        <div class="modal fade" id="createInvoice" tabindex="-1" aria-labelledby="createInvoiceLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-dark fw-bold" id="createInvoiceLabel">إنشاء فاتورة جديدة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('invoices.service.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="customer_id" value="{{ $policy->customer_id }}">
                        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                        <input type="hidden" name="container_ids[]" value="{{ $policy->containers->pluck('id')->join(',') }}">
                        <div class="modal-body text-dark">
                            <div class="row mb-4">
                                <div class="col">
                                    <label class="form-label">طريقة الدفع</label>
                                    <select class="form-select border-primary" name="payment_method" required>
                                        <option value="آجل">آجل</option>
                                        <option value="كاش">كاش</option>
                                        <option value="تحويل بنكي">تحويل بنكي</option>
                                    </select>
                                </div>
                                <div class="col">
                                    <label class="form-label">نسبة الخصم(%)</label>
                                    <input type="number" name="discount" id="discount" class="form-control border-primary" 
                                        min="0" max="100" step="1" value="0" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex justify-content-start">
                            <button type="submit" class="btn btn-primary fw-bold">إنشاء</button>
                            <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                        </div>
                    </form>
                </div>
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
                                    <div class="fw-bold">{{ $policy->company->name }}</div>
                                </div>
                                <div class="col">
                                    <label class="form-label text-muted small">السجل التجاري</label>
                                    <div class="fw-bold">{{ $policy->company->CR }}</div>
                                </div>
                                <div class="col">
                                    <label class="form-label text-muted small">الرقم الضريبي</label>
                                    <div class="fw-bold">{{ $policy->company->vatNumber }}</div>
                                </div>
                            </div>
                            
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-user-tie me-2"></i>
                                الطرف الثاني
                            </h6>
                            <div class="row mb-3">
                                <div class="col">
                                    <label class="form-label text-muted small">الاسم</label>
                                    <div class="fw-bold">{{ $policy->customer ? $policy->customer->name : $policy->external_customer }}</div>
                                </div>
                                <div class="col">
                                    <label class="form-label text-muted small">السجل التجاري</label>
                                    <div class="fw-bold">{{ $policy->customer ? $policy->customer->CR : '-' }}</div>
                                </div>
                                <div class="col">
                                    <label class="form-label text-muted small">الرقم الضريبي</label>
                                    <div class="fw-bold">{{ $policy->customer ? $policy->customer->vatNumber : '-' }}</div>
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
                                    <th class="border-0 text-center fw-bold">رقم الحاوية</th>
                                    <th class="border-0 text-center fw-bold">نوع الحاوية</th>
                                    <th class="border-0 text-center fw-bold">الخدمة</th>
                                    <th class="border-0 text-center fw-bold">السعر</th>
                                    <th class="border-0 text-center fw-bold">التاريخ</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach($policy->containers as $index => $container)
                                    <tr>
                                        <td>{{ $container->id }}</td>
                                        <td>
                                            <a href="{{ route('container.details', $container) }}" class="fw-bold text-decoration-none">
                                                {{ $container->code }}
                                            </a>
                                        </td>
                                        <td class="fw-bold">{{ $container->containerType->name }}</td>
                                        <td>{{ $container->services->first()->description ?? '-' }}</td>
                                        <td class="fw-bold">{{ $container->services->first()->pivot->price ?? '-' }} ريال</td>
                                        <td>{{ \Carbon\Carbon::parse($container->date)->format('Y/m/d') }}</td>
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