@extends('layouts.app')

@section('title', 'تقرير الفاتورة - ' . $invoice->code)

@section('content')

<div class="container mt-4">

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white">
            ZATCA Response Status
        </div>
        <div class="card-body">
            <h5>
                Status:
                <span class="badge bg-{{ $response['validationResults']['status'] === 'ERROR' ? 'danger' : 'success' }}">
                    {{ $response['validationResults']['status'] }}
                </span>
            </h5>

            <p class="text-muted mb-0">
                Clearance Status:
                <strong>{{ $response['clearanceStatus'] }}</strong>
            </p>
        </div>
    </div>

    {{-- ERRORS --}}
    @if(!empty($response['validationResults']['errorMessages']))
        <div class="card shadow-sm mb-4 border-danger">
            <div class="card-header bg-danger text-white">
                Errors 🚨
            </div>
            <div class="card-body">

                @foreach($response['validationResults']['errorMessages'] as $error)
                    <div class="alert alert-danger">
                        <strong>{{ $error['code'] }}</strong><br>
                        {{ $error['message'] }}
                    </div>
                @endforeach

            </div>
        </div>
    @endif

    {{-- WARNINGS --}}
    @if(!empty($response['validationResults']['warningMessages']))
        <div class="card shadow-sm mb-4 border-warning">
            <div class="card-header bg-warning text-dark">
                Warnings ⚠️
            </div>
            <div class="card-body">

                @foreach($response['validationResults']['warningMessages'] as $warning)
                    <div class="alert alert-warning">
                        <strong>{{ $warning['code'] }}</strong><br>
                        {{ $warning['message'] }}
                    </div>
                @endforeach

            </div>
        </div>
    @endif

    {{-- INFO --}}
    @if(!empty($response['validationResults']['infoMessages']))
        <div class="card shadow-sm mb-4 border-info">
            <div class="card-header bg-info text-white">
                Info ℹ️
            </div>
            <div class="card-body">

                @foreach($response['validationResults']['infoMessages'] as $info)
                    <div class="alert alert-info">
                        {{ $info['message'] ?? json_encode($info) }}
                    </div>
                @endforeach

            </div>
        </div>
    @endif

    {{-- RAW JSON (optional debug) --}}
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            Raw Response (Debug)
        </div>
        <div class="card-body">
            <pre class="mb-0" style="white-space: pre-wrap;">
                {{ json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}
            </pre>
        </div>
    </div>

</div>
@endsection