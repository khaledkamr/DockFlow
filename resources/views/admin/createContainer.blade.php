@extends('layouts.admin')

@section('title', 'إضافة حاوية جديدة')

@section('content')
<h2 class="mb-5">إضافة حاوية جديدة</h2>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="" method="GET" class="row mb-3">
    <div class="col-4">
        <label for="user_id" class="form-label">إســم العميــل</label>
        <select class="form-select" id="user_id" name="user_id" onchange="this.form.submit()">
            <option value="">اختر اسم العميل...</option>
            @foreach ($users as $user)
                <option value="{{ $user->id }}" {{ request()->query('user_id') == $user->id ? 'selected' : '' }}>
                    {{ $user->name }}
                </option>
            @endforeach
        </select>
    </div>
</form>

<form action="{{ route('admin.yard.containers.create') }}" method="POST">
    @csrf
    <div class="row mb-3">
        <div class="col">
            <label for="user_id" class="form-label">رقــم العميــل</label>
            <input type="text" class="form-control" id="user_id" name="user_id" value="{{ $client['id'] }}" required>
            @error('user_id')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
        <div class="col">
            <label for="NID" class="form-label">رقم الهويــة الوطنيــة</label>
            <input type="text" class="form-control" id="NID" name="NID" value="{{ $client['NID'] }}" required>
            @error('NID')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
        <div class="col">
            <label for="phone" class="form-label">رقم الهاتــف</label>
            <input type="text" class="form-control" id="phone" name="phone" value="{{ $client['phone'] }}" required>
            @error('phone')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <label for="code" class="form-label">كــود الحاويــة</label>
            <input type="text" class="form-control" id="code" name="code" value="{{ old('code') }}" required>
            @error('code')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
        <div class="col">
            <label for="container_type_id" class="form-label">فئة الحاويــة</label>
            <select class="form-select" id="container_type_id" name="container_type_id" required>
                @foreach ($containerTypes as $type)
                    <option value="{{ $type->id }}" {{ old('container_type_id') == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                    </option>
                @endforeach
            </select>
            @error('container_type_id')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <label for="status" class="form-label">الحالــة</label>
            <select class="form-select" id="status" name="status" required>
                <option value="في الإنتظار" {{ old('status') == 'في الإنتظار' ? 'selected' : '' }}>في الإنتظار</option>
                <option value="موجود" {{ old('status') == 'موحود' ? 'selected' : '' }}>موجود</option>
                <option value="غير متوفر" {{ old('status') == 'غير متوفر' ? 'selected' : '' }}>غير متوفر</option>
            </select>
            @error('status')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
        <div class="col">
            <label for="location" class="form-label">الموقــع</label>
            <input type="text" class="form-control" id="location" name="location" value="{{ old('location') }}" required>
            @error('location')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
    </div>

    <button type="submit" class="btn btn-1 fw-bold mt-2">إضافة الحاويــة</button>
</form>
@endsection