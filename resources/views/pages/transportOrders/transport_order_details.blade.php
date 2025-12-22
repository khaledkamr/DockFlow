@extends('layouts.app')

@section('title', 'تفاصيل إشعار النقل')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
    <h1>
        <i class="fa-solid fa-truck-fast d-none d-md-inline"></i>
        <span class="d-none d-md-inline"> تفاصيل إشعار النقل {{ $transportOrder->code }}</span>
        <span class="d-inline d-md-none">إشعار النقل {{ $transportOrder->code }}</span>
    </h1>
    <div>
        <a href="{{ route('export.transport.order', $transportOrder) }}" target="_blank" class="btn btn-outline-primary">
            <i class="fa-solid fa-print"></i> طباعة الإشعار
        </a>
        <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#editTransportOrderModal">
            <i class="fas fa-edit me-1"></i>
            تعديل الإشعار
        </button>
    </div>
</div>

<div class="modal fade" id="editTransportOrderModal" tabindex="-1" aria-labelledby="editTransportOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white fw-bold" id="editTransportOrderModalLabel">تعديل بيانات اشعار النقل</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('transportOrders.update', $transportOrder) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body text-dark">
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-md-4">
                            <label class="form-label">مكان التحميل</label>
                            <input type="text" class="form-control border-primary" id="from" name="from"
                                value="{{ old('from', $transportOrder->from) }}">
                            @error('from')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">مكان التسليم</label>
                            <input type="text" class="form-control border-primary" id="to" name="to"
                                value="{{ old('to', $transportOrder->to) }}">
                            @error('to')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">نوع الناقل</label>
                            <select name="type" id="type" class="form-select border-primary">
                                <option value="ناقل داخلي" {{ old('type', $transportOrder->type) == 'ناقل داخلي' ? 'selected' : '' }}>ناقل داخلي</option>
                                <option value="ناقل خارجي" {{ old('type', $transportOrder->type) == 'ناقل خارجي' ? 'selected' : '' }}>ناقل خارجي</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-4 internal-field">
                            <label class="form-label">إســم السائق</label>
                            <select name="driver_id" id="driver_id" class="form-select border-primary">
                                <option value="">اختر السائق...</option>
                                @foreach ($drivers as $driver)
                                    <option value="{{ $driver->id }}" data-nid="{{ $driver->NID }}"
                                        data-vehicle-plate="{{ $driver->vehicle ? $driver->vehicle->plate_number : '' }}"
                                        data-vehicle-type="{{ $driver->vehicle ? $driver->vehicle->type : '' }}"
                                        data-vehicle-id="{{ $driver->vehicle ? $driver->vehicle->id : '' }}"
                                        {{ old('driver_id', $transportOrder->driver_id) == $driver->id ? 'selected' : '' }}>
                                        {{ $driver->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('driver_name')
                                <div class="text-danger">{{ $message }}</div>
                            @endif
                        </div>
                        <div class="col-12 col-md-4 internal-field">
                            <label class="form-label">هوية السائق</label>
                            <input type="text" class="form-control border-primary" name="driver_NID" id="driver_NID"
                                value="{{ old('driver_NID', $transportOrder->driver ? $transportOrder->driver->NID : '') }}" readonly>
                        </div>
                        <div class="col-12 col-md-4 internal-field">
                            <label class="form-label">لوحة السيارة</label>
                            <input type="text" class="form-control border-primary" name="plate_number" id="plate_number"
                                value="{{ old('plate_number', $transportOrder->vehicle ? $transportOrder->vehicle->plate_number : '') }}" readonly>
                            <input type="hidden" name="vehicle_id" id="vehicle_id" value="{{ old('vehicle_id', $transportOrder->vehicle_id) }}">
                        </div>
                        <div class="col-12 col-md-4 external-field" style="display: none">
                            <label class="form-label d-block">إســم المورد</label>
                            <select name="supplier_id" id="supplier_id" class="form-select border-primary" style="width: 100%;">
                                <option value="">اختر المورد...</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ old('supplier_id', $transportOrder->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4 external-field" style="display: none">
                            <label class="form-label">إسم السائق</label>
                            <input type="text" class="form-control border-primary" name="driver_name" id="driver_name" 
                                value="{{ old('driver_name', $transportOrder->driver_name) }}">
                        </div>
                        <div class="col-12 col-md-4 external-field" style="display: none">
                            <label class="form-label">لوحة السيارة</label>
                            <input type="text" class="form-control border-primary" name="vehicle_plate" id="vehicle_plate"
                                value="{{ old('vehicle_plate', $transportOrder->vehicle_plate) }}">
                        </div>
                        <div class="col-12 col-md-4 external-field">
                            <label class="form-label">مصاريف المورد</label>
                            <input type="number" class="form-control border-primary" name="supplier_cost" id="supplier_cost"
                                value="{{ old('supplier_cost', $transportOrder->supplier_cost) }}">
                            @error('supplier_cost')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md internal-field">
                            <label class="form-label">مصاريف الديزل</label>
                            <input type="number" class="form-control border-primary" name="diesel_cost" id="diesel_cost"
                                value="{{ old('diesel_cost', $transportOrder->diesel_cost) }}">
                            @error('diesel_cost')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md internal-field">
                            <label class="form-label">عمولة السائق</label>
                            <input type="number" class="form-control border-primary" name="driver_wage" id="driver_wage"
                                value="{{ old('driver_wage', $transportOrder->driver_wage) }}">
                            @error('driver_wage')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md">
                            <label class="form-label">مصاريف أخرى</label>
                            <input type="number" class="form-control border-primary" name="other_expenses" id="other_expenses"
                                value="{{ old('other_expenses', $transportOrder->other_expenses) }}">
                            @error('other_expenses')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md">
                            <label class="form-label">سعر العميل</label>
                            <input type="number" class="form-control border-primary" id="client_cost" name="client_cost"
                                value="{{ old('client_cost', $transportOrder->client_cost) }}">
                            @error('client_cost')
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

<!-- المعلومات الأساسية -->
<div class="row g-3">
    <!-- معلومات النقل -->
    <div class="col-12 col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="fa-solid fa-route"></i> معلومات النقل</h5>
            </div>
            <div class="card-body">
                <div class="row g-2 mb-3">
                    <div class="col-6 col-sm-6">
                        <label class="text-muted small d-block">رقم المعاملة</label>
                        <a href="{{ route('transactions.details', $transportOrder->transaction) }}"
                            class="text-decoration-none text-primary fw-bold">
                            {{ $transportOrder->transaction->code }}
                        </a>
                    </div>
                    <div class="col-6 col-sm-6">
                        <label class="text-muted small">التاريخ</label>
                        <p class="fw-bold mb-0">{{ \Carbon\Carbon::parse($transportOrder->date)->format('Y/m/d') }}</p>
                    </div>
                </div>
                <div class="row g-2">
                    <div class="col-6 col-sm-6">
                        <label class="text-muted small">مكان التحميل والتسليم</label>
                        <p class="fw-semibold mb-0">
                            <i class="fas fa-map-marker-alt text-danger"></i>
                            {{ $transportOrder->from }} - 
                            <i class="fas fa-map-marker-alt text-success"></i>
                            {{ $transportOrder->to }}</p>
                        </p>
                    </div>
                    <div class="col-6 col-sm-6">
                        <label class="text-muted small">اسم العميل</label>
                        <p class="fw-bold mb-0">
                            <a href="{{ route('users.customer.profile', $transportOrder->customer) }}" class="text-decoration-none text-dark">
                                {{ $transportOrder->customer->name }}
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- معلومات الناقل -->
    <div class="col-12 col-md-6 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="bi bi-truck-front-fill"></i> معلومات الناقل</h5>
            </div>
            <div class="card-body">
                <div class="row g-2 mb-3">
                    <div class="col-6 col-sm-6">
                        <label class="text-muted small">نوع الناقل</label>
                        <p class="fw-bold mb-0">{{ $transportOrder->type }}</p>
                    </div>
                    <div class="col-6 col-sm-6">
                        <label
                            class="text-muted small">{{ $transportOrder->driver ? 'اسم السائق' : 'اسم المورد' }}</label>
                        <p class="fw-bold mb-0">
                            {{ $transportOrder->driver->name ?? ($transportOrder->supplier->name ?? '--') }}</p>
                    </div>
                </div>
                <div class="row g-2">
                    @if ($transportOrder->type == 'ناقل داخلي')
                        <div class="col-6 col-sm-6">
                            <label class="text-muted small">الرقم القومي</label>
                            <p class="fw-semibold mb-0">{{ $transportOrder->driver->NID ?? 'N/A' }}</p>
                        </div>
                        <div class="col-6 col-sm-6">
                            <label class="text-muted small">الشاحنة</label>
                            <p class="fw-semibold mb-0">
                                {{ $transportOrder->vehicle->plate_number . ' - ' . $transportOrder->vehicle->type ?? '' }}
                            </p>
                        </div>
                    @elseif($transportOrder->type == 'ناقل خارجي')
                        <div class="col-6 col-sm-6">
                            <label class="text-muted small">اسم السائق</label>
                            <p class="fw-semibold mb-0">{{ $transportOrder->driver_name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-6 col-sm-6">
                            <label class="text-muted small">رقم التواصل</label>
                            <p class="fw-semibold mb-0">{{ $transportOrder->vehicle_plate ?? 'N/A' }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- التكاليف -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="bi bi-cash-stack"></i> التكاليف</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            @if ($transportOrder->type == 'ناقل داخلي')
                <div class="col-6 col-sm-4 col-md-3 col-lg">
                    <label class="text-muted small">تكلفة الديزل</label>
                    <p class="fw-bold mb-0">{{ number_format($transportOrder->diesel_cost, 2) }} <i
                            data-lucide="saudi-riyal"></i></p>
                </div>
                <div class="col-6 col-sm-4 col-md-3 col-lg">
                    <label class="text-muted small">أجرة السائق</label>
                    <p class="fw-bold mb-0">{{ number_format($transportOrder->driver_wage, 2) }} <i
                            data-lucide="saudi-riyal"></i></p>
                </div>
            @elseif($transportOrder->type == 'ناقل خارجي')
                <div class="col-6 col-sm-4 col-md-3 col-lg">
                    <label class="text-muted small">تكلفة المورد</label>
                    <p class="fw-bold mb-0">{{ number_format($transportOrder->supplier_cost, 2) }} <i
                            data-lucide="saudi-riyal"></i></p>
                </div>
            @endif
            <div class="col-6 col-sm-4 col-md-3 col-lg">
                <label class="text-muted small">مصروفات أخرى</label>
                <p class="fw-bold mb-0">{{ number_format($transportOrder->other_expenses, 2) }} <i
                        data-lucide="saudi-riyal"></i></p>
            </div>
            <div class="col-6 col-sm-4 col-md-3 col-lg">
                <label class="text-muted small">سعر العميل</label>
                <p class="fw-bold mb-0 fs-5">{{ number_format($transportOrder->client_cost, 2) }} <i
                        data-lucide="saudi-riyal"></i></p>
            </div>
            <div class="col-6 col-sm-4 col-md-3 col-lg">
                <label class="text-muted small">اجمالي سعر العميل</label>
                <p class="fw-bold text-success mb-0 fs-5">{{ number_format($transportOrder->total_cost, 2) }} <i
                        data-lucide="saudi-riyal"></i></p>
            </div>
        </div>
    </div>
</div>

<!-- الحاويات -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="fa-solid fa-box"></i> الحاويات ({{ count($transportOrder->containers) }})</h5>
    </div>
    <div class="card-body">
        @if (count($transportOrder->containers) > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th class="text-center text-nowrap">#</th>
                            <th class="text-center text-nowrap">رقم الحاوية</th>
                            <th class="text-center text-nowrap">اسم العميل</th>
                            <th class="text-center text-nowrap">النوع</th>
                            <th class="text-center text-nowrap">الحالة</th>
                            <th class="text-center text-nowrap">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transportOrder->containers as $index => $container)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="text-center text-primary fw-bold">
                                    <a href="{{ route('container.details', $container) }}"
                                        class="text-decoration-none">
                                        {{ $container->code }}
                                    </a>
                                </td>
                                <td class="text-center fw-bold">
                                    <a href="{{ route('users.customer.profile', $container->customer) }}" class="text-decoration-none text-dark">
                                        {{ $container->customer->name }}
                                    </a>
                                </td>
                                <td class="text-center">
                                    <span
                                        class="badge rounded-pill bg-primary">{{ $container->containerType->name }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-success">{{ $container->status }}</span>
                                </td>
                                <td class="text-center text-nowrap">{{ $container->notes ?? '---' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted text-center mb-0">لا توجد حاويات مرتبطة بهذا الإشعار</p>
        @endif
    </div>
</div>

<!-- الملاحظات -->
<div class="card shadow-sm mb-5">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0"><i class="fa-solid fa-sticky-note"></i> الملاحظات</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('transportOrders.notes', $transportOrder) }}">
            @csrf
            @method('PATCH')
            <div class="mb-3">
                <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="اكتب الملاحظات هنا...">{{ old('notes', $transportOrder->notes) }}</textarea>
            </div>
            <div class="text-start">
                <button type="submit" class="btn btn-primary">
                    حفظ الملاحظات
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    $('#editTransportOrderModal').on('shown.bs.modal', function () {
        $('#driver_id').select2({
            placeholder: "ابحث عن إسم السائق...",
            allowClear: true,
            dropdownParent: $('#editTransportOrderModal')
        });

        $('#supplier_id').select2({
            placeholder: "ابحث عن إسم المورد...",
            allowClear: true,
            dropdownParent: $('#editTransportOrderModal')
        });
    });

    $('#driver_id').on('change', function() {
        let nid = $(this).find(':selected').data('nid');
        $('#driver_NID').val(nid || '');
        let vehiclePlate = $(this).find(':selected').data('vehicle-plate');
        $('#plate_number').val(vehiclePlate || '');
        let vehicleId = $(this).find(':selected').data('vehicle-id');
        $('#vehicle_id').val(vehicleId || '');
    });

    $(document).ready(function() {
        function toggleFields() {
            const selected = $('#type').val();
            $('.internal-field, .external-field').hide();

            if (selected === "ناقل داخلي") {
                $('.internal-field').show();
                $('#supplier_id').val(null).trigger('change');
            } else if (selected === "ناقل خارجي") {
                $('.external-field').show();
                $('#driver_id').val(null).trigger('change');
            }
        }
        $('#type').on('change', toggleFields);
        toggleFields();
    });
</script>

<style>
    .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
    }
    .select2-container .select2-selection {
        height: 38px;
        border-radius: 8px;
        border: 1px solid #0d6efd;
        padding: 5px;
    }

    .select2-container .select2-selection__rendered {
        line-height: 30px;
    }
</style>
@endsection
