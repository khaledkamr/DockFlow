@extends('layouts.app')

@section('title', 'مخزون البضائع')

@section('content')
    <style>
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

    <div class="d-flex justify-content-between align-items-end mb-4">
        <h1>مخزون البضـــائع</h1>
        <button class="btn btn-primary fw-bold" type="button" data-bs-toggle="modal"
            data-bs-target="#createBulkInventoryModal">
            <i class="fa-solid fa-plus pe-1"></i>
            أضف مخزون جديد
        </button>
    </div>

    <!-- Create Bulk Inventory Modal -->
    <div class="modal fade" id="createBulkInventoryModal" tabindex="-1" aria-labelledby="createBulkInventoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white fw-bold" id="createBulkInventoryModalLabel">إنشاء مخزون جديد</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('yard.bulk.inventory.store') }}" method="POST">
                    @csrf
                    <div class="modal-body text-dark">
                        <div class="mb-3">
                            <label for="customer_id" class="form-label">العميل</label>
                            <select class="form-control select2-search-field" id="customer_id" name="customer_id" required>
                                <option value="">اختر العميل</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="item_id" class="form-label">الصنف</label>
                            <select class="form-control select2-search-field" id="item_id" name="item_id" required>
                                <option value="">اختر الصنف</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('item_id')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="price_per_unit" class="form-label">السعر للوحدة</label>
                            <input type="number" step="0.01" class="form-control border-primary" id="price_per_unit"
                                name="price_per_unit" value="{{ old('price_per_unit') }}" required>
                            @error('price_per_unit')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary fw-bold">إنشاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="table-container">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th class="text-center bg-dark text-white">#</th>
                    <th class="text-center bg-dark text-white">إسم العميل</th>
                    <th class="text-center bg-dark text-white">إسم الصنف</th>
                    <th class="text-center bg-dark text-white">الرصيد</th>
                    <th class="text-center bg-dark text-white">السعر للوحدة</th>
                    <th class="text-center bg-dark text-white">الإجـــراءات</th>
                </tr>
            </thead>
            <tbody>
                @if ($inventories->isEmpty())
                    <tr>
                        <td colspan="6" class="text-center">
                            <div class="status-danger fs-6">لم يتم العثور على اي مخزون!</div>
                        </td>
                    </tr>
                @else
                    @foreach ($inventories as $inventory)
                        <tr>
                            <td class="text-center text-primary fw-bold">{{ $loop->iteration }}</td>
                            <td class="text-center text-nowrap">
                                <a href="{{ route('users.customer.profile', $inventory->customer) }}"
                                    class="text-dark text-decoration-none">
                                    {{ $inventory->customer->name }}
                                </a>
                            </td>
                            <td class="text-center text-nowrap">{{ $inventory->item->name }}</td>
                            <td class="text-center text-nowrap">{{ $inventory->balance }} {{ $inventory->item->unit }}</td>
                            <td class="text-center text-success fw-bold text-nowrap">{{ $inventory->price_per_unit }} ريال
                            </td>
                            <td class="action-icons text-center">
                                <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="modal"
                                    data-bs-target="#editBulkInventoryModal{{ $inventory->id }}">
                                    <i class="fa-solid fa-pen-to-square pe-1"></i>
                                    تعديل
                                </button>
                                <button class="btn btn-sm btn-outline-primary ms-2" type="button" data-bs-toggle="modal"
                                    data-bs-target="#detailsBulkInventoryModal{{ $inventory->id }}">
                                    <i class="fa-solid fa-circle-info pe-1"></i>
                                    التفاصيل
                                </button>
                            </td>
                        </tr>

                        <!-- Update Bulk Inventory Modal -->
                        <div class="modal fade" id="editBulkInventoryModal{{ $inventory->id }}" tabindex="-1"
                            aria-labelledby="editBulkInventoryModalLabel{{ $inventory->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h5 class="modal-title text-white fw-bold"
                                            id="editBulkInventoryModalLabel{{ $inventory->id }}">تعديل بيانات المخزون</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('yard.bulk.inventory.update', $inventory->id) }}"
                                        method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body text-dark">
                                            <div class="mb-3">
                                                <label for="customer_id{{ $inventory->id }}"
                                                    class="form-label">العميل</label>
                                                <select class="form-control border-primary select2-search-field"
                                                    id="customer_id{{ $inventory->id }}" name="customer_id" required>
                                                    @foreach ($customers as $customer)
                                                        <option value="{{ $customer->id }}"
                                                            {{ old('customer_id', $inventory->customer_id) == $customer->id ? 'selected' : '' }}>
                                                            {{ $customer->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('customer_id')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="item_id{{ $inventory->id }}" class="form-label">الصنف</label>
                                                <select class="form-control border-primary select2-search-field"
                                                    id="item_id{{ $inventory->id }}" name="item_id" required>
                                                    @foreach ($items as $item)
                                                        <option value="{{ $item->id }}"
                                                            {{ old('item_id', $inventory->item_id) == $item->id ? 'selected' : '' }}>
                                                            {{ $item->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('item_id')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="price_per_unit{{ $inventory->id }}" class="form-label">السعر
                                                    للوحدة</label>
                                                <input type="number" step="0.01" class="form-control border-primary"
                                                    id="price_per_unit{{ $inventory->id }}" name="price_per_unit"
                                                    value="{{ old('price_per_unit', $inventory->price_per_unit) }}"
                                                    required>
                                                @error('price_per_unit')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary fw-bold"
                                                data-bs-dismiss="modal">إلغاء</button>
                                            <button type="submit" class="btn btn-primary fw-bold">حفظ التغييرات</button>
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

    <script>
        $(document).ready(function() {
            // Initialize Select2 for all select elements with customer_id or item_id
            $('select[id^="customer_id"], select[id^="item_id"]').select2({
                language: 'ar',
                dir: 'rtl',
                width: '100%',
                searchInputPlaceholder: 'ابحث...',
                allowClear: true,
            });

            // Reinitialize Select2 for dynamically created modals
            $(document).on('shown.bs.modal', function(e) {
                let modal = $(e.target);
                modal.find('select[id^="customer_id"], select[id^="item_id"]').select2({
                    language: 'ar',
                    dir: 'rtl',
                    width: '100%',
                    dropdownParent: modal,
                    searchInputPlaceholder: 'ابحث...',
                    allowClear: true,
                });
            });
        });
    </script>
@endsection
