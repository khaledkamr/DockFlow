@extends('layouts.app')

@section('title', 'بوليصة شحن')

@section('content')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
        <h1 class="mb-0">
            <i class="fa-solid fa-truck-fast"></i> <span class="d-none d-md-inline">بوليصة الشحن</span><span class="d-sm-inline d-md-none">بوليصة</span>
            {{ $policy->code }}
        </h1>
        <div class="d-flex flex-row gap-2">
            @if ($policy->invoices->where('type', 'شحن')->first())
                <a href="{{ route('invoices.shipping.details', $policy->invoices->where('type', 'شحن')->first()) }}"
                    target="_blank" class="btn btn-outline-primary flex-fill">
                    <i class="fa-solid fa-scroll"></i> <span class="d-inline">عرض الفاتورة</span>
                </a>
            @endif
            <a href="{{ route('export.shipping.policy', $policy->id) }}" target="_blank" class="btn btn-outline-primary flex-fill">
                <i class="fa-solid fa-print"></i> <span class="d-inline">طباعة</span>
            </a>
            <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#editPolicyModal">
                <i class="fas fa-edit me-1"></i>
                تعديل
            </button>
            @if(auth()->user()->roles->pluck('name')->contains('Admin'))
                <button class="btn btn-outline-danger" type="button" data-bs-toggle="modal" data-bs-target="#deletePolicyModal">
                    <i class="fas fa-trash-alt me-1"></i>
                    حذف
                </button>
            @endif
        </div>
    </div>

    <div class="modal fade" id="deletePolicyModal" tabindex="-1" aria-labelledby="deletePolicyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold" id="deletePolicyModalLabel">تأكيد الحذف</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center text-dark">
                    <p class="mb-0">هل أنت متأكد من حذف بوليصة الشحن <strong>{{ $policy->code }}</strong>؟</p>
                </div>
                <div class="modal-footer d-flex justify-content-start">
                    <form action="{{ route('shipping.policies.delete', $policy) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger fw-bold">حذف</button>
                    </form>
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editPolicyModal" tabindex="-1" aria-labelledby="editPolicyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white fw-bold" id="editPolicyModalLabel">تعديل بيانات بوليصة الشحن</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('shipping.policy.update', $policy) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body text-dark">
                        <div class="row g-3 mb-4">
                            <div class="col-12 col-md-4">
                                <label class="form-label">رقم البوليصة</label>
                                <input type="text" class="form-control border-primary" id="code" name="code"
                                    value="{{ old('code', $policy->code) }}">
                                @error('code')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label d-block">إســم العميل</label>
                                <select name="customer_id" id="customer_id" class="form-select border-primary" style="width: 100%;">
                                    <option value="">اختر عميل...</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}"
                                            {{ old('customer_id', $policy->customer_id) == $customer->id ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">تاريخ البوليصة</label>
                                <input type="date" class="form-control border-primary" id="date" name="date" 
                                    value="{{ old('date', \Carbon\Carbon::parse($policy->date)->format('Y-m-d')) }}">
                                @error('date')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">مكان التحميل</label>
                                <input type="text" class="form-control border-primary" id="from" name="from" 
                                    value="{{ old('from', $policy->from) }}">
                                @error('from')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">مكان التسليم</label>
                                <input type="text" class="form-control border-primary" id="to" name="to"
                                    value="{{ old('to', $policy->to) }}">
                                @error('to')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">نوع الناقل</label>
                                <select name="type" id="type" class="form-select border-primary">
                                    <option value="ناقل داخلي" {{ old('type', $policy->type) == 'ناقل داخلي' ? 'selected' : '' }}>ناقل داخلي</option>
                                    <option value="ناقل خارجي" {{ old('type', $policy->type) == 'ناقل خارجي' ? 'selected' : '' }}>ناقل خارجي</option>
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
                                            {{ old('driver_id', $policy->driver_id) == $driver->id ? 'selected' : '' }}>
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
                                    value="{{ old('driver_NID', $policy->driver ? $policy->driver->NID : '') }}" readonly>
                            </div>
                            <div class="col-12 col-md-4 internal-field">
                                <label class="form-label">لوحة السيارة</label>
                                <input type="text" class="form-control border-primary" name="plate_number" id="plate_number"
                                    value="{{ old('plate_number', $policy->vehicle ? $policy->vehicle->plate_number : '') }}" readonly>
                                <input type="hidden" name="vehicle_id" id="vehicle_id" value="{{ old('vehicle_id', $policy->vehicle_id) }}">
                            </div>
                            <div class="col-12 col-md-4 external-field" style="display: none">
                                <label class="form-label d-block">إســم المورد</label>
                                <select name="supplier_id" id="supplier_id" class="form-select border-primary" style="width: 100%;">
                                    <option value="">اختر المورد...</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}"
                                            {{ old('supplier_id', $policy->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4 external-field" style="display: none">
                                <label class="form-label">إسم السائق</label>
                                <input type="text" class="form-control border-primary" name="driver_name" id="driver_name" 
                                    value="{{ old('driver_name', $policy->driver_name) }}">
                            </div>
                            <div class="col-12 col-md-4 external-field" style="display: none">
                                <label class="form-label">لوحة السيارة</label>
                                <input type="text" class="form-control border-primary" name="vehicle_plate" id="vehicle_plate"
                                    value="{{ old('vehicle_plate', $policy->vehicle_plate) }}">
                            </div>
                            <div class="col-12 col-md-2 external-field">
                                <label class="form-label">مصاريف المورد</label>
                                <input type="number" class="form-control border-primary" name="supplier_cost" id="supplier_cost"
                                    value="{{ old('supplier_cost', $policy->supplier_cost) }}">
                                @error('supplier_cost')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-2 external-field">
                                <label class="form-label">تم الدفع للمورد</label>
                                <select name="paid" id="paid" class="form-select border-primary" style="width: 100%;">
                                    <option value="0" {{ old('paid', $policy->paid) == 0 ? 'selected' : '' }}>لا</option>
                                    <option value="1" {{ old('paid', $policy->paid) == 1 ? 'selected' : '' }}>نعم</option>
                                </select>
                            </div>
                            <div class="col-12 col-md internal-field">
                                <label class="form-label">مصاريف الديزل</label>
                                <input type="number" class="form-control border-primary" name="diesel_cost" id="diesel_cost"
                                    value="{{ old('diesel_cost', $policy->diesel_cost) }}">
                                @error('diesel_cost')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md internal-field">
                                <label class="form-label">عمولة السائق</label>
                                <input type="number" class="form-control border-primary" name="driver_wage" id="driver_wage"
                                    value="{{ old('driver_wage', $policy->driver_wage) }}">
                                @error('driver_wage')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md">
                                <label class="form-label">مصاريف أخرى</label>
                                <input type="number" class="form-control border-primary" name="other_expenses" id="other_expenses"
                                    value="{{ old('other_expenses', $policy->other_expenses) }}">
                                @error('other_expenses')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md">
                                <label class="form-label">سعر العميل</label>
                                <input type="number" class="form-control border-primary" id="client_cost" name="client_cost"
                                    value="{{ old('client_cost', $policy->client_cost) }}">
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
    <div class="row">
        <!-- معلومات النقل -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fa-solid fa-route"></i> معلومات النقل</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col">
                            <label class="text-muted small">مكان التحميل والتسليم</label>
                            <p class="fw-semibold mb-0">
                                <i class="fas fa-map-marker-alt text-danger"></i> {{ $policy->from }} - 
                                <i class="fas fa-map-marker-alt text-success"></i> {{ $policy->to }} 
                            </p>
                        </div>
                        <div class="col">
                            <label class="text-muted small">الى العميل</label>
                            <p class="fw-bold mb-0">
                                <a href="{{ route('users.customer.profile', $policy->customer) }}" class="text-decoration-none text-dark">
                                    {{ $policy->customer->name }}
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col">
                            <label class="text-muted small">التاريخ</label>
                            <p class="fw-bold mb-0">{{ \Carbon\Carbon::parse($policy->date)->format('Y/m/d') }}</p>
                        </div>
                        <div class="col">
                            <label class="text-muted small">تم التسليم؟</label>
                            @if ($policy->is_received)
                                <span class="badge status-delivered">
                                    <i class="fa-solid fa-check-circle"></i> تم التسليم
                                </span>
                            @else
                                <span class="badge status-waiting">
                                    <i class="fa-solid fa-clock"></i> في الانتظار
                                </span>
                            @endif
                            <form method="POST" action="{{ route('shipping.policies.toggle', $policy) }}"
                                class="d-inline mt-2">
                                @csrf
                                @method('PATCH')
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="isReceivedToggle"
                                        {{ $policy->is_received ? 'checked' : '' }} onchange="this.form.submit()">
                                    <label class="form-check-label small" for="isReceivedToggle">
                                        تغيير الحالة
                                    </label>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- معلومات الناقل -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-truck-front-fill"></i> معلومات الناقل</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col">
                            <label class="text-muted small">نوع الناقل</label>
                            <p class="fw-bold mb-0">{{ $policy->type }}</p>
                        </div>
                        <div class="col">
                            <label class="text-muted small">{{ $policy->driver ? 'اسم السائق' : 'اسم المورد' }}</label>
                            <p class="fw-bold mb-0">
                                {{ $policy->driver->name ?? ($policy->supplier->name ?? '--') }}
                                @if($policy->supplier)
                                    <span class="badge bg-{{ $policy->paid ? 'success' : 'danger' }}">{{ $policy->paid ? 'مدفوع' : 'غير مدفوع' }}</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="row g-3">
                        @if ($policy->type == 'ناقل داخلي')
                            <div class="col">
                                <label class="text-muted small">الرقم القومي</label>
                                <p class="fw-semibold mb-0">{{ $policy->driver->NID ?? 'N/A' }}</p>
                            </div>
                            <div class="col">
                                <label class="text-muted small">الشاحنة</label>
                                <p class="fw-semibold mb-0">
                                    {{ $policy->vehicle->plate_number . ' - ' . $policy->vehicle->type ?? '' }}</p>
                            </div>
                        @elseif($policy->type == 'ناقل خارجي')
                            <div class="col">
                                <label class="text-muted small">اسم السائق</label>
                                <p class="fw-semibold mb-0">{{ $policy->driver_name ?? 'N/A' }}</p>
                            </div>
                            <div class="col">
                                <label class="text-muted small">لوحة السيارة</label>
                                <p class="fw-semibold mb-0">{{ $policy->vehicle_plate ?? 'N/A' }}</p>
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
                @if ($policy->type == 'ناقل داخلي')
                    <div class="col-6 col-sm-4 col-md-3 col-lg">
                        <label class="text-muted small">تكلفة الديزل</label>
                        <p class="fw-bold mb-0">{{ number_format($policy->diesel_cost, 2) }} <i
                                data-lucide="saudi-riyal"></i></p>
                    </div>
                    <div class="col-6 col-sm-4 col-md-3 col-lg">
                        <label class="text-muted small">أجرة السائق</label>
                        <p class="fw-bold mb-0">{{ number_format($policy->driver_wage, 2) }} <i
                                data-lucide="saudi-riyal"></i></p>
                    </div>
                @elseif($policy->type == 'ناقل خارجي')
                    <div class="col-6 col-sm-4 col-md-3 col-lg">
                        <label class="text-muted small">تكلفة المورد</label>
                        <p class="fw-bold mb-0">{{ number_format($policy->supplier_cost, 2) }} <i
                                data-lucide="saudi-riyal"></i></p>
                    </div>
                @endif
                <div class="col-6 col-sm-4 col-md-3 col-lg">
                    <label class="text-muted small">مصروفات أخرى</label>
                    <p class="fw-bold mb-0">{{ number_format($policy->other_expenses, 2) }} <i
                            data-lucide="saudi-riyal"></i></p>
                </div>
                <div class="col-6 col-sm-4 col-md-3 col-lg">
                    <label class="text-muted small">سعر العميل</label>
                    <p class="fw-bold text-dark mb-0">{{ number_format($policy->client_cost, 2) }} <i
                            data-lucide="saudi-riyal"></i></p>
                </div>
                <div class="col-6 col-sm-4 col-md-3 col-lg">
                    <label class="text-muted small">اجمالي سعر العميل</label>
                    <p class="fw-bold text-success mb-0 fs-5">{{ number_format($policy->total_cost, 2) }} <i
                            data-lucide="saudi-riyal"></i></p>
                </div>
            </div>
        </div>
    </div>

    <!-- البضائع -->
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center bg-dark text-white">
            <div>
                <h5 class="mb-0"><i class="fa-solid fa-box"></i> البضائع</h5>
            </div>
            <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#editGoodsModal">
                <i class="fas fa-edit me-1"></i>
                تعديل البضائع
            </button>
        </div>
        <div class="card-body">
            @if ($policy->goods && count($policy->goods) > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-primary">
                            <tr>
                                <th class="text-center text-nowrap">البيان</th>
                                <th class="text-center text-nowrap">الكمية</th>
                                <th class="text-center text-nowrap">الوزن</th>
                                <th class="text-center text-nowrap">ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($policy->goods as $good)
                                <tr>
                                    <td class="text-center fw-bold">{{ $good->description }}</td>
                                    <td class="text-center">{{ $good->quantity ?? '---' }}</td>
                                    <td class="text-center">{{ $good->weight ?? '---' }} {{ $good->weight ? 'طن' : '' }}</td>
                                    <td class="text-center text-muted">{{ $good->notes ?? '---' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-4">
                    <i class="fa-solid fa-box-open fa-3x mb-3"></i>
                    <p>لا توجد بضائع مرتبطة بهذه البوليصة</p>
                </div>
            @endif
        </div>
    </div>

    <!-- الملاحظات -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fa-solid fa-sticky-note"></i> الملاحظات</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('shipping.policies.notes', $policy) }}">
                @csrf
                @method('PATCH')
                <div class="mb-3">
                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="اكتب الملاحظات هنا...">{{ old('notes', $policy->notes) }}</textarea>
                </div>
                <div class="text-start">
                    <button type="submit" class="btn btn-primary col-12 col-md-2">
                        <span class="d-inline">حفظ الملاحظات</span><span
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal لتعديل البضائع -->
    <div class="modal fade" id="editGoodsModal" tabindex="-1" aria-labelledby="editGoodsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white fw-bold" id="editGoodsModalLabel">تعديل البضائع</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('shipping.policies.goods.update', $policy) }}" method="POST" id="goodsForm">
                    @csrf
                    @method('PATCH')
                    <div class="modal-body text-dark">
                        <div class="table-container">
                            <table class="table table-bordered" id="goodsTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center" style="width: 30%;">البيان</th>
                                        <th class="text-center" style="width: 20%;">الكمية</th>
                                        <th class="text-center" style="width: 20%;">الوزن</th>
                                        <th class="text-center" style="width: 25%;">ملاحظات</th>
                                        <th class="text-center" style="width: 5%;">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($policy->goods && count($policy->goods) > 0)
                                        @foreach ($policy->goods as $index => $good)
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control" name="goods[{{ $index }}][description]" 
                                                           value="{{ $good->description }}" required>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" class="form-control text-center" name="goods[{{ $index }}][quantity]" 
                                                           value="{{ $good->quantity }}" min="0">
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" class="form-control text-center" name="goods[{{ $index }}][weight]" 
                                                           value="{{ $good->weight }}" min="0">
                                                </td>
                                                <td>
                                                    <textarea class="form-control" name="goods[{{ $index }}][notes]" rows="1">{{ $good->notes }}</textarea>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-danger btn-sm delete-row">
                                                        <i class="fas fa-trash-can"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control" name="goods[0][description]" required>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control text-center" name="goods[0][quantity]" min="0">
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" class="form-control text-center" name="goods[0][weight]" min="0">
                                            </td>
                                            <td>
                                                <textarea class="form-control" name="goods[0][notes]" rows="1"></textarea>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm delete-row">
                                                    <i class="fas fa-trash-can"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <button type="button" class="btn btn-primary rounded-5" id="addGoodRow">
                                <i class="fas fa-plus me-2"></i>إضافة صف جديد
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-start">
                        <button type="submit" class="btn btn-primary fw-bold">حفظ التغييرات</button>
                        <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $('#customer_id').select2({
            placeholder: "ابحث عن إسم العميل...",
            allowClear: true,
            dropdownParent: $('#editPolicyModal')
        });

        $('#editPolicyModal').on('shown.bs.modal', function () {
            $('#driver_id').select2({
                placeholder: "ابحث عن إسم السائق...",
                allowClear: true,
                dropdownParent: $('#editPolicyModal')
            });

            $('#supplier_id').select2({
                placeholder: "ابحث عن إسم المورد...",
                allowClear: true,
                dropdownParent: $('#editPolicyModal')
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

            // Goods Modal Functionality
            let goodIndex = {{ $policy->goods ? count($policy->goods) : 1 }};

            // Add new row
            $('#addGoodRow').on('click', function() {
                const newRow = `
                    <tr>
                        <td>
                            <input type="text" class="form-control" name="goods[${goodIndex}][description]" required>
                        </td>
                        <td>
                            <input type="number" step="0.01" class="form-control text-center" name="goods[${goodIndex}][quantity]" min="0">
                        </td>
                        <td>
                            <input type="number" step="0.01" class="form-control text-center" name="goods[${goodIndex}][weight]" min="0">
                        </td>
                        <td>
                            <textarea class="form-control" name="goods[${goodIndex}][notes]" rows="1"></textarea>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm delete-row">
                                <i class="fas fa-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                `;
                $('#goodsTable tbody').append(newRow);
                goodIndex++;
                updateRowIndexes();
            });

            // Delete row
            $(document).on('click', '.delete-row', function() {
                if ($('#goodsTable tbody tr').length > 1) {
                    $(this).closest('tr').remove();
                    updateRowIndexes();
                } else {
                    alert('يجب وجود صف واحد على الأقل');
                }
            });

            // Update row indexes after add/delete
            function updateRowIndexes() {
                $('#goodsTable tbody tr').each(function(index) {
                    $(this).find('input, textarea').each(function() {
                        const name = $(this).attr('name');
                        if (name) {
                            const newName = name.replace(/goods\[\d+\]/, `goods[${index}]`);
                            $(this).attr('name', newName);
                        }
                    });
                });
            }
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
