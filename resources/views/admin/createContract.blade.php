@extends('layouts.admin')

@section('title', 'إضافة عقد جديد')

@section('content')
<h2 class="mb-4">إضافة عقد جديد</h2>

@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
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

<form action="" method="POST">
    @csrf
    <div class="row mb-3">
        <div class="col">
            <label for="customer_name" class="form-label">رقــم العميــل</label>
            <input type="text" class="form-control" id="customer_name" name="customer_name" value="{{ $client['id'] }}" required>
            @error('customer_name')
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
            <label for="start_date" class="form-label">تاريــخ البدء</label>
            <input type="date" class="form-control" id="start_date" name="start_date" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}" required>
            @error('start_date')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
        <div class="col">
            <label for="expected_end_date" class="form-label">تاريــخ الانتهــاء المتوقــع</label>
            <input type="date" class="form-control" id="expected_end_date" name="expected_end_date" value="{{ old('expected_end_date') }}" required>
            @error('expected_end_date')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <label for="container_type" class="form-label">فئة الحاويــة</label>
            <input type="text" class="form-control" id="container_type" name="container_type" value="كبيــرة" required>
            @error('container_type')
            <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
        <div class="col">
            <label for="container_count" class="form-label">عدد الحاويـات</label>
            <input type="number" class="form-control" id="container_count" name="container_count" value="{{ old('container_count') }}" required>
            @error('container_count')
            <div class="text-danger">{{ $message }}</div>
            @endif
        </div>

        <div class="col">
            <label for="container_type" class="form-label">فئة الحاويــة</label>
            <input type="text" class="form-control" id="container_type" name="container_type" value="متوســطه" required>
            @error('container_type')
            <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
        <div class="col">
            <label for="container_count" class="form-label">عدد الحاويـات</label>
            <input type="number" class="form-control" id="container_count" name="container_count" value="{{ old('container_count') }}" required>
            @error('container_count')
            <div class="text-danger">{{ $message }}</div>
            @endif
        </div>

        <div class="col">
            <label for="container_type" class="form-label">فئة الحاويــة</label>
            <input type="text" class="form-control" id="container_type" name="container_type" value="صغيــرة" required>
            @error('container_type')
            <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
        <div class="col">
            <label for="container_count" class="form-label">عدد الحاويـات</label>
            <input type="number" class="form-control" id="container_count" name="container_count" value="{{ old('container_count') }}" required>
            @error('container_count')
            <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <label for="price" class="form-label">السعر</label>
            <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ old('price') }}" required>
            @error('price')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
        <div class="col">
            <label for="late_fee" class="form-label">غرامة التأخير (للــيوم الواحــد)</label>
            <input type="number" step="0.01" class="form-control" id="late_fee" name="late_fee" value="{{ old('late_fee') }}" required>
            @error('late_fee')
            <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
    </div>

    <button type="submit" class="btn btn-1 fw-bold">إضافة العقد</button>
</form>
@endsection