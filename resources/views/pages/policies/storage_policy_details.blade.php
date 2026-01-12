@extends('layouts.app')

@section('title', 'تفاصيل البوليصة')

@section('content')
    <div class="d-flex flex-row justify-content-between align-items-start align-items-sm-center gap-2 mb-2">
        <div>
            <h2 class="h3 text-primary mb-1">
                <i class="fas fa-clipboard-list me-2 d-none d-md-inline"></i>
                <span class="d-none d-md-inline">تفاصيل بوليصة التخزين {{ $policy->code }}</span>
                <span class="d-inline d-md-none"> بوليصة {{ $policy->code }}</span>
            </h2>
            @if ($policy->customer->contract)
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
        <div class="d-flex gap-2">
            <form action="{{ route('print', 'entry_permission') }}" method="POST" target="_blank">
                @csrf
                @foreach ($policy->containers as $container)
                    <input type="hidden" name="containers[]" value="{{ $container->id }}">
                @endforeach
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-print me-1"></i>
                    <span class="d-none d-sm-inline">طباعة اذن دخول</span><span
                        class="d-inline d-sm-none">طباعة</span>
                </button>
            </form>
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
                                <label class="form-label">البيان الضريبي</label>
                                <input type="text" class="form-control border-primary" name="tax_statement" value="{{ old('tax_statement', $policy->tax_statement) }}">
                                @error('tax_statement')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
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
                            <div class="col-12 col-md-4">
                                <label class="form-label">سعر التخزين</label>
                                <input type="number" class="form-control border-primary" name="storage_price" value="{{ old('storage_price', $policy->storage_price) }}">
                                @error('storage_price')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">مدة التخزين</label>
                                <input type="text" class="form-control border-primary" name="storage_duration" value="{{ old('storage_duration', $policy->storage_duration) }}">
                                @error('storage_duration')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror   
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">غرامة التأخير</label>
                                <input type="number" class="form-control border-primary" name="late_fee" value="{{ old('late_fee', $policy->late_fee) }}">
                                @error('late_fee')
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
                    <div class="mb-3">
                        <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                    </div>
                    <h6 class="mb-3">هل أنت متأكد من حذف هذه البوليصة؟</h6>
                    <p class="text-muted mb-0">
                        سيتم حذف بوليصة التسليم <strong>{{ $policy->code }}</strong> نهائياً ولن يمكن استرجاعها.
                    </p>
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

    <div class="row">
        <div class="col-lg-6 d-flex flex-column gap-3 mb-4">
            <!-- معلومات البوليصة -->
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        معلومات البوليصة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-6 col-sm-4 col-lg-4">
                            <label class="form-label text-muted small">تاريخ البوليصة</label>
                            <div class="fw-bold">{{ \Carbon\Carbon::parse($policy->date)->format('Y/m/d') }}</div>
                        </div>
                        <div class="col-6 col-sm-4 col-lg-4">
                            <label class="form-label text-muted small">البيان الضريبي</label>
                            <div class="fw-bold">{{ $policy->tax_statement ?? 'N/A' }}</div>
                        </div>
                        <div class="col-6 col-sm-4 col-lg-4">
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
                        <div class="col-6 col-sm-4 col-lg-4">
                            <label class="form-label text-muted small">الاسم</label>
                            <div class="fw-bold">{{ $policy->customer->name ?? 'N/A' }}</div>
                        </div>
                        <div class="col-6 col-sm-4 col-lg-4">
                            <label class="form-label text-muted small">السجل التجاري</label>
                            <div class="fw-bold">{{ $policy->customer->CR ?? 'N/A' }}</div>
                        </div>
                        <div class="col-6 col-sm-4 col-lg-4">
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
                        <div class="col-4 col-sm-4 col-lg-4">
                            <label class="form-label text-muted small">سعر التخزين</label>
                            <div class="fw-bold">{{ $policy->storage_price }} <i data-lucide="saudi-riyal"></i>
                            </div>
                        </div>
                        <div class="col-4 col-sm-4 col-lg-4">
                            <label class="form-label text-muted small">مدة التخزين</label>
                            <div class="fw-bold">{{ $policy->storage_duration }} {{ $policy->storage_duration ? 'يوم' : 'مدة مفتوحة' }}</div>
                        </div>
                        <div class="col-4 col-sm-4 col-lg-4">
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
            <div class="d-flex flex-row justify-content-between align-items-center text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-boxes me-2"></i>
                    الحاويات المشمولة في البوليصة
                </h5>
                <span class="badge bg-light text-dark d-none d-sm-block">{{ count($policy->containers) }} حاوية</span>
            </div>
        </div>
        <div class="card-body p-0">
            @if (count($policy->containers) > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-primary">
                            <tr class="">
                                <th class="text-center fw-bold text-nowrap">#</th>
                                <th class="text-center fw-bold text-nowrap">رقم الحاوية</th>
                                <th class="text-center fw-bold text-nowrap">نوع الحاوية</th>
                                <th class="text-center fw-bold text-nowrap">العميل</th>
                                <th class="text-center fw-bold text-nowrap">الحالة</th>
                                <th class="text-center fw-bold text-nowrap">الموقع</th>
                                <th class="text-center fw-bold text-nowrap">تم الإستلام بواسطة</th>
                                <th class="text-center fw-bold text-nowrap">تم التسليم بواسطة</th>
                                <th class="text-center fw-bold text-nowrap">الإجرائات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($policy->containers as $index => $container)
                                <tr class="text-center">
                                    <td class="text-center">{{ $container->id }}</td>
                                    <td>
                                        <a href="{{ route('container.details', $container) }}"
                                            class="fw-bold text-decoration-none">
                                            {{ $container->code }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $container->containerType->name }}</div>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('users.customer.profile', $container->customer) }}"
                                            class="text-dark text-decoration-none fw-bold">
                                            {{ $container->customer->name }}
                                        </a>
                                    </td>
                                    <td>
                                        @if ($container->status == 'في الساحة')
                                            <div class="badge status-available">{{ $container->status }}</div>
                                        @elseif($container->status == 'تم التسليم')
                                            <div class="badge status-delivered">
                                                {{ $container->status }} <i class="fa-solid fa-check"></i>
                                            </div>
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
                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#addServiceModal-{{ $container->id }}"
                                            {{ $container->status == 'تم التسليم' ? 'disabled' : '' }}>
                                            <i class="fas fa-plus me-1 d-none d-sm-inline"></i><span
                                                class="d-none d-sm-inline">إضافة خدمة</span><span
                                                class="d-inline d-sm-none">خدمة</span>
                                        </button>
                                    </td>
                                </tr>

                                <div class="modal fade" id="addServiceModal-{{ $container->id }}"
                                    tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form action="{{ route('containers.add.service', $container->id) }}"
                                            method="POST">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title fw-bold">إضافة خدمة للحاوية
                                                        {{ $container->code }}</h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">الخدمة</label>
                                                        <select name="service_id"
                                                            class="form-select border-primary" required>
                                                            <option value="" disabled selected>اختر خدمة
                                                            </option>
                                                            @foreach ($services as $service)
                                                                <option value="{{ $service->id }}">
                                                                    {{ $service->description }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">السعر</label>
                                                        <input type="number" name="price"
                                                            class="form-control border-primary" step="0.01">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">ملاحظات</label>
                                                        <textarea name="notes" class="form-control border-primary"></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="submit"
                                                        class="btn btn-primary fw-bold">حفظ</button>
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

    <script>
        $('#customer_id').select2({
            placeholder: "ابحث عن إسم العميل...",
            allowClear: true,
            dropdownParent: $('#editPolicyModal'),
            width: '100%',
        });
    </script>

    <style>
        .select2-container .select2-selection {
            height: 38px;
            border-radius: 8px;
            border: 1px solid #0d6efd;
            padding: 5px;
        }
        .select2-container .select2-selection__rendered {
            line-height: 30px;
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
    </style>
@endsection
