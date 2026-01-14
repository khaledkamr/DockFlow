@extends('layouts.app')

@section('title', 'البنود والإجراءات')

@section('content')
<h1 class="mb-4">البنود والإجراءات</h1>

<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link {{ request()->query('view', 'البنود') === 'البنود' ? 'active' : '' }}" href="?view=البنود">البنود</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->query('view') === 'الإجراءات' ? 'active' : '' }}" href="?view=الإجراءات">الإجراءات</a>
    </li>
</ul>

@if(request()->query('view', 'البنود') == 'البنود')
    @include('pages.transactions.items')
@elseif(request()->query('view') == 'الإجراءات')
    @include('pages.transactions.procedures')
@endif

@endsection