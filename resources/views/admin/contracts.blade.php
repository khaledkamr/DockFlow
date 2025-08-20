@extends('layouts.admin')

@section('title', 'العقود')

@section('content')
<style>
    .table-container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 0;
    }
    .table thead {
        background-color: #f8f9fa;
        color: #333;
    }
    .table th {
        padding: 15px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
        border-bottom: 1px solid #e9ecef;
    }
    .table td {
        padding: 15px;
        font-size: 14px;
        color: #333;
        border-bottom: 1px solid #e9ecef;
    }
    .table tbody tr:hover {
        background-color: #f1f3f5;
    }
    .table .status-running {
        background-color: #fff3cd;
        color: #856404;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    .table .status-completed {
        background-color: #d4edda;
        color: #155724;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    .table .status-canceled {
        background-color: #f8d7da;
        color: #721c24;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
</style>

<h2 class="mb-4">العقـــود</h2>

<div class="table-container">
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="text-center bg-dark text-white">رقم العقد</th>
                <th class="text-center bg-dark text-white">إسم العميل</th>
                <th class="text-center bg-dark text-white">تاريخ العقد</th>
                <th class="text-center bg-dark text-white">تاريخ الانتهاء</th>
                <th class="text-center bg-dark text-white">عرض السعر</th>
                <th class="text-center bg-dark text-white">ضريبة التاخير</th>
                <th class="text-center bg-dark text-white">الضريبة المضافة</th>
                <th class="text-center bg-dark text-white">الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @if ($contracts->isEmpty())
                <tr>
                    <td colspan="9" class="text-center">
                        <div class="status-canceled fs-6">لم يتم العثور على اي إتفاقيات!</div>
                    </td>
                </tr>
            @else
                @foreach ($contracts as $contract)
                    <tr>
                        <td class="text-center">{{ $contract->id }}</td>
                        <td class="text-center">
                            <a href=""
                                class="text-dark text-decoration-none">
                                {{ $contract->customer->name }}
                            </a>
                        </td>
                        <td class="text-center">{{ $contract->start_date }}</td>
                        <td class="text-center">{{ $contract->end_date }}</td>
                        <td class="text-center">{{ $contract->price }}</td>
                        <td class="text-center">{{ $contract->late_fee }}</td>
                        <td class="text-center">{{ $contract->tax }}</td>
                        <td class="action-icons text-center">
                            <a href="{{ route('admin.contracts.details', $contract->id) }}" 
                                class="bg-primary text-white text-decoration-none rounded-2 m-0 pe-2 ps-2 p-1">
                                عرض
                            </a>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

@endsection