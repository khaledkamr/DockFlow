@extends('layouts.app')

@section('title', 'تفاصيل المخزون')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">تفاصيل مخزون {{ $inventory->item->name }} للعميل {{ $inventory->customer->name }}</h1>
        <span class="badge bg-primary fs-6">
            الرصيد الحالي:
            <span class="fw-bold">{{ $inventory->balance ?? 0 }}</span>
            {{ $inventory->item->unit }}
        </span>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-4" id="inventoryTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#transactions-content"
                type="button" role="tab" aria-controls="transactions-content" aria-selected="true">
                <i class="fa-solid fa-exchange pe-2"></i>الحركات
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="batches-tab" data-bs-toggle="tab" data-bs-target="#batches-content" type="button"
                role="tab" aria-controls="batches-content" aria-selected="false">
                <i class="fa-solid fa-boxes-stacked pe-2"></i>الدفعات
            </button>
        </li>
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content" id="inventoryTabsContent">
        <!-- Transactions Tab -->
        <div class="tab-pane fade show active" id="transactions-content" role="tabpanel" aria-labelledby="transactions-tab">
            <div class="table-container">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="text-center bg-dark text-white">#</th>
                            <th class="text-center bg-dark text-white">رقم البوليصة</th>
                            <th class="text-center bg-dark text-white">الكمية</th>
                            <th class="text-center bg-dark text-white">نوع الحركة</th>
                            <th class="text-center bg-dark text-white">الرصيد بعد</th>
                            <th class="text-center bg-dark text-white">التاريخ</th>
                            <th class="text-center bg-dark text-white">الإجرائات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($inventory->transactions->isEmpty())
                            <tr>
                                <td colspan="7" class="text-center">
                                    <div class="status-danger fs-6">لم يتم العثور على اي حركات!</div>
                                </td>
                            </tr>
                        @else
                            @foreach ($inventory->transactions as $transaction)
                                <tr>
                                    <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                    <td class="text-center fw-bold">
                                        <a href="{{ route('policies.storage.details', $transaction->policy) }}" class="text-decoration-none" target="_blank">
                                            {{ $transaction->policy->code }}
                                        </a>
                                    </td>
                                    <td class="text-center">{{ $transaction->quantity }} {{ $inventory->item->unit }}</td>
                                    <td class="text-center">
                                        @if($transaction->transaction_type == 'in')
                                            <span class="badge status-delivered">داخل<i class="fa-solid fa-arrow-down ps-1"></i></span>
                                        @else
                                            <span class="badge status-danger">خارج<i class="fa-solid fa-arrow-up ps-1"></i></span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $transaction->balance_after }} {{ $inventory->item->unit }}</td>
                                    <td class="text-center">{{ $transaction->created_at->format('Y/m/d') }}</td>
                                    <td class="action-icons text-center">
                                        <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="modal"
                                            data-bs-target="#editTransactionModal{{ $transaction->id }}">
                                            <i class="fa-solid fa-pen-to-square pe-1"></i>
                                            تعديل
                                        </button>
                                        <a href="{{ route('policies.storage.details', $transaction->policy) }}" class="btn btn-sm btn-outline-primary ms-2">
                                            <i class="fa-solid fa-circle-info pe-1"></i>
                                            التفاصيل
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Batches Tab -->
        <div class="tab-pane fade" id="batches-content" role="tabpanel" aria-labelledby="batches-tab">
            <div class="table-container">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="text-center bg-dark text-white">#</th>
                            <th class="text-center bg-dark text-white">رقم البوليصة</th>
                            <th class="text-center bg-dark text-white">الكمية الداخلة</th>
                            <th class="text-center bg-dark text-white">الكمية المتبقية</th>
                            <th class="text-center bg-dark text-white">التاريخ</th>
                            <th class="text-center bg-dark text-white">الإجرائات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($inventory->batches->isEmpty())
                            <tr>
                                <td colspan="6" class="text-center">
                                    <div class="status-danger fs-6">لم يتم العثور على اي دفعات!</div>
                                </td>
                            </tr>
                        @else
                            @foreach ($inventory->batches as $batch)
                                <tr>
                                    <td class="text-center fw-bold">{{ $loop->iteration }}</td>
                                    <td class="text-center fw-bold">
                                        <a href="{{ route('policies.storage.details', $batch->policy) }}" class="text-decoration-none" target="_blank">
                                            {{ $batch->policy->code }}
                                        </a>
                                    </td>
                                    <td class="text-center">{{ $batch->quantity_in }} {{ $inventory->item->unit }}</td>
                                    <td class="text-center">{{ $batch->quantity_remaining }} {{ $inventory->item->unit }}</td>
                                    <td class="text-center">{{ Carbon\Carbon::parse($batch->entry_date)->format('Y/m/d') }}</td>
                                    <td class="action-icons text-center">
                                        <button class="btn btn-sm btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#editBatchModal{{ $batch->id }}">
                                            <i class="fa-solid fa-pen-to-square pe-1"></i>
                                            تعديل
                                        </button>
                                        <a href="{{ route('policies.storage.details', $batch->policy) }}" class="btn btn-sm btn-outline-primary ms-2">
                                            <i class="fa-solid fa-circle-info pe-1"></i>
                                            التفاصيل
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
