@extends('layouts.admin')

@section('title', 'الخدمات')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>الخدمـــات</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addService">
            <i class="bi bi-plus-circle"></i> إضافة خدمة جديدة
        </button>

        <div class="modal fade" id="addService" tabindex="-1" aria-labelledby="addServiceLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-dark fw-bold" id="addServiceLabel">إضافة خدمة جديدة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('contracts.service.store') }}" method="POST">
                        @csrf
                        <div class="modal-body text-dark">
                            <div class="row mb-3">
                                <div class="col">
                                    <label for="description" class="form-label">الوصف</label>
                                    <input type="text" class="form-control border-primary" name="description" value="">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary fw-bold">حفظ الخدمة</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        @forelse($services as $index => $service)
            <div class="col-md-4 mb-3">
                <div class="card border border-2 border-primary shadow-sm h-100">
                    <div class="px-3 py-2 d-flex justify-content-between align-items-center border-bottom">
                        <div class="text-primary">الخدمة #{{ $index + 1 }}</div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updateService{{ $service->id }}">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <form action="{{ route('contracts.service.delete', $service->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger remove-service">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <p class="card-text flex-grow-1 fw-bold">{{ $service->description }}.</p>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="updateService{{ $service->id }}" tabindex="-1" aria-labelledby="updateServiceLabel{{ $service->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-dark fw-bold" id="updateServiceLabel{{ $service->id }}">تعديل بيانات الخدمة</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('contracts.service.update', $service->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body text-dark">
                                <div class="row mb-3">
                                    <div class="col">
                                        <label for="description" class="form-label">الوصف</label>
                                        <input type="text" class="form-control border-primary" name="description" value="{{ $service->description }}">
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
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    لا توجد خدمات حتى الآن.
                </div>
            </div>
        @endforelse
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

    @if (session('error'))
        @push('scripts')
            <script>
                showToast("{{ session('error') }}", "danger");
            </script>
        @endpush
    @endif

@endsection
