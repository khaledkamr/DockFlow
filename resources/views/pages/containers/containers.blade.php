@extends('layouts.app')

@section('title', 'ساحة التخزين')

@section('content')
<h1 class="mb-4">ساحة التخزين</h1>

<div class="row mb-3">
    <div class="col">
        <div class="card rounded-4 border-0 shadow-sm ps-3 pe-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title fw-bold">إجمالي عدد الحاويات في الساحة</h6>
                    <h6 class="text-primary fw-bold mb-0" style="font-size: 1.4rem;">{{ $containers->where('status', 'في الساحة')->count() }}</h6>
                </div>
                <div>
                    <i class="bi bi-boxes fs-2"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card rounded-4 border-0 shadow-sm ps-3 pe-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title fw-bold">إجمالي الحاويات المتأخرة</h6>
                    <h6 class="text-primary fw-bold mb-0" style="font-size: 1.4rem;">{{ $containers->where('status', 'متأخر')->count() }}</h6>
                </div>
                <div>
                    <i class="fa-solid fa-hourglass-start fs-2"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card rounded-4 border-0 shadow-sm ps-3 pe-3">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title fw-bold">إجمالي الحاويات التي تم تسليمها</h6>
                    <h6 class="text-primary fw-bold mb-0" style="font-size: 1.4rem;">{{ $containers->where('status', 'تم التسليم')->count() }}</h6>
                </div>
                <div>
                    <i class="bi bi-check-circle fs-2"></i>
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
                    <option value="في الساحة" {{ request()->query('status') === 'في الساحة' ? 'selected' : '' }}>
                        في الساحة</option>
                    <option value="تم التسليم" {{ request()->query('status') === 'تم التسليم' ? 'selected' : '' }}>
                        تم التسليم</option>
                    <option value="متأخر" {{ request()->query('status') === 'متأخر' ? 'selected' : '' }}>
                        متأخر</option>
                </select>
                @if (request()->query('search'))
                    <input type="hidden" name="search" value="{{ request()->query('search') }}">
                @endif
            </div>
        </form>
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <a href="{{ route('policies.storage.create') }}" class="btn btn-primary w-100 fw-bold">
            <i class="fa-solid fa-box pe-1"></i>
            أضف حاوية
        </a>
    </div>
</div>

<div class="table-container">
    <table class="table table-hover">
        <thead>
            <tr>
                <th class="text-center bg-dark text-white">رقم الحاوية</th>
                <th class="text-center bg-dark text-white">العميل</th>
                <th class="text-center bg-dark text-white">الفئـــة</th>
                <th class="text-center bg-dark text-white">الموقــع</th>
                <th class="text-center bg-dark text-white">الحالـــة</th>
                <th class="text-center bg-dark text-white">تاريخ الدخول</th>
                <th class="text-center bg-dark text-white">تاريخ الخروج</th>
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
                        <td class="fw-bold">
                            <a href="{{ route('container.details', $container) }}" class="text-decoration-none">
                                {{ $container->code }}
                            </a>
                        </td>
                        <td>
                            @if($container->customer)
                                <a href="{{ route('users.customer.profile', $container->customer) }}"
                                    class="text-dark text-decoration-none fw-bold">
                                    {{ $container->customer->name }}
                                </a>
                            @else
                                <span class="text-dark fw-bold">{{ $container->policies->first()->external_customer }}</span>
                            @endif
                        </td>
                        <td>{{ $container->containerType->name }}</td>
                        <td class="{{ $container->location ? 'fw-bold' : 'text-muted' }}">{{ $container->location ?? 'لم يحدد' }}</td>
                        <td>
                            @if($container->status == 'في الساحة')
                                <div class="status-available">{{ $container->status }}</div>
                            @elseif($container->status == 'تم التسليم')
                                <div class="status-delivered">{{ $container->status }} <i class="fa-solid fa-check"></i></div>
                            @elseif($container->status == 'متأخر')
                                <div class="status-danger">{{ $container->status }}</div>
                            @elseif($container->status == 'خدمات')
                                <div class="status-waiting">{{ $container->status }}</div>
                            @elseif($container->status == 'في الميناء')
                                <div class="status-info">{{ $container->status }}</div>
                            @elseif($container->status == 'قيد النقل')
                                <div class="status-purple">{{ $container->status }}</div>
                            @endif
                        </td>
                        <td>{{ $container->date ? Carbon\Carbon::parse($container->date)->format('Y/m/d') : '-' }}</td>
                        <td>{{ $container->exit_date ? Carbon\Carbon::parse($container->exit_date)->format('Y/m/d') : '-' }}</td>
                        <td class="action-icons">
                            <button class="btn btn-link p-0 pb-1 m-0 me-3" type="button" data-bs-toggle="modal" data-bs-target="#editContainerModal{{ $container->id }}">
                                <i class="fa-solid fa-pen-to-square text-primary" title="تحديث بيانات الحاوية"></i>
                            </button>
                        </td>
                    </tr>

                    <div class="modal fade" id="editContainerModal{{ $container->id }}" tabindex="-1" aria-labelledby="editContainerModalLabel{{ $container->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-dark fw-bold" id="editContainerModalLabel{{ $container->id }}">تعديل بيانات الجاوية</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('yard.containers.update', $container) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <div class="modal-body text-dark">
                                        <div class="row mb-3">
                                            <div class="col">
                                                <label for="code" class="form-label">رقم الحـــاوية</label>
                                                <input type="text" class="form-control border-primary" name="code" value="{{ $container->code }}">
                                                @error('code')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col">
                                                <label for="location" class="form-label">المـــوقـــع</label>
                                                <input type="text" class="form-control border-primary" name="location" value="{{ $container->location }}">
                                                @error('location')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col">
                                                <label class="form-label">ملاحظـــات</label>
                                                <textarea class="form-control border-primary" name="notes">{{ $container->notes }}</textarea>
                                                @error('code')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer d-flex justify-content-start">
                                        <button type="submit" class="btn btn-primary fw-bold">حفظ التغيرات</button>
                                        <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
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