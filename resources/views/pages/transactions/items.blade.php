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
        min-width: 600px;
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
            min-width: 650px;
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

    .select2-container .select2-selection {
        height: 38px;
        border-radius: 8px;
        border: 1px solid #0d6efd;
        padding: 5px;
    }

    .select2-container .select2-selection__rendered {
        line-height: 30px;
    }
</style>

<!-- Search and Add Section -->
<div class="row g-3 mb-4">
    <!-- Search Form -->
    <div class="col-12 col-lg-9">
        <form method="GET" action="" class="d-flex flex-column">
            <div class="d-flex gap-2">
                <input type="text" name="search" class="form-control border-primary flex-grow-1"
                    placeholder="ابحث عن بند بالاسم..." value="{{ request()->query('search') }}">
                <button type="submit" class="btn btn-primary fw-bold d-flex align-items-center px-3 px-md-4">
                    <span class="d-none d-sm-inline">بحث</span>
                    <i class="fa-solid fa-magnifying-glass ms-0 ms-sm-2"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Add Item Button -->
    <div class="col-12 col-lg-3">
        <button class="btn btn-primary w-100 fw-bold d-flex align-items-center justify-content-center" type="button"
            data-bs-toggle="modal" data-bs-target="#createItemModal">
            <i class="fa-solid fa-plus me-2"></i>
            <span>أضف بند جديد</span>
        </button>
    </div>
</div>

<!-- Create Item Modal -->
<div class="modal fade" id="createItemModal" tabindex="-1" aria-labelledby="createItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white fw-bold" id="createItemModalLabel">إنشاء بند جديد</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="{{ route('items.store') }}" method="POST">
                @csrf
                <div class="modal-body text-dark">
                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label class="form-label">اسم البند</label>
                            <input type="text" class="form-control border-primary" name="name"
                                value="{{ old('name') }}" required>
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label class="form-label">النوع</label>
                            <input type="text" class="form-control border-primary bg-light" name="type"
                                value="مصروف" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label">حساب المدين</label>
                            <select class="form-select border-primary select2-account" name="debit_account_id">
                                <option value="">-- لا يوجد --</option>
                                @foreach ($accounts as $account)
                                    <option value="{{ $account->id }}">
                                        {{ $account->code }} - {{ $account->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('debit_account_id')
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

<!-- Items Table -->
<div class="table-container mb-5" id="tableContainer">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th class="text-center bg-dark text-white">#</th>
                <th class="text-center bg-dark text-white">اسم البند</th>
                <th class="text-center bg-dark text-white">النوع</th>
                <th class="text-center bg-dark text-white">حساب المدين</th>
                <th class="text-center bg-dark text-white">الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @php
                $filteredItems = $items;
                if (request()->query('search')) {
                    $search = request()->query('search');
                    $filteredItems = $items->filter(function ($item) use ($search) {
                        return str_contains($item->name, $search);
                    });
                }
            @endphp
            @if ($filteredItems->isEmpty())
                <tr>
                    <td colspan="5" class="text-center py-4">
                        <div class="status-danger fs-6">لم يتم العثور على اي بنود!</div>
                    </td>
                </tr>
            @else
                @foreach ($filteredItems as $item)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center fw-bold">{{ $item->name }}</td>
                        <td class="text-center">
                            <span class="badge status-available">{{ $item->type }}</span>
                        </td>
                        <td class="text-center text-primary fw-bold">
                            {{ $item->debitAccount ? $item->debitAccount->code . ' - ' . $item->debitAccount->name : '-' }}
                        </td>
                        <td class="action-icons text-center">
                            <button class="btn btn-link p-0 pb-1 me-1 me-md-2" type="button" data-bs-toggle="modal"
                                data-bs-target="#editItemModal{{ $item->id }}">
                                <i class="fa-solid fa-pen-to-square text-primary" title="تعديل البند"></i>
                            </button>
                            <button class="btn btn-link p-0 pb-1 m-0" type="button" data-bs-toggle="modal"
                                data-bs-target="#deleteItemModal{{ $item->id }}">
                                <i class="fa-solid fa-trash text-danger" title="حذف البند"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Item Modal -->
                    <div class="modal fade" id="editItemModal{{ $item->id }}" tabindex="-1"
                        aria-labelledby="editItemModalLabel{{ $item->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h5 class="modal-title text-white fw-bold"
                                        id="editItemModalLabel{{ $item->id }}">تعديل بيانات البند</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form action="{{ route('items.update', $item) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body text-dark">
                                        <div class="row g-3 mb-3">
                                            <div class="col-12">
                                                <label class="form-label">اسم البند</label>
                                                <input type="text" class="form-control border-primary"
                                                    name="name" value="{{ $item->name }}" required>
                                                @error('name')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row g-3 mb-3">
                                            <div class="col-12 col-md-6">
                                                <label class="form-label">النوع</label>
                                                <input type="text" class="form-control border-primary bg-light"
                                                    name="type" value="{{ $item->type }}" readonly>
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label class="form-label">حساب المدين</label>
                                                <select class="form-select border-primary select2-account-edit"
                                                    name="debit_account_id"
                                                    data-modal-id="editItemModal{{ $item->id }}">
                                                    <option value="">-- لا يوجد --</option>
                                                    @foreach ($accounts as $account)
                                                        <option value="{{ $account->id }}"
                                                            {{ $item->debit_account_id == $account->id ? 'selected' : '' }}>
                                                            {{ $account->code }} - {{ $account->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('debit_account_id')
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

                    <!-- Delete Item Modal -->
                    <div class="modal fade" id="deleteItemModal{{ $item->id }}" tabindex="-1"
                        aria-labelledby="deleteItemModalLabel{{ $item->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <h5 class="modal-title text-white fw-bold fs-6"
                                        id="deleteItemModalLabel{{ $item->id }}">تأكيد الحذف</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center text-dark px-4 py-4">
                                    هل انت متأكد من حذف البند <strong>{{ $item->name }}</strong>؟
                                </div>
                                <div class="modal-footer d-flex flex-column flex-sm-row justify-content-center gap-2">
                                    <button type="button" class="btn btn-secondary fw-bold order-1"
                                        data-bs-dismiss="modal">إلغاء</button>
                                    <form action="{{ route('items.delete', $item) }}" method="POST"
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

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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

        // Initialize Select2 for create modal
        $('#createItemModal').on('shown.bs.modal', function() {
            $(this).find('.select2-account').select2({
                placeholder: 'اختر الحساب...',
                allowClear: true,
                dir: 'rtl',
                language: 'ar',
                dropdownParent: $('#createItemModal')
            });
        });

        // Initialize Select2 for edit modals when they are shown
        $('.modal[id^="editItemModal"]').on('shown.bs.modal', function() {
            $(this).find('.select2-account-edit').select2({
                placeholder: 'اختر الحساب...',
                allowClear: true,
                dir: 'rtl',
                language: 'ar',
                dropdownParent: $(this)
            });
        });

        // Destroy Select2 when modals are hidden to prevent issues
        $('.modal').on('hidden.bs.modal', function() {
            $(this).find('.select2-account, .select2-account-edit').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }
            });
        });
    });
</script>
