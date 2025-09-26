@extends('layouts.app')

@section('title', 'بيانات الشركة')

@section('content')
<h1 class="mb-4">بيانات الشركة</h1>

<div class="card border-0 shadow-sm bg-white p-4">
    <form action="{{ route('company.update', $company) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-4 bg-light p-3 rounded">
            <div class="row mb-4">
                <div class="col">
                    <label class="form-label">إسم الشركة</label>
                    <input type="text" name="name" class="form-control border-primary" value="{{ $company->name }}">
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col">
                    <label class="form-label">إسم الفرع</label>
                    <input type="text" name="branch" class="form-control border-primary" value="{{ $company->branch }}">
                    @error('branch')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row mb-4">
                <div class="col">
                    <label class="form-label">رقم السجل الضريبي</label>
                    <input type="text" name="CR" class="form-control border-primary" value="{{ $company->CR }}">
                    @error('CR')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col">
                    <label class="form-label">رقم السجل التجاري</label>
                    <input type="text" name="TIN" class="form-control border-primary" value="{{ $company->TIN }}">
                    @error('TIN')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col">
                    <label class="form-label">العنوان الوطني</label>
                    <input type="text" name="national_address" class="form-control border-primary" value="{{ $company->national_address }}">
                    @error('national_address')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
           <div class="row mb-4">
                <div class="col">
                    <label class="form-label">البريد الإلكتروني</label>
                    <input type="text" name="email" class="form-control border-primary" value="{{ $company->email }}">
                    @error('email')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col">
                    <label class="form-label">رقم التواصل</label>
                    <input type="text" name="phone" class="form-control border-primary" value="{{ $company->phone }}">
                    @error('phone')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
           </div>
        </div>
        
        <button type="submit" class="btn btn-primary fw-bold" id="submit-btn">
            حفظ البيانات
        </button>
    </form>
</div>

@endsection