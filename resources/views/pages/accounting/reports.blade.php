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
        <a class="nav-link {{ request()->query('view', 'كشف حساب') === 'كشف حساب' ? 'active' : '' }}" href="?view=كشف حساب">كشف حساب</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->query('view') === 'تقارير القيود' ? 'active' : '' }}" href="?view=تقارير القيود">تقارير القيود</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->query('view') === 'ميزان مراجعة' ? 'active' : '' }}" href="?view=ميزان مراجعة">ميزان مراجعة</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->query('view') === 'قائمة الدخل' ? 'active' : '' }}" href="?view=قائمة الدخل">قائمة الدخل</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->query('view') === 'أعمار الذمم' ? 'active' : '' }}" href="?view=أعمار الذمم">أعمار الذمم</a>
    </li>
</ul>

@if(request()->query('view', 'كشف حساب') == 'كشف حساب')
    @include('pages.accounting.reports.account_statement')
@elseif(request()->query('view') == 'تقارير القيود')
    @include('pages.accounting.reports.journal_entries')
@elseif(request()->query('view') == 'ميزان مراجعة')
    @include('pages.accounting.reports.trial_balance')
@elseif(request()->query('view') == 'قائمة الدخل')
    @include('pages.accounting.reports.income_statement')
@elseif(request()->query('view') == 'أعمار الذمم')
    @include('pages.accounting.reports.aging_report')
@endif

<div class="mb-4"></div>
@endsection