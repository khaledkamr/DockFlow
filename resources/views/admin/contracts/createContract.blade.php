@extends('layouts.admin')

@section('title', 'إضافة عقد')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<h2 class="mb-4">إضافة عقد جديد</h2>

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


<div class="card border-0 shadow-sm bg-white p-4">
    <form action="{{ route('contracts.store') }}" method="POST">
        @csrf
        <input type="hidden" name="start_date" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
        <input type="hidden" name="end_date" value="{{ Carbon\Carbon::now()->addMonths(3)->format('Y-m-d') }}">
        <h5 class="mb-3">بيانات الشركة</h5>
        <div class="mb-4 bg-light p-3 rounded">
            <div class="row mb-4">
                <div class="col">
                    <label class="form-label">إسم الشركة</label>
                    <input type="text" class="form-control border-primary" value="{{ $company->name }}" readonly>
                    <input type="hidden" name="company_id" value="{{ $company->id }}">
                </div>
                <div class="col">
                    <label class="form-label">رقم السجل الضريبي</label>
                    <input type="text" class="form-control border-primary" value="{{ $company->CR }}" readonly>
                </div>
                <div class="col">
                    <label class="form-label">رقم السجل التجاري</label>
                    <input type="text" class="form-control border-primary" value="{{ $company->TIN }}" readonly>
                </div>
                <div class="col">
                    <label class="form-label">العنوان الوطني</label>
                    <input type="text" class="form-control border-primary" value="{{ $company->national_address }}" readonly>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="company_representative" class="form-label">إسم الممثل</label>
                    <input type="text" class="form-control border-primary" name="company_representative" value="{{ old('company_representative') }}">
                </div>
                <div class="col">
                    <label for="company_representative_nationality" class="form-label">الجنسية</label>
                    <input type="text" class="form-control border-primary" name="company_representative_nationality" value="{{ old('company_representative_nationality') }}">
                </div>
                <div class="col">
                    <label for="company_representative_NID" class="form-label">الهوية الوطنية</label>
                    <input type="text" class="form-control border-primary" name="company_representative_NID" value="{{ old('company_representative_NID') }}">
                </div>
                <div class="col">
                    <label for="company_representative_role" class="form-label">بصفته</label>
                    <input type="text" class="form-control border-primary" name="company_representative_role" value="{{ old('company_representative_role') }}">
                </div>
            </div>
        </div>
        <h5 class="mb-3">بيانات العميل</h5>
        <div class="mb-4 bg-light p-3 rounded">
            <div class="row mb-4">
                <div class="col">
                    <label class="mb-2">اسم الشركة</label>
                    <select id="customer_name" class="form-select border-primary" style="width:100%;">
                        <option value="">-- اختر الحساب --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" data-id="{{ $customer->id }}" data-cr="{{ $customer->CR }}" data-tin="{{ $customer->TIN }}" data-add="{{ $customer->national_address }}">
                                {{ $customer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" name="customer_id" id="customer_id" value="">
                <div class="col">
                    <label class="form-label">رقم السجل الضريبي</label>
                    <input type="text" class="form-control border-primary" id="customer_CR" value="">
                </div>
                <div class="col">
                    <label class="form-label">رقم السجل التجاري</label>
                    <input type="text" class="form-control border-primary" id="customer_TIN" value="">
                </div>
                <div class="col">
                    <label class="form-label">العنوان الوطني</label>
                    <input type="text" class="form-control border-primary" id="customer_national_address" value="">
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="customer_representative" class="form-label">إسم الممثل</label>
                    <input type="text" class="form-control border-primary" name="customer_representative" value="{{ old('customer_representative') }}">
                </div>
                <div class="col">
                    <label for="customer_representative_nationality" class="form-label">الجنسية</label>
                    <input type="text" class="form-control border-primary" name="customer_representative_nationality" value="{{ old('customer_representative_nationality') }}">
                </div>
                <div class="col">
                    <label for="customer_representative_NID" class="form-label">الهوية الوطنية</label>
                    <input type="text" class="form-control border-primary" name="customer_representative_NID" value="{{ old('customer_representative_NID') }}">
                </div>
                <div class="col">
                    <label for="customer_representative_role" class="form-label">بصفته</label>
                    <input type="text" class="form-control border-primary" name="customer_representative_role" value="{{ old('customer_representative_role') }}">
                </div>
            </div>
        </div>
        <h5 class="mb-4">الخدمات والأسعار</h5>
        <div class="mb-4 bg-light p-3 rounded">
            <h6 class="mb-0 text-primary">خدمة #<span class="container-number">1</span></h6>
            <div class="row">
                <div class="col-8">
                    <label for="service_one" class="form-label">الوصف</label>
                    <input type="text" class="form-control border-primary" name="service_one" value="خدمة تخزين الحاوية الواحدة في ساحتنا" readonly>
                </div>
                <div class="col">
                    <label for="container_storage_price" class="form-label">السعر</label>
                    <input type="number" class="form-control border-primary" name="container_storage_price" value="{{ old('container_storage_price') }}">
                </div>
                <div class="col">
                    <label for="container_storage_period" class="form-label">عدد الأيام</label>
                    <input type="text" class="form-control border-primary" name="container_storage_period" value="{{ old('container_storage_period') }}">
                </div>
            </div>
        </div>
        <div class="mb-4 bg-light p-3 rounded">
            <h6 class="mb-0 text-primary">خدمة #<span class="container-number">2</span></h6>
            <div class="row">
                <div class="col-8">
                    <label for="service_two" class="form-label">الوصف</label>
                    <input type="text" class="form-control border-primary" name="service_two" value="خدمة تنزيل وتحميل الحاوية بالكرين لدينا بالساحة" readonly>
                </div>
                <div class="col">
                    <label for="move_container_price" class="form-label">السعر</label>
                    <input type="number" class="form-control border-primary" name="move_container_price" value="{{ old('move_container_price') }}">
                </div>
                <div class="col">
                    <label for="move_container_count" class="form-label">عدد الحاويات</label>
                    <input type="text" class="form-control border-primary" name="move_container_count" value="للحاوية الواحدة" readonly>
                </div>
            </div>
        </div>
        <div class="mb-4 bg-light p-3 rounded">
            <h6 class="mb-0 text-primary">خدمة #<span class="container-number">3</span></h6>
            <div class="row">
                <div class="col-8">
                    <label for="service_three" class="form-label">الوصف</label>
                    <input type="text" class="form-control border-primary" name="service_three" value="خدمة تخزين الحاوية بعد المدة المتفق عليها" readonly>
                </div>
                <div class="col">
                    <label for="late_fee" class="form-label">السعر</label>
                    <input type="number" class="form-control border-primary" name="late_fee" value="{{ old('late_fee') }}">
                </div>
                <div class="col">
                    <label for="late_fee_period" class="form-label">عدد الأيام</label>
                    <input type="text" class="form-control border-primary" name="late_fee_period" value="لليوم الواحد" readonly>
                </div>
            </div>
        </div>
        <div class="mb-4 bg-light p-3 rounded">
            <h6 class="mb-0 text-primary">خدمة #<span class="container-number">4</span></h6>
            <div class="row">
                <div class="col-8">
                    <label for="service_four" class="form-label">الوصف</label>
                    <input type="text" class="form-control border-primary" name="service_four" value="خدمة تبديل الحاوية من شاحنة الى شاحنة" readonly>
                </div>
                <div class="col">
                    <label for="exchange_container_price" class="form-label">السعر</label>
                    <input type="number" class="form-control border-primary" name="exchange_container_price" value="{{ old('exchange_container_price') }}">
                </div>
                <div class="col">
                    <label for="exchange_container_count" class="form-label">عدد الحاويات</label>
                    <input type="text" class="form-control border-primary" name="exchange_container_count" value="للحاوية الواحدة" readonly>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary fw-bold">
            حفظ العقد
        </button>
    </form>
</div>

<script>
    $('#customer_name').select2({
        placeholder: "ابحث عن الشركة...",
        allowClear: true
    });

    $('#customer_name').on('change', function () {
        let cr = $(this).find(':selected').data('cr');
        $('#customer_CR').val(cr || '');
        let tin = $(this).find(':selected').data('tin');
        $('#customer_TIN').val(tin || '');
        let add = $(this).find(':selected').data('add');
        $('#customer_national_address').val(add || '');
        let id = $(this).find(':selected').data('id');
        $('#customer_id').val(id || '');
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