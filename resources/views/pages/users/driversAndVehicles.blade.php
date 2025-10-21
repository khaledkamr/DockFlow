@extends('layouts.app')

@section('title', 'السائق والشاحنات')

@section('content')
<h1 class="mb-4">السائق والشاحنات</h1>

<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link {{ request()->query('view', 'السائقين') === 'السائقين' ? 'active' : '' }}" href="?view=السائقين">السائقين</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->query('view') === 'الشاحنات' ? 'active' : '' }}" href="?view=الشاحنات">الشاحنات</a>
    </li>
</ul>

@if(request()->query('view', 'السائقين') == 'السائقين')
    @include('pages.users.drivers')
@elseif(request()->query('view') == 'الشاحنات')
    @include('pages.users.vehicles')
@endif

@endsection