@extends('layouts.app')

@section('title', 'تفاصيل الإشعار')

@section('content')
    @php
        $isCredit = $note->type === 'credit';
        $noteTypeLabel = $isCredit ? 'إشعار دائن' : 'إشعار مدين';
        $badgeClass = $isCredit ? 'bg-danger' : 'bg-success';
        $accentColor = $isCredit ? '#dc3545' : '#198754';
        $accentLight = $isCredit ? 'rgba(220, 53, 69, 0.1)' : 'rgba(25, 135, 84, 0.1)';
        $iconArrow = $isCredit ? 'fa-arrow-down' : 'fa-arrow-up';
        $arrowColor = $isCredit ? 'danger' : 'success';
    @endphp

    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h1 class="mb-2">{{ $noteTypeLabel }}</h1>
                </div>
                <div>
                    @if ($note->is_posted)
                        <span class="badge bg-info ms-2 fs-6">تم الترحيل</span>
                    @else
                        <span class="badge bg-danger ms-2 fs-6">لم يتم الترحيل</span>
                    @endif
                </div>
            </div>

            <!-- Key Information Row -->
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="info-card p-3 rounded bg-light">
                        <small class="text-muted d-block mb-1">رقم الإشعار</small>
                        <h5 class="text-primary fw-bold mb-0">{{ $note->code }}</h5>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-card p-3 rounded bg-light">
                        <small class="text-muted d-block mb-1">التاريخ</small>
                        <h5 class="fw-bold mb-0">{{ \Carbon\Carbon::parse($note->date)->format('Y/m/d') }}</h5>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-card p-3 rounded bg-light">
                        <small class="text-muted d-block mb-1">الفاتورة الأصلية</small>
                        <h5 class="text-primary fw-bold mb-0">
                            <a href="{{ route('invoices.unified.details', $note->invoice) }}" class="text-decoration-none">
                                {{ $note->invoice->code }}
                            </a>
                            <span class="badge status-available">{{ $note->invoice->type }}</span>
                        </h5>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-card p-3 rounded bg-light">
                        <small class="text-muted d-block mb-1">العميل</small>
                        <h5 class="fw-bold mb-0">{{ $note->invoice->customer->name }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Invoice Information Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-0 p-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-receipt me-2 text-primary"></i>تفاصيل الفاتورة الأصلية
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Invoice Amounts Table -->
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted">المبلغ قبل الضريبة</td>
                                    <td class="text-end fw-bold">
                                        {{ number_format($note->invoice->total_amount / (1 + ($note->invoice->tax_rate ?? 15) / 100), 2) }}
                                        ر.س
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">الضريبة ({{ $note->invoice->tax_rate ?? 15 }}%)</td>
                                    <td class="text-end fw-bold text-info">
                                        {{ number_format($note->invoice->total_amount - $note->invoice->total_amount / (1 + ($note->invoice->tax_rate ?? 15) / 100), 2) }}
                                        ر.س
                                    </td>
                                </tr>
                                <tr class="border-top">
                                    <td class="fw-bold">الإجمالي</td>
                                    <td class="text-end fw-bold text-primary">
                                        {{ number_format($note->invoice->total_amount, 2) }} ر.س
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Note Information Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-light border-0 p-3" style="background-color: {{ $accentLight }} !important;">
                    <h5 class="card-title mb-0">
                        <i class="fas {{ $iconArrow }} me-2 text-{{ $arrowColor }}"></i>تفاصيل
                        {{ $noteTypeLabel }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">السبب</small>
                                <p class="fw-bold mb-0">{{ $note->reason }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <small class="text-muted d-block mb-1">أنشئ بواسطة</small>
                                <h6 class="fw-bold mb-0">{{ $note->made_by->name ?? 'غير معروف' }}</h6>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Note Amounts Table -->
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted">مبلغ الإشعار</td>
                                    <td class="text-end fw-bold" style="color: {{ $accentColor }};">
                                        {{ number_format($note->amount, 2) }} ر.س
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">الضريبة ({{ $note->invoice->tax_rate ?? 15 }}%)</td>
                                    <td class="text-end fw-bold text-info">
                                        {{ number_format($note->tax, 2) }} ر.س
                                    </td>
                                </tr>
                                <tr class="border-top">
                                    <td class="fw-bold">الإجمالي</td>
                                    <td class="text-end fw-bold" style="color: {{ $accentColor }}; font-size: 1.1rem;">
                                        {{ number_format($note->total, 2) }} ر.س
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Financial Impact Section -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light border-0 p-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2 text-primary"></i>التأثير المالي
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Before Column -->
                        <div class="col-md-4">
                            <div class="impact-box p-3 bg-light rounded">
                                <h6 class="fw-bold mb-3 text-center text-muted">قبل الإشعار</h6>
                                <div class="impact-item mb-2">
                                    <small class="text-muted d-block">المبلغ</small>
                                    <span class="fw-bold">
                                        {{ number_format($note->invoice->total_amount / (1 + ($note->invoice->tax_rate ?? 15) / 100), 2) }}
                                        ر.س
                                    </span>
                                </div>
                                <div class="impact-item mb-2">
                                    <small class="text-muted d-block">الضريبة</small>
                                    <span class="fw-bold">
                                        {{ number_format($note->invoice->total_amount - $note->invoice->total_amount / (1 + ($note->invoice->tax_rate ?? 15) / 100), 2) }}
                                        ر.س
                                    </span>
                                </div>
                                <div class="impact-item p-2 bg-white rounded border">
                                    <small class="text-muted d-block">الإجمالي</small>
                                    <span class="fw-bold" style="font-size: 1.1rem;">
                                        {{ number_format($note->invoice->total_amount, 2) }} ر.س
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Arrow Column -->
                        <div class="col-md-4 d-flex align-items-center justify-content-center">
                            <div class="text-center">
                                <i class="fas {{ $iconArrow }} fa-2x mb-2" style="color: {{ $accentColor }};"></i>
                                <div class="impact-box p-3 bg-white rounded border"
                                    style="border-color: {{ $accentColor }} !important; border-width: 2px;">
                                    <h6 class="text-muted d-block mb-2">التعديل</h6>
                                    <div class="mb-2">
                                        <small class="text-muted d-block">المبلغ</small>
                                        <span class="fw-bold">
                                            {{ number_format($note->amount, 2) }} ر.س
                                        </span>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted d-block">الضريبة</small>
                                        <span class="fw-bold">
                                            {{ number_format($note->tax, 2) }} ر.س
                                        </span>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">الإجمالي</small>
                                        <span class="fw-bold" style="color: {{ $accentColor }};">
                                            {{ number_format($note->total, 2) }} ر.س
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- After Column -->
                        <div class="col-md-4">
                            <div class="impact-box p-3 bg-light rounded">
                                <h6 class="fw-bold mb-3 text-center text-muted">بعد الإشعار</h6>
                                @php
                                    $beforeAmount = $note->invoice->total_amount / (1 + ($note->invoice->tax_rate ?? 15) / 100);
                                    $beforeTax = $note->invoice->total_amount - $beforeAmount;

                                    $afterAmount = $isCredit
                                        ? $beforeAmount - $note->amount
                                        : $beforeAmount + $note->amount;
                                    $afterTax = $isCredit ? $beforeTax - $note->tax : $beforeTax + $note->tax;
                                    $afterTotal = $afterAmount + $afterTax;
                                @endphp
                                <div class="impact-item mb-2">
                                    <small class="text-muted d-block">المبلغ</small>
                                    <span class="fw-bold">
                                        {{ number_format($afterAmount, 2) }} ر.س
                                    </span>
                                </div>
                                <div class="impact-item mb-2">
                                    <small class="text-muted d-block">الضريبة</small>
                                    <span class="fw-bold">
                                        {{ number_format($afterTax, 2) }} ر.س
                                    </span>
                                </div>
                                <div class="impact-item p-2 bg-white rounded border">
                                    <small class="text-muted d-block">الإجمالي</small>
                                    <span class="fw-bold" style="color: {{ $accentColor }}; font-size: 1.1rem;">
                                        {{ number_format($afterTotal, 2) }} ر.س
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Impact Alert -->
                    <div class="mt-4">
                        @if ($isCredit)
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-arrow-down me-2"></i>
                                <strong>إشعار دائن:</strong> تم تقليل المبلغ المستحق من الفاتورة بمقدار
                                <strong>{{ number_format($note->total, 2) }} ر.س</strong>
                            </div>
                        @else
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-arrow-up me-2"></i>
                                <strong>إشعار مدين:</strong> تم زيادة المبلغ المستحق على الفاتورة بمقدار
                                <strong>{{ number_format($note->total, 2) }} ر.س</strong>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Sidebar -->
        <div class="col-lg-4">
            <!-- Action Buttons -->
            <div class="card border-0 shadow-sm sticky-top" style="top: 20px;">
                <div class="card-header bg-light border-0 p-4">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog me-2 text-primary"></i>الإجراءات
                    </h5>
                </div>
                <div class="card-body d-grid gap-2">
                    <button type="button" class="btn btn-primary" title="طباعة الإشعار">
                        <i class="fas fa-print me-2"></i>طباعة الاشعار
                    </button>
                    <a href="{{ route('invoices.notes.update', $note) }}" class="btn btn-outline-primary" title="تعديل الإشعار">
                        <i class="fas fa-share me-2"></i>ترحيل الاشعار الى قيد
                    </a>
                    <a href="{{ route('invoices.notes.update', $note) }}" class="btn btn-outline-primary" title="تعديل الإشعار">
                        <i class="fas fa-edit me-2"></i>تعديل الاشعار
                    </a>
                    <a href="{{ route('invoices.notes') }}" class="btn btn-outline-primary" title="العودة للإشعارات">
                        <i class="fas fa-arrow-right me-2"></i>العودة الى الاشعارات
                    </a>
                    <button type="button" class="btn btn-outline-danger" title="حذف الإشعار">
                        <i class="fas fa-trash me-2"></i>حذف الاشعار
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        :root {
            --primary-color: #007bff;
            --primary-hover: #0056b3;
        }

        body {
            direction: rtl;
            text-align: right;
        }

        .info-card {
            border-right: 4px solid var(--primary-color);
            transition: all 0.3s ease;
        }

        .info-card:hover {
            box-shadow: 0 0.25rem 0.75rem rgba(0, 123, 255, 0.1);
        }

        .card {
            transition: all 0.3s ease;
            border-color: transparent !important;
        }

        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 123, 255, 0.15) !important;
        }

        .table-responsive {
            border-radius: 0.375rem;
        }

        .impact-box {
            transition: all 0.3s ease;
        }

        .impact-item {
            padding: 0.75rem 0;
        }

        .metadata-item small {
            font-size: 0.8rem;
            font-weight: 500;
        }

        .metadata-item h6 {
            font-size: 0.95rem;
        }

        .btn {
            transition: all 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .sticky-top {
            position: sticky;
            top: 20px;
            z-index: 10;
        }

        .invoice-link-box {
            border-left: 4px solid var(--primary-color);
        }

        /* Responsive adjustments */
        @media (max-width: 991.98px) {
            .sticky-top {
                position: static;
                margin-top: 2rem;
            }

            h1.h2 {
                font-size: 1.5rem;
            }

            .row.g-3>.col-md-4 {
                margin-bottom: 1rem;
            }
        }

        @media (max-width: 576px) {
            .info-card {
                margin-bottom: 0.5rem;
            }

            .table {
                font-size: 0.875rem;
            }

            .btn {
                font-size: 0.875rem;
            }

            .card-body {
                padding: 1rem;
            }

            .d-grid {
                gap: 0.5rem !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Print functionality
            document.querySelector('button[title="طباعة الإشعار"]')?.addEventListener('click', function() {
                window.print();
            });

            // PDF Download (placeholder - would need backend implementation)
            document.querySelector('button[title="تحميل الإشعار"]')?.addEventListener('click', function() {
                // Implementation for PDF download would go here
                alert('سيتم تحميل الإشعار كملف PDF');
            });

            // Delete functionality
            document.querySelector('button[title="حذف الإشعار"]')?.addEventListener('click', function() {
                if (confirm('هل أنت متأكد من حذف هذا الإشعار؟')) {
                    // Delete implementation would go here
                    alert('سيتم حذف الإشعار');
                }
            });
        });
    </script>
@endsection
