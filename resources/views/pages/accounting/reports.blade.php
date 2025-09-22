@extends('layouts.app')

@section('title', 'تقارير')

@section('content')
<style>
    .nav-tabs .nav-link {
        color: #495057;
    }
    .nav-tabs .nav-link.active {
        background-color: #ffffff;
        border-color: #48a0ff #48a0ff #ffffff;
        color: #007bff;
        font-weight: bold;
    }
</style>

<h1 class="mb-4">التقارير المالية</h1>

<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link {{ request()->query('view', 'تقارير القيود') === 'تقارير القيود' ? 'active' : '' }}" href="?view=تقارير القيود">تقارير القيود</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->query('view') === 'كشف حساب' ? 'active' : '' }}" href="?view=كشف حساب">كشف حساب</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->query('view') === 'ميزان مراجعة' ? 'active' : '' }}" href="?view=ميزان مراجعة">ميزان مراجعة</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->query('view') === 'قائمة الدخل' ? 'active' : '' }}" href="?view=قائمة الدخل">قائمة الدخل</a>
    </li>
</ul>

@if(request()->query('view', 'تقارير القيود') == 'تقارير القيود')
    @include('admin.accounting.reports.journal_entries')
@elseif(request()->query('view') == 'كشف حساب')
    @include('admin.accounting.reports.account_statement')
@elseif(request()->query('view') == 'سند قبض بشيك')
    @include('admin.accounting.vouchers.cheque_payment')
@elseif(request()->query('view') == 'سند صرف نقدي')
    @include('admin.accounting.vouchers.cash_receipt')
@elseif(request()->query('view') == 'سند صرف بشيك')
    @include('admin.accounting.vouchers.cheque_receipt')
@elseif(request()->query('view') == 'الصندوق')
    @include('admin.accounting.vouchers.box')
@endif
<div class="mb-4"></div>
@endsection