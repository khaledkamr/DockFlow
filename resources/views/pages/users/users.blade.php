@extends('layouts.app')

@section('title', 'المستخدمين')

@section('content')
    <h1 class="mb-4">المستخدمين</h1>

    <div class="row mb-4">
        <div class="col-md-6">
            <form method="GET" action="" class="d-flex flex-column">
                <label for="search" class="form-label text-dark fw-bold">بحث عن مستخدم:</label>
                <div class="d-flex">
                    <input type="text" name="search" class="form-control border-primary"
                        placeholder=" ابحث عن مستخدم بالإيميل او بالإسم... " value="{{ request()->query('search') }}">
                    <button type="submit" class="btn btn-primary fw-bold ms-2 d-flex align-items-center">
                        <span>بحث</span>
                        <i class="fa-solid fa-magnifying-glass ms-2"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="col-md-4">
            <form method="GET" action="" class="d-flex flex-column">
                <label for="statusFilter" class="form-label text-dark fw-bold">تصفية حسب الصلاحية:</label>
                <div class="d-flex">
                    <select id="statusFilter" name="role" class="form-select border-primary"
                        onchange="this.form.submit()">
                        <option value="all"
                            {{ request()->query('role') === 'all' || !request()->query('role') ? 'selected' : '' }}>
                            جميع المستخدمين</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}"
                                {{ request()->query('role') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    @if (request()->query('search'))
                        <input type="hidden" name="search" value="{{ request()->query('search') }}">
                    @endif
                </div>
            </form>
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-primary w-100 fw-bold" type="button" data-bs-toggle="modal"
                data-bs-target="#createUserModal">
                <i class="fa-solid fa-user-plus pe-1"></i>
                أضف مستخدم
            </button>
        </div>
    </div>

    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-bold" id="createUserModalLabel">إنشاء مستخدم جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @csrf
                    <div class="modal-body text-dark">
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">إسم المستخدم</label>
                                <input type="text" class="form-control border-primary" name="name"
                                    value="{{ old('name') }}">
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label class="form-label">الإيميل</label>
                                <input type="email" class="form-control border-primary" name="email"
                                    value="{{ old('email') }}">
                                @error('email')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">كلمة المرور</label>
                                <input type="password" class="form-control border-primary" name="password">
                                @error('password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label class="form-label">تأكيد كلمة المرور</label>
                                <input type="password" class="form-control border-primary" name="password_confirmation">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">الجنسية</label>
                                <input type="text" class="form-control border-primary" name="nationality"
                                    value="{{ old('nationality') }}">
                                @error('nationality')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label class="form-label">رقم الهوية الوطنية</label>
                                <input type="text" class="form-control border-primary" name="NID"
                                    value="{{ old('NID') }}">
                                @error('NID')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">رقم الهاتف</label>
                                <input type="text" class="form-control border-primary" name="phone"
                                    value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col">
                                <label class="form-label">الوظيفة</label>
                                <select class="form-select border-primary" name="role">
                                    <option value="">اختر الوظيفة</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" {{ old('role') === $role->id ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('role')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-start">
                        <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary fw-bold">إنشاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="table-container">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th class="text-center bg-dark text-white">رقم المستخدم</th>
                    <th class="text-center bg-dark text-white">إسم المستخدم</th>
                    <th class="text-center bg-dark text-white">الإيميل</th>
                    <th class="text-center bg-dark text-white">رقم الهاتف</th>
                    <th class="text-center bg-dark text-white">الوظيفة</th>
                    <th class="text-center bg-dark text-white">تاريخ الإنشاء</th>
                    <th class="text-center bg-dark text-white">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @if ($users->isEmpty())
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="status-danger fs-6">لم يتم العثور على اي مستخدمين!</div>
                        </td>
                    </tr>
                @else
                    @foreach ($users as $index => $user)
                        <tr>
                            <td class="text-center text-primary fw-bold">{{ $index + 1 }}</td>
                            <td class="text-center">
                                <a href="{{ route('admin.user.profile', $user) }}" class="text-dark fw-bold text-decoration-none">
                                    {{ $user->name }}
                                </a>
                            </td>
                            <td class="text-center">{{ $user->email }}</td>
                            <td class="text-center">{{ $user->phone ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge bg-primary">
                                    {{ $user->roles->first()->name }}
                                </span>
                            </td>
                            <td class="text-center">{{ $user->created_at->format('Y/m/d') }}</td>
                            <td class="action-icons text-center">
                                <button class="btn btn-link p-0 pb-1 me-2" type="button" data-bs-toggle="modal"
                                    data-bs-target="#editUserModal{{ $user->id }}">
                                    <i class="fa-solid fa-pen-to-square text-primary" title="تعديل المستخدم"></i>
                                </button>
                                @if ($user->id !== auth()->user()->id)
                                    <button class="btn btn-link p-0 pb-1 m-0" type="button" data-bs-toggle="modal"
                                        data-bs-target="#deleteUserModal{{ $user->id }}">
                                        <i class="fa-solid fa-user-xmark text-danger" title="حذف المستخدم"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>

                        {{-- update modal --}}
                        <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1"
                            aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-dark fw-bold"
                                            id="editUserModalLabel{{ $user->id }}">تعديل بيانات المستخدم</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('admin.users.update', $user) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="modal-body text-dark">
                                            <div class="row mb-3">
                                                <div class="col">
                                                    <label class="form-label">إسم المستخدم</label>
                                                    <input type="text" class="form-control border-primary"
                                                        name="name" value="{{ $user->name }}"
                                                        required>
                                                    @error('name')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col">
                                                    <label class="form-label">الإيميل</label>
                                                    <input type="email" class="form-control border-primary"
                                                        name="email" value="{{ $user->email }}"
                                                        required>
                                                    @error('email')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col">
                                                    <label class="form-label">رقم الهاتف</label>
                                                    <input type="text" class="form-control border-primary"
                                                        name="phone" value="{{ $user->phone }}">
                                                    @error('phone')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col">
                                                    <label class="form-label">الصلاحية</label>
                                                    <select class="form-select border-primary"
                                                        name="role" required>
                                                        @foreach ($roles as $role)
                                                            <option value="{{ $role->id }}" {{ $user->roles->first()->id === $role->id ? 'selected' : '' }}>
                                                                {{ $role->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('role')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer d-flex justify-content-start">
                                            <button type="button" class="btn btn-secondary fw-bold"
                                                data-bs-dismiss="modal">إلغاء</button>
                                            <button type="submit" class="btn btn-primary fw-bold">حفظ التغييرات</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- delete modal --}}
                        @if ($user->id !== auth()->user()->id)
                            <div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1"
                                aria-labelledby="deleteUserModalLabel{{ $user->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title text-dark fw-bold"
                                                id="deleteUserModalLabel{{ $user->id }}">تأكيد الحذف</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body text-center text-dark">
                                            هل انت متأكد من حذف المستخدم <strong>{{ $user->name }}</strong>؟
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary fw-bold"
                                                data-bs-dismiss="modal">إلغاء</button>
                                            <form action="{{ route('admin.users.delete', $user) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger fw-bold">حذف</button>
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
    <div class="mt-4">
        {{-- @if (method_exists($users, 'appends'))
            {{ $users->appends(request()->query())->onEachSide(1)->links() }}
        @endif --}}
    </div>
@endsection
