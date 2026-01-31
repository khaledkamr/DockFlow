@extends('layouts.app')

@section('title', 'تقرير نشاط المستخدمين')

@section('content')
<h1 class="mb-4">تقرير نشاط المستخدمين</h1>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="" method="GET">
            <div class="row g-3">
                <div class="col-6 col-md-3">
                    <label class="form-label fw-semibold">المستخدم</label>
                    <select name="user_id" class="form-select border-primary">
                        <option value="">جميع المستخدمين</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label fw-semibold">الإجراء</label>
                    <select name="action" id="action" class="form-select border-primary">
                        <option value="">جميع الإجراءات</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                {{ $action }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label fw-semibold">من تاريخ</label>
                    <input type="date" name="from" class="form-control border-primary" value="{{ request('from', now()->format('Y-m-d')) }}">
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label fw-semibold">إلى تاريخ</label>
                    <input type="date" name="to" class="form-control border-primary" value="{{ request('to', now()->format('Y-m-d')) }}">
                </div>

                <div class="col-12 mt-3">
                    <button type="submit" class="btn btn-primary fw-bold px-4">
                        عرض التقرير
                        <i class="fas fa-filter ms-1"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3 p-3 mb-5">
    <div class="d-flex flex-row justify-content-between align-items-start align-items-sm-center gap-2 mb-3">
        <div>
            <form method="GET" action="">
                <label for="per_page" class="fw-semibold">عدد الصفوف:</label>
                <select id="per_page" name="per_page" onchange="this.form.submit()"
                    class="form-select form-select-sm d-inline-block w-auto">
                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    <option value="300" {{ $perPage == 300 ? 'selected' : '' }}>300</option>
                    <option value="500" {{ $perPage == 500 ? 'selected' : '' }}>500</option>
                    <option value="1000" {{ $perPage == 1000 ? 'selected' : '' }}>1000</option>
                </select>
            </form>
        </div>
        
        <div class="d-flex gap-2">
            <form method="GET" action="{{ route('export.excel', 'user_activity') }}">
                @foreach(request()->except('per_page') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <button type="submit" class="btn btn-outline-success" data-bs-toggle="tooltip"
                    data-bs-placement="top" title="تصدير Excel">
                    <i class="fa-solid fa-file-excel"></i>
                </button>
            </form>
            <form action="{{ route('print.user.activity.reports') }}" method="GET" target="_blank">
                @foreach (request()->except('page', 'per_page') as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <button type="submit" class="btn btn-outline-primary" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="طباعة">
                    <i class="fa-solid fa-print"></i>
                </button>
            </form>
        </div>
    </div>
    <div class="table-container" id="tableContainer">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="text-center bg-dark text-white text-nowrap">#</th>
                    <th class="text-center bg-dark text-white text-nowrap">المستخدم</th>
                    <th class="text-center bg-dark text-white text-nowrap">النشاط</th>
                    <th class="text-center bg-dark text-white text-nowrap">التفاصيل</th>
                    <th class="text-center bg-dark text-white text-nowrap">التاريخ</th>
                </tr>
            </thead>
            <tbody>
                @if ($activities->isEmpty() || !request()->hasAny(['user_id', 'from', 'to', 'action']))
                    <tr>
                        <td colspan="11" class="text-center">
                            <div class="status-danger fs-6">لم يتم العثور على اي نشاطات!</div>
                        </td>
                    </tr>
                @else
                    @foreach ($activities as $activity)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ $activity->user->name }}</td>
                            <td class="text-center">{{ $activity->action }}</td>
                            <td class="text-center text-muted small">{{ $activity->description ?? '-' }}</td>
                            <td class="text-center text-muted small">
                                @if($activity->created_at && $activity->created_at->diffInHours(now()) < 1)
                                    {{ $activity->created_at->diffForHumans() }}
                                @else
                                    {{ $activity->created_at->format('Y/m/d H:i') }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>

    <div class="scroll-hint text-center text-muted mt-2 d-sm-block d-md-none">
        <i class="fa-solid fa-arrows-left-right me-1"></i>
        اسحب الجدول لليمين أو اليسار لرؤية المزيد
    </div>

    <div class="mt-4">
        {{ $activities->links('components.pagination') }}
    </div>
</div>

<script>
    $('#action').select2({
        width: '100%',
        placeholder: 'اختر الإجراء',
        allowClear: true
    });
</script>

<style>
    .select2-container .select2-selection {
        height: 38px;
        border-radius: 8px;
        border: 1px solid #0d6efd;
        padding: 5px;
    }
    .select2-container .select2-selection__rendered {
        line-height: 30px;
    }
</style>
@endsection