@extends('layouts.app')

@section('title', 'صفحة المستخدم')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Profile Header -->
        <div class="col-12">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-2 text-center">
                            <div class="avatar-lg mx-auto mb-3 mb-md-0">
                                <div class="avatar-title bg-primary rounded-circle display-4 text-white">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <h4 class="mb-1 fw-bold">{{ auth()->user()->name }}</h4>
                            <p class="text-muted mb-2">{{ auth()->user()->email }}</p>
                            <div class="row">
                                <div class="col-sm-6">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        عضو منذ: {{ auth()->user()->created_at->format('Y/m/d') }}
                                    </small>
                                </div>
                                <div class="col-sm-6">
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i>
                                        آخر تحديث: {{ auth()->user()->updated_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 text-center">
                            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                                <i class="fas fa-edit me-1"></i>
                                تعديل الملف الشخصي
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Personal Information -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-user me-2 text-primary"></i>
                        المعلومات الشخصية
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">الاسم الكامل</label>
                            <p class="form-control-plaintext">{{ auth()->user()->name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">البريد الإلكتروني</label>
                            <p class="form-control-plaintext">
                                {{ auth()->user()->email }}
                                @if(auth()->user()->email_verified_at)
                                    <span class="badge bg-success ms-2">موثق</span>
                                @else
                                    <span class="badge bg-warning ms-2">غير موثق</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">رقم الهاتف</label>
                            <p class="form-control-plaintext">
                                {{ auth()->user()->phone ?? 'غير محدد' }}
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">الجنسية</label>
                            <p class="form-control-plaintext">
                                {{ auth()->user()->nationality ?? 'غير محددة' }}
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">الرقم القومي</label>
                            <p class="form-control-plaintext">
                                {{ auth()->user()->NID ?? 'غير محدد' }}
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-muted">معرف فريد (UUID)</label>
                            <p class="form-control-plaintext">
                                <code class="text-muted">{{ auth()->user()->uuid }}</code>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-cog me-2 text-primary"></i>
                        معلومات الحساب
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">معرف المستخدم</label>
                        <p class="form-control-plaintext">#{{ auth()->user()->id }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">معرف الشركة</label>
                        <p class="form-control-plaintext">{{ auth()->user()->company_id }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">تاريخ الإنشاء</label>
                        <p class="form-control-plaintext">
                            {{ auth()->user()->created_at->format('Y/m/d H:i') }}
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">حالة البريد الإلكتروني</label>
                        <p class="form-control-plaintext">
                            @if(auth()->user()->email_verified_at)
                                <span class="badge bg-success">موثق في {{ auth()->user()->email_verified_at->format('Y/m/d') }}</span>
                            @else
                                <span class="badge bg-warning">غير موثق</span>
                                <br>
                                <a href="#" class="btn btn-link btn-sm p-0 mt-1">إرسال رابط التوثيق</a>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-bolt me-2 text-primary"></i>
                        إجراءات سريعة
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            <i class="fas fa-key me-1"></i>
                            تغيير كلمة المرور
                        </button>
                        @if(!auth()->user()->email_verified_at)
                        <button class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-envelope me-1"></i>
                            توثيق البريد الإلكتروني
                        </button>
                        @endif
                        <button class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-download me-1"></i>
                            تحميل بيانات الحساب
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تعديل الملف الشخصي</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الاسم الكامل *</label>
                            <input type="text" class="form-control" name="name" value="{{ auth()->user()->name }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">البريد الإلكتروني *</label>
                            <input type="email" class="form-control" name="email" value="{{ auth()->user()->email }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">رقم الهاتف</label>
                            <input type="text" class="form-control" name="phone" value="{{ auth()->user()->phone }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الجنسية</label>
                            <input type="text" class="form-control" name="nationality" value="{{ auth()->user()->nationality }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الرقم القومي</label>
                            <input type="text" class="form-control" name="NID" value="{{ auth()->user()->NID }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تغيير كلمة المرور</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">كلمة المرور الحالية *</label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">كلمة المرور الجديدة *</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تأكيد كلمة المرور الجديدة *</label>
                        <input type="password" class="form-control" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">تغيير كلمة المرور</button>
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

.avatar-title {
    align-items: center;
    display: flex;
    font-weight: 500;
    height: 100%;
    justify-content: center;
    width: 100%;
}

.form-control-plaintext {
    border: 1px solid transparent;
    border-radius: 0.375rem;
    padding: 0.375rem 0.75rem;
    background-color: #f8f9fa;
    color: #495057;
}

.card {
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
}

.card-header {
    border-bottom: 1px solid #dee2e6;
    padding: 1rem 1.25rem;
}

.badge {
    font-size: 0.75em;
}

.btn-outline-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.modal-content {
    border-radius: 0.5rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
</style>
@endsection