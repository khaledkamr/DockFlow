@extends('layouts.admin')

@section('title', 'dashboard')

@section('content')
    <h2 class="mb-4">مرحباً بك في نظام إدارة الساحــة</h2>
    <div class="row">
        <div class="col">
            <div class="card border-0 shadow-sm ps-3 pe-3">
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
            <div class="card border-0 shadow-sm ps-3 pe-3">
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
            <div class="card border-0 shadow-sm ps-3 pe-3">
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
@endsection