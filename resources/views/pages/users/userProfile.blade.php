@extends('layouts.app')

@section('title', 'صفحة المستخدم')

@section('content')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-2 text-center">
                <div class="avatar-lg mx-auto mb-3 mb-md-0">
                    <img src="{{ $user->avatar_url ?? asset('img/user-profile.jpg') }}" 
                            alt="صورة المستخدم" 
                            class="rounded-circle img-fluid"
                            style="width: 80px; height: 80px; object-fit: cover;">
                </div>
            </div>
            <div class="col-md-8">
                <h4 class="mb-1 fw-bold">{{ $user->name }}</h4>
                <p class="text-muted mb-2">{{ $user->email }}</p>
                <div class="row">
                    <div class="col-sm-6">
                        <small class="text-muted">
                            <i class="fas fa-calendar-alt me-1"></i>
                            عضو منذ: {{ $user->created_at->format('Y/m/d') }}
                        </small>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            آخر تحديث: {{ $user->updated_at->diffForHumans() }}
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-md-2 text-center">
                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                    <i class="fas fa-key me-1"></i>
                    تغير كلمة السر
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Personal Information -->
    <div class="col-lg-8">
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
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted">الاسم الكامل</label>
                        <p class="form-control-plaintext">{{ $user->name }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted">البريد الإلكتروني</label>
                        <p class="form-control-plaintext">{{ $user->email }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted">رقم الهاتف</label>
                        <p class="form-control-plaintext">
                            {{ $user->phone ?? 'غير محدد' }}
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted">الجنسية</label>
                        <p class="form-control-plaintext">
                            {{ $user->nationality ?? 'غير محددة' }}
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted">رقم الإقامة</label>
                        <p class="form-control-plaintext">
                            {{ $user->NID ?? 'غير محدد' }}
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold text-muted">الصلاحية</label>
                        <p class="form-control-plaintext">
                            {{ $user->roles->first()->name }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Account Information -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="my-1 fw-bold">
                    <i class="fas fa-cog me-2 text-primary"></i>
                    معلومات الحساب
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted">معرف المستخدم</label>
                    <p class="form-control-plaintext">#{{ $user->id }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted">الشركة</label>
                    <p class="form-control-plaintext">{{ $user->company->name }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted">تاريخ الإنشاء</label>
                    <p class="form-control-plaintext">
                        {{ $user->created_at->format('Y/m/d H:i') }}
                    </p>
                </div>
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
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الاسم الكامل</label>
                            <input type="text" class="form-control border-primary" name="name" value="{{ $user->name }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control border-primary" name="email" value="{{ $user->email }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">رقم الهاتف</label>
                            <input type="text" class="form-control border-primary" name="phone" value="{{ $user->phone }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الجنسية</label>
                            <input type="text" class="form-control border-primary" name="nationality" value="{{ $user->nationality }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">رقم الإقامة</label>
                            <input type="text" class="form-control border-primary" name="NID" value="{{ $user->NID }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الصلاحية</label>
                            <select class="form-select border-primary" name="role" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}" {{ $user->roles->first()->id === $role->id ? 'selected' : '' }}>
                                        {{ $role->name }}
                                    </option>
                                @endforeach
                            </select>
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
</style>
@endsection