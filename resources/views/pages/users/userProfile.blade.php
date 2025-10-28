@extends('layouts.app')

@section('title', 'صفحة المستخدم')

@section('content')
<div class="row">
    <!-- Account Information -->
    <div class="col-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center border-bottom pb-3">
                <div class="position-relative d-inline-block mb-2">
                    <img src="{{ $user->avatar_url ?? asset('img/user-profile.jpg') }}" 
                        alt="صورة المستخدم" 
                        class="rounded-circle shadow-sm border border-2 border-light"
                        style="width: 90px; height: 90px; object-fit: cover;">
                </div>

                <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                <p class="text-muted small mb-0">{{ $user->email }}</p>
            </div>
            <div class="card-body rounded-3 pt-3 pb-2 bg-light">
                <div class="d-flex flex-column gap-2 text-center text-md-start">
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
        </div>
    </div>

    <!-- Personal Information -->
    <div class="col-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center bg-light py-3">
                <h6 class="mb-0 fw-bold">
                    <i class="fas fa-user me-2 text-primary"></i>
                    المعلومات الشخصية
                </h6>
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