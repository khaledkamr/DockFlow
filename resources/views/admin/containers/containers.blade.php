@extends('layouts.admin')

@section('title', 'ساحة التخزين')

@section('content')
<style>
    .table-container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }
    .table thead {
        background-color: #f8f9fa;
        color: #333;
    }
    .table th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
        border-bottom: 1px solid #e9ecef;
    }
    .table td {
        padding: 15px;
        font-size: 14px;
        color: #333;
        border-bottom: 1px solid #e9ecef;
    }
    .table tbody tr:hover {
        background-color: #f1f3f5;
    }
    .table .status-waiting {
        background-color: #ffe590;
        color: #856404;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    .table .status-available {
        background-color: #d4edda;
        color: #155724;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    .table .status-danger {
        background-color: #f8d7da;
        color: #721c24;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
</style>

<h1 class="mb-4">ساحة التخزين</h1>

<div class="row mb-3">
    <div class="col">
        <div class="card rounded-4 border-0 shadow-sm ps-3 pe-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">إجمالي عدد الحاويات في الساحة</h5>
                    <h2 class="text-primary fw-bold">{{ $containers->where('status', 'متوفر')->count() }}</h2>
                </div>
                <div>
                    <i class="bi bi-boxes fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card rounded-4 border-0 shadow-sm ps-3 pe-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">إجمالي الحاويات في الإنتظار</h5>
                    <h2 class="text-primary fw-bold">{{ $containers->where('status', 'في الإنتظار')->count() }}</h2>
                </div>
                <div>
                    <i class="fa-solid fa-hourglass-start fa-2xl"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card rounded-4 border-0 shadow-sm ps-3 pe-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title">إجمالي الحاويات التي تم تسليمها</h5>
                    <h2 class="text-primary fw-bold">{{ $containers->where('status', 'غير متوفر')->count() }}</h2>
                </div>
                <div>
                    <i class="bi bi-check-circle fs-1"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <form method="GET" action="" class="d-flex flex-column">
            <label for="search" class="form-label text-dark fw-bold">بحث عن حاوية:</label>
            <div class="d-flex">
                <input type="text" name="search" class="form-control border-primary" placeholder=" ابحث عن حاوية بإسم العميل او بالموقع ... "
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
                <select id="statusFilter" name="status" class="form-select border-primary" onchange="this.form.submit()">
                    <option value="all"
                        {{ request()->query('status') === 'all' || !request()->query('status') ? 'selected' : '' }}>
                        جميع الحاويات</option>
                    <option value="متوفر" {{ request()->query('status') === 'متوفر' ? 'selected' : '' }}>
                        متوفر</option>
                    <option value="في الإنتظار" {{ request()->query('status') === 'في الإنتظار' ? 'selected' : '' }}>
                        في الإنتظار</option>
                    <option value="غير متوفر" {{ request()->query('status') === 'غير متوفر' ? 'selected' : '' }}>
                        غير متوفر</option>
                </select>
                @if (request()->query('search'))
                    <input type="hidden" name="search" value="{{ request()->query('search') }}">
                @endif
            </div>
        </form>
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <a href="{{ route('yard.containers.create') }}" class="btn btn-primary w-100 fw-bold">
            <i class="fa-solid fa-box pe-1"></i>
            أضف حاوية
        </a>
    </div>
</div>

@if (session('success'))
    @push('scripts')
        <script>
            showToast("{{ session('success') }}", "success");
        </script>
    @endpush
@endif

@if (session('errors'))
    @push('scripts')
        <script>
            showToast("حدث خطأ في العملية الرجاء مراجعة البيانات", "danger");
        </script>
    @endpush
@endif

<div class="table-container">
    <table class="table table-hover">
        <thead>
            <tr>
                <th class="text-center bg-dark text-white">رقم الحاوية</th>
                <th class="text-center bg-dark text-white">صاحــب الحاويــة</th>
                <th class="text-center bg-dark text-white">كــود الحاويــة</th>
                <th class="text-center bg-dark text-white">الفئـــة</th>
                <th class="text-center bg-dark text-white">الموقــع</th>
                <th class="text-center bg-dark text-white">الحالـــة</th>
                <th class="text-center bg-dark text-white">تم الإستلام بواسطة</th>
                <th class="text-center bg-dark text-white">تم التسليم بواسطة</th>
                <th class="text-center bg-dark text-white">تاريخ الدخول</th>
                <th class="text-center bg-dark text-white">الإجـــراءات</th>
            </tr>
        </thead>
        <tbody class="text-center">
            @if ($containers->isEmpty())
                <tr>
                    <td colspan="10" class="text-center">
                        <div class="status-danger fs-6">لا يوجد اي حاويات في الساحــة!</div>
                    </td>
                </tr>
            @else
                @foreach ($containers as $container)
                    <tr>
                        <td class="text-primary fw-bold">{{ $container->id }}</td>
                        <td>
                            <a href="{{ route('users.customer.profile', $container->customer->id) }}"
                                class="text-dark text-decoration-none">
                                {{ $container->customer->name }}
                            </a>
                        </td>
                        <td>{{ $container->code }}</td>
                        <td>{{ $container->containerType->name }}</td>
                        <td class="{{ $container->location ? 'fw-bold' : 'text-muted' }}">{{ $container->location ?? 'لم يحدد بعد' }}</td>
                        <td>
                            <div class="{{ $container->status == 'متوفر' ? 'status-available' : ($container->status == 'غير متوفر' ? 'status-danger' : 'status-waiting') }}">
                                {{ $container->status }}
                            </div>
                        </td>
                        <td class="{{ $container->received_by ? 'text-dark' : 'text-muted' }}">
                            {{ $container->received_by ?? 'لم يتم الأستلام بعد' }}
                        </td>
                        <td class="{{ $container->delivered_by ? 'text-dark' : 'text-muted' }}">
                            {{ $container->delivered_by ?? 'لم يتم التسليم بعد' }}
                        </td>
                        <td>{{ $container->date ?? '-' }}</td>
                        <td class="action-icons">
                            <button class="btn btn-link p-0 pb-1 m-0 me-3" type="button" data-bs-toggle="modal" data-bs-target="#editContainerModal{{ $container->id }}">
                                <i class="fa-solid fa-pen text-primary" title="Edit container"></i>
                            </button>
                            <button class="btn btn-link p-0 pb-1 m-0" type="button" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $container->id }}">
                                <i class="fa-solid fa-trash-can text-danger" title="delete container"></i>
                            </button>
                        </td>
                    </tr>

                    <div class="modal fade" id="editContainerModal{{ $container->id }}" tabindex="-1" aria-labelledby="editContainerModalLabel{{ $container->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-dark fw-bold" id="editContainerModalLabel{{ $container->id }}">تعديل بيانات العميل</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('yard.containers.update', $container->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body text-dark">
                                        <div class="row mb-3">
                                            <div class="col">
                                                <label for="code" class="form-label">كـــود الحـــاوية</label>
                                                <input type="text" class="form-control border-primary" name="code" value="{{ $container->code }}" readonly>
                                                @error('code')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col">
                                                <label for="customer_name" class="form-label">صــاحب الحـــاوية</label>
                                                <input type="text" class="form-control border-primary" name="customer_name" value="{{ $container->customer->name }}" readonly>
                                                @error('customer_name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col">
                                                <label for="location" class="form-label">المـــوقـــع</label>
                                                <input type="text" class="form-control border-primary" name="location" value="{{ $container->location }}" required>
                                                @error('location')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col">
                                                <label for="status" class="form-label">الحـــالـــة</label>
                                                <select class="form-select border-primary" name="status" required>
                                                    <option value="غير متوفر" {{ $container->status == "غير متوفر" ? 'selected' : '' }}>غير متوفر</option>
                                                    <option value="متوفر" {{ $container->status == "متوفر" ? 'selected' : '' }}>متوفر</option>
                                                    <option value="في الإنتظار" {{ $container->status == "في الإنتظار" ? 'selected' : '' }}>في الإنتظار</option>
                                                </select>
                                                @error('status')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                                        <button type="submit" class="btn btn-primary fw-bold">حفظ التغيرات</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
<div class="mt-4">
    {{ $containers->appends(request()->query())->onEachSide(1)->links() }}
</div>
@endsection