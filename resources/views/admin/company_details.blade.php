@extends('layouts.admin')

@section('title', 'تفاصيل الشركة')

@section('content')
    <!-- Company Header -->
    <div class="rounded-4 p-4 mb-4 text-white" style="background: var(--gradient);">
        <div class="row align-items-center">
            <div class="col-auto">
                @if ($company->logo)
                    <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}"
                        class="bg-white rounded-3 p-2 shadow" style="width: 120px; height: 120px; object-fit: contain;">
                @else
                    <div class="bg-white rounded-3 p-2 shadow d-flex align-items-center justify-content-center"
                        style="width: 120px; height: 120px;">
                        <i class="fa-solid fa-building fa-3x text-primary"></i>
                    </div>
                @endif
            </div>
            <div class="col">
                <h1 class="h2 mb-2 fw-bold">{{ $company->name }}</h1>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="badge bg-white bg-opacity-25 rounded-pill px-3 py-2 fw-medium">
                        <i class="fa-solid fa-code-branch me-2"></i>
                        {{ $company->branch }}
                    </span>
                    <span class="badge bg-white bg-opacity-25 rounded-pill px-3 py-2 fw-medium">
                        <i class="fa-solid fa-calendar-alt me-2"></i>
                        تم الإنشاء: {{ $company->created_at->format('Y/m/d') }}
                    </span>
                </div>
            </div>
            <div class="col-12 col-md-auto mt-3 mt-md-0 d-flex flex-column gap-2">
                <button class="btn btn-light fw-bold" type="button" data-bs-toggle="modal"
                    data-bs-target="#editCompanyModal">
                    <i class="fa-solid fa-pen-to-square me-2"></i>
                    تعديل بيانات الشركة
                </button>
                <button class="btn btn-light fw-bold" type="button" data-bs-toggle="modal"
                    data-bs-target="#deleteCompanyModal"
                    onmouseover="this.classList.remove('btn-light'); this.classList.add('btn-danger');" 
                    onmouseout="this.classList.remove('btn-danger'); this.classList.add('btn-light');">
                    <i class="fa-solid fa-trash-can me-2"></i>
                    حذف الشركة
                </button>
            </div>
        </div>
    </div>

    <!-- Edit Company Modal -->
    <div class="modal fade" id="editCompanyModal" tabindex="-1"
        aria-labelledby="editCompanyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white fw-bold"
                        id="editCompanyModalLabel">تعديل بيانات الشركة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.companies.update', $company) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body text-dark">
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label for="name" class="form-label">اسم الشركة</label>
                                <input type="text" class="form-control border-primary"
                                    id="name" name="name"
                                    value="{{ $company->name }}" required>
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="branch" class="form-label">اسم الفرع</label>
                                <input type="text" class="form-control border-primary"
                                    id="branch" name="branch"
                                    value="{{ $company->branch }}" required>
                                @error('branch')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="CR" class="form-label">السجل التجاري</label>
                                <input type="text" class="form-control border-primary"
                                    id="CR" name="CR"
                                    value="{{ $company->CR }}">
                                @error('CR')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="vatNumber" class="form-label">الرقم الضريبي</label>
                                <input type="text" class="form-control border-primary"
                                    id="vatNumber" name="vatNumber"
                                    value="{{ $company->vatNumber }}">
                                @error('vatNumber')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <input type="email" class="form-control border-primary"
                                    id="email" name="email"
                                    value="{{ $company->email }}">
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="phone" class="form-label">رقم الهاتف</label>
                                <input type="text" class="form-control border-primary"
                                    id="phone" name="phone"
                                    value="{{ $company->phone }}">
                                @error('phone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="national_address" class="form-label">العنوان الوطني</label>
                                <input type="text" class="form-control border-primary"
                                    id="national_address" name="national_address"
                                    value="{{ $company->national_address }}">
                                @error('national_address')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="logo" class="form-label">شعار الشركة</label>
                                <div class="d-flex align-items-center mb-2">
                                    @if ($company->logo)
                                        <img src="{{ asset('storage/' . $company->logo) }}"
                                            alt="{{ $company->name }}" class="rounded me-2"
                                            style="width: 40px; height: 40px; object-fit: contain;">
                                    @endif
                                    <input type="file" class="form-control border-primary" id="logo" name="logo" accept="image/*">
                                </div>
                                @error('logo')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div
                        class="modal-footer d-flex flex-column flex-sm-row justify-content-start gap-2">
                        <button type="submit" class="btn btn-primary fw-bold order-2 order-sm-1">حفظ
                            التغييرات</button>
                        <button type="button" class="btn btn-secondary fw-bold order-1 order-sm-2"
                            data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Company Modal -->
    <div class="modal fade" id="deleteCompanyModal" tabindex="-1"
        aria-labelledby="deleteCompanyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title text-white fw-bold fs-6"
                        id="deleteCompanyModalLabel">تأكيد الحذف</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center text-dark px-4 py-4">
                    هل أنت متأكد من حذف الشركة <strong>{{ $company->name }}</strong>؟
                </div>
                <div class="alert alert-danger mx-3 mb-3 d-flex align-items-center fw-bold" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                    <small>تنبيه: سيتم حذف جميع البيانات المرتبطة بهذه الشركة نهائياً.</small>
                </div>
                <div class="modal-footer d-flex flex-column flex-sm-row justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary fw-bold order-1"
                        data-bs-dismiss="modal">إلغاء</button>
                    <form action="{{ route('admin.companies.delete', $company) }}" method="POST" class="order-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger fw-bold">حذف</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Company Setup Section -->
    @if (!$setupCompleted)
        <div class="card border-0 shadow-sm rounded-3 mb-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div
                        class="bg-warning bg-opacity-10 text-warning rounded-3 p-2 d-flex align-items-center justify-content-center me-3">
                        <i class="fa-solid fa-tasks fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1">إعداد الشركة</h5>
                        <small class="text-muted">أكمل الخطوات التالية لتجهيز الشركة للعمل</small>
                    </div>
                    <div class="ms-auto">
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                            {{ collect($setupStatus)->filter()->count() }} / {{ count($setupStatus) }} مكتمل
                        </span>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="progress mb-4" style="height: 8px;">
                    <div class="progress-bar bg-success" role="progressbar"
                        style="width: {{ (collect($setupStatus)->filter()->count() / count($setupStatus)) * 100 }}%">
                    </div>
                </div>

                <div class="row g-3">
                    <!-- Primary Accounts Setup -->
                    <div class="col-12 col-md-6 col-lg-4">
                        <div
                            class="card h-100 border {{ $setupStatus['accounts'] ? 'border-success bg-success bg-opacity-10' : 'border-warning' }} rounded-3">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start">
                                    <div class="rounded-circle p-2 d-flex align-items-center justify-content-center me-3 {{ $setupStatus['accounts'] ? 'bg-success text-white' : 'bg-warning bg-opacity-25 text-warning' }}"
                                        style="width: 40px; height: 40px;">
                                        @if ($setupStatus['accounts'])
                                            <i class="fa-solid fa-check"></i>
                                        @else
                                            <i class="fa-solid fa-sitemap"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1">الحسابات الرئيسية</h6>
                                        <small class="text-muted d-block mb-2">إضافة الحسابات الأساسية للشجرة
                                            المحاسبية</small>
                                        @if ($setupStatus['accounts'])
                                            <span class="badge bg-success rounded-pill">
                                                <i class="fa-solid fa-check me-1"></i>مكتمل
                                            </span>
                                        @else
                                            <form action="{{ route('admin.company.seed.accounts', $company) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning fw-bold">
                                                    <i class="fa-solid fa-plus me-1"></i>إضافة الآن
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Container Types Setup -->
                    <div class="col-12 col-md-6 col-lg-4">
                        <div
                            class="card h-100 border {{ $setupStatus['container_types'] ? 'border-success bg-success bg-opacity-10' : 'border-warning' }} rounded-3">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start">
                                    <div class="rounded-circle p-2 d-flex align-items-center justify-content-center me-3 {{ $setupStatus['container_types'] ? 'bg-success text-white' : 'bg-warning bg-opacity-25 text-warning' }}"
                                        style="width: 40px; height: 40px;">
                                        @if ($setupStatus['container_types'])
                                            <i class="fa-solid fa-check"></i>
                                        @else
                                            <i class="fa-solid fa-box"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1">أنواع الحاويات</h6>
                                        <small class="text-muted d-block mb-2">إضافة أنواع الحاويات المتاحة للشركة</small>
                                        @if ($setupStatus['container_types'])
                                            <span class="badge bg-success rounded-pill">
                                                <i class="fa-solid fa-check me-1"></i>مكتمل
                                            </span>
                                        @else
                                            <form action="{{ route('admin.company.seed.container-types', $company) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning fw-bold">
                                                    <i class="fa-solid fa-plus me-1"></i>إضافة الآن
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Roles Setup -->
                    <div class="col-12 col-md-6 col-lg-4">
                        <div
                            class="card h-100 border {{ $setupStatus['roles'] ? 'border-success bg-success bg-opacity-10' : 'border-warning' }} rounded-3">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start">
                                    <div class="rounded-circle p-2 d-flex align-items-center justify-content-center me-3 {{ $setupStatus['roles'] ? 'bg-success text-white' : 'bg-warning bg-opacity-25 text-warning' }}"
                                        style="width: 40px; height: 40px;">
                                        @if ($setupStatus['roles'])
                                            <i class="fa-solid fa-check"></i>
                                        @else
                                            <i class="fa-solid fa-user-shield"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="fw-bold mb-1">أدوار المستخدمين</h6>
                                        <small class="text-muted d-block mb-2">إضافة الأدوار والصلاحيات للشركة</small>
                                        @if ($setupStatus['roles'])
                                            <span class="badge bg-success rounded-pill">
                                                <i class="fa-solid fa-check me-1"></i>مكتمل
                                            </span>
                                        @else
                                            <form action="{{ route('admin.company.seed.roles', $company) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning fw-bold">
                                                    <i class="fa-solid fa-plus me-1"></i>إضافة الآن
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- All Setup Complete Banner (shown once via session) -->
    @if (session('setup_completed'))
        <div class="alert alert-success d-flex align-items-center mb-4 rounded-3 border-0 shadow-sm" role="alert">
            <div class="bg-success bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center me-3"
                style="width: 40px; height: 40px;">
                <i class="fa-solid fa-check-circle text-success"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-0">تم إعداد الشركة بنجاح!</h6>
                <small class="text-muted">جميع الإعدادات الأساسية مكتملة والشركة جاهزة للعمل</small>
            </div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Company Details -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="d-flex mb-2 gap-2">
                <div style="width: 4px; height: 30px; background: var(--gradient);" class="rounded"></div>
                <h4 class="fw-bold">معلومات التسجيل</h4>
            </div>
            <div class="row g-3">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-start">
                                <div
                                    class="bg-primary bg-opacity-10 text-primary rounded-3 p-3 d-flex align-items-center justify-content-center fs-5">
                                    <i class="fa-solid fa-file-contract"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <small class="text-muted d-block mb-1">السجل التجاري (CR)</small>
                                    <span class="fw-semibold text-dark">{{ $company->CR ?? 'غير محدد' }}</span>
                                </div>
                                @if ($company->CR)
                                    <button class="btn btn-sm btn-link p-0 copy-btn"
                                        onclick="copyToClipboard('{{ $company->CR }}')" title="نسخ">
                                        <i class="fa-regular fa-copy text-muted"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-start">
                                <div
                                    class="bg-success bg-opacity-10 text-success rounded-3 p-3 d-flex align-items-center justify-content-center fs-5">
                                    <i class="fa-solid fa-id-card"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <small class="text-muted d-block mb-1">الرقم الموحد (TIN)</small>
                                    <span class="fw-semibold text-dark">{{ $company->TIN ?? 'غير محدد' }}</span>
                                </div>
                                @if ($company->TIN)
                                    <button class="btn btn-sm btn-link p-0 copy-btn"
                                        onclick="copyToClipboard('{{ $company->TIN }}')" title="نسخ">
                                        <i class="fa-regular fa-copy text-muted"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-start">
                                <div
                                    class="bg-info bg-opacity-10 text-info rounded-3 p-3 d-flex align-items-center justify-content-center fs-5">
                                    <i class="fa-solid fa-percent"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <small class="text-muted d-block mb-1">الرقم الضريبي (VAT Number)</small>
                                    <span class="fw-semibold text-dark">{{ $company->vatNumber ?? 'غير محدد' }}</span>
                                </div>
                                @if ($company->vatNumber)
                                    <button class="btn btn-sm btn-link p-0 copy-btn"
                                        onclick="copyToClipboard('{{ $company->vatNumber }}')" title="نسخ">
                                        <i class="fa-regular fa-copy text-muted"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="col-12">
            <div class="d-flex mb-2 gap-2">
                <div style="width: 4px; height: 30px; background: var(--gradient);" class="rounded"></div>
                <h4 class="fw-bold">معلومات التواصل</h4>
            </div>
            <div class="row g-3">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-start">
                                <div
                                    class="bg-danger bg-opacity-10 text-danger rounded-3 p-3 d-flex align-items-center justify-content-center fs-5">
                                    <i class="fa-solid fa-envelope"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <small class="text-muted d-block mb-1">البريد الإلكتروني</small>
                                    <span class="fw-semibold text-dark">
                                        @if ($company->email)
                                            <a href="mailto:{{ $company->email }}"
                                                class="text-decoration-none text-dark">
                                                {{ $company->email }}
                                            </a>
                                        @else
                                            غير محدد
                                        @endif
                                    </span>
                                </div>
                                @if ($company->email)
                                    <button class="btn btn-sm btn-link p-0 copy-btn"
                                        onclick="copyToClipboard('{{ $company->email }}')" title="نسخ">
                                        <i class="fa-regular fa-copy text-muted"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-start">
                                <div
                                    class="bg-warning bg-opacity-10 text-warning rounded-3 p-3 d-flex align-items-center justify-content-center fs-5">
                                    <i class="fa-solid fa-phone"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <small class="text-muted d-block mb-1">رقم الهاتف</small>
                                    <span class="fw-semibold text-dark">
                                        @if ($company->phone)
                                            <a href="tel:{{ $company->phone }}" class="text-decoration-none text-dark"
                                                dir="ltr">
                                                {{ $company->phone }}
                                            </a>
                                        @else
                                            غير محدد
                                        @endif
                                    </span>
                                </div>
                                @if ($company->phone)
                                    <button class="btn btn-sm btn-link p-0 copy-btn"
                                        onclick="copyToClipboard('{{ $company->phone }}')" title="نسخ">
                                        <i class="fa-regular fa-copy text-muted"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-3 h-100">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-start">
                                <div
                                    class="bg-primary bg-opacity-10 text-primary rounded-3 p-3 d-flex align-items-center justify-content-center fs-5">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <small class="text-muted d-block mb-1">العنوان الوطني</small>
                                    <span
                                        class="fw-semibold text-dark">{{ $company->national_address ?? 'غير محدد' }}</span>
                                </div>
                                @if ($company->national_address)
                                    <button class="btn btn-sm btn-link p-0 copy-btn"
                                        onclick="copyToClipboard('{{ $company->national_address }}')" title="نسخ">
                                        <i class="fa-regular fa-copy text-muted"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Modules Section -->
    <div class="col-12 mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex mb-2 gap-2">
                <div style="width: 4px; height: 30px; background: var(--gradient);" class="rounded"></div>
                <h4 class="fw-bold">المديولات المفعلة</h4>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary mb-2" data-bs-toggle="modal" data-bs-target="#addModuleModal">
                <i class="fas fa-plus me-2"></i>إضافة مديول جديد
            </button>
        </div>
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="row g-3">
                    @forelse ($company->modules as $module)
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 {{ $module->pivot->is_active ? 'bg-primary bg-opacity-10' : 'bg-secondary bg-opacity-10' }}">
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid {{ $module->pivot->is_active ? 'fa-check-circle text-primary' : 'fa-times-circle text-secondary' }} me-2"></i>
                                    <span class="fw-medium">{{ $module->name }}</span>
                                </div>
                                <form action="{{ route('companies.toggle.module', ['company' => $company, 'moduleId' => $module->id]) }}"
                                    method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="btn btn-sm {{ $module->pivot->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                        style="border-radius: 8px;">
                                        <i class="fas {{ $module->pivot->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-warning mb-0" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                لا توجد مديولات مفعلة حالياً. اضغط على زر "إضافة مديول جديد" لتفعيل المديولات.
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Add Module Modal -->
    <div class="modal fade" id="addModuleModal" tabindex="-1" aria-labelledby="addModuleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="addModuleModalLabel">إضافة مديول جديد</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('companies.add.modules', ['company' => $company]) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">اختر المديولات</label>
                            <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                @foreach ($modules as $module)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="module_ids[]"
                                            value="{{ $module->id }}" id="module{{ $module->id }}"
                                            {{ in_array($module->id, $company->modules->pluck('id')->toArray()) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="module{{ $module->id }}">
                                            {{ $module->name }}
                                            @if ($module->description)
                                                <small class="text-muted d-block">{{ $module->description }}</small>
                                            @endif
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex flex-column flex-sm-row gap-2">
                        <button type="button" class="btn btn-secondary order-2 order-sm-1"
                            data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary order-1 order-sm-2">إضافة المديول</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bank Accounts Section -->
    <div class="col-12 mb-4">
        <div class="d-flex mb-2 gap-2">
            <div style="width: 4px; height: 30px; background: var(--gradient);" class="rounded"></div>
            <h4 class="fw-bold">الحسابات البنكية</h4>
        </div>
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-center bg-dark text-white">#</th>
                                <th class="text-center bg-dark text-white">اسم البنك</th>
                                <th class="text-center bg-dark text-white">رقم الحساب</th>
                                <th class="text-center bg-dark text-white">IBAN</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($company->bankAccounts as $bankAccount)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-center">{{ $bankAccount->bank }}</td>
                                    <td class="text-center" dir="ltr">{{ $bankAccount->account_number }}</td>
                                    <td class="text-center" dir="ltr">{{ $bankAccount->iban ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        <div class="text-muted">
                                            <i class="fa-solid fa-ban fa-2x mb-2"></i>
                                            <p class="mb-0">لا توجد حسابات بنكية مضافة.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Company Address Section -->
    @if ($company->address)
        <div class="col-12 mb-4">
            <div class="d-flex mb-2 gap-2">
                <div style="width: 4px; height: 30px; background: var(--gradient);" class="rounded"></div>
                <h4 class="fw-bold">تفاصيل العنوان الوطني</h4>
            </div>
            <div class="row g-3">
                @if ($company->address->building_number)
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card border-0 shadow-sm rounded-3 h-100">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start">
                                    <div
                                        class="bg-primary bg-opacity-10 text-primary rounded-3 p-3 d-flex align-items-center justify-content-center fs-5">
                                        <i class="fa-solid fa-building"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <small class="text-muted d-block mb-1">رقم المبنى</small>
                                        <span
                                            class="fw-semibold text-dark">{{ $company->address->building_number }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if ($company->address->street_name)
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card border-0 shadow-sm rounded-3 h-100">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start">
                                    <div
                                        class="bg-info bg-opacity-10 text-info rounded-3 p-3 d-flex align-items-center justify-content-center fs-5">
                                        <i class="fa-solid fa-road"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <small class="text-muted d-block mb-1">اسم الشارع</small>
                                        <span class="fw-semibold text-dark">{{ $company->address->street_name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if ($company->address->district)
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card border-0 shadow-sm rounded-3 h-100">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start">
                                    <div
                                        class="bg-success bg-opacity-10 text-success rounded-3 p-3 d-flex align-items-center justify-content-center fs-5">
                                        <i class="fa-solid fa-map"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <small class="text-muted d-block mb-1">الحي</small>
                                        <span class="fw-semibold text-dark">{{ $company->address->district }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if ($company->address->city)
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card border-0 shadow-sm rounded-3 h-100">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start">
                                    <div
                                        class="bg-warning bg-opacity-10 text-warning rounded-3 p-3 d-flex align-items-center justify-content-center fs-5">
                                        <i class="fa-solid fa-city"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <small class="text-muted d-block mb-1">المدينة</small>
                                        <span class="fw-semibold text-dark">{{ $company->address->city }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if ($company->address->postal_code)
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card border-0 shadow-sm rounded-3 h-100">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start">
                                    <div
                                        class="bg-secondary bg-opacity-10 text-secondary rounded-3 p-3 d-flex align-items-center justify-content-center fs-5">
                                        <i class="fa-solid fa-envelope"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <small class="text-muted d-block mb-1">الرمز البريدي</small>
                                        <span class="fw-semibold text-dark">{{ $company->address->postal_code }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                @if ($company->address->additional_number)
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card border-0 shadow-sm rounded-3 h-100">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start">
                                    <div
                                        class="bg-danger bg-opacity-10 text-danger rounded-3 p-3 d-flex align-items-center justify-content-center fs-5">
                                        <i class="fa-solid fa-hashtag"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <small class="text-muted d-block mb-1">الرقم الإضافي</small>
                                        <span
                                            class="fw-semibold text-dark">{{ $company->address->additional_number }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Company Users Section -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="d-flex mb-2 gap-2">
                <div style="width: 4px; height: 30px; background: var(--gradient);" class="rounded"></div>
                <h4 class="fw-bold">مستخدمو الشركة</h4>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <!-- Add User Button -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">
                            <i class="fa-solid fa-users me-2"></i>
                            إجمالي المستخدمين: <strong>{{ $users->count() }}</strong>
                        </span>
                        @if ($roles->count() > 0)
                            <button class="btn btn-sm btn-primary fw-bold" type="button" data-bs-toggle="modal"
                                data-bs-target="#createCompanyUserModal">
                                <i class="fa-solid fa-user-plus me-2"></i>
                                إضافة مستخدم
                            </button>
                        @else
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                <i class="fa-solid fa-exclamation-triangle me-1"></i>
                                يجب إضافة الأدوار أولاً
                            </span>
                        @endif
                    </div>

                    <!-- Users Table -->
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center bg-dark text-white text-nowrap">#</th>
                                    <th class="text-center bg-dark text-white text-nowrap">اسم المستخدم</th>
                                    <th class="text-center bg-dark text-white text-nowrap">البريد الإلكتروني</th>
                                    <th class="text-center bg-dark text-white text-nowrap">رقم الهاتف</th>
                                    <th class="text-center bg-dark text-white text-nowrap">الوظيفة</th>
                                    <th class="text-center bg-dark text-white text-nowrap">تاريخ الإنشاء</th>
                                    <th class="text-center bg-dark text-white text-nowrap">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($users->isEmpty())
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fa-solid fa-users-slash fa-2x mb-2"></i>
                                                <p class="mb-0">لا يوجد مستخدمين لهذه الشركة</p>
                                            </div>
                                        </td>
                                    </tr>
                                @else
                                    @foreach ($users as $user)
                                        <tr>
                                            <td class="text-center text-primary fw-bold">{{ $loop->iteration }}</td>
                                            <td class="text-center fw-bold">{{ $user->name }}</td>
                                            <td class="text-center">{{ $user->email }}</td>
                                            <td class="text-center">{{ $user->phone ?? '-' }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-primary rounded-pill">
                                                    {{ $user->roles->first()->name ?? 'بدون وظيفة' }}
                                                </span>
                                            </td>
                                            <td class="text-center">{{ $user->created_at->format('Y/m/d') }}</td>
                                            <td class="text-center text-nowrap">
                                                <button class="btn btn-link p-0 pb-1 me-2" type="button"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editCompanyUserModal{{ $user->id }}">
                                                    <i class="fa-solid fa-pen-to-square text-primary" title="تعديل"></i>
                                                </button>
                                                @if ($user->id !== auth()->user()->id)
                                                    <button class="btn btn-link p-0 pb-1" type="button"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteCompanyUserModal{{ $user->id }}">
                                                        <i class="fa-solid fa-trash-can text-danger" title="حذف"></i>
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>

                                        <!-- Edit User Modal -->
                                        <div class="modal fade" id="editCompanyUserModal{{ $user->id }}"
                                            tabindex="-1" aria-labelledby="editCompanyUserModalLabel{{ $user->id }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary">
                                                        <h5 class="modal-title text-white fw-bold"
                                                            id="editCompanyUserModalLabel{{ $user->id }}">تعديل بيانات
                                                            المستخدم</h5>
                                                        <button type="button" class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form
                                                        action="{{ route('admin.company.users.update', [$company, $user]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <div class="modal-body text-dark">
                                                            <div class="row g-3 mb-3">
                                                                <div class="col-12 col-md-6">
                                                                    <label class="form-label">اسم المستخدم</label>
                                                                    <input type="text"
                                                                        class="form-control border-primary" name="name"
                                                                        value="{{ $user->name }}" required>
                                                                </div>
                                                                <div class="col-12 col-md-6">
                                                                    <label class="form-label">البريد الإلكتروني</label>
                                                                    <input type="email"
                                                                        class="form-control border-primary" name="email"
                                                                        value="{{ $user->email }}" required>
                                                                </div>
                                                            </div>
                                                            <div class="row g-3 mb-3">
                                                                <div class="col-12 col-md-6">
                                                                    <label class="form-label">رقم الهاتف</label>
                                                                    <input type="text"
                                                                        class="form-control border-primary" name="phone"
                                                                        value="{{ $user->phone }}">
                                                                </div>
                                                                <div class="col-12 col-md-6">
                                                                    <label class="form-label">الوظيفة</label>
                                                                    <select class="form-select border-primary"
                                                                        name="role" required>
                                                                        @foreach ($roles as $role)
                                                                            <option value="{{ $role->id }}"
                                                                                {{ ($user->roles->first()->id ?? '') == $role->id ? 'selected' : '' }}>
                                                                                {{ $role->name }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div
                                                            class="modal-footer d-flex flex-column flex-sm-row justify-content-start gap-2">
                                                            <button type="submit"
                                                                class="btn btn-primary fw-bold order-1 order-sm-2">
                                                                حفظ التغييرات
                                                            </button>
                                                            <button type="button"
                                                                class="btn btn-secondary fw-bold order-2 order-sm-1"
                                                                data-bs-dismiss="modal">إلغاء</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Delete User Modal -->
                                        @if ($user->id !== auth()->user()->id)
                                            <div class="modal fade" id="deleteCompanyUserModal{{ $user->id }}"
                                                tabindex="-1"
                                                aria-labelledby="deleteCompanyUserModalLabel{{ $user->id }}"
                                                aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger">
                                                            <h5 class="modal-title text-white fw-bold"
                                                                id="deleteCompanyUserModalLabel{{ $user->id }}">تأكيد
                                                                الحذف</h5>
                                                            <button type="button" class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-center text-dark">
                                                            <i
                                                                class="fa-solid fa-exclamation-triangle text-danger fa-3x mb-3"></i>
                                                            <p class="mb-0">هل أنت متأكد من حذف المستخدم
                                                                <strong>{{ $user->name }}</strong>؟
                                                            </p>
                                                        </div>
                                                        <div
                                                            class="modal-footer d-flex flex-column flex-sm-row justify-content-center gap-2">
                                                            <button type="button"
                                                                class="btn btn-secondary fw-bold order-2 order-sm-1"
                                                                data-bs-dismiss="modal">إلغاء</button>
                                                            <form
                                                                action="{{ route('admin.company.users.delete', [$company, $user]) }}"
                                                                method="POST" class="order-1 order-sm-2">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="btn btn-danger fw-bold w-100">
                                                                    <i class="fa-solid fa-trash-can me-1"></i>حذف
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create User Modal -->
    @if ($roles->count() > 0)
        <div class="modal fade" id="createCompanyUserModal" tabindex="-1" aria-labelledby="createCompanyUserModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white fw-bold" id="createCompanyUserModalLabel">
                            إضافة مستخدم جديد للشركة
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="{{ route('admin.company.users.store', $company) }}" method="POST">
                        @csrf
                        <div class="modal-body text-dark">
                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label">اسم المستخدم <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control border-primary" name="name"
                                        value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control border-primary" name="email"
                                        value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control border-primary" name="password" required>
                                    @error('password')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                                    <input type="password" class="form-control border-primary"
                                        name="password_confirmation" required>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label">الجنسية</label>
                                    <input type="text" class="form-control border-primary" name="nationality"
                                        value="{{ old('nationality') }}">
                                    @error('nationality')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">رقم الهوية الوطنية</label>
                                    <input type="text" class="form-control border-primary" name="NID"
                                        value="{{ old('NID') }}">
                                    @error('NID')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-12 col-md-6">
                                    <label class="form-label">رقم الهاتف</label>
                                    <input type="text" class="form-control border-primary" name="phone"
                                        value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-12 col-md-6">
                                    <label class="form-label">الوظيفة <span class="text-danger">*</span></label>
                                    <select class="form-select border-primary" name="role" required>
                                        <option value="">اختر الوظيفة</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}"
                                                {{ old('role') == $role->id ? 'selected' : '' }}>
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('role')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer d-flex flex-column flex-sm-row justify-content-start gap-2">
                            <button type="submit" class="btn btn-primary fw-bold order-1 order-sm-2">
                                <i class="fa-solid fa-user-plus me-1"></i>إضافة المستخدم
                            </button>
                            <button type="button" class="btn btn-secondary fw-bold order-2 order-sm-1"
                                data-bs-dismiss="modal">إلغاء</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Company Roles & Permissions Section -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="d-flex mb-2 gap-2">
                <div style="width: 4px; height: 30px; background: var(--gradient);" class="rounded"></div>
                <h4 class="fw-bold">الوظائف والصلاحيات</h4>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <!-- Header with Add Buttons -->
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-3">
                        <span class="text-muted">
                            <i class="fa-solid fa-user-shield me-2"></i>
                            إجمالي الوظائف: <strong>{{ $roles->count() }}</strong>
                        </span>
                        <div class="d-flex flex-row gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary fw-bold" data-bs-toggle="modal"
                                data-bs-target="#addCompanyPermissionModal">
                                <i class="fa-solid fa-key me-1"></i>
                                <span class="d-none d-md-inline">إضافة صلاحية</span>
                                <span class="d-inline d-md-none">صلاحية</span>
                            </button>
                            <button type="button" class="btn btn-sm btn-primary fw-bold" data-bs-toggle="modal"
                                data-bs-target="#addCompanyRoleModal">
                                <i class="fa-solid fa-plus me-1"></i>
                                <span class="d-none d-md-inline">إضافة وظيفة</span>
                                <span class="d-inline d-md-none">وظيفة</span>
                            </button>
                        </div>
                    </div>

                    <!-- Roles Table -->
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center bg-dark text-white text-nowrap">#</th>
                                    <th class="text-center bg-dark text-white text-nowrap">اسم الوظيفة</th>
                                    <th class="text-center bg-dark text-white text-nowrap">عدد الصلاحيات</th>
                                    <th class="text-center bg-dark text-white text-nowrap">تاريخ الإنشاء</th>
                                    <th class="text-center bg-dark text-white text-nowrap">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($roles->isEmpty())
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fa-solid fa-user-shield fa-2x mb-2"></i>
                                                <p class="mb-0">لا توجد وظائف لهذه الشركة</p>
                                            </div>
                                        </td>
                                    </tr>
                                @else
                                    @foreach ($roles as $role)
                                        <tr>
                                            <td class="text-center text-primary fw-bold">{{ $loop->iteration }}</td>
                                            <td class="text-center fw-bold">{{ $role->name }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-primary rounded-pill">
                                                    {{ $role->permissions->count() }}
                                                </span>
                                            </td>
                                            <td class="text-center">{{ $role->created_at->format('Y/m/d') }}</td>
                                            <td class="text-center text-nowrap">
                                                <button type="button" class="btn btn-sm btn-primary me-1"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editCompanyRoleModal{{ $role->id }}">
                                                    <i class="fa-solid fa-edit me-1"></i>
                                                    <span class="d-none d-md-inline">تحديث الصلاحيات</span>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteCompanyRoleModal{{ $role->id }}">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Edit Role Permissions Modal -->
                                        <div class="modal fade" id="editCompanyRoleModal{{ $role->id }}"
                                            tabindex="-1" aria-labelledby="editCompanyRoleModalLabel{{ $role->id }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary">
                                                        <h5 class="modal-title text-white fw-bold"
                                                            id="editCompanyRoleModalLabel{{ $role->id }}">
                                                            <i class="fa-solid fa-edit me-2"></i>
                                                            تحديث صلاحيات الوظيفة: {{ $role->name }}
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form
                                                        action="{{ route('admin.company.roles.update', [$company, $role]) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-bold">اسم الوظيفة</label>
                                                                <input type="text" class="form-control border-primary"
                                                                    name="name" value="{{ $role->name }}" required>
                                                            </div>
                                                            <div class="mb-3">
                                                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center mb-2 gap-2">
                                                                    <label class="form-label fw-bold mb-0">الصلاحيات المتاحة</label>
                                                                    <div class="d-flex gap-2">
                                                                        <button type="button"
                                                                            class="btn btn-outline-primary btn-sm"
                                                                            onclick="selectAllPermissions({{ $role->id }})">
                                                                            <i class="fa-solid fa-check-double me-1"></i>
                                                                            تحديد الكل
                                                                        </button>
                                                                        <button type="button"
                                                                            class="btn btn-outline-danger btn-sm"
                                                                            onclick="deselectAllPermissions({{ $role->id }})">
                                                                            <i class="fa-solid fa-times me-1"></i>
                                                                            إلغاء الكل
                                                                        </button>
                                                                    </div>
                                                                </div>

                                                                <div class="border border-primary rounded p-3 bg-light"
                                                                    style="max-height: 300px; overflow-y: auto;">
                                                                    <div class="row g-2">
                                                                        @forelse($permissions as $permission)
                                                                            <div class="col-12 col-md-6">
                                                                                <div class="form-check">
                                                                                    <input class="form-check-input permission-checkbox-{{ $role->id }}"
                                                                                        type="checkbox"
                                                                                        value="{{ $permission->id }}"
                                                                                        id="edit_perm_{{ $role->id }}_{{ $permission->id }}"
                                                                                        name="permissions[]"
                                                                                        {{ in_array($permission->id, $role->permissions->pluck('id')->toArray()) ? 'checked' : '' }}>
                                                                                    <label class="form-check-label fw-bold"
                                                                                        for="edit_perm_{{ $role->id }}_{{ $permission->id }}">
                                                                                        {{ $permission->name }}
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        @empty
                                                                            <div class="col-12">
                                                                                <p class="text-muted text-center mb-0">لا توجد صلاحيات متاحة</p>
                                                                            </div>
                                                                        @endforelse
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div> 
                                                        <div class="modal-footer d-flex flex-column flex-sm-row justify-content-start gap-2">
                                                            <button type="submit"
                                                                class="btn btn-primary fw-bold order-1 order-sm-2">
                                                                تحديث الوظيفة
                                                            </button>
                                                            <button type="button"
                                                                class="btn btn-secondary fw-bold order-2 order-sm-1"
                                                                data-bs-dismiss="modal">إلغاء</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Delete Role Modal -->
                                        <div class="modal fade" id="deleteCompanyRoleModal{{ $role->id }}" tabindex="-1"
                                            aria-labelledby="deleteCompanyRoleModalLabel{{ $role->id }}"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger">
                                                        <h5 class="modal-title text-white fw-bold"
                                                            id="deleteCompanyRoleModalLabel{{ $role->id }}">
                                                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                                            تأكيد الحذف
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white"
                                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-center">
                                                        <i class="fa-solid fa-exclamation-triangle text-danger fa-3x mb-3"></i>
                                                        <p class="fs-5 mb-1">هل أنت متأكد من حذف هذه الوظيفة؟</p>
                                                        <p class="text-muted">الوظيفة: <strong>{{ $role->name }}</strong></p>
                                                    </div>
                                                    <div class="modal-footer d-flex flex-column flex-sm-row justify-content-center gap-2">
                                                        <button type="button" class="btn btn-secondary fw-bold order-2 order-sm-1"
                                                            data-bs-dismiss="modal">إلغاء</button>
                                                        <form action="{{ route('admin.company.roles.delete', [$company, $role]) }}"
                                                            method="POST" class="order-1 order-sm-2">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger fw-bold w-100">
                                                                <i class="fa-solid fa-trash-can me-1"></i>حذف الوظيفة
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Role Modal -->
    <div class="modal fade" id="addCompanyRoleModal" tabindex="-1" aria-labelledby="addCompanyRoleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white fw-bold" id="addCompanyRoleModalLabel">
                        <i class="fa-solid fa-plus me-2"></i>
                        إضافة وظيفة جديدة
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.company.roles.store', $company) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">اسم الوظيفة <span class="text-danger">*</span></label>
                            <input type="text" class="form-control border-primary" name="name"
                                placeholder="أدخل اسم الوظيفة..." required>
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">الصلاحيات</label>
                            <div class="border border-primary rounded p-3 bg-light"
                                style="max-height: 300px; overflow-y: auto;">
                                <div class="row g-2">
                                    @forelse($permissions as $permission)
                                        <div class="col-12 col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    value="{{ $permission->id }}" id="add_perm_{{ $permission->id }}"
                                                    name="permissions[]">
                                                <label class="form-check-label fw-bold"
                                                    for="add_perm_{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <p class="text-muted text-center mb-0">لا توجد صلاحيات متاحة</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex flex-column flex-sm-row justify-content-start gap-2">
                        <button type="submit" class="btn btn-primary fw-bold order-1 order-sm-2">
                            <i class="fa-solid fa-plus me-1"></i>إضافة الوظيفة
                        </button>
                        <button type="button" class="btn btn-secondary fw-bold order-2 order-sm-1"
                            data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Permission Modal -->
    <div class="modal fade" id="addCompanyPermissionModal" tabindex="-1"
        aria-labelledby="addCompanyPermissionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white fw-bold" id="addCompanyPermissionModalLabel">
                        <i class="fa-solid fa-key me-2"></i>
                        إضافة صلاحية جديدة
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.company.permissions.store', $company) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">اسم الصلاحية <span class="text-danger">*</span></label>
                            <input type="text" class="form-control border-primary" name="name"
                                placeholder="أدخل اسم الصلاحية..." required>
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer d-flex flex-column flex-sm-row justify-content-start gap-2">
                        <button type="submit" class="btn btn-primary fw-bold order-1 order-sm-2">
                            <i class="fa-solid fa-plus me-1"></i>إضافة الصلاحية
                        </button>
                        <button type="button" class="btn btn-secondary fw-bold order-2 order-sm-1"
                            data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .copy-btn {
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .card:hover .copy-btn {
            opacity: 1;
        }

        .copy-btn:hover i {
            color: var(--bs-primary) !important;
        }
    </style>

    <script>
        function copyToClipboard(text) {
            // Try using the Clipboard API first
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(function() {
                    showToast('تم نسخ البيانات بنجاح', 'success');
                }).catch(function(err) {
                    console.error('Failed to copy text: ', err);
                    showToast('فشل في نسخ البيانات', 'error');
                });
            } else {
                // Fallback for older browsers or insecure contexts
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-9999px';
                textArea.style.top = '-9999px';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();

                try {
                    const successful = document.execCommand('copy');
                    showToast(successful ? 'تم نسخ البيانات بنجاح' : 'فشل في نسخ البيانات', successful ? 'success' :
                        'error');
                } catch (err) {
                    console.error('Fallback: Failed to copy text: ', err);
                    showToast('فشل في نسخ البيانات', 'error');
                }

                document.body.removeChild(textArea);
            }
        }

        // Select all permissions for a role
        function selectAllPermissions(roleId) {
            const checkboxes = document.querySelectorAll('.permission-checkbox-' + roleId);
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
        }

        // Deselect all permissions for a role
        function deselectAllPermissions(roleId) {
            const checkboxes = document.querySelectorAll('.permission-checkbox-' + roleId);
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        }
    </script>

@endsection
