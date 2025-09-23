@extends('layouts.app')

@section('title', 'أنواع الحاويات')

@section('content')

<div class="d-flex justify-content-between align-items-end mb-4">
    <h1>أنواع الحاويات</h1>
    <button class="btn btn-primary fw-bold" type="button" data-bs-toggle="modal" data-bs-target="#createContainerTypeModal">
        <i class="fa-solid fa-plus pe-1"></i>
        أضف فئة جديدة
    </button>
</div>

<div class="modal fade" id="createContainerTypeModal" tabindex="-1" aria-labelledby="createContainerTypeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark fw-bold" id="createContainerTypeModalLabel">إنشاء فئة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('yard.containers.types.store') }}" method="POST">
                @csrf
                <div class="modal-body text-dark">
                    <div class="mb-3">
                        <label for="name" class="form-label">إســـم الفئـــة</label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="daily_price" class="form-label">السعـــر اليومـــي</label>
                        <input type="text" class="form-control" id="daily_price" name="daily_price" value="{{ old('daily_price') }}" required>
                        @error('daily_price')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إالغاء</button>
                    <button type="submit" class="btn btn-primary fw-bold">إنشاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if (session('success'))
    @push('scripts')
        <script>
            showToast("{{ session('success') }}", "success");
        </script>
    @endpush
@endif

@if (session('error'))
    @push('scripts')
        <script>
            showToast("{{ session('error') }}", "danger");
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

@if ($errors->any())
    @foreach ($errors->all() as $error)
        @push('scripts')
            <script>
                showToast("{{ $error }}", "danger");
            </script>
        @endpush
    @endforeach
@endif

<div class="table-container">
    <table class="table table-hover">
        <thead>
            <tr>
                <th class="text-center bg-dark text-white">رقم الفئة</th>
                <th class="text-center bg-dark text-white">إســـم الفئـــة</th>
                <th class="text-center bg-dark text-white">السعــر اليــومــي</th>
                <th class="text-center bg-dark text-white">الإجـــراءات</th>
            </tr>
        </thead>
        <tbody>
            @if ($containerTypes->isEmpty())
                <tr>
                    <td colspan="4" class="text-center">
                        <div class="status-danger fs-6">لم يتم العثور على اي انواع حاويات!</div>
                    </td>
                </tr>
            @else
                @foreach ($containerTypes as $containerType)
                    <tr>
                        <td class="text-center">{{ $containerType->id }}</td>
                        <td class="text-center">{{ $containerType->name }}</td>
                        <td class="text-center text-success fw-bold">{{ $containerType->daily_price }} ريال</td>
                        <td class="action-icons text-center">
                            <button class="btn btn-link p-0 pb-1 me-2" type="button" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $containerType->id }}">
                                <i class="fa-solid fa-pen-to-square text-primary" title="Edit user"></i>
                            </button>
                            <button class="btn btn-link p-0 pb-1 m-0" type="button" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $containerType->id }}">
                                <i class="fa-solid fa-trash-can text-danger" title="delete user"></i>
                            </button>
                        </td>
                    </tr>

                    <div class="modal fade" id="editUserModal{{ $containerType->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $containerType->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-dark fw-bold" id="editUserModalLabel{{ $containerType->id }}">تعديل بيانات الفئــة</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('yard.containers.types.update', $containerType->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body text-dark">
                                        <div class="mb-3">
                                            <label for="name{{ $containerType->id }}" class="form-label">إسم الفئــة</label>
                                            <input type="text" class="form-control border-primary" id="name{{ $containerType->id }}" name="name" value="{{ old('name', $containerType->name) }}" required>
                                            @error('name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="daily_price{{ $containerType->id }}" class="form-label">السعــر اليومــي</label>
                                            <input type="text" class="form-control border-primary" id="daily_price{{ $containerType->id }}" name="daily_price" value="{{ old('daily_price', $containerType->daily_price) }}" required>
                                            @error('daily_price')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                                        <button type="submit" class="btn btn-primary fw-bold">حفظ التغييرات</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="deleteUserModal{{ $containerType->id }}" tabindex="-1" aria-labelledby="deleteUserModalLabel{{ $containerType->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-dark fw-bold" id="deleteUserModalLabel{{ $containerType->id }}">تأكيد الحذف</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center text-dark">
                                    هل انت متأكد من حذف  <strong>{{ $containerType->name }}</strong>؟
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                                    <form action="{{ route('yard.containers.types.delete', $containerType->id) }}" method="POST">
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