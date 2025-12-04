@extends('layouts.app')

@section('title', 'العملاء')

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
        background: linear-gradient(to left, rgba(0,0,0,0.1), transparent);
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .table-container.has-scroll::after {
        opacity: 1;
    }
</style>

<h1 class="mb-3 mb-md-4 fs-3 fs-md-1">العمـــلاء</h1>

<!-- Search and Filter Section -->
<div class="row g-3 mb-4">
    <!-- Search Form -->
    <div class="col-12 col-lg-6">
        <form method="GET" action="" class="d-flex flex-column">
            <label for="search" class="form-label text-dark fw-bold mb-2">بحث عن عميل:</label>
            <div class="d-flex gap-2">
                <input type="text" name="search" class="form-control border-primary flex-grow-1" 
                    placeholder="ابحث عن عميل بالإيميل او بالإسم..."
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
                    جميع العملاء
                </option>
            </select>
            @if (request()->query('search'))
                <input type="hidden" name="search" value="{{ request()->query('search') }}">
            @endif
        </form>
    </div>
    
    <!-- Add Customer Button -->
    <div class="col-12 col-sm-6 col-lg-2">
        <label class="form-label d-none d-lg-block opacity-0 user-select-none">.</label>
        <button class="btn btn-primary w-100 fw-bold d-flex align-items-center justify-content-center" 
            type="button" data-bs-toggle="modal" data-bs-target="#createUserModal">
            <i class="fa-solid fa-user-plus ms-2"></i>
            <span>أضف عميل</span>
        </button>
    </div>
</div>

<!-- Create Customer Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark fw-bold" id="createUserModalLabel">إنشاء عميل جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('users.customer.store') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                <div class="modal-body text-dark">
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label for="name" class="form-label">إسم العميل</label>
                            <input type="text" class="form-control border-primary" id="name" name="name" value="{{ old('name') }}">
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="CR" class="form-label">السجل التجاري</label>
                            <input type="text" class="form-control border-primary" id="CR" name="CR" value="{{ old('CR') }}">
                            @error('CR')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label">الرقم الضريبي</label>
                            <input type="text" class="form-control border-primary" name="vatNumber" value="{{ old('vatNumber') }}">
                            @error('vatNumber')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="national_address" class="form-label">العنوان الوطني</label>
                            <input type="text" class="form-control border-primary" id="national_address" name="national_address" value="{{ old('national_address') }}">
                            @error('national_address')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label for="phone" class="form-label">رقم الهاتف</label>
                            <input type="text" class="form-control border-primary" id="phone" name="phone" value="{{ old('phone') }}">
                            @error('phone')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input type="text" class="form-control border-primary" id="email" name="email" value="{{ old('email') }}">
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

<!-- Customers Table -->
<div class="table-container" id="tableContainer">
    <table class="table table-hover">
        <thead>
            <tr>
                <th class="text-center bg-dark text-white text-nowrap">رقم حساب العميل</th>
                <th class="text-center bg-dark text-white text-nowrap">إسم العميل</th>
                <th class="text-center bg-dark text-white text-nowrap">السجل التجاري</th>
                <th class="text-center bg-dark text-white text-nowrap">الرقم الضريبي</th>
                <th class="text-center bg-dark text-white text-nowrap">العنوان الوطني</th>
                <th class="text-center bg-dark text-white text-nowrap">تم بواسطة</th>
                <th class="text-center bg-dark text-white text-nowrap">الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @if ($customers->isEmpty())
                <tr>
                    <td colspan="8" class="text-center py-4">
                        <div class="status-danger fs-6">لم يتم العثور على اي عملاء!</div>
                    </td>
                </tr>
            @else
                @foreach ($customers as $customer)
                    <tr>
                        <td class="text-center text-primary fw-bold text-nowrap">{{ $customer->account->code }}</td>
                        <td class="text-center">
                            <a href="{{ route('users.customer.profile', $customer) }}"
                                class="text-dark fw-bold text-decoration-none">
                                {{ $customer->name }}
                            </a>
                        </td>
                        <td class="text-center text-nowrap">{{ $customer->CR }}</td>
                        <td class="text-center text-nowrap">{{ $customer->vatNumber }}</td>
                        <td class="text-center">{{ $customer->national_address }}</td>
                        <td class="text-center text-nowrap">
                            <a href="{{ route('admin.user.profile', $customer->made_by) }}" class="text-dark text-decoration-none">
                                {{ $customer->made_by->name ?? '-' }}
                            </a>
                        </td>
                        <td class="text-center text-nowrap">
                            <button class="btn btn-link p-0 pb-1 me-1 me-md-2" type="button" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $customer->id }}">
                                <i class="fa-solid fa-pen-to-square text-primary fs-5 fs-md-6" title="تعديل العميل"></i>
                            </button>
                            <button class="btn btn-link p-0 pb-1 m-0" type="button" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $customer->id }}">
                                <i class="fa-solid fa-user-xmark text-danger fs-5 fs-md-6" title="حذف العميل"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Customer Modal -->
                    <div class="modal fade" id="editUserModal{{ $customer->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $customer->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
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
                                        <div class="row g-3 mb-3">
                                            <div class="col-12 col-md-6">
                                                <label for="name{{ $customer->id }}" class="form-label">إسم العميل</label>
                                                <input type="text" class="form-control border-primary" id="name{{ $customer->id }}" name="name" value="{{ $customer->name }}">
                                                @error('name')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="CR{{ $customer->id }}" class="form-label">السجل التجاري</label>
                                                <input type="text" class="form-control border-primary" id="CR{{ $customer->id }}" name="CR" value="{{ $customer->CR }}">
                                                @error('CR')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row g-3 mb-3">
                                            <div class="col-12 col-md-6">
                                                <label for="vatNumber{{ $customer->id }}" class="form-label">الرقم الضريبي</label>
                                                <input type="text" class="form-control border-primary" id="vatNumber{{ $customer->id }}" name="vatNumber" value="{{ $customer->vatNumber }}">
                                                @error('vatNumber')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="national_address{{ $customer->id }}" class="form-label">العنوان الوطني</label>
                                                <input type="text" class="form-control border-primary" id="national_address{{ $customer->id }}" name="national_address" value="{{ $customer->national_address }}">
                                                @error('national_address')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row g-3 mb-3">
                                            <div class="col-12 col-md-6">
                                                <label for="phone{{ $customer->id }}" class="form-label">رقم الهاتف</label>
                                                <input type="text" class="form-control border-primary" id="phone{{ $customer->id }}" name="phone" value="{{ $customer->phone }}">
                                                @error('phone')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label for="email{{ $customer->id }}" class="form-label">البريد الإلكتروني</label>
                                                <input type="text" class="form-control border-primary" id="email{{ $customer->id }}" name="email" value="{{ $customer->email }}">
                                                @error('email')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer d-flex flex-column flex-sm-row justify-content-start gap-2">
                                        <button type="submit" class="btn btn-primary fw-bold order-2 order-sm-1">حفظ التغييرات</button>
                                        <button type="button" class="btn btn-secondary fw-bold order-1 order-sm-2" data-bs-dismiss="modal">إلغاء</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Customer Modal -->
                    <div class="modal fade" id="deleteUserModal{{ $customer->id }}" tabindex="-1" aria-labelledby="deleteUserModalLabel{{ $customer->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-dark fw-bold fs-6" id="deleteUserModalLabel{{ $customer->id }}">تأكيد الحذف</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center text-dark px-4 py-4">
                                    هل انت متأكد من حذف العميل <strong>{{ $customer->name }}</strong>؟
                                </div>
                                <div class="modal-footer d-flex flex-column flex-sm-row justify-content-center gap-2">
                                    <button type="button" class="btn btn-secondary fw-bold order-1" data-bs-dismiss="modal">إلغاء</button>
                                    <form action="{{ route('users.customer.delete', $customer) }}" method="POST" class="order-2">
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
<div class="mt-3 mt-md-4 d-flex justify-content-center">
    {{ $customers->appends(request()->query())->onEachSide(1)->links() }}
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
            }, { once: true });
        }
    });
</script>
@endsection