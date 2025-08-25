@extends('layouts.admin')

@section('title', 'إضافة إتفاقية')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<h2 class="mb-4">إضافة إتفاقية تخزين</h2>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 bg-white p-4 rounded-3 shadow-sm">
    <form action="{{ route('policies.storage.store') }}" method="POST">
        @csrf
        <input type="hidden" name="date" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
        <input type="hidden" name="type" value="تخزين">
        <div class="row mb-3">
            <div class="col">
                <label class="form-label">إســم الشركة</label>
                <input type="text" name="company_name" class="form-control border-primary" value="{{ $company->name }}">
            </div>
            <div class="col">
                <label class="form-label">رقــم الشركة</label>
                <input type="text" class="form-control border-primary" id="company_id" name="company_id" value="{{ $company->id }}" readonly>
                @error('company_id')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col">
                <label class="form-label">إســم العميــل</label>
                <select class="form-select border-primary" id="customer_name">
                    <option value="">اختر اسم العميل...</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" data-id="{{ $customer->id }}" 
                            data-contract="{{ $customer->contract ? $customer->contract->id : null }}" 
                            data-storage-period="{{ $customer->contract ? $customer->contract->container_storage_period : null }}"
                            data-storage-price="{{ $customer->contract ? $customer->contract->container_storage_price : null }}"
                            data-late-fee="{{ $customer->contract ? $customer->contract->late_fee : null }}">
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <input type="hidden" id="contract_id" name="contract_id">
            <div class="col">
                <label class="form-label">رقــم العميــل</label>
                <input type="text" class="form-control border-primary" id="customer_id" name="customer_id" value="" readonly>
                @error('customer_id')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label class="form-label">إســم السائق</label>
                <input type="text" name="driver_name" class="form-control border-primary">
                @error('driver_name')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col">
                <label class="form-label">رقــم هوية السائق</label>
                <input type="text" class="form-control border-primary" name="driver_NID">
                @error('driver_NID')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col">
                <label class="form-label">نوع السيارة</label>
                <input type="text" class="form-control border-primary" name="driver_car">
                @error('driver_car')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col">
                <label class="form-label">لوحة السيارة</label>
                <input type="text" class="form-control border-primary" name="car_code">
                @error('car_code')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label id="storage_period" class="form-label">سعر تخزين الحاوية</label>
                <input type="text" class="form-control border-primary" id="storage_price" name="storage_price">
                @error('storage_price')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col">
                <label for="late_fee" class="form-label">غرامة التأخير (للــيوم الواحــد)</label>
                <input type="text" class="form-control border-primary" id="late_fee" name="late_fee" value="{{ old('late_fee') }}">
                @error('late_fee')
                <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
            <div class="col">
                <label for="tax" class="form-label">الضريبة</label>
                <select class="form-select border-primary" id="tax" name="tax">
                    <option value="غير معفي" {{ old('tax') == 'غير معفي' ? 'selected' : '' }}>غير معفي</option>
                    <option value="معفي" {{ old('tax') == 'معفي' ? 'selected' : '' }}>معفي</option>
                </select>
                @error('tax')
                    <div class="text-danger">{{ $message }}</div>
                @endif
            </div>
        </div>

        <button type="submit" class="btn btn-primary fw-bold">حفظ الإتفاقية</button>
    </form>
</div>

<script>
    $('#customer_name').select2({
        placeholder: "ابحث عن إسم العميل...",
        allowClear: true
    });

    $('#customer_name').on('change', function () {
        let id = $(this).find(':selected').data('id');
        $('#customer_id').val(id || '');
        let contract = $(this).find(':selected').data('contract');
        $('#contract_id').val(contract || '');
        let storage_period = $(this).find(':selected').data('storage-period');
        $('#storage_period').html(`سعر التخزين لمدة ${storage_period} أيام` || 'سعر التخزين');
        let storage_price = $(this).find(':selected').data('storage-price');
        // if(storage_price == 0) { storage_price = 'مجاناً' }
        $('#storage_price').val(`${storage_price}` || '');
        let late_fee = $(this).find(':selected').data('late-fee');
        $('#late_fee').val(`${late_fee}` || '');
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
    /* .select2-container .select2-selection__arrow {
        height: 100%; /* يخلي السهم في النص */
    } */
</style>

@endsection