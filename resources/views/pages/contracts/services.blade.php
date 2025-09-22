@extends('layouts.app')

@section('title', 'الخدمات')

@section('content')
<style>
    .service-card {
       transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .service-card:hover {
        transform: translateY(-5px);
    }
    .service-number {
        background: linear-gradient(135deg, #42b3af 0%, #0b56a9 100%);
        color: white;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 0.9rem;
    }
    .btn-edit, .btn-delete {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .btn-edit:hover, .btn-delete:hover {
        transform: scale(1.1);
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-5">
    <h1>الخدمـــات</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addService">
        <i class="fas fa-plus me-2"></i>
        <span class="">إضافة خدمة جديدة</span>
    </button>
</div>

<div class="row">
    @foreach($services as $index => $service)
    <div class="col-md-4 mb-4">
        <div class="card border-0 rounded-3 shadow relative overflow-hidden border-top border-3 border-primary service-card ">
            <div class="card-header border-bottom p-3 bg-light d-flex justify-content-between align-items-center">
                <div class="service-number">الخدمة #{{ $index + 1 }}</div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-edit btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updateService{{ $service->id }}" title="تعديل">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-delete btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteService{{ $service->id }}" title="حذف">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <p class="fw-medium">{{ $service->description }}.</p>
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
                                <textarea class="form-control border-primary" name="description" rows="2">{{ $service->description }}</textarea>
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
    <div class="modal fade" id="deleteService{{ $service->id }}" tabindex="-1" aria-labelledby="deleteServiceLabel{{ $service->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-bold" id="deleteServiceLabel{{ $service->id }}">حذف الخدمة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('contracts.service.delete', $service->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        هل أنت متأكد من حذف الخدمة <strong>({{$service->description}})</strong> بشكل نهائي؟
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger fw-bold">تأكيد الحذف</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

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
                            <label class="form-label">وصف الخدمة</label>
                            <textarea class="form-control border-primary" name="description" rows="2" placeholder="اكتب هنا..."></textarea>
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

@if ($errors->any())
    @foreach ($errors->all() as $error)
        @push('scripts')
            <script>
                showToast("{{ $error }}", "danger");
            </script>
        @endpush
    @endforeach
@endif

@endsection



