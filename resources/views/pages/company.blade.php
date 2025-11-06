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
                    <label class="form-label">السجل التجاري</label>
                    <input type="text" name="CR" class="form-control border-primary" value="{{ $company->CR }}">
                    @error('CR')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col">
                    <label class="form-label">الرقم الموحد</label>
                    <input type="text" name="TIN" class="form-control border-primary" value="{{ $company->TIN }}">
                    @error('TIN')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col">
                    <label class="form-label">الرقم الضريبي</label>
                    <input type="text" name="vatNumber" class="form-control border-primary" value="{{ $company->vatNumber }}">
                    @error('vatNumber')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
           <div class="row mb-4">
                <div class="col">
                    <label class="form-label">العنوان الوطني</label>
                    <input type="text" name="national_address" class="form-control border-primary" value="{{ $company->national_address }}">
                    @error('national_address')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
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

<div class="card border-0 shadow-sm bg-white p-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">مديولات النظام</h3>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModuleModal">
            <i class="fas fa-plus me-2"></i>إضافة مديول جديدة
        </button>
    </div>

    <div class="row">
        @forelse($company->modules ?? [] as $module)
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card h-100 {{ $module->pivot->is_active ? 'border-primary' : 'border-secondary' }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title">{{ $module->name }}</h5>
                            <span class="badge {{ $module->pivot->is_active ? 'bg-primary' : 'bg-secondary' }}">
                                {{ $module->pivot->is_active ? 'مفعل' : 'غير مفعل' }}
                            </span>
                        </div>
                        <p class="card-text text-muted">{{ $module->description }}</p>
                        <div class="d-flex gap-2">
                            <form action="{{ route('companies.toggle.module', ['company' => $company, 'moduleId' => $module->id]) }}" method="POST" class="flex-fill">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $module->pivot->is_active ? 'btn-outline-danger' : 'btn-outline-success' }} w-100">
                                    {{ $module->pivot->is_active ? 'إلغاء التفعيل' : 'تفعيل' }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-4">
                    <i class="fas fa-puzzle-piece fa-3x text-muted mb-3"></i>
                    <p class="text-muted">لا توجد مديولات مضافة بعد</p>
                </div>
            </div>
        @endforelse
    </div>
</div>

<!-- Add Module Modal -->
<div class="modal fade" id="addModuleModal" tabindex="-1" aria-labelledby="addModuleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="addModuleModalLabel">إضافة مديول جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('companies.add.modules', ['company' => $company]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">اختر المديولات</label>
                        <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            @foreach($modules as $module)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="module_ids[]" value="{{ $module->id }}" id="module{{ $module->id }}">
                                    <label class="form-check-label" for="module{{ $module->id }}">
                                        {{ $module->name }}
                                        @if($module->description)
                                            <small class="text-muted d-block">{{ $module->description }}</small>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة المديول</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection