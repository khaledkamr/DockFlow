@extends('layouts.app')

@section('title', 'انواع البضائع')

@section('content')

    <div class="d-flex justify-content-between align-items-end mb-4">
        <h1>أنــواع البضـــائع</h1>
        <button class="btn btn-primary fw-bold" type="button" data-bs-toggle="modal" data-bs-target="#createBulkItemModal">
            <i class="fa-solid fa-plus pe-1"></i>
            أضف صنف جديد
        </button>
    </div>

    <div class="modal fade" id="createBulkItemModal" tabindex="-1" aria-labelledby="createBulkItemModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title text-white fw-bold" id="createBulkItemModalLabel">إنشاء صنف جديد</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('yard.bulk.items.store') }}" method="POST">
                    @csrf
                    <div class="modal-body text-dark">
                        <div class="mb-3">
                            <label for="name" class="form-label">إسم الصنف</label>
                            <input type="text" class="form-control" id="name" name="name"
                                value="{{ old('name') }}" required>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="unit" class="form-label">الوحدة</label>
                            <input type="text" class="form-control" id="unit" name="unit"
                                value="{{ old('unit') }}" required>
                            @error('unit')
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
                    <th class="text-center bg-dark text-white">إسم الصنف</th>
                    <th class="text-center bg-dark text-white">الوحدة</th>
                    <th class="text-center bg-dark text-white">الإجـــراءات</th>
                </tr>
            </thead>
            <tbody>
                @if ($items->isEmpty())
                    <tr>
                        <td colspan="4" class="text-center">
                            <div class="status-danger fs-6">لم يتم العثور على اي اصناف!</div>
                        </td>
                    </tr>
                @else
                    @foreach ($items as $item)
                        <tr>
                            <td class="text-center text-primary fw-bold">{{ $loop->iteration }}</td>
                            <td class="text-center fw-bold text-nowrap">{{ $item->name }}</td>
                            <td class="text-center text-nowrap">{{ $item->unit }}</td>
                            <td class="action-icons text-center">
                                <button class="btn btn-link p-0 pb-1 me-2" type="button" data-bs-toggle="modal"
                                    data-bs-target="#editBulkItemModal{{ $item->id }}">
                                    <i class="fa-solid fa-pen-to-square text-primary" title="Edit item"></i>
                                </button>
                                <button class="btn btn-link p-0 pb-1 m-0" type="button" data-bs-toggle="modal"
                                    data-bs-target="#deleteBulkItemModal{{ $item->id }}">
                                    <i class="fa-solid fa-trash-can text-danger" title="Delete item"></i>
                                </button>
                            </td>
                        </tr>

                        <div class="modal fade" id="editBulkItemModal{{ $item->id }}" tabindex="-1"
                            aria-labelledby="editBulkItemModalLabel{{ $item->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-primary">
                                        <h5 class="modal-title text-white fw-bold"
                                            id="editBulkItemModalLabel{{ $item->id }}">تعديل بيانات الصنف</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <form action="{{ route('yard.bulk.items.update', $item) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body text-dark">
                                            <div class="mb-3">
                                                <label for="name{{ $item->id }}" class="form-label">إسم الصنف</label>
                                                <input type="text" class="form-control border-primary"
                                                    id="name{{ $item->id }}" name="name"
                                                    value="{{ old('name', $item->name) }}" required>
                                                @error('name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="unit{{ $item->id }}" class="form-label">الوحدة</label>
                                                <input type="text" class="form-control border-primary"
                                                    id="unit{{ $item->id }}" name="unit"
                                                    value="{{ old('unit', $item->unit) }}" required>
                                                @error('unit')
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

                        <div class="modal fade" id="deleteBulkItemModal{{ $item->id }}" tabindex="-1"
                            aria-labelledby="deleteBulkItemModalLabel{{ $item->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger">
                                        <h5 class="modal-title text-white fw-bold"
                                            id="deleteBulkItemModalLabel{{ $item->id }}">تأكيد الحذف</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center text-dark">
                                        هل انت متأكد من حذف <strong>{{ $item->name }}</strong>?
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary fw-bold"
                                            data-bs-dismiss="modal">إلغاء</button>
                                        <form action="{{ route('yard.bulk.items.delete', $item) }}" method="POST">
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

@endsection
