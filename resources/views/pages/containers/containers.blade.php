@extends('layouts.app')

@section('title', 'ساحة التخزين')

@section('content')
    <div class="row g-3 mb-3">
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card rounded-4 border-0 shadow-sm ps-3 pe-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title fw-bold">إجمالي عدد الحاويات في الساحة</h6>
                        <h6 class="text-primary fw-bold mb-0" style="font-size: 1.4rem;">
                            {{ $existingContainers }}</h6>
                    </div>
                    <div>
                        <i class="bi bi-boxes fs-2"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card rounded-4 border-0 shadow-sm ps-3 pe-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title fw-bold">إجمالي الحاويات المتأخرة</h6>
                        <h6 class="text-primary fw-bold mb-0" style="font-size: 1.4rem;">
                            {{ $lateContainers }}
                        </h6>
                    </div>
                    <div>
                        <i class="fa-solid fa-hourglass-start fs-2"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card rounded-4 border-0 shadow-sm ps-3 pe-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title fw-bold">إجمالي الحاويات التي تم تسليمها</h6>
                        <h6 class="text-primary fw-bold mb-0" style="font-size: 1.4rem;">
                            {{ $deliveredContainers }}
                        </h6>
                    </div>
                    <div>
                        <i class="bi bi-check-circle fs-2"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-6">
            <form method="GET" action="" class="d-flex flex-column">
                <label for="search" class="form-label text-dark fw-bold">بحث عن حاوية:</label>
                <div class="d-flex">
                    <input type="text" name="search" class="form-control border-primary"
                        placeholder=" ابحث عن حاوية برقم الحاوية او بإسم العميل او بالموقع ... " value="{{ request()->query('search') }}">
                    <button type="submit" class="btn btn-primary fw-bold ms-2 d-flex align-items-center">
                        <span class="d-none d-sm-inline">بحث</span>
                        <i class="fa-solid fa-magnifying-glass ms-0 ms-sm-2"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="col-6 col-sm-7 col-lg-4">
            <form method="GET" action="" class="d-flex flex-column">
                <label for="statusFilter" class="form-label text-dark fw-bold">تصفية حسب الحالة:</label>
                <div class="d-flex">
                    <select id="statusFilter" name="status" class="form-select border-primary"
                        onchange="this.form.submit()">
                        <option value="all"
                            {{ request()->query('status') === 'all' || !request()->query('status') ? 'selected' : '' }}>
                            جميع الحاويات</option>
                        <option value="في الساحة" {{ request()->query('status') === 'في الساحة' ? 'selected' : '' }}>
                            في الساحة
                        </option>
                        <option value="تم التسليم" {{ request()->query('status') === 'تم التسليم' ? 'selected' : '' }}>
                            تم التسليم
                        </option>
                        <option value="متأخر" {{ request()->query('status') === 'متأخر' ? 'selected' : '' }}>
                            متأخر
                        </option>
                        <option value="قيد النقل" {{ request()->query('status') === 'قيد النقل' ? 'selected' : '' }}>
                            قيد النقل
                        </option>
                        <option value="في الميناء" {{ request()->query('status') === 'في الميناء' ? 'selected' : '' }}>
                            في الميناء
                        </option>
                    </select>
                    @if (request()->query('search'))
                        <input type="hidden" name="search" value="{{ request()->query('search') }}">
                    @endif
                </div>
            </form>
        </div>
        <div class="col-6 col-sm-5 col-lg-2 d-flex align-items-end">
            <a href="{{ route('policies.storage.create') }}" class="btn btn-primary w-100 fw-bold">
                <i class="fa-solid fa-box pe-1"></i>
                <span class="d-inline">أضف حاوية</span>
            </a>
        </div>
    </div>

    <div class="table-container" id="tableContainer">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th class="text-center bg-dark text-white text-nowrap">رقم الحاوية</th>
                    <th class="text-center bg-dark text-white text-nowrap">العميل</th>
                    <th class="text-center bg-dark text-white text-nowrap">الفئـــة</th>
                    <th class="text-center bg-dark text-white text-nowrap">الموقــع</th>
                    <th class="text-center bg-dark text-white text-nowrap">الحالـــة</th>
                    <th class="text-center bg-dark text-white text-nowrap">تاريخ الدخول</th>
                    <th class="text-center bg-dark text-white text-nowrap">تاريخ الخروج</th>
                    <th class="text-center bg-dark text-white text-nowrap">الإجـــراءات</th>
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
                            <td class="fw-bold text-nowrap">
                                <a href="{{ route('container.details', $container) }}" class="text-decoration-none">
                                    {{ $container->code }}
                                </a>
                            </td>
                            <td class="">
                                @if ($container->customer)
                                    <a href="{{ route('users.customer.profile', $container->customer) }}" class="text-dark text-decoration-none fw-bold">
                                        {{ $container->customer->name }}
                                    </a>
                                @else
                                    <span class="text-dark fw-bold">{{ $container->policies->first()->external_customer }}</span>
                                @endif
                            </td>
                            <td class="text-nowrap">{{ $container->containerType->name }}</td>
                            <td class="{{ $container->location ? 'fw-bold' : 'text-muted' }}">
                                {{ $container->location ?? 'لم يحدد' }}</td>
                            <td class="text-nowrap">
                                @if ($container->status == 'في الساحة')
                                    <div class="badge status-available">{{ $container->status }}</div>
                                @elseif($container->status == 'تم التسليم')
                                    <div class="badge status-delivered">{{ $container->status }} <i
                                            class="fa-solid fa-check"></i></div>
                                @elseif($container->status == 'متأخر')
                                    <div class="badge status-danger">{{ $container->status }}</div>
                                @elseif($container->status == 'خدمات')
                                    <div class="badge status-waiting">{{ $container->status }}</div>
                                @elseif($container->status == 'في الميناء')
                                    <div class="badge status-info">{{ $container->status }}</div>
                                @elseif($container->status == 'قيد النقل')
                                    <div class="badge status-purple">{{ $container->status }}</div>
                                @endif

                                @php
                                    $storage_policy = $container->policies->where('type', 'تخزين')->first();
                                @endphp

                                @if ($container->status == 'في الساحة' && $storage_policy && $storage_policy->storage_duration 
                                    && $container->days > $storage_policy->storage_duration)
                                    <div class="text-danger fw-semibold mt-1" style="font-size: 0.85rem;">
                                        متأخر منذ {{ (int) ($container->days - $storage_policy->storage_duration) }} أيام
                                    </div>
                                @endif
                            </td>
                            <td class="text-nowrap">{{ $container->date ? Carbon\Carbon::parse($container->date)->format('Y/m/d') : '-' }}</td>
                            <td class="text-nowrap">{{ $container->exit_date ? Carbon\Carbon::parse($container->exit_date)->format('Y/m/d') : '-' }}
                            </td>
                            <td class="action-icons text-nowrap">
                                <button class="btn btn-link p-0 pb-1 m-0 me-3" type="button" data-bs-toggle="modal"
                                    data-bs-target="#editContainerModal{{ $container->id }}">
                                    <i class="fa-solid fa-pen-to-square text-primary" title="تحديث بيانات الحاوية"></i>
                                </button>
                            </td>
                        </tr>

                        <div class="modal fade" id="editContainerModal{{ $container->id }}" tabindex="-1"
                            aria-labelledby="editContainerModalLabel{{ $container->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h5 class="modal-title text-white fw-bold"
                                            id="editContainerModalLabel{{ $container->id }}">تعديل بيانات الحاوية</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('yard.containers.update', $container) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <div class="modal-body text-dark">
                                            <div class="row g-3 mb-3">
                                                <div class="col-12 col-md">
                                                    <label for="code" class="form-label">رقم الحـــاوية</label>
                                                    <input type="text" class="form-control border-primary"
                                                        name="code" value="{{ $container->code }}">
                                                    @error('code')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-12 col-md">
                                                    <label for="location" class="form-label">المـــوقـــع</label>
                                                    <input type="text" class="form-control border-primary"
                                                        name="location" value="{{ $container->location }}">
                                                    @error('location')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-12 col-md">
                                                    <label for="container_type_id" class="form-label">نوع الحاوية</label>
                                                    <select class="form-select border-primary" name="container_type_id" required>
                                                        <option value="">اختر نوع الحاوية</option>
                                                        @foreach($containerTypes as $type)
                                                            <option value="{{ $type->id }}" {{ $container->container_type_id == $type->id ? 'selected' : '' }}>
                                                                {{ $type->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('container_type_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label">ملاحظـــات</label>
                                                    <textarea class="form-control border-primary" name="notes">{{ $container->notes }}</textarea>
                                                    @error('code')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer d-flex flex-column flex-sm-row justify-content-start gap-2">
                                            <button type="submit" class="btn btn-primary fw-bold">حفظ التغيرات</button>
                                            <button type="button" class="btn btn-secondary fw-bold"
                                                data-bs-dismiss="modal">إلغاء</button>
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

    <div class="scroll-hint text-center text-muted mt-2 d-sm-block d-md-none">
        <i class="fa-solid fa-arrows-left-right me-1"></i>
        اسحب الجدول لليمين أو اليسار لرؤية المزيد
    </div>
    
    <div class="mt-4">
        {{ $containers->links('components.pagination') }}
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
                }, { once: true });
            }
        });
    </script>
@endsection
