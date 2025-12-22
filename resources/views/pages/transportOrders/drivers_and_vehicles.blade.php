@extends('layouts.app')

@section('title', 'السائق والشاحنات')

@section('content')
<h1 class="mb-4">النقل</h1>

<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link {{ request()->query('view', 'السائقين') === 'السائقين' ? 'active' : '' }}" href="?view=السائقين">السائقين</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->query('view') === 'الشاحنات' ? 'active' : '' }}" href="?view=الشاحنات">الشاحنات</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->query('view') === 'الوجهات' ? 'active' : '' }}" href="?view=الوجهات">الوجهات</a>
    </li>
</ul>

@if(request()->query('view', 'السائقين') == 'السائقين')
    @include('pages.transportOrders.drivers')
@elseif(request()->query('view') == 'الشاحنات')
    @include('pages.transportOrders.vehicles')
@elseif(request()->query('view') == 'الوجهات')
    @include('pages.transportOrders.destinations')
@endif

@endsection