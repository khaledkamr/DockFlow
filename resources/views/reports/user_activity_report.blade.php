@extends('layouts.print')

@section('title', 'تقرير نشاط المستخدمين')

@section('content')
<h5 class="fw-bold text-center mt-3">
    تقرير نشاط المستخدمين
</h5>
<p class="text-center fw-bold">من الفترة ({{ Carbon\Carbon::parse($from)->format('Y/m/d') }}) إلى الفترة ({{ Carbon\Carbon::parse($to)->format('Y/m/d') }})</p>

<div class="p-3">
    <div class="table-container">
        <table class="table table-bordered border-dark">
            <thead>
                <tr class="table-dark border-dark fw-bold">
                    <th class="text-center">#</th>
                    <th class="text-center">المستخدم</th>
                    <th class="text-center">النشاط</th>
                    <th class="text-center">التفاصيل</th>
                    <th class="text-center">التاريخ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activities as $activity)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">{{ $activity->user->name }}</td>
                        <td class="text-center">{{ $activity->action }}</td>
                        <td class="text-center">{{ $activity->description }}</td>
                        <td class="text-center">
                            @if($activity->created_at && $activity->created_at->diffInHours(now()) < 1)
                                {{ $activity->created_at->diffForHumans() }}
                            @else
                                {{ $activity->created_at->format('Y/m/d H:i') }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection