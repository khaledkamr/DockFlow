@extends('layouts.app')

@section('title', 'تفاصيل فاتورة ZATCA')

@section('content')
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 mb-0">
            <i class="bi bi-file-earmark-check"></i> تفاصيل فاتورة ZATCA للفاتورة رقم: {{ $zatcaInvoice->invoice->code ?? 'N/A' }}
        </h1>
        
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-2">حالة الفاتورة</p>
                            @php
                                $statusBadgeClass = match ($zatcaInvoice->status) {
                                    'CLEARED' => 'bg-success',
                                    'NOT_CLEARED' => 'bg-danger',
                                    'pending' => 'bg-warning',
                                    default => 'bg-secondary',
                                };
                                $statusText = $zatcaInvoice->status ?? 'N/A';
                            @endphp
                            <h5 class="card-title mb-0">
                                <span class="badge {{ $statusBadgeClass }}">{{ $statusText }}</span>
                            </h5>
                        </div>
                        <i class="fas fa-info text-primary fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-2">تاريخ الإصدار</p>
                            @if ($zatcaInvoice->issue_date)
                                <h5 class="card-title mb-0">
                                    {{ \Carbon\Carbon::parse($zatcaInvoice->issue_date)->format('Y-m-d') ?? 'N/A' }}
                                </h5>
                            @else
                                <h5 class="card-title mb-0">N/A</h5>
                            @endif
                        </div>
                        <i class="bi bi-calendar-event text-primary fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-2">المبلغ قبل الضريبة</p>
                            <h5 class="card-title mb-0">{{ number_format($zatcaInvoice->invoice_amount, 2) ?? 'N/A' }}</h5>
                        </div>
                        <i class="bi bi-receipt text-info fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-2">مبلغ الضريبة</p>
                            <h5 class="card-title mb-0">{{ number_format($zatcaInvoice->invoice_vat_amount, 2) ?? 'N/A' }}
                            </h5>
                        </div>
                        <i class="bi bi-percent text-warning fs-5"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-2">إجمالي المبلغ</p>
                            <h5 class="card-title mb-0">{{ number_format($zatcaInvoice->invoice_total, 2) ?? 'N/A' }}</h5>
                        </div>
                        <i class="bi bi-cash-coin text-success fs-5"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ZATCA Response Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-exclamation-circle"></i> رسائل استجابة ZATCA
                    </h5>
                    <span class="badge bg-info ms-2">
                        {{ $responseLog['validationResults']['status'] ?? 'N/A' }}
                    </span>
                </div>
                <div class="card-body">
                    @if ($responseLog && is_array($responseLog))
                        <!-- Error Messages (Red) -->
                        @if (
                            !empty($responseLog['validationResults']['errorMessages']) &&
                                is_array($responseLog['validationResults']['errorMessages']))
                            <div class="mb-3">
                                <h6 class="text-danger mb-3">
                                    <i class="bi bi-x-circle-fill"></i> رسائل الخطأ
                                </h6>
                                @foreach ($responseLog['validationResults']['errorMessages'] as $error)
                                    <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                        <strong>{{ $error['code'] ?? 'Error' }}:</strong>
                                        {{ $error['message'] ?? $error }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Warning Messages (Yellow) -->
                        @if (
                            !empty($responseLog['validationResults']['warningMessages']) &&
                                is_array($responseLog['validationResults']['warningMessages']))
                            <div class="mb-3">
                                <h6 class="text-warning mb-3">
                                    <i class="bi bi-exclamation-triangle-fill"></i> رسائل التحذير
                                </h6>
                                @foreach ($responseLog['validationResults']['warningMessages'] as $warning)
                                    <div class="alert alert-warning alert-dismissible fade show mb-2" role="alert">
                                        <strong>{{ $warning['code'] ?? 'Warning' }}:</strong>
                                        {{ $warning['message'] ?? $warning }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Info Messages (Blue) -->
                        @if (
                            !empty($responseLog['validationResults']['infoMessages']) &&
                                is_array($responseLog['validationResults']['infoMessages']))
                            <div class="mb-3">
                                <h6 class="text-info mb-3">
                                    <i class="bi bi-info-circle-fill"></i> رسائل معلومات
                                </h6>
                                @foreach ($responseLog['validationResults']['infoMessages'] as $info)
                                    <div class="alert alert-info alert-dismissible fade show mb-2" role="alert">
                                        <strong>{{ $info['code'] ?? 'Info' }}:</strong>
                                        {{ $info['message'] ?? $info }}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if (empty($responseLog['validationResults']['errorMessages']) &&
                                empty($responseLog['validationResults']['warningMessages']) &&
                                empty($responseLog['validationResults']['infoMessages']))
                            <div class="alert alert-info mb-0">
                                <i class="bi bi-info-circle"></i> لا توجد رسائل محددة من ZATCA
                            </div>
                        @endif
                    @else
                        <div class="alert alert-secondary mb-0">
                            <i class="bi bi-dash-circle"></i> بيانات الاستجابة غير متوفرة
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- XML Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-code"></i> بيانات XML
                    </h5>
                    <div class="btn-group btn-group-sm" role="group">
                        <input type="radio" class="btn-check" name="xmlViewMode" id="xmlViewRaw" value="raw" checked>
                        <label class="btn btn-outline-secondary" for="xmlViewRaw">
                            <i class="bi bi-file-code"></i> خام
                        </label>

                        <input type="radio" class="btn-check" name="xmlViewMode" id="xmlViewPretty" value="pretty">
                        <label class="btn btn-outline-secondary" for="xmlViewPretty">
                            <i class="bi bi-file-text"></i> منسق
                        </label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="btn-group w-100" role="group">
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                onclick="copyToClipboard('xmlContent', this)">
                                <i class="bi bi-clipboard"></i> نسخ XML
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="downloadXML()">
                                <i class="bi bi-download"></i> تحميل XML
                            </button>
                        </div>
                    </div>

                    <div class="position-relative"
                        style="max-height: 600px; overflow-y: auto; background: #f8f9fa; border-radius: 0.25rem;">
                        <pre id="xmlContent" dir="ltr"
                            style="margin: 0; padding: 1rem; font-size: 0.875rem; white-space: pre-wrap; word-wrap: break-word; direction: ltr; text-align: left;">{{ $zatcaInvoice->request_xml ?? 'N/A' }}</pre>
                    </div>

                    <small class="text-muted d-block mt-2">
                        <i class="bi bi-info-circle"></i> استخدم أزرار التبديل أعلاه للتنقل بين طرق العرض المختلفة
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Section -->
    {{-- <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-qr-code"></i> بيانات رمز الاستجابة السريعة (QR)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">نص البيانات</label>
                            <div class="input-group">
                                <textarea class="form-control" id="qrData" rows="6" readonly>{{ $zatcaInvoice->qr_data ?? 'N/A' }}</textarea>
                                <button class="btn btn-outline-primary" type="button"
                                    onclick="copyToClipboard('qrData', this)">
                                    <i class="bi bi-clipboard"></i> نسخ
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">معاينة رمز QR</label>
                            <div class="p-4 bg-light rounded d-flex align-items-center justify-content-center"
                                style="height: 280px;">
                                @if ($zatcaInvoice->qr_data)
                                    <div id="qrCodeContainer">
                                        <p class="text-muted text-center mb-0">
                                            <i class="bi bi-qr-code fs-1"></i><br>
                                            <small>يتم عرض رمز QR هنا</small>
                                        </p>
                                    </div>
                                @else
                                    <p class="text-muted text-center mb-0">
                                        <i class="bi bi-question-circle fs-1"></i><br>
                                        <small>بيانات QR غير متوفرة</small>
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}

    <!-- Technical Metadata Section (Collapsible) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <button class="btn btn-link text-decoration-none w-100 text-start" type="button"
                        data-bs-toggle="collapse" data-bs-target="#technicalMetadata">
                        <h5 class="mb-0">
                            <i class="bi bi-gear"></i> البيانات الفنية
                            <small class="float-end text-muted">(انقر للتوسيع)</small>
                        </h5>
                    </button>
                </div>
                <div id="technicalMetadata" class="collapse">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><small class="text-muted">معرف الفاتورة
                                        (UUID)</small></label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control font-monospace"
                                        value="{{ $zatcaInvoice->invoice_uuid ?? 'N/A' }}" readonly>
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="copyToClipboard(this.previousElementSibling, this)">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label"><small class="text-muted">بصمة الفاتورة
                                        (Hash)</small></label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control font-monospace"
                                        value="{{ $zatcaInvoice->invoice_hash ?? 'N/A' }}" readonly>
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="copyToClipboard(this.previousElementSibling, this)">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label"><small class="text-muted">البصمة السابقة (Pre
                                        Hash)</small></label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control font-monospace"
                                        value="{{ $zatcaInvoice->pre_hash ?? 'N/A' }}" readonly>
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="copyToClipboard(this.previousElementSibling, this)">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label"><small class="text-muted">XML المشفر</small></label>
                                <div class="input-group input-group-sm">
                                    <textarea class="form-control font-monospace" rows="4" readonly>{{ $zatcaInvoice->encoded_xml ?? 'N/A' }}</textarea>
                                    <button class="btn btn-outline-secondary" type="button"
                                        onclick="copyToClipboard(this.previousElementSibling, this)">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6 mb-0">
                                <label class="form-label"><small class="text-muted">تاريخ الإنشاء</small></label>
                                <input type="text" class="form-control form-control-sm"
                                    value="{{ $zatcaInvoice->created_at ?? 'N/A' }}" readonly>
                            </div>

                            <div class="col-md-6 mb-0">
                                <label class="form-label"><small class="text-muted">آخر تحديث</small></label>
                                <input type="text" class="form-control form-control-sm"
                                    value="{{ $zatcaInvoice->updated_at ?? 'N/A' }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Styles -->
    <style>
        .card {
            transition: box-shadow 0.3s ease, transform 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
            transform: translateY(-2px);
        }

        .btn-group-sm .btn {
            font-size: 0.875rem;
        }

        pre {
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', 'Courier New', monospace;
            letter-spacing: 0.5px;
        }

        .alert {
            border-left: 4px solid;
        }

        .alert-danger {
            border-left-color: var(--bs-danger);
        }

        .alert-warning {
            border-left-color: var(--bs-warning);
        }

        .alert-info {
            border-left-color: var(--bs-info);
        }

        .btn-check:checked+.btn {
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .card-header {
                flex-wrap: wrap;
            }

            .card-header h5 {
                width: 100%;
                margin-bottom: 0.5rem;
            }

            .btn-group {
                width: 100% !important;
            }
        }
    </style>

    <!-- Scripts -->
    <script>
        // Copy to Clipboard Function
        function copyToClipboard(elementOrId, buttonElement) {
            let text = '';

            if (typeof elementOrId === 'string') {
                // ID was passed
                const element = document.getElementById(elementOrId);
                text = element?.innerText || element?.value || '';
            } else {
                // Element was passed
                text = elementOrId.value || elementOrId.innerText || '';
            }

            if (!text) {
                alert('لا يوجد نص للنسخ');
                return;
            }

            navigator.clipboard.writeText(text).then(() => {
                const originalContent = buttonElement.innerHTML;
                buttonElement.innerHTML = '<i class="bi bi-check-circle"></i> تم النسخ!';
                buttonElement.classList.add('btn-success');
                buttonElement.classList.remove('btn-outline-primary', 'btn-outline-secondary');

                setTimeout(() => {
                    buttonElement.innerHTML = originalContent;
                    buttonElement.classList.remove('btn-success');
                    buttonElement.classList.add('btn-outline-secondary');
                }, 2000);
            }).catch(err => {
                console.error('Failed to copy:', err);
                alert('فشل نسخ النص');
            });
        }

        // XML View Mode Toggle
        document.querySelectorAll('input[name="xmlViewMode"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const xmlContent = document.getElementById('xmlContent');
                const rawXml = `{{ $zatcaInvoice->request_xml ?? '' }}`;

                if (this.value === 'pretty') {
                    try {
                        const formatted = formatXml(rawXml);
                        xmlContent.innerText = formatted;
                    } catch (e) {
                        xmlContent.innerText = rawXml;
                    }
                } else {
                    xmlContent.innerText = rawXml;
                }
            });
        });

        // Simple XML Formatter
        function formatXml(xml) {
            if (!xml) return xml;

            let formatted = '';
            let indent = 0;
            const tab = '  ';

            xml.split(/(<[^>]+>)/g).forEach(token => {
                if (token.match(/^<\/\w/)) {
                    indent--;
                }

                if (token.trim()) {
                    if (!token.startsWith('<')) {
                        formatted += tab.repeat(indent) + token.trim() + '\n';
                    } else {
                        formatted += tab.repeat(indent) + token + '\n';
                    }
                }

                if (token.match(/^<\w[^>]*[^\/]>$/)) {
                    indent++;
                }
            });

            return formatted;
        }

        // Download XML
        function downloadXML() {
            const xml = document.getElementById('xmlContent').innerText;
            const blob = new Blob([xml], {
                type: 'application/xml'
            });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'invoice-{{ $zatcaInvoice->invoice_uuid ?? 'export' }}.xml';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // You can add QR code generation here if needed
            // Example: Using QRCode library if available
        });
    </script>
@endsection
