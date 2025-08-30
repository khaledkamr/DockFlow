<form method="GET" action="" class="row g-3 bg-white p-3 rounded-3 shadow-sm border-0 mb-4">
    <input type="hidden" name="view" value="كشف حساب">
    <div class="col-md-4">
        <label class="form-label">الحساب</label>
        <select name="account" class="form-select border-primary" required>
            <option value="">اختر الحساب</option>
            @foreach($accounts as $account)
                <option value="{{ $account->id }}" {{ request('account') == $account->id ? 'selected' : '' }}>
                    {{ $account->name }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">من تاريخ</label>
        <input type="date" name="from" class="form-control border-primary" value="{{ request('from') }}">
    </div>
    <div class="col-md-3">
        <label class="form-label">إلى تاريخ</label>
        <input type="date" name="to" class="form-control border-primary" value="{{ request('to') }}">
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-primary fw-bold w-100">عرض التقرير</button>
    </div>
</form>

<div class="bg-white p-3 rounded-3 shadow-sm border-0">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h5>الرصيد الافتتاحي: <strong>{{ $opening_balance ?? 0.00 }}</strong></h5>
        <div>
            <button class="btn btn-outline-success" data-bs-toggle="tooltip" data-bs-placement="top" title="تصدير Excel">
                <i class="fa-solid fa-file-excel"></i>
            </button>

            <button class="btn btn-outline-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="تصدير PDF">
                <i class="fa-solid fa-file-pdf"></i>
            </button>

            <button class="btn btn-outline-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="طباعة">
                <i class="fa-solid fa-print"></i>
            </button>
        </div>
    </div>
    <div class="table-container">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="bg-dark text-center text-white">التاريخ</th>
                    <th class="bg-dark text-center text-white">رقم القيد</th>
                    <th class="bg-dark text-center text-white">البيان</th>
                    <th class="bg-dark text-center text-white">مدين</th>
                    <th class="bg-dark text-center text-white">دائن</th>
                    <th class="bg-dark text-center text-white">الرصيد</th>
                </tr>
            </thead>
            <tbody>
                @forelse($statement as $row)
                    <tr class="text-center">
                        <td>{{ $row->date }}</td>
                        <td>{{ $row->entry_id }}</td>
                        <td>{{ $row->description }}</td>
                        <td>{{ $row->debit }}</td>
                        <td>{{ $row->credit }}</td>
                        <td>{{ $row->balance }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            <div class="status-danger fs-6">لا توجد حركات</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <h5 class="mt-4">الرصيد الختامي: <strong>{{ $closing_balance ?? 0.00 }}</strong></h5>
</div>
