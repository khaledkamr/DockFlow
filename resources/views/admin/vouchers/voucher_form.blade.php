<form action="{{ route('admin.create.voucher') }}" method="POST" class="bg-white p-4 rounded-4 mb-5 shadow-sm">
    @csrf
    <div class="row mb-3">
        <div class="col">
            <label for="type" class="form-label">نوع السنــد</label>
            <select id="type" name="type" class="form-select border-primary" style="width:100%;">
                <option value="سند صرف نقدي">سند صرف نقدي</option>
                <option value="سند صرف بشيك">سند صرف بشيك</option>
                <option value="سند قبض نقدي">سند قبض نقدي</option>
                <option value="سند قبض بشيك">سند قبض بشيك</option>
            </select>
        </div>
        <div class="col">
            <label for="date" class="form-label">التاريــخ</label>
            <input type="date" class="form-control border-primary" id="date" name="date" value="{{ Carbon\Carbon::now()->format('Y-m-d') }}">
            @error('date')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
        <div class="col">
            <label for="code" class="form-label">رقم السنــد </label>
            <input type="text" class="form-control border-primary" id="code" name="code" value="">
            @error('code')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
        <div class="col-3">
            <label for="account_name">اسم الحساب</label>
            <select id="account_name" class="form-select border-primary" style="width:100%;">
                <option value="">-- اختر الحساب --</option>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}" data-code="{{ $account->code }}">
                        {{ $account->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col">
            <label for="account_code" class="form-label">رقم الحســاب</label>
            <input type="text" class="form-control border-primary" id="account_code" name="account_code" value="">
            @error('account_code')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
    </div>
    <div class="row mb-3">
        <div class="col-3">
            <label for="amount" class="form-label">المبلــغ</label>
            <input type="text" class="form-control border-primary" id="amount" name="amount" value="">
            @error('amount')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
        <div class="col-9">
            <label for="hatching" class="form-label">التفقيـــط</label>
            <input type="text" class="form-control border-primary" id="hatching" name="hatching" value="">
            @error('hatching')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <label for="description" class="form-label">البيـــان</label>
            <input type="text" class="form-control border-primary" id="description" name="description" value="">
            @error('description')
                <div class="text-danger">{{ $message }}</div>
            @endif
        </div>
        
    </div>
    <button type="submit" class="btn btn-primary fw-bold mt-2">إضافة سند</button>
</form>

