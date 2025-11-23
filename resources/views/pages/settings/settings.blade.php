@extends('layouts.app')

@section('title', 'الإعدادات')

@section('content')
<h1 class="mb-4">الإعدادات</h1>

<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header">
                <h5 class="fw-bold mb-1">إعدادات النظام</h5>
            </div>
            <div class="card-body">
                <div class="setting-item d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6>الإشعارات</h6>
                        <small class="text-muted">تفعيل إشعارات النظام</small>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="notifications" checked>
                    </div>
                </div>

                <div class="setting-item d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6>المنطقة الزمنية</h6>
                        <small class="text-muted">اختر المنطقة الزمنية المفضلة</small>
                    </div>
                    <div>
                        <form action="{{ route('settings.timezone.update') }}" method="POST">
                            @csrf
                            <select name="timezone" class="form-select border-primary" onchange="this.form.submit()">
                                @foreach(['Africa/Cairo', 'Asia/Riyadh'] as $timezone)
                                    <option value="{{ $timezone }}" {{ auth()->user()->timezone == $timezone ? 'selected' : '' }}>
                                        {{ $timezone }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>

                <div class="setting-item d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6>منع اذن الخروج للعملاء المديونين</h6>
                        <small class="text-muted">منع إذن الخروج لاخر حاوية للعملاء الذين لديهم فواتير غير مدفوعة</small>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="inventoryTracking" checked>
                    </div>
                </div>

                <div class="setting-item d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6>الوضع المظلم</h6>
                        <small class="text-muted">تفعيل المظهر المظلم</small>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="darkMode">
                    </div>
                </div>

                <button class="btn btn-primary">حفظ الإعدادات</button>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header">
                <h5 class="fw-bold mb-1">إعدادات الحساب</h5>
            </div>
            <div class="card-body">
                <form>
                    <div class="mb-3">
                        <label for="userName" class="form-label">اسم المستخدم</label>
                        <input type="text" class="form-control" id="userName" value="{{ auth()->user()->name ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label for="userEmail" class="form-label">البريد الإلكتروني</label>
                        <input type="email" class="form-control" id="userEmail" value="{{ auth()->user()->email ?? '' }}">
                    </div>
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label">كلمة المرور الحالية</label>
                        <input type="password" class="form-control" id="currentPassword">
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">كلمة المرور الجديدة</label>
                        <input type="password" class="form-control" id="newPassword">
                    </div>
                    <button type="submit" class="btn btn-success w-100 mb-3">تحديث البيانات</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.form-check-input').forEach(toggle => {
    toggle.addEventListener('change', function() {
        // Add your toggle handling logic here
        console.log(`${this.id} is now ${this.checked ? 'enabled' : 'disabled'}`);
    });
});
</script>

@endsection