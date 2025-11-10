@extends('layouts.app')

@section('title', 'العملاء')

@section('content')
<h1 class="mb-4">العمـــلاء</h1>

<div class="row mb-4">
    <div class="col-md-6">
        <form method="GET" action="" class="d-flex flex-column">
            <label for="search" class="form-label text-dark fw-bold">بحث عن عميل:</label>
            <div class="d-flex">
                <input type="text" name="search" class="form-control border-primary" placeholder=" ابحث عن عميل بالإيميل او بالإسم... "
                    value="{{ request()->query('search') }}">
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
                <select id="statusFilter" name="role" class="form-select border-primary" onchange="this.form.submit()">
                    <option value="all"
                        {{ request()->query('role') === 'all' || !request()->query('role') ? 'selected' : '' }}>
                        جميع العملاء</option>
                </select>
                @if (request()->query('search'))
                    <input type="hidden" name="search" value="{{ request()->query('search') }}">
                @endif
            </div>
        </form>
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <button class="btn btn-primary w-100 fw-bold" type="button" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fa-solid fa-user-plus pe-1"></i>
            أضف عميل
        </button>
    </div>
</div>

<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark fw-bold" id="createUserModalLabel">إنشاء عميل جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('users.customer.store') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                <div class="modal-body text-dark">
                    <div class="row mb-3">
                        <div class="col">
                            <label for="name" class="form-label">إسم العميل</label>
                            <input type="text" class="form-control border-primary" id="name" name="name" value="{{ old('name') }}">
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col">
                            <label for="CR" class="form-label">السجل التجاري</label>
                            <input type="text" class="form-control border-primary" id="CR" name="CR" value="{{ old('CR') }}">
                            @error('CR')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">الرقم الضريبي</label>
                            <input type="text" class="form-control border-primary" name="vatNumber" value="{{ old('vatNumber') }}">
                            @error('vatNumber')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col">
                            <label for="national_address" class="form-label">العنوان الوطني</label>
                            <input type="text" class="form-control border-primary" id="national_address" name="national_address" value="{{ old('national_address') }}">
                            @error('national_address')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="phone" class="form-label">رقم الهاتف</label>
                            <input type="text" class="form-control border-primary" id="phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input type="text" class="form-control border-primary" id="email" name="email" value="{{ old('email') }}">
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
                <th class="text-center bg-dark text-white">رقم حساب العميل</th>
                <th class="text-center bg-dark text-white">إسم العميل</th>
                <th class="text-center bg-dark text-white">رقم السجل التجاري</th>
                <th class="text-center bg-dark text-white">العنوان الوطني</th>
                <th class="text-center bg-dark text-white">رقم الهاتف</th>
                <th class="text-center bg-dark text-white">الإيميل</th>
                <th class="text-center bg-dark text-white">تم بواسطة</th>
                <th class="text-center bg-dark text-white">الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @if ($customers->isEmpty())
                <tr>
                    <td colspan="8" class="text-center">
                        <div class="status-danger fs-6">لم يتم العثور على اي عملاء!</div>
                    </td>
                </tr>
            @else
                @foreach ($customers as $customer)
                    <tr>
                        <td class="text-center text-primary fw-bold">{{ $customer->account->code }}</td>
                        <td class="text-center">
                            <a href="{{ route('users.customer.profile', $customer) }}"
                                class="text-dark fw-bold text-decoration-none">
                                {{ $customer->name }}
                            </a>
                        </td>
                        <td class="text-center">{{ $customer->CR }}</td>
                        <td class="text-center">{{ $customer->national_address }}</td>
                        <td class="text-center">{{ $customer->phone ?? '-' }}</td>
                        <td class="text-center">{{ $customer->email ?? '-' }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.user.profile', $customer->made_by) }}" class="text-dark text-decoration-none">
                                {{ $customer->made_by->name ?? '-' }}
                            </a>
                        </td>
                        <td class="action-icons text-center">
                            <button class="btn btn-link p-0 pb-1 me-2" type="button" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $customer->id }}">
                                <i class="fa-solid fa-pen-to-square text-primary" title="تعديل العميل"></i>
                            </button>
                            <button class="btn btn-link p-0 pb-1 m-0" type="button" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $customer->id }}">
                                <i class="fa-solid fa-user-xmark text-danger" title="حذف العميل"></i>
                            </button>
                        </td>
                    </tr>

                    {{-- update modal --}}
                    <div class="modal fade" id="editUserModal{{ $customer->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $customer->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-dark fw-bold" id="editUserModalLabel{{ $customer->id }}">تعديل بيانات العميل</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('users.customer.update', $customer) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                                    <div class="modal-body text-dark">
                                        <div class="row mb-3">
                                            <div class="col">
                                                <label for="name" class="form-label">إسم العميل</label>
                                                <input type="text" class="form-control border-primary" id="name" name="name" value="{{ $customer->name }}">
                                                @error('name')
                                                <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col">
                                                <label for="CR" class="form-label">السجل التجاري</label>
                                                <input type="text" class="form-control border-primary" id="CR" name="CR" value="{{ $customer->CR }}">
                                                @error('CR')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col">
                                                <label for="vatNumber" class="form-label">الرقم الضريبي</label>
                                                <input type="text" class="form-control border-primary" id="vatNumber" name="vatNumber" value="{{ $customer->vatNumber }}">
                                                @error('vatNumber')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col">
                                                <label for="national_address" class="form-label">العنوان الوطني</label>
                                                <input type="text" class="form-control border-primary" id="national_address" name="national_address" value="{{ $customer->national_address }}">
                                                @error('national_address')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col">
                                                <label for="phone" class="form-label">رقم الهاتف</label>
                                                <input type="text" class="form-control border-primary" id="phone" name="phone" value="{{ $customer->phone }}">
                                                @error('phone')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col">
                                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                                <input type="text" class="form-control border-primary" id="email" name="email" value="{{ $customer->email }}">
                                                @error('email')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
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

                    {{-- delete modal --}}
                    <div class="modal fade" id="deleteUserModal{{ $customer->id }}" tabindex="-1" aria-labelledby="deleteUserModalLabel{{ $customer->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-dark fw-bold" id="deleteUserModalLabel{{ $customer->id }}">تأكيد الحذف</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center text-dark">
                                    هل انت متأكد من حذف العميل <strong>{{ $customer->name }}</strong>؟
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                                    <form action="{{ route('users.customer.delete', $customer) }}" method="POST">
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
<div class="mt-4">
    {{ $customers->appends(request()->query())->onEachSide(1)->links() }}
</div>
@endsection