@extends('layouts.admin')

@section('title', 'ساحة التخزين')

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
    .table .status-average {
        background-color: #fff3cd;
        color: #856404;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    .table .status-high {
        background-color: #d4edda;
        color: #155724;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
    .table .status-danger {
        background-color: #f8d7da;
        color: #721c24;
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
    }
</style>

<h1 class="mb-5">ساحة التخزين</h1>

<div class="table-container">
    <table class="table table-striped">
        <thead>
            <tr>
                <th class="text-center bg-dark text-white">ID</th>
                <th class="text-center bg-dark text-white">صاحــب الحاويــة</th>
                <th class="text-center bg-dark text-white">الموقــع</th>
                <th class="text-center bg-dark text-white">الفئـــة</th>
                <th class="text-center bg-dark text-white">الحالـــة</th>
                <th class="text-center bg-dark text-white">الإجـــراءات</th>
            </tr>
        </thead>
        <tbody>
            @if ($containers->isEmpty())
                <tr>
                    <td colspan="9" class="text-center">
                        <div class="status-danger fs-6">لا يوجد اي حاويات في الساحــة!</div>
                    </td>
                </tr>
            @else
                @foreach ($containers as $container)
                    <tr>
                        <td class="text-center">{{ $container->id }}</td>
                        <td class="text-center">
                            <a href=""
                                class="text-dark text-decoration-none">
                                {{ $container->user->name }}
                            </a>
                        </td>
                        <td class="text-center">{{ $container->location }}</td>
                        <td class="text-center">{{ $container->containerType->name }}</td>
                        <td class="text-center">{{ $container->status }}</td>
                        <td class="action-icons text-center">
                            <button class="btn btn-link p-0 pb-1 m-0 me-3" type="button" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $container->id }}">
                                <i class="fa-solid fa-pen text-primary" title="Edit container"></i>
                            </button>
                            <button class="btn btn-link p-0 pb-1 m-0" type="button" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $container->id }}">
                                <i class="fa-solid fa-trash-can text-danger" title="delete container"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
@endsection