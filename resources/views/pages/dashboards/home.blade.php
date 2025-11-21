@extends('layouts.app')

@section('title', 'dashboard')

@section('content')
<style>
    /* Responsive cards adjustments */
    @media (max-width: 768px) {
        .stats-card {
            margin-bottom: 15px;
        }
        
        .card-body h6.card-title {
            font-size: 14px;
        }
        
        .card-body h6.fw-bold {
            font-size: 1.2rem !important;
        }
        
        .card-body i.fa-xl {
            font-size: 1.2rem !important;
        }
    }
    
    @media (max-width: 576px) {
        .card-body {
            padding: 1rem !important;
        }
        
        .card-body h6.card-title {
            font-size: 13px;
        }
        
        .card-body h6.fw-bold {
            font-size: 1.1rem !important;
        }
    }

    /* Chart containers responsive */
    @media (max-width: 768px) {
        .chart-container {
            height: 200px !important;
        }
        
        .chart-container canvas {
            max-height: 200px;
        }
    }

    /* Events container responsive */
    @media (max-width: 768px) {
        .event-card {
            padding: 0.75rem !important;
        }
        
        .event-card h6 {
            font-size: 13px;
        }
        
        .event-card p {
            font-size: 14px;
        }
    }

    /* Button responsive */
    @media (max-width: 576px) {
        .btn-sm {
            font-size: 12px;
            padding: 0.35rem 0.7rem;
        }
        
        .card-title {
            font-size: 15px !important;
        }
    }

    /* Date input responsive */
    @media (max-width: 768px) {
        .form-control[type="date"] {
            font-size: 12px;
            padding: 0.4rem;
        }
    }

    /* Spacing adjustments for mobile */
    @media (max-width: 768px) {
        .row.mb-4 {
            margin-bottom: 1.5rem !important;
        }
    }
</style>

<!-- Statistics Cards Row -->
<div class="row mb-4">
    <div class="col-12 col-sm-6 col-lg-3 stats-card">
        <a href="{{ route('relation.customers') }}" class="text-decoration-none">
            <div class="card rounded-3 border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">إجمالي العمـــلاء</h6>
                        <h6 class="text-primary fw-bold mb-0" style="font-size: 1.4rem;">{{ $customers }}</h6>
                    </div>
                    <div>
                        <i class="fa-solid fa-users fa-xl"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 stats-card">
        <a href="{{ route('admin.users') }}" class="text-decoration-none">
            <div class="card rounded-3 border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">إجمالي المستخدمين</h6>
                        <h6 class="text-primary fw-bold mb-0" style="font-size: 1.4rem;">{{ $users }}</h6>
                    </div>
                    <div>
                        <i class="fa-solid fa-users fa-xl"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 stats-card">
        <a href="{{ route('contracts') }}" class="text-decoration-none">
            <div class="card rounded-3 border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">إجمالي العقود</h6>
                        <h6 class="text-primary fw-bold mb-0" style="font-size: 1.4rem;">{{ $contracts }}</h6>
                    </div>
                    <div>
                        <i class="fa-solid fa-file-circle-check fa-xl"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 stats-card">
        <a href="{{ route('invoices') }}" class="text-decoration-none">
            <div class="card rounded-3 border-0 shadow-sm">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">إجمالي الفواتير</h6>
                        <h6 class="text-primary fw-bold mb-0" style="font-size: 1.4rem;">{{ $invoices }}</h6>
                    </div>
                    <div>
                        <i class="fa-solid fa-scroll fa-xl"></i>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

@endsection