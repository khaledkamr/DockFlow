<style>
    /* Minimal custom styles - mostly using Bootstrap */
    .table-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        position: relative;
    }

    /* Add scroll indicator shadow */
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

    .table {
        min-width: 400px;
    }

    .table thead th,
    .table tbody td {
        white-space: nowrap;
    }

    @media (max-width: 768px) {
        .table thead th {
            font-size: 13px;
            padding: 12px 8px;
        }

        .table tbody td {
            font-size: 13px;
            padding: 12px 8px;
        }

        .action-icons i {
            font-size: 18px;
        }
    }

    @media (max-width: 576px) {
        .table thead th {
            font-size: 12px;
            padding: 10px 6px;
        }

        .table tbody td {
            font-size: 12px;
            padding: 10px 6px;
        }

        .table {
            min-width: 450px;
        }
    }

    /* Scroll hint */
    .scroll-hint {
        display: none;
        text-align: center;
        color: #6c757d;
        font-size: 14px;
        margin-top: 10px;
    }

    @media (max-width: 768px) {
        .scroll-hint {
            display: block;
        }
    }
</style>

<!-- Search and Add Section -->
<div class="row g-3 mb-4">
    <!-- Search Form -->
    <div class="col-12 col-lg-9">
        <form method="GET" action="" class="d-flex flex-column">
            <div class="d-flex gap-2">
                <input type="text" name="search" class="form-control border-primary flex-grow-1"
                    placeholder="ابحث عن إجراء بالاسم..." value="{{ request()->query('search') }}">
                <button type="submit" class="btn btn-primary fw-bold d-flex align-items-center px-3 px-md-4">
                    <span class="d-none d-sm-inline">بحث</span>
                    <i class="fa-solid fa-magnifying-glass ms-0 ms-sm-2"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Add Procedure Button -->
    <div class="col-12 col-lg-3">
        <button class="btn btn-primary w-100 fw-bold d-flex align-items-center justify-content-center" type="button"
            data-bs-toggle="modal" data-bs-target="#createProcedureModal">
            <i class="fa-solid fa-plus me-2"></i>
            <span>أضف إجراء جديد</span>
        </button>
    </div>
</div>

<!-- Create Procedure Modal -->
<div class="modal fade" id="createProcedureModal" tabindex="-1" aria-labelledby="createProcedureModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white fw-bold" id="createProcedureModalLabel">إنشاء إجراء جديد</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="{{ route('procedures.store') }}" method="POST">
                @csrf
                <div class="modal-body text-dark">
                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label class="form-label">اسم الإجراء</label>
                            <input type="text" class="form-control border-primary" name="name"
                                value="{{ old('name') }}" required>
                            @error('name')
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

<!-- Procedures Table -->
<div class="table-container mb-5" id="tableContainer">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th class="text-center bg-dark text-white">#</th>
                <th class="text-center bg-dark text-white">اسم الإجراء</th>
                <th class="text-center bg-dark text-white">الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @php
                $filteredProcedures = $procedures;
                if (request()->query('search')) {
                    $search = request()->query('search');
                    $filteredProcedures = $procedures->filter(function ($procedure) use ($search) {
                        return str_contains($procedure->name, $search);
                    });
                }
            @endphp
            @if ($filteredProcedures->isEmpty())
                <tr>
                    <td colspan="3" class="text-center py-4">
                        <div class="status-danger fs-6">لم يتم العثور على اي إجراءات!</div>
                    </td>
                </tr>
            @else
                @foreach ($filteredProcedures as $procedure)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center fw-bold">{{ $procedure->name }}</td>
                        <td class="action-icons text-center">
                            <button class="btn btn-link p-0 pb-1 me-1 me-md-2" type="button" data-bs-toggle="modal"
                                data-bs-target="#editProcedureModal{{ $procedure->id }}">
                                <i class="fa-solid fa-pen-to-square text-primary" title="تعديل الإجراء"></i>
                            </button>
                            <button class="btn btn-link p-0 pb-1 m-0" type="button" data-bs-toggle="modal"
                                data-bs-target="#deleteProcedureModal{{ $procedure->id }}">
                                <i class="fa-solid fa-trash text-danger" title="حذف الإجراء"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Procedure Modal -->
                    <div class="modal fade" id="editProcedureModal{{ $procedure->id }}" tabindex="-1"
                        aria-labelledby="editProcedureModalLabel{{ $procedure->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h5 class="modal-title text-white fw-bold"
                                        id="editProcedureModalLabel{{ $procedure->id }}">تعديل بيانات الإجراء</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form action="{{ route('procedures.update', $procedure) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body text-dark">
                                        <div class="row g-3 mb-3">
                                            <div class="col-12">
                                                <label class="form-label">اسم الإجراء</label>
                                                <input type="text" class="form-control border-primary"
                                                    name="name" value="{{ $procedure->name }}" required>
                                                @error('name')
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

                    <!-- Delete Procedure Modal -->
                    <div class="modal fade" id="deleteProcedureModal{{ $procedure->id }}" tabindex="-1"
                        aria-labelledby="deleteProcedureModalLabel{{ $procedure->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <h5 class="modal-title text-white fw-bold fs-6"
                                        id="deleteProcedureModalLabel{{ $procedure->id }}">تأكيد الحذف</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center text-dark px-4 py-4">
                                    هل انت متأكد من حذف الإجراء <strong>{{ $procedure->name }}</strong>؟
                                </div>
                                <div class="modal-footer d-flex flex-column flex-sm-row justify-content-center gap-2">
                                    <button type="button" class="btn btn-secondary fw-bold order-1"
                                        data-bs-dismiss="modal">إلغاء</button>
                                    <form action="{{ route('procedures.delete', $procedure) }}" method="POST"
                                        class="order-2">
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
<div class="scroll-hint">
    <i class="fa-solid fa-arrows-left-right me-1"></i>
    اسحب الجدول لليمين أو اليسار لرؤية المزيد
</div>

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
