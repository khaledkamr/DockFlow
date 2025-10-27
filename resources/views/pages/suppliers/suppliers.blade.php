@extends('layouts.app')

@section('title', 'الموردين')

@section('content')
    <h1 class="mb-4">الموردين</h1>

    <div class="row mb-4">
        <div class="col-md-6">
            <form method="GET" action="" class="d-flex flex-column">
                <label for="search" class="form-label text-dark fw-bold">بحث عن مورد:</label>
                <div class="d-flex">
                    <input type="text" name="search" class="form-control border-primary"
                        placeholder=" ابحث عن مورد بالإيميل او بالإسم... " value="{{ request()->query('search') }}">
                    <button type="submit" class="btn btn-primary fw-bold ms-2 d-flex align-items-center">
                        <span>بحث</span>
                        <i class="fa-solid fa-magnifying-glass ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="col-md-4">
            <form method="GET" action="" class="d-flex flex-column">
                <label for="statusFilter" class="form-label text-dark fw-bold">تصفية حسب الحالة:</label>
                <div class="d-flex">
                    <select id="statusFilter" name="role" class="form-select border-primary"
                        onchange="this.form.submit()">
                        <option value="all"
                            {{ request()->query('role') === 'all' || !request()->query('role') ? 'selected' : '' }}>
                            جميع العملاء</option>
                        <option value="professor" {{ request()->query('role') === 'professor' ? 'selected' : '' }}>
                            Professors</option>
                        <option value="admin" {{ request()->query('role') === 'admin' ? 'selected' : '' }}>
                            Admins</option>
                        <option value="student" {{ request()->query('role') === 'student' ? 'selected' : '' }}>
                            Students</option>
                        <option value="parent" {{ request()->query('role') === 'parent' ? 'selected' : '' }}>
                            Parents</option>
                    </select>
                    @if (request()->query('search'))
                        <input type="hidden" name="search" value="{{ request()->query('search') }}">
                    @endif
                </div>
            </form>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary w-100 fw-bold" type="button" data-bs-toggle="modal"
                data-bs-target="#createSupplierModal">
                <i class="fa-solid fa-user-plus pe-1"></i>
                أضف مورد
            </button>
        </div>
    </div>

    <div class="modal fade" id="createSupplierModal" tabindex="-1" aria-labelledby="createSupplierModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-bold" id="createSupplierModalLabel">إنشاء مورد جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('users.supplier.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                    <div class="modal-body text-dark">
                        <div class="row mb-3">
                            <div class="col">
                                <label for="name" class="form-label">إسم المورد</label>
                                <input type="text" class="form-control border-primary" id="name" name="name"
                                    value="{{ old('name') }}">
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label for="CR" class="form-label">نوع المورد</label>
                                <select name="account_id" class="form-select border-primary">
                                    <option value="">اختر نوع المورد</option>
                                    @foreach ($supplierAccounts as $account)
                                        <option value="{{ $account->id }}" {{ old('account_id') == $account->id ? 'selected' : '' }}>
                                            {{ $account->code }} - {{ $account->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('account_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="CR" class="form-label">السجل التجاري</label>
                                <input type="text" class="form-control border-primary" id="CR" name="CR" value="{{ old('CR') }}">
                                @error('CR')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label class="form-label">الرقم الضريبي</label>
                                <input type="text" class="form-control border-primary" name="vat_number" value="{{ old('vat_number') }}">
                                @error('vat_number')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="national_address" class="form-label">العنوان الوطني</label>
                                <input type="text" class="form-control border-primary" id="national_address"
                                    name="national_address" value="{{ old('national_address') }}">
                                @error('national_address')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label for="phone" class="form-label">رقم الهاتف</label>
                                <input type="text" class="form-control border-primary" id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <input type="text" class="form-control border-primary" id="email" name="email"
                                    value="{{ old('email') }}">
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-start">
                        <button type="submit" class="btn btn-primary fw-bold">إنشاء</button>
                        <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="table-container">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th class="text-center bg-dark text-white">رقم المورد</th>
                    <th class="text-center bg-dark text-white">إسم المورد</th>
                    <th class="text-center bg-dark text-white">رقم السجل التجاري</th>
                    <th class="text-center bg-dark text-white">العنوان الوطني</th>
                    <th class="text-center bg-dark text-white">رقم الهاتف</th>
                    <th class="text-center bg-dark text-white">الإيميل</th>
                    <th class="text-center bg-dark text-white">تم بواسطة</th>
                    <th class="text-center bg-dark text-white">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @if ($suppliers->isEmpty())
                    <tr>
                        <td colspan="8" class="text-center">
                            <div class="status-danger fs-6">لم يتم العثور على اي موردين!</div>
                        </td>
                    </tr>
                @else
                    @foreach ($suppliers as $supplier)
                        <tr>
                            <td class="text-center text-primary fw-bold">{{ $supplier->account->code }}</td>
                            <td class="text-center">
                                <a href="{{ route('users.supplier.profile', $supplier) }}"
                                    class="text-dark fw-bold text-decoration-none">
                                    {{ $supplier->name }}
                                </a>
                            </td>
                            <td class="text-center">{{ $supplier->CR }}</td>
                            <td class="text-center">{{ $supplier->national_address }}</td>
                            <td class="text-center">{{ $supplier->phone ?? '-' }}</td>
                            <td class="text-center">{{ $supplier->email ?? '-' }}</td>
                            <td class="text-center">{{ $supplier->made_by->name ?? '-' }}</td>
                            <td class="action-icons text-center">
                                <button class="btn btn-link p-0 pb-1 me-2" type="button" data-bs-toggle="modal"
                                    data-bs-target="#editSupplierModal{{ $supplier->id }}">
                                    <i class="fa-solid fa-pen-to-square text-primary" title="تعديل المورد"></i>
                                </button>
                                <button class="btn btn-link p-0 pb-1 m-0" type="button" data-bs-toggle="modal"
                                    data-bs-target="#deleteSupplierModal{{ $supplier->id }}">
                                    <i class="fa-solid fa-user-xmark text-danger" title="حذف المورد"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- update modal --}}
                        <div class="modal fade" id="editSupplierModal{{ $supplier->id }}" tabindex="-1"
                            aria-labelledby="editSupplierModalLabel{{ $supplier->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-dark fw-bold"
                                            id="editSupplierModalLabel{{ $supplier->id }}">تعديل بيانات المورد</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('users.supplier.update', $supplier) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                                        <div class="modal-body text-dark">
                                            <div class="row mb-3">
                                                <div class="col">
                                                    <label for="name" class="form-label">إسم المورد</label>
                                                    <input type="text" class="form-control border-primary"
                                                        id="name" name="name" value="{{ $supplier->name }}">
                                                    @error('name')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col">
                                                    <label for="CR" class="form-label">السجل التجاري</label>
                                                    <input type="text" class="form-control border-primary"
                                                        id="CR" name="CR" value="{{ $supplier->CR }}">
                                                    @error('CR')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col">
                                                    <label for="vatNumber" class="form-label">الرقم الضريبي</label>
                                                    <input type="text" class="form-control border-primary"
                                                        id="vatNumber" name="vat_number"
                                                        value="{{ $supplier->vat_number }}">
                                                    @error('vat_number')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col">
                                                    <label for="national_address" class="form-label">العنوان
                                                        الوطني</label>
                                                    <input type="text" class="form-control border-primary"
                                                        id="national_address" name="national_address"
                                                        value="{{ $supplier->national_address }}">
                                                    @error('national_address')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col">
                                                    <label for="phone" class="form-label">رقم الهاتف</label>
                                                    <input type="text" class="form-control border-primary"
                                                        id="phone" name="phone" value="{{ $supplier->phone }}">
                                                    @error('phone')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col">
                                                    <label for="email" class="form-label">البريد الإلكتروني</label>
                                                    <input type="text" class="form-control border-primary"
                                                        id="email" name="email" value="{{ $supplier->email }}">
                                                    @error('email')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer d-flex justify-content-start">
                                            <button type="submit" class="btn btn-primary fw-bold">حفظ التغييرات</button>
                                            <button type="button" class="btn btn-secondary fw-bold"
                                                data-bs-dismiss="modal">إلغاء</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- delete modal --}}
                        <div class="modal fade" id="deleteSupplierModal{{ $supplier->id }}" tabindex="-1"
                            aria-labelledby="deleteSupplierModalLabel{{ $supplier->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-dark fw-bold"
                                            id="deleteSupplierModalLabel{{ $supplier->id }}">تأكيد الحذف</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center text-dark">
                                        هل انت متأكد من حذف المورد <strong>{{ $supplier->name }}</strong>؟
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary fw-bold"
                                            data-bs-dismiss="modal">إلغاء</button>
                                        <form action="{{ route('users.supplier.delete', $supplier) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger fw-bold">حذف</button>
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
    
@endsection
