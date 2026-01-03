@extends('layouts.admin')

@section('title', 'المستخدمين')

@section('content')
    <style>
        /* Only essential custom styles that Bootstrap can't handle */
        .table-container::after {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            width: 30px;
            background: linear-gradient(to left, rgba(0, 0, 0, 0.1), transparent);
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .table-container.has-scroll::after {
            opacity: 1;
        }
    </style>

    <h1 class="mb-3 mb-md-4 fs-3 fs-md-1">المستخدمـــين</h1>

    <!-- Search and Filter Section -->
    <div class="row g-3 mb-4">
        <!-- Search Form -->
        <div class="col-12 col-lg-8">
            <form method="GET" action="{{ route('admin.users') }}" class="d-flex flex-column">
                <label for="search" class="form-label text-dark fw-bold mb-2">بحث عن مستخدم:</label>
                <div class="d-flex gap-2">
                    <input type="text" name="search" class="form-control border-primary flex-grow-1"
                        placeholder="ابحث عن مستخدم بالإسم أو البريد الإلكتروني..."
                        value="{{ request()->query('search') }}">
                    <button type="submit" class="btn btn-primary fw-bold d-flex align-items-center px-3 px-md-4">
                        <span class="d-none d-sm-inline">بحث</span>
                        <i class="fa-solid fa-magnifying-glass ms-0 ms-sm-2"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Add User Button -->
        <div class="col-12 col-lg-4">
            <label class="form-label d-none d-lg-block opacity-0 user-select-none">.</label>
            <button class="btn btn-primary w-100 fw-bold d-flex align-items-center justify-content-center" type="button"
                data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fa-solid fa-user-plus me-2"></i>
                <span>إضافة مستخدم جديد</span>
            </button>
        </div>
    </div>

    <!-- Create User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white fw-bold" id="addUserModalLabel">إضافة مستخدم جديد</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body text-dark">
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label for="name" class="form-label">اسم المستخدم</label>
                                <input type="text" class="form-control border-primary" id="name" name="name"
                                    value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <input type="email" class="form-control border-primary" id="email" name="email"
                                    value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label for="password" class="form-label">كلمة المرور</label>
                                <input type="password" class="form-control border-primary" id="password" name="password"
                                    required>
                                @error('password')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                                <input type="password" class="form-control border-primary" id="password_confirmation"
                                    name="password_confirmation" required>
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label for="phone" class="form-label">رقم الهاتف</label>
                                <input type="text" class="form-control border-primary" id="phone" name="phone"
                                    value="{{ old('phone') }}">
                                @error('phone')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="nationality" class="form-label">الجنسية</label>
                                <input type="text" class="form-control border-primary" id="nationality"
                                    name="nationality" value="{{ old('nationality') }}">
                                @error('nationality')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label for="NID" class="form-label">رقم الهوية</label>
                                <input type="text" class="form-control border-primary" id="NID" name="NID"
                                    value="{{ old('NID') }}">
                                @error('NID')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="avatar" class="form-label">صورة المستخدم</label>
                                <input type="file" class="form-control border-primary" id="avatar" name="avatar"
                                    accept="image/*">
                                @error('avatar')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex flex-column flex-sm-row justify-content-start gap-2">
                        <button type="submit" class="btn btn-primary fw-bold order-2 order-sm-1">إنشاء</button>
                        <button type="button" class="btn btn-secondary fw-bold order-1 order-sm-2"
                            data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="table-container" id="tableContainer">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th class="text-center bg-dark text-white text-nowrap">#</th>
                    <th class="text-center bg-dark text-white text-nowrap">اسم المستخدم</th>
                    <th class="text-center bg-dark text-white text-nowrap">البريد الإلكتروني</th>
                    <th class="text-center bg-dark text-white text-nowrap">رقم الهاتف</th>
                    <th class="text-center bg-dark text-white text-nowrap">الجنسية</th>
                    <th class="text-center bg-dark text-white text-nowrap">رقم الهوية</th>
                    <th class="text-center bg-dark text-white text-nowrap">الشركة</th>
                    <th class="text-center bg-dark text-white text-nowrap">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @if ($users->isEmpty())
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <div class="status-danger fs-6">لم يتم العثور على أي مستخدمين!</div>
                        </td>
                    </tr>
                @else
                    @foreach ($users as $user)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center fw-bold">{{ $user->name }}</td>
                            <td class="text-center text-nowrap">{{ $user->email }}</td>
                            <td class="text-center text-nowrap">{{ $user->phone ?? '-' }}</td>
                            <td class="text-center text-nowrap">{{ $user->nationality ?? '-' }}</td>
                            <td class="text-center text-nowrap">{{ $user->NID ?? '-' }}</td>
                            <td class="text-center text-nowrap">{{ $user->company->name ?? '-' }}</td>
                            <td class="text-center text-nowrap">
                                <button class="btn btn-link p-0 pb-1 me-1 me-md-2" type="button" data-bs-toggle="modal"
                                    data-bs-target="#editUserModal{{ $user->id }}">
                                    <i class="fa-solid fa-pen-to-square text-primary" title="تعديل المستخدم"></i>
                                </button>
                                <button class="btn btn-link p-0 pb-1 m-0" type="button" data-bs-toggle="modal"
                                    data-bs-target="#deleteUserModal{{ $user->id }}">
                                    <i class="fa-solid fa-trash-can text-danger" title="حذف المستخدم"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Edit User Modal -->
                        <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1"
                            aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h5 class="modal-title text-white fw-bold"
                                            id="editUserModalLabel{{ $user->id }}">تعديل بيانات المستخدم</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('admin.users.update', $user) }}" method="POST"
                                        enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body text-dark">
                                            <div class="row g-3 mb-3">
                                                <div class="col-12 col-md-6">
                                                    <label for="name{{ $user->id }}" class="form-label">اسم
                                                        المستخدم</label>
                                                    <input type="text" class="form-control border-primary"
                                                        id="name{{ $user->id }}" name="name"
                                                        value="{{ $user->name }}" required>
                                                    @error('name')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label for="email{{ $user->id }}" class="form-label">البريد
                                                        الإلكتروني</label>
                                                    <input type="email" class="form-control border-primary"
                                                        id="email{{ $user->id }}" name="email"
                                                        value="{{ $user->email }}" required>
                                                    @error('email')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row g-3 mb-3">
                                                <div class="col-12 col-md-6">
                                                    <label for="password{{ $user->id }}" class="form-label">كلمة
                                                        المرور الجديدة</label>
                                                    <input type="password" class="form-control border-primary"
                                                        id="password{{ $user->id }}" name="password">
                                                    <small class="text-muted">اتركه فارغاً للإبقاء على كلمة المرور
                                                        الحالية</small>
                                                    @error('password')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label for="password_confirmation{{ $user->id }}"
                                                        class="form-label">تأكيد كلمة المرور</label>
                                                    <input type="password" class="form-control border-primary"
                                                        id="password_confirmation{{ $user->id }}"
                                                        name="password_confirmation">
                                                </div>
                                            </div>
                                            <div class="row g-3 mb-3">
                                                <div class="col-12 col-md-6">
                                                    <label for="phone{{ $user->id }}" class="form-label">رقم
                                                        الهاتف</label>
                                                    <input type="text" class="form-control border-primary"
                                                        id="phone{{ $user->id }}" name="phone"
                                                        value="{{ $user->phone }}">
                                                    @error('phone')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label for="nationality{{ $user->id }}"
                                                        class="form-label">الجنسية</label>
                                                    <input type="text" class="form-control border-primary"
                                                        id="nationality{{ $user->id }}" name="nationality"
                                                        value="{{ $user->nationality }}">
                                                    @error('nationality')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row g-3 mb-3">
                                                <div class="col-12 col-md-6">
                                                    <label for="NID{{ $user->id }}" class="form-label">رقم
                                                        الهوية</label>
                                                    <input type="text" class="form-control border-primary"
                                                        id="NID{{ $user->id }}" name="NID"
                                                        value="{{ $user->NID }}">
                                                    @error('NID')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label for="avatar{{ $user->id }}" class="form-label">صورة
                                                        المستخدم</label>
                                                    @if ($user->avatar)
                                                        <div class="mb-2">
                                                            <img src="{{ asset('storage/' . $user->avatar) }}"
                                                                alt="{{ $user->name }}" class="rounded-circle border"
                                                                style="width: 60px; height: 60px; object-fit: cover;">
                                                        </div>
                                                    @endif
                                                    <input type="file" class="form-control border-primary"
                                                        id="avatar{{ $user->id }}" name="avatar" accept="image/*">
                                                    <small class="text-muted">اتركه فارغاً للإبقاء على الصورة
                                                        الحالية</small>
                                                    @error('avatar')
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

                        <!-- Delete User Modal -->
                        <div class="modal fade" id="deleteUserModal{{ $user->id }}" tabindex="-1"
                            aria-labelledby="deleteUserModalLabel{{ $user->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <h5 class="modal-title text-white fw-bold fs-6"
                                            id="deleteUserModalLabel{{ $user->id }}">تأكيد الحذف</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center text-dark px-4 py-4">
                                        هل أنت متأكد من حذف المستخدم <strong>{{ $user->name }}</strong>؟
                                    </div>
                                    <div class="modal-footer d-flex flex-column flex-sm-row justify-content-center gap-2">
                                        <button type="button" class="btn btn-secondary fw-bold order-1"
                                            data-bs-dismiss="modal">إلغاء</button>
                                        <form action="" method="POST" class="order-2">
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
    <div class="text-center text-secondary small mt-2 d-md-none">
        <i class="fa-solid fa-arrows-left-right me-1"></i>
        اسحب الجدول لليمين أو اليسار لرؤية المزيد
    </div>

    <!-- Pagination -->
    @if (method_exists($users, 'links'))
        <div class="mt-3 mt-md-4 d-flex justify-content-center">
            {{ $users->links('components.pagination') }}
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tableContainer = document.getElementById('tableContainer');

            // Check if table needs scrolling
            function checkScroll() {
                if (tableContainer && tableContainer.scrollWidth > tableContainer.clientWidth) {
                    tableContainer.classList.add('has-scroll');
                } else if (tableContainer) {
                    tableContainer.classList.remove('has-scroll');
                }
            }

            // Check on load and resize
            checkScroll();
            window.addEventListener('resize', checkScroll);

            // Remove scroll hint after first interaction
            const scrollHint = document.querySelector('.scroll-hint');
            if (scrollHint && tableContainer) {
                tableContainer.addEventListener('scroll', function() {
                    scrollHint.style.display = 'none';
                }, {
                    once: true
                });
            }
        });
    </script>
@endsection
