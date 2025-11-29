@extends('layouts.app')

@section('title', 'الخدمات')

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>الخدمـــات</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addService">
            <i class="fas fa-plus me-2"></i>
            <span>إضافة خدمة جديدة</span>
        </button>
    </div>

    <div class="row g-3">
        @forelse($services as $index => $service)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="border-3 border-start border-primary rounded p-3 bg-white shadow-sm d-flex justify-content-between align-items-center">
                    <div class="flex-grow-1">
                        <span>{{ $service->description }}</span>
                    </div>
                    <div class="d-flex gap-2 ms-3">
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#updateService{{ $service->id }}" title="تعديل">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                            data-bs-target="#deleteService{{ $service->id }}" title="حذف">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Update Service Modal -->
            <div class="modal fade" id="updateService{{ $service->id }}" tabindex="-1"
                aria-labelledby="updateServiceLabel{{ $service->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-dark fw-bold" id="updateServiceLabel{{ $service->id }}">
                                تعديل بيانات الخدمة
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('contracts.service.update', $service->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body text-dark">
                                <div class="mb-3">
                                    <label for="description{{ $service->id }}" class="form-label fw-bold">الوصف</label>
                                    <textarea class="form-control border-primary" id="description{{ $service->id }}" name="description" rows="3"
                                        required>{{ $service->description }}</textarea>
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

            <!-- Delete Service Modal -->
            <div class="modal fade" id="deleteService{{ $service->id }}" tabindex="-1"
                aria-labelledby="deleteServiceLabel{{ $service->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title fw-bold" id="deleteServiceLabel{{ $service->id }}">
                                <i class="fas fa-exclamation-triangle me-2"></i>حذف الخدمة
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form action="{{ route('contracts.service.delete', $service->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="modal-body text-center">
                                <i class="fas fa-exclamation-circle text-danger" style="font-size: 3rem;"></i>
                                <p class="mt-3">هل أنت متأكد من حذف الخدمة؟</p>
                                <p class="fw-bold">{{ $service->description }}</p>
                                <p class="text-muted small">لا يمكن التراجع عن هذا الإجراء</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary fw-bold"
                                    data-bs-dismiss="modal">إلغاء</button>
                                <button type="submit" class="btn btn-danger fw-bold">تأكيد الحذف</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">لا توجد خدمات مضافة</p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Add Service Modal -->
    <div class="modal fade" id="addService" tabindex="-1" aria-labelledby="addServiceLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="addServiceLabel">
                        <i class="fas fa-plus-circle me-2"></i>إضافة خدمة جديدة
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('contracts.service.store') }}" method="POST">
                    @csrf
                    <div class="modal-body text-dark">
                        <div class="mb-3">
                            <label for="new_description" class="form-label fw-bold">وصف الخدمة</label>
                            <textarea class="form-control border-primary" id="new_description" name="description" rows="3"
                                placeholder="اكتب وصف الخدمة هنا..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary fw-bold">
                            <i class="fas fa-check me-1"></i>حفظ الخدمة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
