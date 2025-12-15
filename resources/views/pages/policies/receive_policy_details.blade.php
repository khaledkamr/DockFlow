@extends('layouts.app')

@section('title', 'تفاصيل البوليصة')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-4">
                <div>
                    <h2 class="h3 text-primary mb-1">
                        <i class="fas fa-clipboard-list me-2 d-none d-md-inline"></i>
                        <span class="d-none d-md-inline">تفاصيل بوليصة التسليم {{ $policy->code }}</span>
                        <span class="d-inline d-md-none">بوليصة {{ $policy->code }}</span>
                    </h2>
                </div>
                <div class="d-flex gap-2">
                    <form action="{{ route('print', 'exit_permission') }}" method="POST" target="_blank">
                        @csrf
                        @foreach ($policy->containers as $container)
                            <input type="hidden" name="containers[]" value="{{ $container->id }}">
                        @endforeach
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-print me-1"></i>
                            <span class="d-inline">طباعة اذن خروج</span>
                        </button>
                    </form>
                    @if(auth()->user()->roles->contains('name', 'Super Admin'))
                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                            <i class="fas fa-trash me-1"></i>
                            <span class="d-inline">حذف البوليصة</span>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="deleteModalLabel">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                تأكيد حذف البوليصة
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                            </div>
                            <h6 class="mb-3">هل أنت متأكد من حذف هذه البوليصة؟</h6>
                            <p class="text-muted mb-0">
                                سيتم حذف بوليصة التسليم <strong>{{ $policy->code }}</strong> نهائياً ولن يمكن استرجاعها.
                            </p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>
                                إلغاء
                            </button>
                            <form action="{{ route('policy.delete', $policy) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash me-1"></i>
                                    تأكيد الحذف
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 d-flex flex-column gap-3 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-dark text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                معلومات الإتفاقية
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="row g-3">
                                        <div class="col-6 col-sm-6">
                                            <label class="form-label text-muted small">تاريخ البوليصة</label>
                                            <div class="fw-bold">{{ $policy->created_at->format('Y/m/d') }}</div>
                                        </div>
                                        <div class="col-6 col-sm-6">
                                            <label class="form-label text-muted small">تم الإنشاء بواسطة</label>
                                            <div class="fw-bold">{{ $policy->made_by->name }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-dark text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-user me-2"></i>
                                معلومات السائق والمركبة
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="row g-3">
                                        <div class="col-6 col-sm-6 col-lg-3">
                                            <label class="form-label text-muted small">اسم السائق</label>
                                            <div class="fw-bold">{{ $policy->driver_name }}</div>
                                        </div>
                                        <div class="col-6 col-sm-6 col-lg-3">
                                            <label class="form-label text-muted small">الرقم القومي</label>
                                            <div class="fw-bold">{{ $policy->driver_NID }}</div>
                                        </div>
                                        <div class="col-6 col-sm-6 col-lg-3">
                                            <label class="form-label text-muted small">نوع المركبة</label>
                                            <div class="fw-bold">{{ $policy->driver_car }}</div>
                                        </div>
                                        <div class="col-6 col-sm-6 col-lg-3">
                                            <label class="form-label text-muted small">رقم اللوحة</label>
                                            <div class="fw-bold">{{ $policy->car_code }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-dark text-white">
                    <div class="d-flex flex-row justify-content-between align-items-center text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-boxes me-2"></i>
                            الحاويات المشمولة في البوليصة
                        </h5>
                        <span class="badge bg-light text-dark d-none d-md-inline">{{ count($policy->containers) }} حاوية</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if (count($policy->containers) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-primary">
                                    <tr>
                                        <th class="border-0 text-center fw-bold text-nowrap">#</th>
                                        <th class="border-0 text-center fw-bold text-nowrap">رقم الحاوية</th>
                                        <th class="border-0 text-center fw-bold text-nowrap">نوع الحاوية</th>
                                        <th class="border-0 text-center fw-bold text-nowrap">العميل</th>
                                        <th class="border-0 text-center fw-bold text-nowrap">تاريخ الدخول</th>
                                        <th class="border-0 text-center fw-bold text-nowrap">تاريخ الخروج</th>
                                        <th class="border-0 text-center fw-bold text-nowrap">تم الإستلام بواسطة</th>
                                        <th class="border-0 text-center fw-bold text-nowrap">تم التسليم بواسطة</th>
                                        <th class="border-0 text-center fw-bold text-nowrap">ملاحظات</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    @foreach ($policy->containers as $index => $container)
                                        <tr>
                                            <td>{{ $container->id }}</td>
                                            <td>
                                                <a href="{{ route('container.details', $container) }}"
                                                    class="fw-bold text-decoration-none">
                                                    {{ $container->code }}
                                                </a>
                                            </td>
                                            <td class="fw-bold">{{ $container->containerType->name }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('users.customer.profile', $container->customer) }}"
                                                    class="text-dark text-decoration-none fw-bold">
                                                    {{ $container->customer->name }}
                                                </a>
                                            </td>
                                            <td>{{ \Carbon\Carbon::parse($container->date)->format('Y/m/d') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($container->exit_date)->format('Y/m/d') }}</td>
                                            <td>{{ $container->received_by }}</td>
                                            <td>{{ $container->delivered_by }}</td>
                                            <td>{{ $container->notes ?? '---' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">لا توجد حاويات مرتبطة بهذه الإتفاقية</h5>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .bg-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
        }

        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        @media (max-width: 768px) {
            .d-flex.justify-content-between {
                flex-direction: column;
                gap: 1rem;
            }

            .btn-group {
                justify-content: center;
            }
        }

        .table .status-waiting {
            background-color: #ffe590;
            color: #745700;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
        }

        .table .status-available {
            background-color: #d4d7ed;
            color: #151657;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
        }

        .table .status-delivered {
            background-color: #c1eccb;
            color: #155724;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
        }

        .table .status-danger {
            background-color: #f8d7da;
            color: #721c24;
            padding: 5px 10px;
            border-radius: 12px;
            font-size: 12px;
            display: inline-block;
        }
    </style>
@endsection
