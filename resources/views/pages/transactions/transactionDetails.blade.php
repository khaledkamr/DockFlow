@extends('layouts.app')

@section('title', 'تفاصيل المعاملة')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h2 class="h3 text-primary mb-1">
                <i class="fas fa-clipboard-list me-2"></i>
                تفاصيل المعاملة #{{ $transaction->id }}
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    @if($transaction->contract_id)
                        <li class="breadcrumb-item">
                            <a href="{{ route('contracts.details', $transaction->customer->contract) }}"
                                class="text-decoration-none">العقد #{{ $transaction->contract_id }}</a>
                        </li>
                    @endif
                    <li class="breadcrumb-item">
                        <a href="" class="text-decoration-none">
                            المعاملات
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        المعاملة #{{ $transaction->id }}
                        @if($transaction->status == 'معلقة')
                            <span class="badge bg-warning text-dark ms-2">معلقة</span>
                        @elseif($transaction->status == 'مغلقة')
                            <span class="badge bg-success ms-2">مغلقة</span>
                        @endif
                    </li>
            </nav>
        </div>
        <div class="d-flex gap-2">
            @if ($transaction->containers->first()->invoices->isEmpty())
                <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal"
                    data-bs-target="#createInvoice">
                    <i class="fas fa-scroll"></i>
                    إنشاء فاتورة
                </button>
            @endif
        </div>
    </div>

    <!-- Create Invoice Modal -->
    <div class="modal fade" id="createInvoice" tabindex="-1" aria-labelledby="createInvoiceLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-dark fw-bold" id="createInvoiceLabel">إنشاء فاتورة جديدة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('invoices.clearance.store', $transaction) }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="تخليص">
                    <input type="hidden" name="customer_id" value="{{ $transaction->customer_id }}">
                    <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">
                    <input type="hidden" name="date" value="{{ Carbon\Carbon::now() }}">
                    <input type="hidden" name="container_ids[]" value="{{ $transaction->containers->pluck('id')->join(',') }}">
                    <div class="modal-body text-dark">
                        <div class="row mb-4">
                            <div class="col">
                                <label class="form-label">طريقة الدفع</label>
                                <select class="form-select border-primary" name="payment_method" required>
                                    <option value="آجل">آجل</option>
                                    <option value="كاش">كاش</option>
                                    <option value="تحويل بنكي">تحويل بنكي</option>
                                </select>
                            </div>
                            <div class="col">
                                <label class="form-label">نسبة الخصم(%)</label>
                                <input type="number" name="discount" id="discount" class="form-control border-primary" 
                                    min="0" max="100" step="1" value="0" placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-start">
                        <button type="submit" class="btn btn-primary fw-bold">إنشاء</button>
                        <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Transaction Information Section -->
    <div class="row">
        <div class="col-lg-6 d-flex flex-column gap-3 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header d-flex justify-content-between align-items-center bg-dark text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        معلومات المعاملة
                    </h5>
                    <span class="small">{{ Carbon\Carbon::parse($transaction->date)->format('Y/m/d') }}</span>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-12">
                            <div>
                                <div class="row">
                                    <div class="col">
                                        <label class="form-label text-muted small">رقم البوليصة</label>
                                        <div class="fw-bold fs-5">{{ $transaction->policy_number }}</div>
                                    </div>
                                    <div class="col">
                                        <label class="form-label text-muted small">البيان الجمركي</label>
                                        <div class="fw-bold fs-5">{{ $transaction->customs_declaration }}</div>
                                    </div>
                                    <div class="col">
                                        <label class="form-label text-muted small">تاريخ البيان الجمركي</label>
                                        <div class="fw-bold fs-5">{{ Carbon\Carbon::parse($transaction->customs_declaration_date)->format('Y/m/d') }}</div>
                                    </div>
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
                        <i class="fas fa-money-bill-wave me-2"></i>
                        اطراف المعاملة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-building me-2"></i>
                                الطرف الأول
                            </h6>
                            <div class="">
                                <div class="fw-bold">{{ $transaction->made_by->company->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">
                                <i class="fas fa-user-tie me-2"></i>
                                الطرف الثاني
                            </h6>
                            <div class="">
                                <div class="fw-bold">{{ $transaction->customer->name }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Items Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
            <div class="d-flex justify-content-between align-items-center text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>
                    بنود المعاملة
                </h5>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                    <i class="fas fa-plus me-1"></i>
                    إضافة بند جديد
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            @if (count($transaction->items) > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-primary">
                            <tr>
                                <th class="text-center fw-bold">#</th>
                                <th class="text-center fw-bold">البند</th>
                                <th class="text-center fw-bold">المبلغ</th>
                                <th class="text-center fw-bold">الضريبة</th>
                                <th class="text-center fw-bold">الإجمالي</th>
                                <th class="text-center fw-bold">الإجرائات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaction->items as $index => $item)
                                <tr class="align-middle" id="item-row-{{ $item->id }}">
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-center fw-bold">{{ $item->description }}</td>
                                    <td class="text-center">{{ number_format($item->amount, 2) }}</td>
                                    <td class="text-center">{{ number_format($item->tax, 2) }}</td>
                                    <td class="text-center fw-bold text-primary">{{ number_format($item->total, 2) }}</td>
                                    <td class="text-center">
                                        <a href="#" class="text-primary me-2" type="button" data-bs-toggle="modal" data-bs-target="#editItemModal{{ $item->id }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="#" class="text-danger" type="button" data-bs-toggle="modal" data-bs-target="#deleteItemModal{{ $item->id }}">
                                            <i class="fas fa-trash-can"></i>
                                        </a>
                                    </td>
                                </tr>

                                <!-- Edit Item Modal -->
                                <div class="modal fade" id="editItemModal{{ $item->id }}" tabindex="-1" aria-labelledby="editItemModalLabel{{ $item->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold" id="editItemModalLabel{{ $item->id }}">
                                                    <i class="fas fa-edit me-2"></i>
                                                    تعديل البند
                                                </h5>
                                                <button type="button" class="btn-close btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('transactions.item.update', $item) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <input type="hidden" name="transaction_id" value="{{ $item->transaction->id }}">
                                                    <div class="mb-3">
                                                        <label for="editDescription" class="form-label">البند</label>
                                                        <textarea class="form-control border-primary" id="editDescription" name="description" rows="1" required>{{ $item->description }}</textarea>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col">
                                                            <label class="form-label">المبلغ</label>
                                                            <input type="number" class="form-control border-primary" name="amount" id="editAmount{{ $loop->index }}" value="{{ $item->amount }}"
                                                                step="1" required onchange="calculateEditTotal({{ $loop->index }})">
                                                        </div>
                                                        <div class="col">
                                                            <label for="editTax" class="form-label">الضريبة</label>
                                                            <select class="form-select border-primary" id="editTaxPercentage{{ $loop->index }}" onchange="calculateEditTotal({{ $loop->index }})">
                                                                <option disabled selected>نوع الضريبة</option>
                                                                <option value="15" data-rate="15">خاضع للضريبة (15%)</option>
                                                                <option value="0" data-rate="0">غير خاضع للضريبة</option>
                                                            </select>
                                                            <input type="hidden" id="editTax{{ $loop->index }}" name="tax" value="{{ $item->tax }}">
                                                        </div>
                                                        <div class="col">
                                                            <label for="editTotal" class="form-label">الإجمالي</label>
                                                            <input type="number" class="form-control border-primary" name="total" id="editTotal{{ $loop->index }}" value="{{ $item->total }}"
                                                                step="1" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer d-flex justify-content-start">
                                                    <button type="submit" class="btn btn-primary fw-bold">حفظ التغييرات</button>
                                                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Item Modal -->
                                <div class="modal fade" id="deleteItemModal{{ $item->id }}" tabindex="-1" aria-labelledby="deleteItemModalLabel{{ $item->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title text-dark fw-bold" id="deleteItemModalLabel{{ $item->id }}">تأكيد الحذف</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body text-center text-dark">
                                                هل انت متأكد من البند <strong>{{ $item->description }}</strong>؟
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                                                <form action="{{ route('transactions.item.delete', $item) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger fw-bold">حذف</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-light fw-bold">
                                <td colspan="2" class="text-center">الإجمالي:</td>
                                <td class="text-center">{{ number_format($transaction->items->sum('amount'), 2) }}</td>
                                <td class="text-center">{{ number_format($transaction->items->sum('tax'), 2) }}</td>
                                <td class="text-center text-primary">
                                    {{ number_format($transaction->items->sum('total'), 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <h5 class="text-muted">لا توجد بنود مرتبطة بهذه المعاملة</h5>
                </div>
            @endif
        </div>
    </div>

    <!-- Procedures Timeline Section -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-clock me-2"></i>
                الخط زمني للإجرائات
            </h5>
        </div>
        <div class="card-body">
            @if (count($transaction->procedures) > 0)
                <div class="timeline">
                    @foreach ($transaction->procedures as $procedure)
                        <div class="timeline-item mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="timeline-content flex-grow-1 d-flex justify-content-between">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <div class="timeline-dot bg-primary"></div>
                                        <h6 class="mb-0 fw-bold">{{ $procedure->name }}</h6>
                                        <small class="text-muted d-block ps-4">
                                            <i class="fas fa-calendar-days me-1"></i>
                                            {{ Carbon\Carbon::parse($procedure->created_at)->format('Y/m/d') }}
                                        </small>
                                    </div>
                                    <a href="#" class="text-danger small" type="button" data-bs-toggle="modal" data-bs-target="#deleteProcedureModal{{ $procedure->id }}">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Procedure Modal -->
                        <div class="modal fade" id="deleteProcedureModal{{ $procedure->id }}" tabindex="-1" aria-labelledby="deleteProcedureModalLabel{{ $procedure->id }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-dark fw-bold" id="deleteProcedureModalLabel{{ $procedure->id }}">تأكيد الحذف</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body text-center text-dark">
                                        هل انت متأكد من الإجراء <strong>{{ $procedure->name }}</strong>؟
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                                        <form action="{{ route('transactions.delete.procedure', $procedure) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger fw-bold">حذف</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <p class="text-muted">لا توجد إجرائات لهذه المعاملة حتى الآن</p>
                </div>
            @endif

            <!-- Add Procedure Form -->
            <div class="mt-4 pt-3 border-top">
                <h6 class="fw-bold mb-3">إضافة إجراء جديد</h6>
                <form action="{{ route('transactions.store.procedure', $transaction) }}" method="POST" id="addProcedureForm">
                    @csrf
                    <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                    <div class="input-group">
                        <input type="text" class="form-control border-primary" name="name" placeholder="وصف الإجراء..." required>
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-plus me-1"></i>
                            إضافة
                        </button>
                    </div>
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </form>
            </div>
        </div>
    </div>

    <!-- Containers Section -->
    <div class="card border-0 shadow-sm mb-5">
        <div class="card-header bg-dark text-white">
            <div class="d-flex justify-content-between align-items-center text-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-boxes me-2"></i>
                    الحاويات المشمولة في المعاملة
                </h5>
                <span class="badge bg-light text-dark">{{ count($transaction->containers) }} حاوية</span>
            </div>
        </div>
        <div class="card-body p-0">
            @if (count($transaction->containers) > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-primary">
                            <tr>
                                <th class="text-center fw-bold">#</th>
                                <th class="text-center fw-bold">رقم الحاوية</th>
                                <th class="text-center fw-bold">نوع الحاوية</th>
                                <th class="text-center fw-bold">اسم العميل</th>
                                <th class="text-center fw-bold">الحالة</th>
                                <th class="text-center fw-bold">الإجرائات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaction->containers as $index => $container)
                                <tr class="text-center">
                                    <td class="text-center">{{ $container->id }}</td>
                                    <td>
                                        <a href="{{ route('container.details', $container) }}"
                                            class="fw-bold text-decoration-none">
                                            {{ $container->code }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $container->containerType->name }}</div>
                                    </td>
                                    <td>
                                        <div>{{ $container->customer->name }}</div>
                                    </td>
                                    <td>
                                        @if($container->transportOrders->isNotEmpty())
                                            <div class="status-available">تم اشعار نقل</div>
                                        @else
                                            <div class="status-danger">في الميناء</div>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">
                                            عرض
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">لا توجد حاويات مرتبطة بهذه المعاملة</h5>
                </div>
            @endif
        </div>
    </div>

    <!-- Add Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="addItemModalLabel">
                        <i class="fas fa-plus me-2"></i>
                        إضافة بند جديد
                    </h5>
                    <button type="button" class="btn-close btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="addItemForm" method="POST" action="{{ route('transactions.item.store') }}">
                    @csrf
                    <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="description" class="form-label">البند</label>
                            <textarea class="form-control border-primary" id="description" name="description" rows="1" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="amount" class="form-label">المبلغ</label>
                                <input type="number" class="form-control border-primary" id="amount" name="amount"
                                    step="0.01" required onchange="calculateTotal()">
                            </div>
                            <div class="col">
                                <label for="tax" class="form-label">الضريبة</label>
                                <select class="form-select border-primary" id="tax_percentage" onchange="calculateTotal()">
                                    <option disabled selected>نوع الضريبة</option>
                                    <option value="15" data-rate="15">خاضع للضريبة (15%)</option>
                                    <option value="0" data-rate="0">غير خاضع للضريبة</option>
                                </select>
                                <input type="hidden" id="tax" name="tax" value="0">
                            </div>
                            <div class="col">
                                <label for="total" class="form-label">الإجمالي</label>
                                <input type="number" class="form-control border-primary" id="total" name="total"
                                    step="0.01" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-start">
                        <button type="submit" class="btn btn-primary fw-bold">حفظ</button>
                        <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="text-center mt-4 mb-3">
        <small class="text-muted">
            تم إنشاء هذه المعاملة بواسطة: {{ $transaction->made_by->name }}
        </small>
    </div>

    <style>
        .timeline {
            position: relative;
        }
        .timeline-item {
            position: relative;
            padding-left: 10px;
        }
        .timeline-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            flex-shrink: 0;
        }
        .timeline-content {
            background-color: #f8f9fa;
            padding: 12px 15px;
            border-radius: 0.375rem;
            border-right: 3px solid #0d6efd;
        }
        .timeline-content:hover {
            background-color: #e5e6e6;
            transition: all 0.3s ease;
        }
    </style>
    
    <script>
        function calculateTotal() {
            const amount = parseFloat(document.getElementById('amount').value) || 0;
            const taxPercent = parseFloat(document.getElementById('tax_percentage').value) || 0;
            const tax = (amount * taxPercent) / 100;
            const total = amount + tax;
            document.getElementById('tax').value = tax.toFixed(2);
            document.getElementById('total').value = total.toFixed(2);
        }

        function calculateEditTotal(index) {
            const amount = parseFloat(document.getElementById('editAmount' + index).value) || 0;
            const taxPercent = parseFloat(document.getElementById('editTaxPercentage' + index).value) || 0;
            const tax = (amount * taxPercent) / 100;
            const total = amount + tax;
            document.getElementById('editTax' + index).value = tax.toFixed(2);
            document.getElementById('editTotal' + index).value = total.toFixed(2);
        }
    </script>
@endsection