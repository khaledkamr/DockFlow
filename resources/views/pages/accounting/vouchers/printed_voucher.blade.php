@extends('layouts.print')

@section('title', $voucher->type)

@section('content')
    <style>
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }

        .signature-line {
            border-bottom: 1px solid #000;
            width: 150px;
            display: inline-block;
        }
    </style>

    <div class="container" style="max-width: 800px;">
        <!-- Header -->
        <div class="text-center mb-4 pb-2 border-bottom border-2 border-dark">
            <h3 class="fw-bold">{{ $voucher->type }}</h3>
        </div>

        <!-- Meta Information -->
        <table class="table table-bordered mb-4">
            <tr>
                <td class="fw-bold" width="25%">رقم السند:</td>
                <td width="25%">{{ $voucher->code }}</td>
                <td class="fw-bold" width="25%">التاريخ:</td>
                <td width="25%">{{ \Carbon\Carbon::parse($voucher->date)->format('Y/m/d') }}</td>
            </tr>
        </table>

        <!-- Accounts -->
        <table class="table table-bordered mb-4">
            <tr>
                <td class="fw-bold bg-light" colspan="2">الحساب المدين</td>
            </tr>
            <tr>
                <td class="fw-bold" width="30%">اسم الحساب:</td>
                <td>{{ $voucher->debit_account->name ?? '---' }}</td>
            </tr>
            <tr>
                <td class="fw-bold">رقم الحساب:</td>
                <td>{{ $voucher->debit_account->code ?? '---' }}</td>
            </tr>
            <tr>
                <td class="fw-bold bg-light" colspan="2">الحساب الدائن</td>
            </tr>
            <tr>
                <td class="fw-bold">اسم الحساب:</td>
                <td>{{ $voucher->credit_account->name ?? '---' }}</td>
            </tr>
            <tr>
                <td class="fw-bold">رقم الحساب:</td>
                <td>{{ $voucher->credit_account->code ?? '---' }}</td>
            </tr>
        </table>

        <!-- Amount -->
        <table class="table table-bordered mb-4">
            <tr>
                <td class="fw-bold bg-light" width="30%">المبلغ:</td>
                <td class="fs-5 fw-bold">{{ number_format($voucher->amount, 2) }} ر.س</td>
            </tr>
            <tr>
                <td class="fw-bold bg-light">التفقيط:</td>
                <td>{{ $voucher->hatching }}</td>
            </tr>
        </table>

        <!-- Description -->
        @if ($voucher->description)
            <table class="table table-bordered mb-4">
                <tr>
                    <td class="fw-bold bg-light" width="30%">البيان:</td>
                    <td>{{ $voucher->description }}</td>
                </tr>
            </table>
        @endif

        <!-- Signatures -->
        <div class="row mt-5 pt-4">
            <div class="col-6 text-center">
                <p class="mb-4">تم الإعداد بواسطة</p>
                <div class="signature-line mb-2"></div>
                <p class="small">{{ $voucher->made_by->name ?? '---' }}</p>
            </div>
            <div class="col-6 text-center">
                <p class="mb-4">المعتمد</p>
                <div class="signature-line mb-2"></div>
                <p class="small">&nbsp;</p>
            </div>
        </div>
    </div>
@endsection
