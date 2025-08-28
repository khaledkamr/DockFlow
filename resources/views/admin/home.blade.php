@extends('layouts.admin')

@section('title', 'dashboard')

@section('content')
    <h2 class="mb-4">مرحباً بك في نظام إدارة الساحــة</h2>
    <div class="row mb-4">
        <div class="col">
            <div class="card rounded-3 border-0 shadow-sm ps-3 pe-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">إجمالي العمـــلاء</h5>
                        <h2 class="text-primary">{{ $customers }}</h2>
                    </div>
                    <div>
                        <i class="fa-solid fa-users fa-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card rounded-3 border-0 shadow-sm ps-3 pe-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">إجمالي العقود</h5>
                        <h2 class="text-primary">{{ $contracts }}</h2>
                    </div>
                    <div>
                        <i class="fa-solid fa-file-circle-check fa-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card rounded-3 border-0 shadow-sm ps-3 pe-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">إجمالي الفواتير</h5>
                        <h2 class="text-primary">{{ $invoices }}</h2>
                    </div>
                    <div>
                        <i class="fa-solid fa-scroll fa-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <div class="card rounded-3 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title d-flex justify-content-between align-items-center text-dark fw-bold mb-4">
                        <div>
                            <i class="fa-solid fa-file-circle-question me-1"></i>
                            تقارير الحاويات
                        </div>
                        <div>
                            <form method="GET" action="">
                                <input type="date" name="date" class="form-control border-primary"
                                    value="{{ request('date', Carbon\Carbon::now()->format('Y-m-d')) }}"
                                    onchange="this.form.submit()">
                            </form>
                        </div>
                    </h5>
                    <div class="events-container">
                        <div class="event-card mb-3 p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1 fw-bold">إجمالي الحاويات في الساحة</h6>
                                </div>
                                <p class="mb-0 text-muted fw-bold">
                                    {{ $availableContainers }} <i class="fa-solid fa-boxes-stacked  ms-1"></i>
                                </p>
                            </div>
                        </div>
                        <div class="event-card mb-3 p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1 fw-bold">الحاويات المنتظرة</h6>
                                </div>
                                <p class="mb-0 text-muted fw-bold">
                                    {{ $waitingContainers  }} <i class="fa-solid fa-hourglass-start text-warning ms-2"></i>
                                </p>
                            </div>
                        </div>
                        <div class="event-card mb-3 p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1 fw-bold">الحاويات التي تم إستلامها</h6>
                                </div>
                                <p class="mb-0 text-muted fw-bold">
                                    {{ $receivedContainers  }} <i class="fa-solid fa-circle-check text-primary ms-1"></i>
                                </p>
                            </div>
                        </div>
                        <div class="event-card mb-3 p-3 bg-light rounded shadow-sm border-start border-4 border-primary">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1 fw-bold">الحاويات التي تم تسليمها</h6>
                                </div>
                                <p class="mb-0 text-muted fw-bold">
                                    {{ $deliveredContainers  }} <i class="fa-solid fa-circle-check text-primary ms-1"></i>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        @push('scripts')
            <script>
                showToast("{{ session('success') }}", "success");
            </script>
        @endpush
    @endif

    @if ($errors->any())
        @push('scripts')
            <script>
                @foreach ($errors->all() as $error)
                    showToast("{{ $error }}", "danger");
                @endforeach
            </script>
        @endpush
    @endif
@endsection