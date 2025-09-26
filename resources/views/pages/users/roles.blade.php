@extends('layouts.app')

@section('title', 'الصلاحيات')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>الصلاحيات والوظائف</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
        <i class="fa-solid fa-plus me-2"></i>
        إضافة وظيفة جديدة
    </button>
</div>

<div class="table-container">
    <table class="table table-hover">
        <thead>
            <tr>
                <th class="text-center bg-dark text-white">#</th>
                <th class="text-center bg-dark text-white">اسم الوظيفة</th>
                <th class="text-center bg-dark text-white">عدد الصلاحيات</th>
                <th class="text-center bg-dark text-white">تاريخ الإنشاء</th>
                <th class="text-center bg-dark text-white">الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($roles ?? [] as $role)
                <tr>
                    <td class="text-center text-primary fw-bold">{{ $loop->iteration }}</td>
                    <td class="text-center fw-bold">{{ $role->name }}</td>
                    <td class="text-center">
                        <span class="badge bg-primary rounded-pill">
                            {{ $role->permissions->count() ?? 0 }}
                        </span>
                    </td>
                    <td class="text-center">{{ $role->created_at->format('Y/m/d') ?? 'غير محدد' }}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editPermissionsModal{{ $role->id }}">
                            <i class="fa-solid fa-edit me-1"></i>
                            تحديث الصلاحيات
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $role->id }}">
                            <i class="fa-solid fa-trash-can me-1"></i>
                            حذف
                        </button>
                    </td>
                </tr>

                <!-- Edit Permissions Modal -->
                <div class="modal fade" id="editPermissionsModal{{ $role->id }}" tabindex="-1" aria-labelledby="editPermissionsModalLabel{{ $role->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold" id="editPermissionsModalLabel{{ $role->id }}">
                                    <i class="fa-solid fa-edit text-primary me-2"></i>
                                    تحديث صلاحيات الوظيفة: <span>{{ $role->name }}</span>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('admin.roles.update', $role) }}" method="POST" id="editPermissionsForm">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label fw-bold">الصلاحيات المتاحة</label>
                                            <div>
                                                <button type="button" class="btn btn-outline-primary btn-sm me-1" onclick="selectAllPermissions({{ $role->id }})">
                                                    <i class="fa-solid fa-check-double me-1"></i>
                                                    تحديد الكل
                                                </button>
                                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="deselectAllPermissions({{ $role->id }})">
                                                    <i class="fa-solid fa-times me-1"></i>
                                                    إلغاء الكل
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="border border-primary rounded p-3 bg-light" style="max-height: 400px; overflow-y: auto;">
                                            <div class="row" id="permissionsContainer">
                                                @forelse($permissions ?? [] as $permission)
                                                    <div class="col-md-6 mb-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input permission-checkbox permission-checkbox-{{ $role->id }}" 
                                                                type="checkbox" value="{{ $permission->id }}" 
                                                                id="edit_permission_{{ $permission->id }}" name="permissions[]" 
                                                                {{ in_array($permission->id, $role->permissions->pluck('id')->toArray()) ? 'checked' : '' }}>
                                                            <label class="form-check-label fw-bold" for="edit_permission_{{ $permission->id }}">
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
                                <div class="modal-footer d-flex justify-content-start">
                                    <button type="submit" class="btn btn-primary fw-bold">
                                        تحديث الصلاحيات
                                    </button>
                                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">
                                        إلغاء
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Delete Confirmation Modal -->
                <div class="modal fade" id="deleteModal{{ $role->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $role->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold" id="deleteModalLabel{{ $role->id }}">
                                    <i class="fa-solid fa-exclamation-triangle text-danger me-2"></i>
                                    تأكيد الحذف
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center">
                                <p class="fs-5">هل أنت متأكد من حذف هذه الوظيفة؟</p>
                                <p class="text-muted mb-1">الوظيفة: <strong>{{ $role->name }}</strong></p>
                            </div>
                            <div class="modal-footer d-flex justify-content-start">
                                <form action="{{ route('admin.roles.delete', $role) }}" method="POST" id="deleteForm" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger fw-bold">
                                        حذف الوظيفة
                                    </button>
                                </form>
                                <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">
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
                        <input type="text" class="form-control border-primary" id="roleName" name="name" placeholder="أدخل اسم الوظيفة...">
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">الصلاحيات</label>
                        <div class="border border-primary rounded p-3 bg-light" style="max-height: 300px; overflow-y: auto;">
                            @forelse($permissions ?? [] as $permission)
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" value="{{ $permission->id }}" 
                                       id="permission_{{ $permission->id }}" name="permissions[]">
                                <label class="form-check-label fw-bold" for="permission_{{ $permission->id }}">
                                    {{ $permission->name }}
                                </label>
                            </div>
                            @empty
                                <p class="text-muted text-center">لا توجد صلاحيات متاحة</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="modal-footer row px-4">
                    <button type="submit" class="col btn btn-primary fw-bold">
                        حفظ الوظيفة
                    </button>
                    <button type="button" class="col btn btn-secondary fw-bold" data-bs-dismiss="modal">
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
</script>
@endpush

@endsection