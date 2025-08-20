@extends('layouts.admin')

@section('title', 'إضافة عقد')

@section('content')

<h2 class="mb-4">إضافة عقد جديد</h2>

<div class="card border-0 shadow-sm bg-white p-4">
    <form action="" method="POST">
        <h5 class="mb-3">بيانات الشركة</h5>
        <div class="mb-4 bg-light p-3 rounded">
            <div class="row mb-4">
                <div class="col">
                    <label for="company_name" class="form-label">إسم الشركة</label>
                    <input type="text" class="form-control border-primary" name="company_name" value="شركة تاج الأعمال للخدمات اللوجستية" readonly>
                </div>
                <div class="col">
                    <label for="CR" class="form-label">رقم السجل الضريبي</label>
                    <input type="text" class="form-control border-primary" name="CR" value="302259535900003" readonly>
                </div>
                <div class="col">
                    <label for="TIN" class="form-label">رقم السجل التجاري</label>
                    <input type="text" class="form-control border-primary" name="TIN" value="2050119977" readonly>
                </div>
                <div class="col">
                    <label for="national_address" class="form-label">العنوان الوطني</label>
                    <input type="text" class="form-control border-primary" name="national_address" value="طريق الظهران الجبيل - 8348" readonly>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col">
                    <label for="company_name" class="form-label">إسم الممثل</label>
                    <input type="text" class="form-control border-primary" name="company_name" value="شركة تاج الأعمال للخدمات اللوجستية" readonly>
                </div>
                <div class="col">
                    <label for="TIN" class="form-label">الجنسية</label>
                    <input type="text" class="form-control border-primary" name="TIN" value="2050119977" readonly>
                </div>
                <div class="col">
                    <label for="CR" class="form-label">الهوية الوطنية</label>
                    <input type="text" class="form-control border-primary" name="CR" value="302259535900003" readonly>
                </div>
            </div>
        </div>
        <h5 class="mb-3">بيانات العميل</h5>
        <div class="mb-4 bg-light p-3 rounded">
            <div class="row mb-4">
                <div class="col">
                    <label for="company_name" class="form-label">إسم الشركة</label>
                    <input type="text" class="form-control border-primary" name="company_name" value="شركة تاج الأعمال للخدمات اللوجستية" readonly>
                </div>
                <div class="col">
                    <label for="CR" class="form-label">رقم السجل الضريبي</label>
                    <input type="text" class="form-control border-primary" name="CR" value="302259535900003" readonly>
                </div>
                <div class="col">
                    <label for="TIN" class="form-label">رقم السجل التجاري</label>
                    <input type="text" class="form-control border-primary" name="TIN" value="2050119977" readonly>
                </div>
                <div class="col">
                    <label for="national_address" class="form-label">العنوان الوطني</label>
                    <input type="text" class="form-control border-primary" name="national_address" value="طريق الظهران الجبيل - 8348" readonly>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col">
                    <label for="company_name" class="form-label">إسم الممثل</label>
                    <input type="text" class="form-control border-primary" name="company_name" value="شركة تاج الأعمال للخدمات اللوجستية" readonly>
                </div>
                <div class="col">
                    <label for="TIN" class="form-label">الجنسية</label>
                    <input type="text" class="form-control border-primary" name="TIN" value="2050119977" readonly>
                </div>
                <div class="col">
                    <label for="CR" class="form-label">الهوية الوطنية</label>
                    <input type="text" class="form-control border-primary" name="CR" value="302259535900003" readonly>
                </div>
            </div>
        </div>
        <h5 class="mb-4">الخدمات والأسعار</h5>
        <div class="mb-4 bg-light p-3 rounded">

        </div>
    </form>
</div>

@endsection