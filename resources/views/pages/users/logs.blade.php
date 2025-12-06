@extends('layouts.app')

@section('title', 'Logs')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>سجلات المستخدمين</h3>
    <form action="{{ route('admin.logs.delete') }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف جميع السجلات؟');">
        @csrf
        @method('DELETE')
        @foreach(request()->except('_token', '_method') as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
        <button type="submit" class="btn btn-outline-danger fw-bold">
            حذف السجلات
            <i class="fas fa-trash-alt me-1"></i>
        </button>
    </form>
</div>


{{-- Filters Card --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-3">
        <form action="{{ route('admin.logs') }}" method="GET">
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
                    <select name="action" class="form-select border-primary">
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
                        تصفية
                        <i class="fas fa-search me-1"></i>
                    </button>
                    <a href="{{ route('admin.logs') }}" class="btn btn-outline-primary fw-bold">
                        إعادة تعيين
                        <i class="fas fa-redo me-1"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Logs List --}}
@forelse ($logs as $log)
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body p-3">
            <div class="d-flex justify-content-between align-items-start mb-3 pb-2 border-bottom">
                <div>
                    <span class="badge bg-primary bg-opacity-10 text-primary fw-bold px-3 py-2 fw-semibold">
                        {{ $log->action }}
                    </span>
                    <span class="mx-2 text-muted">|</span>
                    <span class="fw-semibold text-muted">{{ $log->user->name }}</span>
                </div>
                <small class="d-flex flex-wrap align-items-center text-secondary">
                    <span>
                        @if($log->created_at->isToday())
                            اليوم
                        @elseif($log->created_at->isYesterday())
                            أمس
                        @else
                            {{ $log->created_at->format('d/m/Y') }}
                        @endif
                    </span>
                    <span class="rounded-5 px-2 ms-1" style="background-color: #e9ecef;">
                        {{ $log->created_at->timezone(auth()->user()->timezone)->format('h:i A') }}
                    </span>
                </small>
            </div>

            @if ($log->description)
                <p class="text-dark fw-semibold mb-3">{{ $log->description }}</p>
            @endif

            @if ($log->diff)
                <div class="bg-light border border-primary border-opacity-25 rounded p-3 mb-3" dir="ltr" style="text-align: left;">
                    <strong class="text-primary d-block mb-2">
                        <i class="fas fa-exchange-alt me-1"></i>
                        Changes:
                    </strong>
                    <ul class="list-unstyled mb-0 pe-3">
                        @foreach ($log->diff as $key => $data)
                            <li class="mb-2">
                                <strong class="text-secondary">{{ $key }}:</strong>
                                <span class="badge bg-danger bg-opacity-10 text-danger mx-1">
                                    @if(is_array($data['old']))
                                        <pre dir="ltr" style="text-align: left;">{{ json_encode($data['old'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    @else
                                        "{{ $data['old'] }}"
                                    @endif
                                </span>
                                <i class="fas fa-right-long text-muted mx-1"></i>
                                <span class="badge bg-success bg-opacity-10 text-success mx-1">
                                    @if(is_array($data['new']))
                                        <pre dir="ltr" style="text-align: left;">{{ json_encode($data['new'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    @else
                                        "{{ $data['new'] }}"
                                    @endif
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-3">
                @if($log->new_data)
                    <div class="col-12 col-sm-6 col-md-6">
                        <div class="bg-dark text-white rounded p-3 overflow-auto" style="max-height: 200px;">
                            <strong class="d-block mb-2 text-white-50 small" dir="ltr" style="text-align: left;">
                                New Data
                            </strong>
                            <pre class="mb-0 small text-white" dir="ltr" style="text-align: left;">{{ json_encode($log->new_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                @endif

                @if($log->old_data)
                    <div class="col-12 col-sm-6 col-md-6">
                        <div class="bg-dark text-white rounded p-3 overflow-auto" style="max-height: 200px;">
                            <strong class="d-block mb-2 text-white-50 small" dir="ltr" style="text-align: left;">
                                Old Data
                            </strong>
                            <pre class="mb-0 small text-white" dir="ltr" style="text-align: left;">{{ json_encode($log->old_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                @endif
            </div>

            <div class="mt-3 pt-2 border-top" dir="ltr" style="text-align: left;">
                <div class="row g-2 small text-muted">
                    <div class="col-md-4">
                        <i class="fas fa-globe me-1"></i>
                        <strong>IP:</strong> {{ $log->ip }}
                    </div>
                    <div class="col-md-4">
                        <i class="fas fa-user-secret me-1"></i>
                        <strong>User Agent:</strong> {{ Str::limit($log->user_agent, 40) }}
                    </div>
                    <div class="col-md-4">
                        <i class="fas fa-link me-1"></i>
                        <strong>URL:</strong> {{ Str::limit($log->url, 40) }}
                    </div>
                </div>
            </div>

        </div>
    </div>
@empty
    <div class="alert alert-info border-0 shadow-sm d-flex align-items-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="me-2" viewBox="0 0 16 16">
            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
        </svg>
        <span>لا توجد سجلات.</span>
    </div>
@endforelse

{{-- Pagination --}}
<div class="mt-4">
    {{ $logs->links() }}
</div>

@endsection