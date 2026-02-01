@extends('layouts.admin')

@section('title', 'الشركات')

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
            background: linear-gradient(to left, rgba(0, 0, 0, 0.1), transparent);
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .table-container.has-scroll::after {
            opacity: 1;
        }
    </style>

    <h1 class="mb-4">الشركـــات</h1>

    <!-- Search and Filter Section -->
    <div class="row g-3 mb-4">
        <!-- Search Form -->
        <div class="col-12 col-lg-8">
            <form method="GET" action="{{ route('admin.companies') }}" class="d-flex flex-column">
                <label for="search" class="form-label text-dark fw-bold mb-2">بحث عن شركة:</label>
                <div class="d-flex gap-2">
                    <input type="text" name="search" class="form-control border-primary flex-grow-1"
                        placeholder="ابحث عن شركة بالإسم أو البريد الإلكتروني..." value="{{ request()->query('search') }}">
                    <button type="submit" class="btn btn-primary fw-bold d-flex align-items-center px-3 px-md-4">
                        <span class="d-none d-sm-inline">بحث</span>
                        <i class="fa-solid fa-magnifying-glass ms-0 ms-sm-2"></i>
                    </button>
                </div>
            </form>
        </div>

        <!-- Add Company Button -->
        <div class="col-12 col-lg-4">
            <label class="form-label d-none d-lg-block opacity-0 user-select-none">.</label>
            <button class="btn btn-primary w-100 fw-bold d-flex align-items-center justify-content-center" type="button"
                data-bs-toggle="modal" data-bs-target="#addCompanyModal">
                <i class="fa-solid fa-building-circle-arrow-right me-2"></i>
                <span>إضافة شركة جديدة</span>
            </button>
        </div>
    </div>

    <!-- Create Company Modal -->
    <div class="modal fade" id="addCompanyModal" tabindex="-1" aria-labelledby="addCompanyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white fw-bold" id="addCompanyModalLabel">إضافة شركة جديدة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.companies.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body text-dark">
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label for="name" class="form-label">اسم الشركة</label>
                                <input type="text" class="form-control border-primary" id="name" name="name"
                                    value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="branch" class="form-label">اسم الفرع</label>
                                <input type="text" class="form-control border-primary" id="branch" name="branch"
                                    value="{{ old('branch') }}" required>
                                @error('branch')
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
                                <label for="vatNumber" class="form-label">الرقم الضريبي</label>
                                <input type="text" class="form-control border-primary" id="vatNumber" name="vatNumber"
                                    value="{{ old('vatNumber') }}">
                                @error('vatNumber')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <input type="email" class="form-control border-primary" id="email" name="email"
                                    value="{{ old('email') }}">
                                @error('email')
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
                                <label for="national_address" class="form-label">العنوان الوطني</label>
                                <input type="text" class="form-control border-primary" id="national_address"
                                    name="national_address" value="{{ old('national_address') }}">
                                @error('national_address')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label for="logo" class="form-label">شعار الشركة</label>
                                <input type="file" class="form-control border-primary" id="logo" name="logo"
                                    accept="image/*">
                                @error('logo')
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

    <!-- Companies Table -->
    <div class="table-container" id="tableContainer">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th class="text-center bg-dark text-white text-nowrap">#</th>
                    <th class="text-center bg-dark text-white text-nowrap">الشعار</th>
                    <th class="text-center bg-dark text-white text-nowrap">اسم الشركة</th>
                    <th class="text-center bg-dark text-white text-nowrap">البريد الإلكتروني</th>
                    <th class="text-center bg-dark text-white text-nowrap">رقم الهاتف</th>
                    <th class="text-center bg-dark text-white text-nowrap">العنوان الوطني</th>
                    <th class="text-center bg-dark text-white text-nowrap">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @if ($companies->isEmpty())
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <div class="status-danger fs-6">لم يتم العثور على أي شركات!</div>
                        </td>
                    </tr>
                @else
                    @foreach ($companies as $company)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">
                                @if ($company->logo)
                                    <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}"
                                        class="rounded" style="width: 40px; height: 40px; object-fit: contain;">
                                @else
                                    <img src="{{ asset('img/placeholder.jpg') }}" alt=""
                                        class="rounded" style="width: 40px; height: 40px; object-fit: contain;">
                                @endif
                            </td>
                            <td class="text-center fw-bold">
                                <a href="{{ route('admin.company.details', $company) }}" class="text-decoration-none text-dark">
                                    {{ $company->name }}
                                </a>
                            </td>
                            <td class="text-center text-nowrap">{{ $company->email }}</td>
                            <td class="text-center text-nowrap">{{ $company->phone }}</td>
                            <td class="text-center">{{ $company->national_address }}</td>
                            <td class="text-center text-nowrap">
                                <button class="btn btn-link p-0 pb-1 me-1 me-md-2" type="button" data-bs-toggle="modal"
                                    data-bs-target="#editCompanyModal{{ $company->id }}">
                                    <i class="fa-solid fa-pen-to-square text-primary" title="تعديل الشركة"></i>
                                </button>
                                <button class="btn btn-link p-0 pb-1 m-0" type="button" data-bs-toggle="modal"
                                    data-bs-target="#deleteCompanyModal{{ $company->id }}">
                                    <i class="fa-solid fa-trash-can text-danger" title="حذف الشركة"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Edit Company Modal -->
                        <div class="modal fade" id="editCompanyModal{{ $company->id }}" tabindex="-1"
                            aria-labelledby="editCompanyModalLabel{{ $company->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h5 class="modal-title text-white fw-bold"
                                            id="editCompanyModalLabel{{ $company->id }}">تعديل بيانات الشركة</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('admin.companies.update', $company) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body text-dark">
                                            <div class="row g-3 mb-3">
                                                <div class="col-12 col-md-6">
                                                    <label for="name{{ $company->id }}" class="form-label">اسم الشركة</label>
                                                    <input type="text" class="form-control border-primary"
                                                        id="name{{ $company->id }}" name="name"
                                                        value="{{ $company->name }}" required>
                                                    @error('name')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label for="branch{{ $company->id }}" class="form-label">اسم الفرع</label>
                                                    <input type="text" class="form-control border-primary"
                                                        id="branch{{ $company->id }}" name="branch"
                                                        value="{{ $company->branch }}" required>
                                                    @error('branch')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label for="CR{{ $company->id }}" class="form-label">السجل التجاري</label>
                                                    <input type="text" class="form-control border-primary"
                                                        id="CR{{ $company->id }}" name="CR"
                                                        value="{{ $company->CR }}" required>
                                                    @error('CR')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label for="vatNumber{{ $company->id }}" class="form-label">الرقم الضريبي</label>
                                                    <input type="text" class="form-control border-primary"
                                                        id="vatNumber{{ $company->id }}" name="vatNumber"
                                                        value="{{ $company->vatNumber }}" required>
                                                    @error('vatNumber')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label for="email{{ $company->id }}" class="form-label">البريد الإلكتروني</label>
                                                    <input type="email" class="form-control border-primary"
                                                        id="email{{ $company->id }}" name="email"
                                                        value="{{ $company->email }}">
                                                    @error('email')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label for="phone{{ $company->id }}" class="form-label">رقم الهاتف</label>
                                                    <input type="text" class="form-control border-primary"
                                                        id="phone{{ $company->id }}" name="phone"
                                                        value="{{ $company->phone }}">
                                                    @error('phone')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label for="national_address{{ $company->id }}" class="form-label">العنوان الوطني</label>
                                                    <input type="text" class="form-control border-primary"
                                                        id="national_address{{ $company->id }}" name="national_address"
                                                        value="{{ $company->national_address }}">
                                                    @error('national_address')
                                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label for="logo{{ $company->id }}" class="form-label">شعار الشركة</label>
                                                    <div class="d-flex align-items-center mb-2">
                                                        @if ($company->logo)
                                                            <img src="{{ asset('storage/' . $company->logo) }}"
                                                                alt="{{ $company->name }}" class="rounded me-2"
                                                                style="width: 40px; height: 40px; object-fit: contain;">
                                                        @endif
                                                        <input type="file" class="form-control border-primary" id="logo{{ $company->id }}" name="logo" accept="image/*">
                                                    </div>
                                                    @error('logo')
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

                        <!-- Delete Company Modal -->
                        <div class="modal fade" id="deleteCompanyModal{{ $company->id }}" tabindex="-1"
                            aria-labelledby="deleteCompanyModalLabel{{ $company->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <h5 class="modal-title text-white fw-bold fs-6"
                                            id="deleteCompanyModalLabel{{ $company->id }}">تأكيد الحذف</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center text-dark px-4 py-4">
                                        هل أنت متأكد من حذف الشركة <strong>{{ $company->name }}</strong>؟
                                    </div>
                                    <div class="alert alert-danger mx-3 mb-3 d-flex align-items-center fw-bold" role="alert">
                                        <i class="fa-solid fa-triangle-exclamation me-2"></i>
                                        <small>تنبيه: سيتم حذف جميع البيانات المرتبطة بهذه الشركة نهائياً.</small>
                                    </div>
                                    <div class="modal-footer d-flex flex-column flex-sm-row justify-content-center gap-2">
                                        <button type="button" class="btn btn-secondary fw-bold order-1"
                                            data-bs-dismiss="modal">إلغاء</button>
                                        <form action="{{ route('admin.companies.delete', $company) }}" method="POST" class="order-2">
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
    @if (method_exists($companies, 'links'))
        <div class="mt-3 mt-md-4 d-flex justify-content-center">
            {{ $companies->links('components.pagination') }}
        </div>
    @endif

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
@endsection
