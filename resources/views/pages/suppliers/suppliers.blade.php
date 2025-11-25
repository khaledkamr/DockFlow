@extends('layouts.app')

@section('title', 'الموردين')

@section('content')
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
        background: linear-gradient(to left, rgba(0,0,0,0.1), transparent);
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .table-container.has-scroll::after {
        opacity: 1;
    }
    
    .table {
        min-width: 800px;
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
            min-width: 900px;
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

<h1 class="mb-3 mb-md-4 fs-3 fs-md-1">الموردين</h1>

<!-- Search and Filter Section -->
<div class="row g-3 mb-4">
    <!-- Search Form -->
    <div class="col-12 col-lg-6">
        <form method="GET" action="" class="d-flex flex-column">
            <label for="search" class="form-label text-dark fw-bold mb-2">بحث عن مورد:</label>
            <div class="d-flex gap-2">
                <input type="text" name="search" class="form-control border-primary flex-grow-1"
                    placeholder="ابحث عن مورد بالإيميل او بالإسم..."
                    value="{{ request()->query('search') }}">
                <button type="submit" class="btn btn-primary fw-bold d-flex align-items-center px-3 px-md-4">
                    <span class="d-none d-sm-inline">بحث</span>
                    <i class="fa-solid fa-magnifying-glass ms-0 ms-sm-2"></i>
                </button>
            </div>
        </form>
    </div>
    
    <!-- Status Filter -->
    <div class="col-12 col-sm-6 col-lg-4 d-none d-sm-block">
        <form method="GET" action="" class="d-flex flex-column">
            <label for="statusFilter" class="form-label text-dark fw-bold mb-2">تصفية حسب الحالة:</label>
            <select id="statusFilter" name="role" class="form-select border-primary" onchange="this.form.submit()">
                <option value="all" {{ request()->query('role') === 'all' || !request()->query('role') ? 'selected' : '' }}>
                    جميع الموردين
                </option>
            </select>
            @if (request()->query('search'))
                <input type="hidden" name="search" value="{{ request()->query('search') }}">
            @endif
        </form>
    </div>
    
    <!-- Add Supplier Button -->
    <div class="col-12 col-sm-6 col-lg-2">
        <label class="form-label d-none d-lg-block opacity-0">.</label>
        <button class="btn btn-primary w-100 fw-bold d-flex align-items-center justify-content-center"
            type="button" data-bs-toggle="modal" data-bs-target="#createSupplierModal">
            <i class="fa-solid fa-user-plus ms-2"></i>
            <span>أضف مورد</span>
        </button>
    </div>
</div>

<!-- Create Supplier Modal -->
<div class="modal fade" id="createSupplierModal" tabindex="-1" aria-labelledby="createSupplierModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark fw-bold" id="createSupplierModalLabel">إنشاء مورد جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('users.supplier.store') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                <div class="modal-body text-dark">
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label for="name" class="form-label">إسم المورد</label>
                            <input type="text" class="form-control border-primary" id="name" name="name"
                                value="{{ old('name') }}">
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="CR" class="form-label">السجل التجاري</label>
                            <input type="text" class="form-control border-primary" id="CR" name="CR"
                                value="{{ old('CR') }}">
                            @error('CR')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="vat_number" class="form-label">الرقم الضريبي</label>
                            <input type="text" class="form-control border-primary" id="vat_number" name="vat_number"
                                value="{{ old('vat_number') }}">
                            @error('vat_number')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="national_address" class="form-label">العنوان الوطني</label>
                            <input type="text" class="form-control border-primary" id="national_address"
                                name="national_address" value="{{ old('national_address') }}">
                            @error('national_address')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="phone" class="form-label">رقم الهاتف</label>
                            <input type="text" class="form-control border-primary" id="phone" name="phone"
                                value="{{ old('phone') }}">
                            @error('phone')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input type="text" class="form-control border-primary" id="email" name="email"
                                value="{{ old('email') }}">
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex flex-column flex-sm-row justify-content-start gap-2">
                    <button type="submit" class="btn btn-primary fw-bold order-2 order-sm-1">إنشاء</button>
                    <button type="button" class="btn btn-secondary fw-bold order-1 order-sm-2" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Suppliers Table -->
<div class="table-container" id="tableContainer">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th class="text-center bg-dark text-white">رقم حساب المورد</th>
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
                    <td colspan="8" class="text-center py-4">
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
                            <button class="btn btn-link p-0 pb-1 me-1 me-md-2" type="button" data-bs-toggle="modal"
                                data-bs-target="#editSupplierModal{{ $supplier->id }}">
                                <i class="fa-solid fa-pen-to-square text-primary" title="تعديل المورد"></i>
                            </button>
                            <button class="btn btn-link p-0 pb-1 m-0" type="button" data-bs-toggle="modal"
                                data-bs-target="#deleteSupplierModal{{ $supplier->id }}">
                                <i class="fa-solid fa-user-xmark text-danger" title="حذف المورد"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Supplier Modal -->
                    <div class="modal fade" id="editSupplierModal{{ $supplier->id }}" tabindex="-1"
                        aria-labelledby="editSupplierModalLabel{{ $supplier->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
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
                                        <div class="row g-3 mb-3">
                                            <div class="col-12 col-md-6">
                                                <label for="name{{ $supplier->id }}" class="form-label">إسم المورد</label>
                                                <input type="text" class="form-control border-primary"
                                                    id="name{{ $supplier->id }}" name="name" value="{{ $supplier->name }}">
                                                @error('name')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="CR{{ $supplier->id }}" class="form-label">السجل التجاري</label>
                                                <input type="text" class="form-control border-primary"
                                                    id="CR{{ $supplier->id }}" name="CR" value="{{ $supplier->CR }}">
                                                @error('CR')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row g-3 mb-3">
                                            <div class="col-12 col-md-6">
                                                <label for="vatNumber{{ $supplier->id }}" class="form-label">الرقم الضريبي</label>
                                                <input type="text" class="form-control border-primary"
                                                    id="vatNumber{{ $supplier->id }}" name="vat_number"
                                                    value="{{ $supplier->vat_number }}">
                                                @error('vat_number')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="national_address{{ $supplier->id }}" class="form-label">العنوان الوطني</label>
                                                <input type="text" class="form-control border-primary"
                                                    id="national_address{{ $supplier->id }}" name="national_address"
                                                    value="{{ $supplier->national_address }}">
                                                @error('national_address')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row g-3 mb-3">
                                            <div class="col-12 col-md-6">
                                                <label for="phone{{ $supplier->id }}" class="form-label">رقم الهاتف</label>
                                                <input type="text" class="form-control border-primary"
                                                    id="phone{{ $supplier->id }}" name="phone" value="{{ $supplier->phone }}">
                                                @error('phone')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="email{{ $supplier->id }}" class="form-label">البريد الإلكتروني</label>
                                                <input type="text" class="form-control border-primary"
                                                    id="email{{ $supplier->id }}" name="email" value="{{ $supplier->email }}">
                                                @error('email')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer d-flex flex-column flex-sm-row justify-content-start gap-2">
                                        <button type="submit" class="btn btn-primary fw-bold order-2 order-sm-1">حفظ التغييرات</button>
                                        <button type="button" class="btn btn-secondary fw-bold order-1 order-sm-2"
                                            data-bs-dismiss="modal">إلغاء</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Supplier Modal -->
                    <div class="modal fade" id="deleteSupplierModal{{ $supplier->id }}" tabindex="-1"
                        aria-labelledby="deleteSupplierModalLabel{{ $supplier->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-dark fw-bold fs-6"
                                        id="deleteSupplierModalLabel{{ $supplier->id }}">تأكيد الحذف</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center text-dark px-4 py-4">
                                    هل انت متأكد من حذف المورد <strong>{{ $supplier->name }}</strong>؟
                                </div>
                                <div class="modal-footer d-flex flex-column flex-sm-row justify-content-center gap-2">
                                    <button type="button" class="btn btn-secondary fw-bold order-1"
                                        data-bs-dismiss="modal">إلغاء</button>
                                    <form action="{{ route('users.supplier.delete', $supplier) }}" method="POST" class="order-2">
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

<!-- Pagination -->
{{-- <div class="mt-3 mt-md-4 d-flex justify-content-center">
    {{ $suppliers->appends(request()->query())->onEachSide(1)->links() }}
</div> --}}

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
            }, { once: true });
        }
    });
</script>
@endsection