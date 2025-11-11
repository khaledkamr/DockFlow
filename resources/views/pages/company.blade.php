@extends('layouts.app')

@section('title', 'بيانات الشركة')

@section('content')
<h1 class="mb-4">بيانات الشركة</h1>

<div class="card border-0 shadow-sm bg-white p-4 mb-4">
    <form action="{{ route('company.update', $company) }}" method="POST" enctype="multipart/form-data">
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
                <div class="col">
                    <label class="form-label">شعار الشركة</label>
                    <div class="d-flex align-items-start gap-3">
                        <input type="file" name="logo" class="form-control border-primary" accept="image/*">
                        @if($company->logo)
                            <div class="flex-shrink-0">
                                <img src="{{ asset('storage/' . $company->logo) }}" alt="شعار الشركة" class="img-thumbnail" style="max-width: 60px; max-height: 60px;">
                            </div>
                        @endif
                    </div>
                    @error('logo')
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
            <div class="row">
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

<div class="card border-0 shadow-sm bg-white p-4 mb-4">
    <h3 class="mb-4">العنوان الوطني</h3>
    <form action="{{ route($company->address ? 'companies.update.address' : 'companies.store.address', $company) }}" method="POST">
        @csrf
        @method($company->address ? 'PUT' : 'POST')
        <div class="mb-3 bg-light p-3 rounded">
            <div class="row mb-4">
                <div class="col">
                     <label class="form-label">الدولة</label>
                    <input type="text" name="country" class="form-control border-primary" value="{{ $company->address->country ?? '' }}">
                </div>
                <div class="col">
                    <label class="form-label">المدينة</label>
                    <input type="text" name="city" class="form-control border-primary" value="{{ $company->address->city ?? '' }}">
                </div>
                <div class="col">
                    <label class="form-label">الحي</label>
                    <input type="text" name="district" class="form-control border-primary" value="{{ $company->address->district ?? '' }}">
                </div>
                <div class="col">
                    <label class="form-label">الشارع</label>
                    <input type="text" name="street" class="form-control border-primary" value="{{ $company->address->street ?? '' }}">
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label class="form-label">رقم المبنى</label>
                    <input type="text" name="building_number" class="form-control border-primary" value="{{ $company->address->building_number ?? '' }}">
                </div>
                <div class="col">
                    <label class="form-label">الرقم الفرعي</label>
                    <input type="text" name="secondary_number" class="form-control border-primary" value="{{ $company->address->secondary_number ?? '' }}">
                </div>
                <div class="col">
                    <label class="form-label">الرمز البريدي</label>
                    <input type="text" name="postal_code" class="form-control border-primary" value="{{ $company->address->postal_code ?? '' }}">
                </div>
                <div class="col">
                    <label class="form-label">العنوان المختصر</label>
                    <input type="text" name="short_address" class="form-control border-primary" value="{{ $company->address->short_address ?? '' }}">
                </div>
            </div>
            
        </div>
        <button type="submit" class="btn btn-primary fw-bold" id="submit-btn">
            حفظ البيانات
        </button>
    </form>
</div>

<div class="card border-0 shadow-sm bg-white p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">ارقام البنوك</h3>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBankModal">
            <i class="fas fa-plus me-2"></i>إضافة رقم بنك جديد
        </button>
    </div>

    @if($company->bankAccounts && $company->bankAccounts->count() > 0)
        <div class="row">
            @foreach($company->bankAccounts as $bankNumber)
                <div class="col-4 mb-3">
                    <div class="card border-0 rounded-4 shadow-sm overflow-hidden" style="background: linear-gradient(135deg, var(--blue-5), var(--blue-1));">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-white bg-opacity-25 rounded-circle p-2 me-3" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-university text-white fs-5"></i>
                                        </div>
                                        <h5 class="card-title fw-bold text-white mb-0">{{ $bankNumber->bank }}</h5>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="text-white small me-2">رقم الحساب:</span>
                                        <span class=" rounded-3 text-white fw-semibold" style="font-size: 20px; font-family: 'Courier New';">
                                            {{ $bankNumber->account_number }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="text-white small me-2">IBAN:</span>
                                        <span class=" rounded-3 text-white fw-semibold" style="font-family: 'Courier New';">
                                            {{ $bankNumber->iban ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="d-flex flex-column gap-2 ms-3">
                                    <button type="button" class="btn btn-light btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#editBankModal{{ $bankNumber->id }}" style="width: 36px; height: 36px; border-radius: 8px; padding: 0;">
                                        <i class="fas fa-edit" style="font-size: 14px;"></i>
                                    </button>
                                    <button type="button" class="btn btn-light btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#deleteBankModal{{ $bankNumber->id }}" style="width: 36px; height: 36px; border-radius: 8px; padding: 0;">
                                        <i class="fas fa-trash text-danger" style="font-size: 14px;"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delete Bank Modal -->
                <div class="modal fade" id="deleteBankModal{{ $bankNumber->id }}" tabindex="-1" aria-labelledby="deleteBankModalLabel{{ $bankNumber->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold" id="deleteBankModalLabel{{ $bankNumber->id }}">تأكيد الحذف</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="text-center">هل أنت متأكد من حذف حساب بنك <strong>{{ $bankNumber->bank }}</strong>؟</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                                <form action="{{ route('companies.delete.bank', $bankNumber->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger fw-bold">حذف</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- update Bank Modal -->
                <div class="modal fade" id="editBankModal{{ $bankNumber->id }}" tabindex="-1" aria-labelledby="editBankModalLabel{{ $bankNumber->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold" id="editBankModalLabel{{ $bankNumber->id }}">تعديل بيانات حساب البنك</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('companies.update.bank', $bankNumber->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">اسم البنك</label>
                                        <input type="text" name="bank" class="form-control border-primary" value="{{ $bankNumber->bank }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">رقم الحساب</label>
                                        <input type="text" name="account_number" class="form-control border-primary" value="{{ $bankNumber->account_number }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">IBAN</label>
                                        <input type="text" name="iban" class="form-control border-primary" value="{{ $bankNumber->iban }}">
                                    </div>
                                </div>
                                <div class="modal-footer d-flex flex-row-reverse">
                                    <button type="submit" class="btn btn-primary fw-bold">تعديل الحساب</button>
                                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center">
            <i class="fas fa-university fa-3x text-muted mb-3"></i>
            <p class="text-muted">لا توجد أرقام بنوك مضافة بعد</p>
        </div>
    @endif

    <!-- Add Bank Modal -->
    <div class="modal fade" id="addBankModal" tabindex="-1" aria-labelledby="addBankModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="addBankModalLabel">إضافة رقم بنك جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('companies.store.bank', $company) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">اسم البنك</label>
                            <input type="text" name="bank" class="form-control border-primary" value="{{ old('bank') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">رقم الحساب</label>
                            <input type="text" name="account_number" class="form-control border-primary" value="{{ old('account_number') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">IBAN</label>
                            <input type="text" name="iban" class="form-control border-primary" value="{{ old('iban') }}">
                        </div>
                    </div>
                    <div class="modal-footer d-flex flex-row-reverse">
                        <button type="submit" class="btn btn-primary fw-bold">إضافة الحساب</button>
                        <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm bg-white p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="mb-0">مديولات النظام</h3>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModuleModal">
            <i class="fas fa-plus me-2"></i>إضافة مديول جديد
        </button>
    </div>

    <div class="row">
        @forelse($company->modules ?? [] as $module)
            <div class="col-4 mb-3">
                <div class="card h-100 border-0 rounded-4 shadow-sm overflow-hidden" style="{{ $module->pivot->is_active ? 'background: linear-gradient(135deg, var(--blue-5), var(--blue-1));' : 'background: linear-gradient(135deg, #adb5bd, #6c757d);' }}">
                    <div class="card-body p-3 position-relative">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-white bg-opacity-25 rounded-circle p-2 me-3" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-puzzle-piece text-white fs-5"></i>
                                    </div>
                                    <h5 class="card-title fw-bold text-white mb-0">{{ $module->name }}</h5>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <form action="{{ route('companies.toggle.module', ['company' => $company, 'moduleId' => $module->id]) }}" method="POST" class="flex-fill">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-light shadow-sm w-100 fw-semibold" style="border-radius: 8px; padding: 10px;">
                                    <i class="fas {{ $module->pivot->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }} me-2"></i>
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
                                    <input class="form-check-input" type="checkbox" name="module_ids[]" value="{{ $module->id }}" id="module{{ $module->id }}"
                                        {{ in_array($module->id, $company->modules->pluck('id')->toArray()) ? 'checked' : '' }}>
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