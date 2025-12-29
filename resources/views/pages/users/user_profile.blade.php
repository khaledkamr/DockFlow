@extends('layouts.app')

@section('title', 'صفحة المستخدم')

@section('content')
    <div class="row g-4">
        <!-- Account Information -->
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center border-bottom pb-3">
                    <div class="position-relative d-inline-block mb-3">
                        <img src="{{ $user->avatar_url ?? asset('img/user-profile.jpg') }}" alt="صورة المستخدم"
                            class="rounded-circle shadow-sm border border-2 border-light"
                            style="width: 80px; height: 80px; object-fit: cover;">
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
        <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center bg-light py-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-user me-2 text-primary"></i>
                        المعلومات الشخصية
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-sm-6 col-lg-4">
                            <label class="form-label fw-bold text-muted small">الاسم الكامل</label>
                            <p class="form-control-plaintext">{{ $user->name }}</p>
                        </div>
                        <div class="col-6 col-sm-6 col-lg-4">
                            <label class="form-label fw-bold text-muted small">البريد الإلكتروني</label>
                            <p class="form-control-plaintext text-break">{{ $user->email }}</p>
                        </div>
                        <div class="col-6 col-sm-6 col-lg-4">
                            <label class="form-label fw-bold text-muted small">الصلاحية</label>
                            <p class="form-control-plaintext">
                                {{ $user->roles->first()->name }}
                            </p>
                        </div>
                        <div class="col-6 col-sm-6 col-lg-4">
                            <label class="form-label fw-bold text-muted small">رقم الهاتف</label>
                            <p class="form-control-plaintext">
                                {{ $user->phone ?? 'غير محدد' }}
                            </p>
                        </div>
                        <div class="col-6 col-sm-6 col-lg-4">
                            <label class="form-label fw-bold text-muted small">الجنسية</label>
                            <p class="form-control-plaintext">
                                {{ $user->nationality ?? 'غير محددة' }}
                            </p>
                        </div>
                        <div class="col-6 col-sm-6 col-lg-4">
                            <label class="form-label fw-bold text-muted small">رقم الإقامة</label>
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
                <div class="card-header bg-light py-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-shield-alt me-2 text-primary"></i>
                        صلاحيات المستخدم
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($permissions as $permission)
                            @php
                                $hasPermission = $user->hasPermission($permission->name);
                            @endphp
                            <span class="permission-badge {{ $hasPermission ? 'has' : 'not' }}">
                                <i class="fas {{ $hasPermission ? 'fa-check' : 'fa-times' }} me-1"></i>
                                {{ $permission->name }}
                            </span>
                        @endforeach
                    </div>
                    @if (count($user->permissions()) == 0)
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-info-circle me-2"></i>
                            لا توجد صلاحيات محددة لهذا المستخدم
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-light py-3">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-history me-2 text-primary"></i>
                        آخر نشاط
                    </h6>
                </div>
                <div class="card-body">
                    @if($activities && $activities->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-primary">
                                    <tr>
                                        <th class="text-center small">النشاط</th>
                                        <th class="text-center small">التفاصيل</th>
                                        <th class="text-center small">التاريخ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activities->take(10) as $activity)
                                        <tr>
                                            <td>
                                                <i class="fas fa-circle me-2" style="font-size: 0.5rem; color: #0d6efd;"></i>
                                                {{ $activity->action }}
                                            </td>
                                            <td class="text-center text-muted small">{{ $activity->description ?? '-' }}</td>
                                            <td class="text-center text-muted small">{{ $activity->created_at->diffForHumans() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-info-circle me-2"></i>
                            لا يوجد نشاط مسجل لهذا المستخدم
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .form-control-plaintext {
            border: 1px solid transparent;
            border-radius: 0.375rem;
            padding: 0.5rem 0.75rem;
            background-color: #f8f9fa;
            color: #495057;
            min-height: 38px;
            word-wrap: break-word;
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
