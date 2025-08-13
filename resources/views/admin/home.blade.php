@extends('layouts.admin')

@section('title', 'dashboard')

@section('content')
    <h2>مرحباً بك في نظام إدارة المستودع</h2>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">إجمالي المستخدمين</h5>
                    <h2 class="text-primary">25</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">إجمالي العقود</h5>
                    <h2 class="text-success">12</h2>
                </div>
            </div>
        </div>
    </div>
@endsection