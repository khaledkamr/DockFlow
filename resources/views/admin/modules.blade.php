@extends('layouts.admin')

@section('title', 'المديولات')

@section('content')
    <h1 class="mb-4">المديولات</h1>

    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-8">
            <form method="GET" action="" class="d-flex flex-column h-100">
                <label for="search" class="form-label text-dark fw-bold">بحث عن مديول:</label>
                <div class="d-flex flex-grow-1">
                    <input type="text" name="search" class="form-control border-primary"
                        placeholder=" ابحث عن مديول بالإسم أو الـ Slug... " value="{{ request()->query('search') }}">
                    <button type="submit" class="btn btn-primary fw-bold ms-2 d-flex align-items-center">
                        <span class="d-none d-sm-inline">بحث</span>
                        <i class="fa-solid fa-magnifying-glass ms-sm-2"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="col-12 col-sm-12 col-lg-4 d-flex align-items-end">
            <button class="btn btn-primary w-100 fw-bold" type="button" data-bs-toggle="modal"
                data-bs-target="#createModuleModal">
                <i class="fa-solid fa-cube pe-1"></i>
                <span class="d-none d-sm-inline">أضف مديول</span>
                <span class="d-inline d-sm-none">إضافة</span>
            </button>
        </div>
    </div>

    <div class="modal fade" id="createModuleModal" tabindex="-1" aria-labelledby="createModuleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white fw-bold" id="createModuleModalLabel">إنشاء مديول جديد</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.modules.store') }}" method="POST">
                    @csrf
                    <div class="modal-body text-dark">
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">اسم المديول</label>
                                <input type="text" class="form-control border-primary" name="name"
                                    value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">Slug</label>
                                <input type="text" class="form-control border-primary" name="slug"
                                    value="{{ old('slug') }}" required>
                                @error('slug')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-12">
                                <label class="form-label">الوصف</label>
                                <textarea class="form-control border-primary" name="description" rows="4">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex flex-column flex-sm-row justify-content-start gap-2">
                        <button type="button" class="btn btn-secondary fw-bold order-2 order-sm-1"
                            data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary fw-bold order-1 order-sm-2">إنشاء</button>
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
                    <th class="text-center bg-dark text-white text-nowrap">اسم المديول</th>
                    <th class="text-center bg-dark text-white text-nowrap">Slug</th>
                    <th class="text-center bg-dark text-white text-nowrap">الوصف</th>
                    <th class="text-center bg-dark text-white text-nowrap">عدد الشركات المستخدمة</th>
                    <th class="text-center bg-dark text-white text-nowrap">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @if ($modules->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center">
                            <div class="status-danger fs-6">لم يتم العثور على اي مديولات!</div>
                        </td>
                    </tr>
                @else
                    @foreach ($modules as $module)
                        <tr>
                            <td class="text-center text-primary fw-bold text-nowrap">{{ $loop->iteration }}</td>
                            <td class="text-center text-nowrap">
                                <span class="text-dark fw-bold">
                                    {{ $module->name }}
                                </span>
                            </td>
                            <td class="text-center text-nowrap">
                                {{ $module->slug }}
                            </td>
                            <td class="text-center text-nowrap">
                                @if ($module->description)
                                    <span class="text-muted" title="{{ $module->description }}">
                                        {{ Str::limit($module->description, 50) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                <span class="badge status-available">
                                    {{ $module->companies->count() ?? 0 }}
                                </span>
                            </td>
                            <td class="action-icons text-center text-nowrap">
                                <button class="btn btn-link p-0 pb-1 me-2" type="button" data-bs-toggle="modal"
                                    data-bs-target="#editModuleModal{{ $module->id }}">
                                    <i class="fa-solid fa-pen-to-square text-primary" title="تعديل المديول"></i>
                                </button>
                                <button class="btn btn-link p-0 pb-1 m-0" type="button" data-bs-toggle="modal"
                                    data-bs-target="#deleteModuleModal{{ $module->id }}">
                                    <i class="fa-solid fa-trash text-danger" title="حذف المديول"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- update modal --}}
                        <div class="modal fade" id="editModuleModal{{ $module->id }}" tabindex="-1"
                            aria-labelledby="editModuleModalLabel{{ $module->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h5 class="modal-title text-white fw-bold"
                                            id="editModuleModalLabel{{ $module->id }}">تعديل المديول</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('admin.modules.update', $module) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body text-dark">
                                            <div class="row g-3 mb-3">
                                                <div class="col-12 col-md-6">
                                                    <label class="form-label">اسم المديول</label>
                                                    <input type="text" class="form-control border-primary"
                                                        name="name" value="{{ $module->name }}" required>
                                                    @error('name')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label class="form-label">Slug</label>
                                                    <input type="text" class="form-control border-primary"
                                                        name="slug" value="{{ $module->slug }}" required>
                                                    @error('slug')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row g-3 mb-3">
                                                <div class="col-12">
                                                    <label class="form-label">الوصف</label>
                                                    <textarea class="form-control border-primary" name="description" rows="4">{{ $module->description }}</textarea>
                                                    @error('description')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer d-flex flex-column flex-sm-row justify-content-start gap-2">
                                            <button type="button" class="btn btn-secondary fw-bold order-2 order-sm-1"
                                                data-bs-dismiss="modal">إلغاء</button>
                                            <button type="submit" class="btn btn-primary fw-bold order-1 order-sm-2">حفظ
                                                التغييرات</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- delete modal --}}
                        <div class="modal fade" id="deleteModuleModal{{ $module->id }}" tabindex="-1"
                            aria-labelledby="deleteModuleModalLabel{{ $module->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <h5 class="modal-title text-white fw-bold"
                                            id="deleteModuleModalLabel{{ $module->id }}">تأكيد الحذف</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center text-dark">
                                        هل انت متأكد من حذف المديولش <strong>{{ $module->name }}</strong>؟
                                    </div>
                                    <div class="modal-footer d-flex flex-column flex-sm-row justify-content-center gap-2">
                                        <button type="button" class="btn btn-secondary fw-bold order-2 order-sm-1"
                                            data-bs-dismiss="modal">إلغاء</button>
                                        <form action="{{ route('admin.modules.delete', $module) }}" method="POST"
                                            class="order-1 order-sm-2 w-100 w-sm-auto">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger fw-bold w-100">حذف</button>
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

    <div class="scroll-hint text-center text-muted mt-2 d-sm-block d-md-none">
        <i class="fa-solid fa-arrows-left-right me-1"></i>
        اسحب الجدول لليمين أو اليسار لرؤية المزيد
    </div>

    <div class="mt-4">
        @if (method_exists($modules, 'appends'))
            {{ $modules->links('components.pagination') }}
        @endif
    </div>

    <script>
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
                }, {
                    once: true
                });
            }
        });
    </script>
@endsection
