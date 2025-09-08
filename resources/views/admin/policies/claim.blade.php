@extends('layouts.admin')

@section('title', 'مطالبة فواتير')

@section('content')
<div class="container">
    <h1 class="mb-4">مطالبة فواتير</h1>

    <form method="GET" action="" class="mb-4">
        <div class="row g-2">
            <div class="col-md-6">
                <select name="customer_id" class="form-select border-primary" required>
                    <option value="">-- اختر العميل --</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary fw-bold">عرض الفواتير</button>
            </div>
        </div>
    </form>

    @if(isset($invoices) && $invoices->count() > 0)
    <form method="POST" action="{{ route('invoices.claim.store') }}">
        @csrf
        <input type="hidden" name="customer_id" value="{{ request('customer_id') }}">
        
        <!-- Selected invoices counter -->
        <div class="alert alert-info mb-3" id="selection-counter" style="display: none;">
            <i class="fas fa-info-circle"></i>
            تم تحديد <span id="selected-count">0</span> فاتورة من أصل {{ $invoices->count() }} فاتورة
        </div>

        <div class="table-container">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="text-center bg-dark text-white" width="10%">
                            <button type="button" id="select-all-btn" class="btn btn-sm btn-primary fw-bold">
                                <i class="fas fa-check-square me-1"></i>
                                تحديد الكل
                            </button>
                        </th>
                        <th class="text-center fw-bold bg-dark text-white">#</th>
                        <th class="text-center fw-bold bg-dark text-white">رقم الفاتورة</th>
                        <th class="text-center fw-bold bg-dark text-white">إسم العميل</th>
                        <th class="text-center fw-bold bg-dark text-white">التاريخ</th>
                        <th class="text-center fw-bold bg-dark text-white">المبلغ</th>
                        <th class="text-center fw-bold bg-dark text-white">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                    <tr class="text-center invoice-row" data-invoice-id="{{ $invoice->id }}">
                        <td class="checkbox-cell" style="cursor: pointer;">
                            <div class="form-check d-flex justify-content-center">
                                <input type="checkbox" 
                                       name="invoice_ids[]" 
                                       value="{{ $invoice->id }}" 
                                       class="form-check-input invoice-checkbox"
                                       style="transform: scale(1.2);">
                            </div>
                        </td>
                        <td class="fw-bold">{{ $loop->iteration }}</td>
                        <td class="text-primary fw-bold">
                            <a href="{{ route('invoices.details', $invoice->code) }}" class="text-decoration-none">
                                {{ $invoice->code }}
                            </a>
                        </td>
                        <td class="fw-bold">
                            <a href="{{ route('users.customer.profile', $invoice->customer->id) }}" class="text-decoration-none text-dark">
                                {{ $invoice->customer->name }}
                            </a>
                        </td>
                        <td>{{ $invoice->date }}</td>
                        <td class="fw-bold">{{ number_format($invoice->amount, 2) }}</td>
                        <td><span class="badge bg-danger">غير مدفوعة</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <button type="submit" class="btn btn-primary fw-bold" id="create-claim-btn" disabled>
                <i class="fas fa-plus me-1"></i>
                إنشاء مطالبة
            </button>
            <button type="button" class="btn btn-outline-danger fw-bold" id="clear-selection-btn" style="display: none;">
                <i class="fas fa-times me-1"></i>
                إلغاء التحديد
            </button>
        </div>
    </form>
    @elseif(request('customer_id'))
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            لا توجد فواتير غير مدفوعة لهذا العميل.
        </div>
    @endif
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllBtn = document.getElementById('select-all-btn');
    const clearSelectionBtn = document.getElementById('clear-selection-btn');
    const createClaimBtn = document.getElementById('create-claim-btn');
    const selectionCounter = document.getElementById('selection-counter');
    const selectedCountSpan = document.getElementById('selected-count');
    const checkboxes = document.querySelectorAll('.invoice-checkbox');
    const rows = document.querySelectorAll('.invoice-row');
    const checkboxCells = document.querySelectorAll('.checkbox-cell');
    
    let allSelected = false;

    // Function to update UI based on selection
    function updateUI() {
        const selectedCheckboxes = document.querySelectorAll('.invoice-checkbox:checked');
        const selectedCount = selectedCheckboxes.length;
        const totalCount = checkboxes.length;
        
        // Update counter
        selectedCountSpan.textContent = selectedCount;
        
        // Show/hide elements based on selection
        if (selectedCount > 0) {
            selectionCounter.style.display = 'block';
            createClaimBtn.disabled = false;
            clearSelectionBtn.style.display = 'inline-block';
        } else {
            selectionCounter.style.display = 'none';
            createClaimBtn.disabled = true;
            clearSelectionBtn.style.display = 'none';
        }
        
        // Update select all button
        if (selectedCount === totalCount) {
            selectAllBtn.innerHTML = 'إلغاء الكل';
            selectAllBtn.classList.remove('btn-primary');
            selectAllBtn.classList.add('btn-warning');
            allSelected = true;
        } else {
            selectAllBtn.innerHTML = 'تحديد الكل';
            selectAllBtn.classList.remove('btn-warning');
            selectAllBtn.classList.add('btn-primary');
            allSelected = false;
        }
        
        // Update row styling
        rows.forEach(row => {
            const checkbox = row.querySelector('.invoice-checkbox');
            if (checkbox.checked) {
                row.classList.add('table-primary');
                row.style.transform = 'scale(1.01)';
                row.style.boxShadow = '0 2px 4px rgba(0,123,255,0.3)';
            } else {
                row.classList.remove('table-primary');
                row.style.transform = 'scale(1)';
                row.style.boxShadow = 'none';
            }
        });
    }

    // Select/Deselect all functionality
    selectAllBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        if (allSelected) {
            // Deselect all
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
        } else {
            // Select all
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
        }
        
        updateUI();
        
        // Add animation effect
        rows.forEach((row, index) => {
            setTimeout(() => {
                row.style.transition = 'all 0.2s ease';
                row.classList.add('animate__animated', 'animate__pulse');
                setTimeout(() => {
                    row.classList.remove('animate__animated', 'animate__pulse');
                }, 200);
            }, index * 50);
        });
    });

    // Clear selection functionality
    clearSelectionBtn.addEventListener('click', function() {
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        updateUI();
    });

    // Click on checkbox cell to toggle checkbox
    checkboxCells.forEach(cell => {
        cell.addEventListener('click', function(e) {
            if (e.target.type !== 'checkbox') {
                const checkbox = this.querySelector('.invoice-checkbox');
                checkbox.checked = !checkbox.checked;
                updateUI();
                
                // Add click animation
                const row = this.closest('.invoice-row');
                row.style.transition = 'all 0.2s ease';
                row.classList.add('animate__animated', 'animate__pulse');
                setTimeout(() => {
                    row.classList.remove('animate__animated', 'animate__pulse');
                }, 200);
            }
        });
    });

    // Individual checkbox change
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateUI();
        });
    });

    // Add hover effects
    rows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            if (!this.classList.contains('table-primary')) {
                this.style.backgroundColor = '#f8f9fa';
            }
        });
        
        row.addEventListener('mouseleave', function() {
            if (!this.classList.contains('table-primary')) {
                this.style.backgroundColor = '';
            }
        });
    });

    // Initial UI update
    updateUI();
});
</script>
@endpush

@push('styles')
<style>
.invoice-row {
    transition: all 0.3s ease;
    cursor: pointer;
}

.invoice-row.table-primary {
    background-color: rgba(13, 110, 253, 0.1) !important;
    border-left: 4px solid #0d6efd;
}

.checkbox-cell {
    position: relative;
}

.checkbox-cell:hover {
    background-color: rgba(13, 110, 253, 0.05);
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn {
    transition: all 0.2s ease;
}

.btn:hover {
    transform: translateY(-1px);
}

#create-claim-btn:disabled {
    transform: none !important;
}

.table-container {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.alert {
    border-radius: 8px;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

/* Animation classes if you want to add animate.css */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.02); }
    100% { transform: scale(1); }
}

.animate__pulse {
    animation: pulse 0.2s ease-in-out;
}
</style>
@endpush

@endsection