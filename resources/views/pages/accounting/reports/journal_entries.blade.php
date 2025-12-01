<style>
    .table-container {
        max-height: 650px; /* Adjust height as needed */
        overflow-y: auto;
        border: 1px solid #dee2e6;
    }
    
    .table thead th {
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .table-container::-webkit-scrollbar {
        width: 5px;
        height: 0; 
    }
</style>

<form method="GET" action="" class="row g-3 bg-white p-3 rounded-3 shadow-sm border-0 mb-4">
    <input type="hidden" name="view" value="تقارير القيود">
    <div class="col-md-3">
        <label class="form-label">نوع القيد</label>
        <select name="journal_type" class="form-select border-primary">
            <option value="all" {{ request()->query('journal_type') === 'all' || !request()->query('journal_type') ? 'selected' : '' }}>
                جميع القيود
            </option>
            <option value="all_journals" {{ request()->query('journal_type') === 'all_journals' ? 'selected' : '' }}>
                القيود اليومية
            </option>
            <option value="all_receipts" {{ request()->query('journal_type') === 'all_receipts' ? 'selected' : '' }}>
                جميع سندات القبض
            </option>
            <option value="all_payments" {{ request()->query('journal_type') === 'all_payments' ? 'selected' : '' }}>
                جميع سندات الصرف
            </option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">من تاريخ</label>
        <input type="date" name="from" class="form-control border-primary" value="{{ request('from', now()->startOfYear()->format('Y-m-d')) }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">إلى تاريخ</label>
        <input type="date" name="to" class="form-control border-primary" value="{{ request('to', now()->format('Y-m-d')) }}">
    </div>
    <div class="col-md-3 d-flex align-items-end">
        <button type="submit" class="btn btn-primary fw-bold w-100" onclick="this.querySelector('i').className='fas fa-spinner fa-spin ms-1'">
            عرض التقرير
            <i class="fa-solid fa-eye ms-1"></i>
        </button>
    </div>
</form>

<div class="bg-white p-3 rounded-3 shadow-sm border-0">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <div></div>
        <div class="export-buttons d-flex gap-2 align-items-center">
            <form action="{{ route('export.excel', 'journal_entries') }}" method="GET">
                <input type="hidden" name="type" value="{{ request()->query('type') }}">
                <input type="hidden" name="from" value="{{ request()->query('from') }}">
                <input type="hidden" name="to" value="{{ request()->query('to') }}">
                <button class="btn btn-outline-success" data-bs-toggle="tooltip" data-bs-placement="top" title="تصدير Excel">
                    <i class="fa-solid fa-file-excel"></i>
                </button>
            </form>

            <button class="btn btn-outline-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="تصدير PDF">
                <i class="fa-solid fa-file-pdf"></i>
            </button>
            
            <form action="{{ route('print', 'journal_entries') }}" method="POST" target="_blank">
                @csrf
                <input type="hidden" name="type" value="{{ request()->query('type') }}">
                <input type="hidden" name="from" value="{{ request()->query('from') }}">
                <input type="hidden" name="to" value="{{ request()->query('to') }}">
                <button type="submit" class="btn btn-outline-primary" target="top" data-bs-toggle="tooltip" data-bs-placement="top" title="طباعة">
                    <i class="fa-solid fa-print"></i>
                </button>
            </form>
        </div>
    </div>
    <div class="table-container">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th class="text-center bg-dark text-white">رقم القيد</th>
                    <th class="text-center bg-dark text-white">سطر</th>
                    <th class="text-center bg-dark text-white">رقم الحساب</th>
                    <th class="text-center bg-dark text-white">اسم الحساب</th>
                    <th class="text-center bg-dark text-white">البيان</th>
                    <th class="text-center bg-dark text-white">مدين</th>
                    <th class="text-center bg-dark text-white">دائن</th>
                </tr>
            </thead>
            <tbody class="text-center">
                @php
                    $totalEntriesDebit = 0;
                    $totalEntriesCredit = 0;
                @endphp
                @forelse($entries as $entry)
                    <tr>
                        <td colspan="7" class="text-start table-secondary fw-bold">
                            <a href="{{ route('journal.details', $entry) }}" class="text-decoration-none">
                                قيد - {{ $entry->voucher->type ?? 'قيد يومي' }} - بتاريخ {{ $entry->date }}
                            </a>
                        </td>
                    </tr>
                    @foreach($entry->lines as $index => $line)
                        <tr>
                            <td>{{ $line->journal->code }}</td>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $line->account->code }}</td>
                            <td>{{ $line->account->name }}</td>
                            <td>{{ $line->description ?? '-' }}</td>
                            <td>{{ number_format($line->debit, 2) }}</td>
                            <td>{{ number_format($line->credit, 2) }}</td>
                        </tr>
                    @endforeach
                    <tr class="table-secondary fw-bold">
                        <td colspan="4"></td>
                        <td>إجمالي</td>
                        <td>{{ number_format($entry->totalDebit, 2) }}</td>
                        <td>{{ number_format($entry->totalCredit, 2) }}</td>
                    </tr>
                    <tr><td colspan="7" class="p-1"></td></tr>
                    @php
                        $totalEntriesDebit += $entry->totalDebit;
                        $totalEntriesCredit += $entry->totalCredit;
                    @endphp
                @empty
                    <tr>
                        <td colspan="7" class="text-center">
                            <div class="status-danger fs-6">لم يتم العثور على أي قيود</div>
                        </td>
                    </tr>
                @endforelse
                <tr>
                    <tr class="table-primary fw-bold">
                        <td colspan="4"></td>
                        <td class="fs-6">إجمالي القيود</td>
                        <td class="fs-6">{{ number_format($totalEntriesDebit, 2) }}</td>
                        <td class="fs-6">{{ number_format($totalEntriesCredit, 2) }}</td>
                    </tr>
                </tr>
            </tbody>
        </table>
    </div>
</div>

