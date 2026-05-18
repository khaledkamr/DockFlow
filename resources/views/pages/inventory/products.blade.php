@extends('layouts.app')

@section('title', 'المنتجات')

@section('content')
    <h1 class="mb-4">المنتجــــات</h1>

    <div class="row g-3 mb-4">
        <div class="col-12 col-lg-8">
            <form method="GET" action="" class="d-flex flex-column h-100">
                <label for="search" class="form-label text-dark fw-bold">بحث عن منتج:</label>
                <div class="d-flex flex-grow-1">
                    <input type="text" name="search" class="form-control border-primary"
                        placeholder=" ابحث عن منتج بالإسم أو SKU... " value="{{ request()->query('search') }}">
                    <button type="submit" class="btn btn-primary fw-bold ms-2 d-flex align-items-center">
                        <span class="d-none d-sm-inline">بحث</span>
                        <i class="fa-solid fa-magnifying-glass ms-sm-2"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="col-12 col-sm-12 col-lg-4 d-flex align-items-end">
            <button class="btn btn-primary w-100 fw-bold" type="button" data-bs-toggle="modal"
                data-bs-target="#createProductModal">
                <i class="fa-solid fa-box-open pe-1"></i>
                <span class="d-none d-sm-inline">أضف منتج</span>
                <span class="d-inline d-sm-none">إضافة</span>
            </button>
        </div>
    </div>

    <div class="modal fade" id="createProductModal" tabindex="-1" aria-labelledby="createProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white fw-bold" id="createProductModalLabel">إنشاء منتج جديد</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('inventory.products.store') }}" method="POST">
                    @csrf
                    <div class="modal-body text-dark">
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">اسم المنتج (عربي)</label>
                                <input type="text" class="form-control border-primary" name="name_ar"
                                    value="{{ old('name_ar') }}" required>
                                @error('name_ar')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">اسم المنتج (إنجليزي)</label>
                                <input type="text" class="form-control border-primary" name="name_en"
                                    value="{{ old('name_en') }}">
                                @error('name_en')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label">الفئة</label>
                                <select class="form-select border-primary" name="category_id" required>
                                    <option value="">اختر الفئة</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name_ar }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 col-md-6">
                                <label class="form-label">SKU</label>
                                <input type="text" class="form-control border-primary" name="sku"
                                    value="{{ old('sku') }}">
                                @error('sku')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-12">
                                <label class="form-label">الوحدة</label>
                                <input type="text" class="form-control border-primary" name="unit"
                                    placeholder="مثال: قطعة، كيلو، متر..." value="{{ old('unit') }}">
                                @error('unit')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-12">
                                <label class="form-label">الوصف</label>
                                <textarea class="form-control border-primary" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
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
                    <th class="text-center bg-dark text-white text-nowrap">اسم المنتج</th>
                    <th class="text-center bg-dark text-white text-nowrap">الفئة</th>
                    <th class="text-center bg-dark text-white text-nowrap">SKU</th>
                    <th class="text-center bg-dark text-white text-nowrap">الوحدة</th>
                    <th class="text-center bg-dark text-white text-nowrap">بواسطة</th>
                    <th class="text-center bg-dark text-white text-nowrap">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @if ($products->isEmpty())
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="status-danger fs-6">لم يتم العثور على اي منتجات!</div>
                        </td>
                    </tr>
                @else
                    @foreach ($products as $product)
                        <tr>
                            <td class="text-center text-primary fw-bold text-nowrap">{{ $loop->iteration }}</td>
                            <td class="text-center text-nowrap">
                                <span class="text-dark fw-bold">
                                    {{ $product->name_ar }}
                                </span>
                            </td>
                            <td class="text-center text-nowrap">
                                @if ($product->category)
                                    <span class="badge status-available">{{ $product->category->name_ar }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                <span class="text-dark">{{ $product->sku }}</span>
                            </td>
                            <td class="text-center text-nowrap">
                                <span class="text-dark">{{ $product->unit }}</span>
                            </td>
                            <td class="text-center text-nowrap">
                                @if ($product->made_by)
                                    <a href="{{ route('admin.user.profile', $product->made_by) }}"
                                        class="text-dark fw-bold text-decoration-none">
                                        {{ $product->made_by->name }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="action-icons text-center text-nowrap">
                                <button class="btn btn-link p-0 pb-1 me-2" type="button" data-bs-toggle="modal"
                                    data-bs-target="#editProductModal{{ $product->id }}">
                                    <i class="fa-solid fa-pen-to-square text-primary" title="تعديل المنتج"></i>
                                </button>
                                <button class="btn btn-link p-0 pb-1 m-0" type="button" data-bs-toggle="modal"
                                    data-bs-target="#deleteProductModal{{ $product->id }}">
                                    <i class="fa-solid fa-trash text-danger" title="حذف المنتج"></i>
                                </button>
                            </td>
                        </tr>

                        {{-- update modal --}}
                        <div class="modal fade" id="editProductModal{{ $product->id }}" tabindex="-1"
                            aria-labelledby="editProductModalLabel{{ $product->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h5 class="modal-title text-white fw-bold"
                                            id="editProductModalLabel{{ $product->id }}">تعديل المنتج</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('inventory.products.update', $product) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body text-dark">
                                            <div class="row g-3 mb-3">
                                                <div class="col-12 col-md-6">
                                                    <label class="form-label">اسم المنتج (عربي)</label>
                                                    <input type="text" class="form-control border-primary"
                                                        name="name_ar" value="{{ $product->name_ar }}" required>
                                                    @error('name_ar')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label class="form-label">اسم المنتج (إنجليزي)</label>
                                                    <input type="text" class="form-control border-primary"
                                                        name="name_en" value="{{ $product->name_en }}" required>
                                                    @error('name_en')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row g-3 mb-3">
                                                <div class="col-12 col-md-6">
                                                    <label class="form-label">الفئة</label>
                                                    <select class="form-select border-primary" name="category_id"
                                                        required>
                                                        <option value="">اختر الفئة</option>
                                                        @foreach ($categories as $category)
                                                            <option value="{{ $category->id }}"
                                                                {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                                                {{ $category->name_ar }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @error('category_id')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-12 col-md-6">
                                                    <label class="form-label">SKU</label>
                                                    <input type="text" class="form-control border-primary"
                                                        name="sku" value="{{ $product->sku }}" required>
                                                    @error('sku')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row g-3 mb-3">
                                                <div class="col-12">
                                                    <label class="form-label">الوحدة</label>
                                                    <input type="text" class="form-control border-primary"
                                                        name="unit" value="{{ $product->unit }}" required>
                                                    @error('unit')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row g-3 mb-3">
                                                <div class="col-12">
                                                    <label class="form-label">الوصف</label>
                                                    <textarea class="form-control border-primary" name="description" rows="3">{{ $product->description }}</textarea>
                                                    @error('description')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row g-3 mb-3">
                                                <div class="col-12">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="active"
                                                            id="activeCheckbox{{ $product->id }}"
                                                            {{ $product->active ? 'checked' : '' }} value="1">
                                                        <label class="form-check-label"
                                                            for="activeCheckbox{{ $product->id }}">
                                                            المنتج مفعل
                                                        </label>
                                                    </div>
                                                    @error('active')
                                                        <div class="text-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="modal-footer d-flex flex-column flex-sm-row justify-content-start gap-2">
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
                        <div class="modal fade" id="deleteProductModal{{ $product->id }}" tabindex="-1"
                            aria-labelledby="deleteProductModalLabel{{ $product->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <h5 class="modal-title text-white fw-bold"
                                            id="deleteProductModalLabel{{ $product->id }}">تأكيد الحذف</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center text-dark">
                                        هل انت متأكد من حذف المنتج <strong>{{ $product->name_ar }}</strong>؟
                                    </div>
                                    <div class="modal-footer d-flex flex-column flex-sm-row justify-content-center gap-2">
                                        <button type="button" class="btn btn-secondary fw-bold order-2 order-sm-1"
                                            data-bs-dismiss="modal">إلغاء</button>
                                        <form action="{{ route('inventory.products.delete', $product) }}" method="POST"
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
        @if (method_exists($products, 'appends'))
            {{ $products->links('components.pagination') }}
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
