@extends('layouts.print')

@section('title', 'اذن خروج')

@section('content')
<h5 class="text-center fw-bold mb-4">اذن خروج</h5>

<div class="table-responsive">
    <table class="table table-hover mb-0">
        <thead class="">
            <tr class="">
                <th class="text-center bg-dark text-white fw-bold">#</th>
                <th class="text-center bg-dark text-white fw-bold">كود الحاوية</th>
                <th class="text-center bg-dark text-white fw-bold">صاحب الحاوية</th>
                <th class="text-center bg-dark text-white fw-bold">نوع الحاوية</th>
                <th class="text-center bg-dark text-white fw-bold">الموقع</th>
            </tr>
        </thead>
        <tbody>
            @foreach($policyContainers as $index => $container)
            <tr class="text-center">
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="fw-bold">{{ $container->containerType->name }}</td>
                <td class="fw-bold text-primary">{{ $container->code }}</td>
                <td>{{ $container->customer->name }}</td>
                <td class="fw-bold">{{ $container->location }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endsection