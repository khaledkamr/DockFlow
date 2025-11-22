@extends('layouts.app')

@section('title', 'الصلاحيات')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <h1 class="mb-0">الصلاحيات والوظائف</h1>
        <div class="d-flex flex-row gap-2">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPermissionModal">
                <i class="fa-solid fa-plus me-2"></i>
                <span class="d-none d-md-inline">إضافة صلاحية جديدة</span>
                <span class="d-inline d-md-none">إضافة صلاحية</span>
            </button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                <i class="fa-solid fa-plus me-2"></i>
                <span class="d-none d-md-inline">إضافة وظيفة جديدة</span>
                <span class="d-inline d-md-none">إضافة وظيفة</span>
            </button>
        </div>
    </div>

    <!-- Add permission modal -->
    <div class="modal fade" id="addPermissionModal" tabindex="-1" aria-labelledby="addPermissionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-bold" id="addPermissionModalLabel">إضافة صلاحية جديدة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.permissions.store') }}" method="POST">
                    @csrf
                    <div class="modal-body text-dark">
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">اسم الصلاحية</label>
                                <input type="text" class="form-control border-primary" name="name" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex flex-column flex-sm-row justify-content-start gap-2">
                        <button type="submit" class="btn btn-primary fw-bold order-1 order-sm-1">حفظ</button>
                        <button type="button" class="btn btn-secondary fw-bold order-2 order-sm-2"
                            data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="table-container" id="tableContainer">
        <table class="table table-hover">
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
                @foreach ($roles ?? [] as $role)
                    <tr>
                        <td class="text-center text-primary fw-bold text-nowrap">{{ $loop->iteration }}</td>
                        <td class="text-center fw-bold text-nowrap">{{ $role->name }}</td>
                        <td class="text-center text-nowrap">
                            <span class="badge bg-primary rounded-pill">
                                {{ $role->permissions->count() ?? 0 }}
                            </span>
                        </td>
                        <td class="text-center text-nowrap">{{ $role->created_at->format('Y/m/d') ?? 'غير محدد' }}</td>
                        <td class="text-center text-nowrap">
                            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center">
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#editPermissionsModal{{ $role->id }}">
                                    <i class="fa-solid fa-edit me-1"></i>
                                    <span class="d-none d-md-inline">تحديث الصلاحيات</span>
                                    <span class="d-inline d-md-none">تحديث</span>
                                </button>
                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                    data-bs-target="#deleteModal{{ $role->id }}">
                                    <i class="fa-solid fa-trash-can me-1"></i>
                                    <span class="d-none d-md-inline">حذف</span>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Edit Permissions Modal -->
                    <div class="modal fade" id="editPermissionsModal{{ $role->id }}" tabindex="-1"
                        aria-labelledby="editPermissionsModalLabel{{ $role->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold" id="editPermissionsModalLabel{{ $role->id }}">
                                        <i class="fa-solid fa-edit text-primary me-2"></i>
                                        تحديث صلاحيات الوظيفة: <span>{{ $role->name }}</span>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form action="{{ route('admin.roles.update', $role) }}" method="POST"
                                    id="editPermissionsForm">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <div
                                                class="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center mb-2 gap-2">
                                                <label class="form-label fw-bold mb-0">الصلاحيات المتاحة</label>
                                                <div class="d-flex gap-2">
                                                    <button type="button"
                                                        class="btn btn-outline-primary btn-sm flex-grow-1 flex-sm-grow-0"
                                                        onclick="selectAllPermissions({{ $role->id }})">
                                                        <i class="fa-solid fa-check-double me-1"></i>
                                                        تحديد الكل
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-outline-danger btn-sm flex-grow-1 flex-sm-grow-0"
                                                        onclick="deselectAllPermissions({{ $role->id }})">
                                                        <i class="fa-solid fa-times me-1"></i>
                                                        إلغاء الكل
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="border border-primary rounded p-3 bg-light"
                                                style="max-height: 400px; overflow-y: auto;">
                                                <div class="row g-2" id="permissionsContainer">
                                                    @forelse($permissions ?? [] as $permission)
                                                        <div class="col-12 col-md-6">
                                                            <div class="form-check">
                                                                <input
                                                                    class="form-check-input permission-checkbox permission-checkbox-{{ $role->id }}"
                                                                    type="checkbox" value="{{ $permission->id }}"
                                                                    id="edit_permission_{{ $permission->id }}"
                                                                    name="permissions[]"
                                                                    {{ in_array($permission->id, $role->permissions->pluck('id')->toArray()) ? 'checked' : '' }}>
                                                                <label class="form-check-label fw-bold"
                                                                    for="edit_permission_{{ $permission->id }}">
                                                                    {{ $permission->name }}
                                                                </label>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <div class="col-12">
                                                            <p class="text-muted text-center">لا توجد صلاحيات متاحة</p>
                                                        </div>
                                                    @endforelse
                                                </div>
                                            </div>

                                            {{-- <div class="mt-2">
                                            <small class="text-muted">
                                                <span id="selectedCount">0</span> من {{ count($permissions ?? []) }} صلاحية محددة
                                            </small>
                                        </div> --}}
                                        </div>
                                    </div>
                                    <div class="modal-footer d-flex flex-column flex-sm-row justify-content-start gap-2">
                                        <button type="submit" class="btn btn-primary fw-bold order-1 order-sm-1">
                                            تحديث الصلاحيات
                                        </button>
                                        <button type="button" class="btn btn-secondary fw-bold order-2 order-sm-2"
                                            data-bs-dismiss="modal">
                                            إلغاء
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Confirmation Modal -->
                    <div class="modal fade" id="deleteModal{{ $role->id }}" tabindex="-1"
                        aria-labelledby="deleteModalLabel{{ $role->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold" id="deleteModalLabel{{ $role->id }}">
                                        <i class="fa-solid fa-exclamation-triangle text-danger me-2"></i>
                                        تأكيد الحذف
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <p class="fs-5">هل أنت متأكد من حذف هذه الوظيفة؟</p>
                                    <p class="text-muted mb-1">الوظيفة: <strong>{{ $role->name }}</strong></p>
                                </div>
                                <div class="modal-footer d-flex flex-column flex-sm-row justify-content-start gap-2">
                                    <form action="{{ route('admin.roles.delete', $role) }}" method="POST"
                                        id="deleteForm" class="order-1 order-sm-1 w-100 w-sm-auto">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger fw-bold w-100">
                                            حذف الوظيفة
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-secondary fw-bold order-2 order-sm-2"
                                        data-bs-dismiss="modal">
                                        إلغاء
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="scroll-hint text-center text-muted mt-2 d-sm-block d-md-none">
        <i class="fa-solid fa-arrows-left-right me-1"></i>
        اسحب الجدول لليمين أو اليسار لرؤية المزيد
    </div>

    <!-- Add Role Modal -->
    <div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content overflow-hidden">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="addRoleModalLabel">
                        <i class="fa-solid fa-plus text-primary me-2"></i>
                        إضافة وظيفة جديدة
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.roles.store') }}" method="POST" id="addRoleForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="roleName" class="form-label fw-bold">اسم الوظيفة</label>
                            <input type="text" class="form-control border-primary" id="roleName" name="name"
                                placeholder="أدخل اسم الوظيفة...">
                            <div class="invalid-feedback"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">الصلاحيات</label>
                            <div class="border border-primary rounded p-3 bg-light"
                                style="max-height: 300px; overflow-y: auto;">
                                <div class="row g-2">
                                    @forelse($permissions ?? [] as $permission)
                                        <div class="col-12 col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    value="{{ $permission->id }}" id="permission_{{ $permission->id }}"
                                                    name="permissions[]">
                                                <label class="form-check-label fw-bold"
                                                    for="permission_{{ $permission->id }}">
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
                    <div class="modal-footer d-flex flex-column flex-sm-row gap-2 px-4">
                        <button type="submit" class="btn btn-primary fw-bold order-1 order-sm-1 flex-grow-1">
                            حفظ الوظيفة
                        </button>
                        <button type="button" class="btn btn-secondary fw-bold order-2 order-sm-2 flex-grow-1"
                            data-bs-dismiss="modal">
                            إلغاء
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Global function to update selected permissions count
            function updateSelectedCount() {
                const checkedBoxes = document.querySelectorAll('.permission-checkbox:checked').length;
                const countElement = document.getElementById('selectedCount');
                if (countElement) {
                    countElement.textContent = checkedBoxes;
                }
            }

            function selectAllPermissions(roleId) {
                document.querySelectorAll(`.permission-checkbox-${roleId}`).forEach(checkbox => {
                    checkbox.checked = true;
                });
                updateSelectedCount();
            }

            function deselectAllPermissions(roleId) {
                document.querySelectorAll(`.permission-checkbox-${roleId}`).forEach(checkbox => {
                    checkbox.checked = false;
                });
                updateSelectedCount();
            }

            document.addEventListener('DOMContentLoaded', function() {
                // Add event listeners to permission checkboxes
                document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', updateSelectedCount);
                });

                // Initial count update
                updateSelectedCount();
            });

            document.addEventListener('DOMContentLoaded', function() {
                const tableContainer = document.getElementById('tableContainer');
                
                // Check if table needs scrolling
                function checkScroll() {
                    if (tableContainer.scrollWidth > tableContainer.clientWidth) {
                        tableContainer.classList.add('has-scroll');
                    } else {
                        tableContainer.classList.remove('has-scroll');
                    }
                }
                
                // Check on load and resize
                checkScroll();
                window.addEventListener('resize', checkScroll);
                
                // Remove scroll hint after first interaction
                const scrollHint = document.querySelector('.scroll-hint');
                if (scrollHint) {
                    tableContainer.addEventListener('scroll', function() {
                        scrollHint.style.display = 'none';
                    }, { once: true });
                }
            });
        </script>
    @endpush

@endsection
