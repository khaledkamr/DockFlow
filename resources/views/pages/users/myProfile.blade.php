@extends('layouts.app')

@section('title', 'صفحة المستخدم')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <div class="row align-items-center g-3">
            <div class="col-md-2 text-center">
                <div class="position-relative d-inline-block">
                    <img src="{{ $user->avatar_url ?? asset('img/user-profile.jpg') }}" 
                         alt="صورة المستخدم" 
                         class="rounded-circle shadow-sm border border-2 border-light"
                         style="width: 90px; height: 90px; object-fit: cover;">
                    <span class="position-absolute bottom-0 end-0 translate-middle p-1 bg-success border border-light rounded-circle"
                          title="متصل الآن"></span>
                </div>
            </div>
            <div class="col-md-7 text-center text-md-start">
                <h4 class="fw-bold mb-0">{{ $user->name }}</h4>
                <p class="text-muted mb-1">{{ $user->email }}</p>
                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-md-start mt-2">
                    <small class="text-muted">
                        <i class="fas fa-calendar-alt me-1 text-primary"></i>
                        عضو منذ: <span class="fw-semibold">{{ $user->created_at->format('Y/m/d') }}</span>
                    </small>
                    <small class="text-muted">
                        <i class="fas fa-clock me-1 text-primary"></i>
                        آخر تحديث: <span class="fw-semibold">{{ $user->updated_at->diffForHumans() }}</span>
                    </small>
                </div>
            </div>
            <div class="col-md-3 text-center">
                <div class="d-flex flex-column align-items-center">
                    <div class="badge bg-primary-subtle text-primary border rounded-pill px-3 py-2">
                        <i class="fas fa-building me-1"></i> {{ $user->company->name }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Personal Information -->
    <div class="col-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center bg-light">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-user me-2 text-primary"></i>
                    المعلومات الشخصية
                </h6>
                <button class="btn btn-link p-0 m-0" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                    <i class="fas fa-edit me-1"></i>
                </button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-4 mb-3">
                        <label class="form-label fw-bold text-muted">الاسم الكامل</label>
                        <p class="form-control-plaintext">{{ $user->name }}</p>
                    </div>
                    <div class="col-4 mb-3">
                        <label class="form-label fw-bold text-muted">البريد الإلكتروني</label>
                        <p class="form-control-plaintext">{{ $user->email }}</p>
                    </div>
                    <div class="col-4 mb-3">
                        <label class="form-label fw-bold text-muted">الصلاحية</label>
                        <p class="form-control-plaintext">
                            {{ $user->roles->first()->name }}
                        </p>
                    </div>
                    <div class="col-4 mb-3">
                        <label class="form-label fw-bold text-muted">رقم الهاتف</label>
                        <p class="form-control-plaintext">
                            {{ $user->phone ?? 'غير محدد' }}
                        </p>
                    </div>
                    <div class="col-4 mb-3">
                        <label class="form-label fw-bold text-muted">الجنسية</label>
                        <p class="form-control-plaintext">
                            {{ $user->nationality ?? 'غير محددة' }}
                        </p>
                    </div>
                    <div class="col-4 mb-3">
                        <label class="form-label fw-bold text-muted">رقم الإقامة</label>
                        <p class="form-control-plaintext">
                            {{ $user->NID ?? 'غير محدد' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Information -->
    <div class="col-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="my-1 fw-bold">
                    <i class="fas fa-bolt me-2 text-primary"></i>
                    إجراءات سريعة
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-4">
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="fas fa-edit me-2"></i>
                        تعديل الملف الشخصي
                    </button>
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                        <i class="fas fa-key me-2"></i>
                        تغيير كلمة المرور
                    </button>
                    <button class="btn btn-outline-primary mb-2">
                        <i class="fas fa-download me-2"></i>
                        تصدير البيانات
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Account Permissions -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-light">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-shield-alt me-2 text-primary"></i>
                    صلاحيات المستخدم
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach($permissions as $permission)
                        @php
                            $hasPermission = $user->hasPermission($permission->name);
                        @endphp
                        <div class="col-auto">
                            <span class="permission-badge {{ $hasPermission ? 'has' : 'not' }}">
                                <i class="fas {{ $hasPermission ? 'fa-check' : 'fa-times' }} me-1"></i>
                                {{ $permission->name }}
                            </span>
                        </div>
                    @endforeach
                </div>
                @if(count($user->permissions()) === 0)
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-info-circle me-2"></i>
                        لا توجد صلاحيات محددة لهذا المستخدم
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center">
                    <i class="fas fa-user-edit text-primary me-2"></i>
                    <h5 class="modal-title fw-bold">تعديل الملف الشخصي</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.user.update', $user) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">الاسم الكامل</label>
                            <input type="text" class="form-control border-primary" name="name" value="{{ $user->name }}" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control border-primary" name="email" value="{{ $user->email }}" required>
                        </div>
                        <div class="col-4 mb-3">
                            <label class="form-label">رقم الهاتف</label>
                            <input type="text" class="form-control border-primary" name="phone" value="{{ $user->phone }}">
                        </div>
                        <div class="col-4 mb-3">
                            <label class="form-label">الجنسية</label>
                            <input type="text" class="form-control border-primary" name="nationality" value="{{ $user->nationality }}">
                        </div>
                        <div class="col-4 mb-3">
                            <label class="form-label">رقم الإقامة</label>
                            <input type="text" class="form-control border-primary" name="NID" value="{{ $user->NID }}">
                        </div>
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

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center">
                    <i class="fas fa-key text-primary me-2"></i>
                    <h5 class="modal-title fw-bold">تغيير كلمة المرور</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.users.update.password', $user) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">كلمة المرور الجديدة</label>
                        <input type="password" class="form-control border-primary" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تأكيد كلمة المرور الجديدة</label>
                        <input type="password" class="form-control border-primary" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-start">
                    <button type="submit" class="btn btn-primary fw-bold">تغيير كلمة المرور</button>
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.avatar-lg {
    height: 80px;
    width: 80px;
}

.form-control-plaintext {
    border: 1px solid transparent;
    border-radius: 0.375rem;
    padding: 0.375rem 0.75rem;
    background-color: #f8f9fa;
    color: #495057;
}

.card {
    border-radius: 0.5rem;
}

.card-header {
    border-bottom: 1px solid #dee2e6;
    padding: 1rem 1.25rem;
}

.modal-content {
    border-radius: 0.5rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
.permission-badge {
    display: inline-flex;
    align-items: center;
    font-size: 0.85rem;
    font-weight: 500;
    border-radius: 50px;
    padding: 0.35rem 0.75rem;
    transition: all 0.2s ease-in-out;
    cursor: default;
}

.permission-badge.has {
    background-color: #cadfff; 
    color: #0d6efd; 
}

.permission-badge.has:hover {
    background-color: #a2c7ff;
}

.permission-badge.not {
    background-color: #ececec; 
    color: #6c757d; 
}

.permission-badge.not:hover {
    background-color: #dcdcdc;
}
</style>
@endsection