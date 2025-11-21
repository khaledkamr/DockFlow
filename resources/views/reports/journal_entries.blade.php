@extends('layouts.print')

@section('title', 'تقارير القيود')

@section('content')
<h5 class="fw-bold text-center">تقارير القيود من الفترة ({{ $from }}) للفترة ({{ $to }})</h5>

<div class="table-container">
    <table class="table table-bordered border-dark table-hover">
        <thead>
            <tr>
                <th class="text-center bg-dark text-white">رقم القيد</th>
                <th class="text-center bg-dark text-white">سطر</th>
                <th class="text-center bg-dark text-white">رقم الحساب</th>
                <th class="text-center bg-dark text-white">اسم الحساب</th>
                <th class="text-center bg-dark text-white">البيان</th>
                <th class="text-center bg-dark text-white">مدين</th>
                <th class="text-center bg-dark text-white">دائن</th>
            </tr>
        </thead>
        <tbody class="text-center">
            @php
                $totalEntriesDebit = 0;
                $totalEntriesCredit = 0;
            @endphp
            @forelse($entries as $entry)
                <tr>
                    <td colspan="7" class="text-start table-secondary border-dark fw-bold">
                        <a href="{{ route('journal.details', $entry->id) }}" class="text-decoration-none text-dark">
                            قيد - {{ $entry->voucher->type ?? 'قيد يومي' }} - بتاريخ {{ $entry->date }}
                        </a>
                    </td>
                </tr>
                @foreach($entry->lines as $index => $line)
                    <tr>
                        <td>{{ $line->journal->code }}</td>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $line->account->code }}</td>
                        <td>{{ $line->account->name }}</td>
                        <td>{{ $line->description ?? '-' }}</td>
                        <td>{{ number_format($line->debit, 2) }}</td>
                        <td>{{ number_format($line->credit, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="table-secondary border-dark fw-bold">
                    <td colspan="4"></td>
                    <td>إجمالي</td>
                    <td>{{ number_format($entry->totalDebit, 2) }}</td>
                    <td>{{ number_format($entry->totalCredit, 2) }}</td>
                </tr>
                <tr><td colspan="7" class="p-1"></td></tr>
                @php
                    $totalEntriesDebit += $entry->totalDebit;
                    $totalEntriesCredit += $entry->totalCredit;
                @endphp
            @empty
                <tr>
                    <td colspan="7" class="text-center">
                        <div class="status-danger fs-6">لم يتم العثور على أي قيود</div>
                    </td>
                </tr>
            @endforelse
            <tr>
                <tr class="table-primary border-dark fw-bold">
                    <td colspan="4"></td>
                    <td class="fs-6">إجمالي القيود</td>
                    <td class="fs-6">{{ number_format($totalEntriesDebit, 2) }}</td>
                    <td class="fs-6">{{ number_format($totalEntriesCredit, 2) }}</td>
                </tr>
            </tr>
        </tbody>
    </table>
</div>
@endsection