@extends('layouts.app')

@section('title', 'تفاصيل البوليصة')

@section('content')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-2">
        <div>
            <h2 class="h3 text-primary mb-1">
                <i class="fas fa-clipboard-list me-2 d-none d-md-inline"></i>
                <span class="d-none d-md-inline">تفاصيل بوليصة الخدمات {{ $policy->code }}</span>
                <span class="d-inline d-md-none">بوليصة {{ $policy->code }}</span>
            </h2>
            @if ($policy->customer && $policy->customer->contract)
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('contracts.details', $policy->customer->contract) }}"
                                class="text-decoration-none">العقد #{{ $policy->customer->contract->id }}</a>
                        </li>
                        <li class="breadcrumb-item active d-none d-md-inline" aria-current="page">البوليصة #{{ $policy->code }}</li>
                    </ol>
                </nav>
            @endif
        </div>
        <div class="d-flex flex-wrap gap-2">
            <form action="{{ route('print', 'service_permission') }}" method="POST" target="_blank">
                @csrf
                @foreach ($policy->containers as $container)
                    <input type="hidden" name="containers[]" value="{{ $container->id }}">
                @endforeach
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-print me-1"></i>
                    <span class="d-inline">طباعة اذن خدمات</span>
                </button>
            </form>
            @if ($policy->containers->first()->invoices->isEmpty())
                <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                    data-bs-target="#createInvoice">
                    <i class="fas fa-scroll me-1"></i>
                    <span class="d-inline">إنشاء فاتورة</span>
                </button>
            @endif
            <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#editPolicyModal">
                <i class="fas fa-edit me-1"></i>
                تعديل البوليصة
            </button>
            @if(auth()->user()->roles->contains('name', 'Admin'))
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="fas fa-trash me-1"></i>
                    <span class="d-inline">حذف البوليصة</span>
                </button>
            @endif
        </div>
    </div>

    <!-- edit policy modal -->
    <div class="modal fade" id="editPolicyModal" tabindex="-1" aria-labelledby="editPolicyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white fw-bold" id="editPolicyModalLabel">تعديل بيانات بوليصة الشحن</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('policies.storage.update', $policy) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body text-dark">
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label">رقم البوليصة</label>
                                <input type="text" class="form-control border-primary" name="code" value="{{ old('code', $policy->code) }}">
                                @error('code')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">تاريخ البوليصة</label>
                                <input type="date" class="form-control border-primary" name="date" value="{{ old('date', \Carbon\Carbon::parse($policy->date)->format('Y-m-d')) }}">
                                @error('date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">العميل</label>
                                <select class="form-select border-primary" name="customer_id" id="customer_id" required>
                                    <option disabled selected>اختر العميل...</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}"
                                            {{ $policy->customer_id == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">إســم السائق</label>
                                <input type="text" class="form-control border-primary" name="driver_name" value="{{ old('driver_name', $policy->driver_name) }}">
                                @error('driver_name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">هوية السائق</label>
                                <input type="text" class="form-control border-primary" name="driver_NID" value="{{ old('driver_NID', $policy->driver_NID) }}">
                                @error('driver_NID')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror   
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">رقم السائق</label>
                                <input type="text" class="form-control border-primary" name="driver_number" value="{{ old('driver_number', $policy->driver_number) }}">
                                @error('driver_number')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror   
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">نوع السيارة</label>
                                <input type="text" class="form-control border-primary" name="driver_car" value="{{ old('driver_car', $policy->driver_car) }}">
                                @error('driver_car')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">لوحة السيارة</label>
                                <input type="text" class="form-control border-primary" name="car_code" value="{{ old('plate_number', $policy->car_code) }}">
                                @error('car_code')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-start">
                        <button type="submit" class="btn btn-primary fw-bold">حفظ</button>
                        <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        تأكيد حذف البوليصة
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <h6 class="mb-3">هل أنت متأكد من حذف هذه البوليصة؟</h6>
                    <p class="text-muted mb-0">
                        سيتم حذف بوليصة التسليم <strong>{{ $policy->code }}</strong> نهائياً ولن يمكن استرجاعها.
                    </p>
                    <div class="alert alert-warning mt-3 mb-0">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>تنبيه:</strong> سيتم حذف الحاويات المرتبطة بهذه البوليصة أيضاً.
                    </div>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        إلغاء
                    </button>
                    <form action="{{ route('policy.delete', $policy) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>
                            تأكيد الحذف
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Invoice Modal -->
    <div class="modal fade" id="createInvoice" tabindex="-1" aria-labelledby="createInvoiceLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-bold" id="createInvoiceLabel">إنشاء فاتورة جديدة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('invoices.service.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="خدمات">
                    <input type="hidden" name="customer_id" value="{{ $policy->customer_id }}">
                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                    <input type="hidden" name="container_ids[]" value="{{ $policy->containers->pluck('id')->join(',') }}">
                    <div class="modal-body text-dark">
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-6">
                                <label class="form-label">طريقة الدفع</label>
                                <select class="form-select border-primary" name="payment_method" required>
                                    <option value="آجل">آجل</option>
                                    <option value="كاش">كاش</option>
                                    <option value="تحويل بنكي">تحويل بنكي</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">تاريخ الفاتورة</label>
                                <input type="date" name="date" class="form-control border-primary"
                                    value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="tax_rate" class="form-label">الضريبة المضافة</label>
                                <select name="tax_rate" id="tax_rate" class="form-select border-primary" required>
                                    <option value="15">خاضع للضريبة (15%)</option>
                                    <option value="0">غير خاضع للضريبة</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">نسبة الخصم(%)</label>
                                <input type="number" name="discount" id="discount"
                                    class="form-control border-primary" min="0" max="100" step="1"
                                    value="0" placeholder="0.00" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-start">
                        <button type="submit" class="btn btn-primary fw-bold">إنشاء</button>
                        <button type="button" class="btn btn-secondary fw-bold"
                            data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-12 col-md-6 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        معلومات البوليصة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-4 col-sm-4">
                            <label class="form-label text-muted small">اسم العميل</label>
                            <div class="fw-bold">{{ $policy->customer->name }}</div>
                        </div>
                        <div class="col-4 col-sm-4">
                            <label class="form-label text-muted small">تاريخ البوليصة</label>
                            <div class="fw-bold">{{ $policy->created_at->format('Y/m/d') }}</div>
                        </div>
                        <div class="col-4 col-sm-4">
                            <label class="form-label text-muted small">تم الإنشاء بواسطة</label>
                            <div class="fw-bold">{{ $policy->made_by->name }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>
                        معلومات السائق والمركبة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-sm-6 col-lg-3">
                            <label class="form-label text-muted small">اسم السائق</label>
                            <div class="fw-bold">{{ $policy->driver_name }}</div>
                        </div>
                        <div class="col-6 col-sm-6 col-lg-3">
                            <label class="form-label text-muted small">الرقم القومي</label>
                            <div class="fw-bold">{{ $policy->driver_NID }}</div>
                        </div>
                        <div class="col-6 col-sm-6 col-lg-3">
                            <label class="form-label text-muted small">نوع المركبة</label>
                            <div class="fw-bold">{{ $policy->driver_car }}</div>
                        </div>
                        <div class="col-6 col-sm-6 col-lg-3">
                            <label class="form-label text-muted small">رقم اللوحة</label>
                            <div class="fw-bold">{{ $policy->car_code }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header bg-dark text-white">
            <div class="d-flex flex-row justify-content-between align-items-center text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-boxes me-2"></i>
                    الحاويات المشمولة في البوليصة
                </h5>
                <span class="badge bg-light text-dark d-none d-md-inline">{{ count($policy->containers) }} حاوية</span>
            </div>
        </div>
        <div class="card-body p-0">
            @if (count($policy->containers) > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-primary">
                            <tr>
                                <th class="border-0 text-center fw-bold text-nowrap">#</th>
                                <th class="border-0 text-center fw-bold text-nowrap">رقم الحاوية</th>
                                <th class="border-0 text-center fw-bold text-nowrap">نوع الحاوية</th>
                                <th class="border-0 text-center fw-bold text-nowrap">الخدمة</th>
                                <th class="border-0 text-center fw-bold text-nowrap">السعر</th>
                                <th class="border-0 text-center fw-bold text-nowrap">التاريخ</th>
                                <th class="border-0 text-center fw-bold text-nowrap">إجرائات</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach ($policy->containers as $index => $container)
                                <tr>
                                    <td>{{ $container->id }}</td>
                                    <td>
                                        <a href="{{ route('container.details', $container) }}"
                                            class="fw-bold text-decoration-none">
                                            {{ $container->code }}
                                        </a>
                                    </td>
                                    <td class="fw-bold">{{ $container->containerType->name }}</td>
                                    <td class="text-nowrap">{{ $container->services->first()->description ?? '-' }}</td>
                                    <td class="fw-bold">{{ $container->services->first()->pivot->price ?? '-' }}
                                        ريال</td>
                                    <td>{{ \Carbon\Carbon::parse($container->date)->format('Y/m/d') }}</td>
                                    <td class="d-flex justify-content-center gap-2">
                                        <button type="button" class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#editContainerModal{{ $container->id }}">
                                            <i class="fas fa-edit text-primary"></i>
                                        </button>
                                        <button type="button" class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#deleteContainerModal{{ $container->id }}">
                                            <i class="fas fa-trash-can text-danger"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Edit Container Service Modal -->
                                <div class="modal fade" id="editContainerModal{{ $container->id }}" tabindex="-1" aria-labelledby="editContainerModalLabel{{ $container->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary">
                                                <h5 class="modal-title text-white fw-bold" id="editContainerModalLabel{{ $container->id }}">تعديل خدمة الحاوية {{ $container->code }}</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('policies.services.update.container', $policy) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="container_id" value="{{ $container->id }}">
                                                <input type="hidden" name="old_service_id" value="{{ $container->services->first()->id ?? '' }}">
                                                <div class="modal-body text-dark">
                                                    <div class="row g-3">
                                                        <div class="col-12">
                                                            <label class="form-label">الخدمة</label>
                                                            <select class="form-select border-primary" name="service_id" required>
                                                                <option disabled>اختر الخدمة...</option>
                                                                @foreach ($services as $service)
                                                                    <option value="{{ $service->id }}"
                                                                        {{ $container->services->first() && $container->services->first()->id == $service->id ? 'selected' : '' }}>
                                                                        {{ $service->description }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label">السعر</label>
                                                            <input type="number" class="form-control border-primary" name="price" step="0.01" min="0" value="{{ $container->services->first()->pivot->price ?? 0 }}" required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer d-flex justify-content-start">
                                                    <button type="submit" class="btn btn-primary fw-bold">حفظ</button>
                                                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Container Confirmation Modal -->
                                <div class="modal fade" id="deleteContainerModal{{ $container->id }}" tabindex="-1" aria-labelledby="deleteContainerModalLabel{{ $container->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title" id="deleteContainerModalLabel{{ $container->id }}">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    تأكيد إزالة الحاوية
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                <h6 class="mb-3">هل أنت متأكد من إزالة هذه الحاوية من البوليصة؟</h6>
                                                <p class="text-muted mb-0">
                                                    سيتم إزالة الحاوية <strong>{{ $container->code }}</strong> من هذه البوليصة.
                                                </p>
                                                <div class="alert alert-danger mt-3 mb-0">
                                                    <i class="fas fa-exclamation-circle me-2"></i>
                                                    <strong>تنبيه:</strong> هذا الإجراء سيؤدي إلى حذف الحاوية نهائياً من النظام.
                                                </div>
                                            </div>
                                            <div class="modal-footer justify-content-center">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <i class="fas fa-times me-1"></i>
                                                    إلغاء
                                                </button>
                                                <form action="{{ route('policies.container.remove', [$policy, $container->id]) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="fas fa-trash-can me-1"></i>
                                                        تأكيد الإزالة
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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

    <div class="text-center mt-4">
        <small class="text-muted">
            تم إنشاء هذه البوليصة بواسطة <strong>{{ $policy->made_by->name }}</strong>
        </small>
    </div>

    <style>
        .bg-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
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
