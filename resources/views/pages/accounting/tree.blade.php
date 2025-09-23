@extends('layouts.app')

@section('title', 'دليل الحسابات')

@section('content')
<style>
    .tree-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .account-node {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        border-radius: 6px;
        margin: 2px 0;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
        background: #fafafa;
    }
    
    .bg-level-1 {
        background-color: var(--blue-1);
        color: white;
        font-weight: 600;
    }

    .bg-level-2 {
        background-color: var(--blue-2);
        color: white;
        font-weight: 600;
    }

    .bg-level-3 {
        background-color: var(--blue-3);
        color: white;
        font-weight: 600;
    }

    .bg-level-4 {
        background-color: var(--blue-4);
        color: white;
        font-weight: 600;
    }

    .bg-level-5 {
        background-color: white;
    }

    .bg-level-5:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
        transform: translateX(-2px);
    }
    
    .toggle-btn {
        width: 20px;
        height: 20px;
        border: none;
        background: rgba(255,255,255,0.2);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 12px;
        font-weight: bold;
    }
    
    .toggle-btn:hover {
        background: rgba(255,255,255,0.3);
        transform: scale(1.1);
    }
    
    .toggle-btn.collapsed {
        transform: rotate(90deg);
    }
    
    .toggle-btn.collapsed i {
        transform: translateY(2px);
    }
    
    .account-info {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .account-code {
        background: rgba(255,255,255,0.9);
        color: #1f2937;
        padding: 4px 8px;
        border-radius: 4px;
        font-weight: 600;
        margin-left: 12px;
        font-size: 0.875rem;
        min-width: 60px;
        text-align: center;
    }
    
    .account-node:not(.has-children) .account-code {
        background: #e5e7eb;
        color: #374151;
    }
    
    .account-name {
        font-size: 1rem;
        font-weight: 500;
    }
    
    .children-container {
        padding-right: 30px;
        border-right: 2px solid #e5e7eb;
        margin-right: 10px;
        margin-top: 8px;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .children-container.collapsed {
        max-height: 0;
        padding-top: 0;
        padding-bottom: 0;
        margin-top: 0;
        opacity: 0;
    }
    
    .children-container:not(.collapsed) {
        max-height: none;
        opacity: 1;
    }
</style>

@if (session('success'))
    @push('scripts')
        <script>
            showToast("{{ session('success') }}", "success");
        </script>
    @endpush
@endif

@if (session('error'))
    @push('scripts')
        <script>
            showToast("{{ session('error') }}", "danger");
        </script>
    @endpush
@endif

@if (session('errors'))
    @push('scripts')
        <script>
            showToast("حدث خطأ في العملية الرجاء مراجعة البيانات", "danger");
        </script>
    @endpush
@endif

@if ($errors->any())
    @foreach ($errors->all() as $error)
        @push('scripts')
            <script>
                showToast("{{ $error }}", "danger");
            </script>
        @endpush
    @endforeach
@endif

<div class="accounts-tree">
    <h1 class="mb-4">دليل الحسابات</h1>

    <div class="bg-white rounded-3 shadow-sm p-3 mt-3">
        <div class="d-flex gap-2 mb-3">
            <button class="btn btn-sm btn-outline-primary" onclick="expandAll()">
                <i class="fa-solid fa-maximize"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger" onclick="collapseAll()">
                <i class="fa-solid fa-minimize"></i>
            </button>
        </div>
        <ul class="tree-list">
            @foreach($accounts as $account)
                <li class="relative m-0 p-0 mb-3">
                    <div class="account-node {{ $account->children->count() ? 'has-children' : '' }} bg-level-{{$account->level}}" 
                         onclick="toggleNode(this)">
                        @if($account->children->count())
                            <button class="toggle-btn">
                                <i class="fa-solid fa-angle-down"></i>
                            </button>
                        @endif
                        <div class="account-info">
                            <div class="d-flex align-items-center">
                                <div class="account-code">{{ $account->code }}</div>
                                <div class="account-name">{{ $account->name }}</div>
                                <div class="ms-2">({{ $account->level }})</div>
                            </div>
                            <div>
                                <button class="z-3 badge bg-dark text-white fs-6 rounded-1 px-2 border-0" style="z-index: 1000;" 
                                    type="button" data-bs-toggle="modal" data-bs-target="#addRoot{{ $account->id }}">
                                    <i class="fa-solid fa-plus fa-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    @if($account->children->count())
                        <div class="children-container">
                            @include('pages.accounting.children', ['children' => $account->children])
                        </div>
                    @endif
                </li>

                <div class="modal fade" id="addRoot{{ $account->id }}" tabindex="-1" aria-labelledby="addRootLabel{{ $account->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title text-dark fw-bold" id="addRootLabel{{ $account->id }}">إنشاء فرع جديد من {{ $account->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="{{ route('admin.create.root') }}" method="POST">
                                @csrf
                                <div class="modal-body text-dark">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">إسم الفرع</label>
                                        <input type="text" class="form-control border-primary" id="name" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="code" class="form-label">الرقم الفرع</label>
                                        <input type="text" class="form-control border-primary" id="code" name="code" value="{{ $account->children->count() ? $account->children->last()->code + 1 : (int) ($account->code . '00') + 1 }}" required>
                                        @error('code')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <input type="hidden" name="parent_id" value={{ $account->id }}>
                                    <input type="hidden" name="type_id" value={{ $account->type_id }}>
                                    <input type="hidden" name="level" value={{ $account->level + 1 }}>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إالغاء</button>
                                    <button type="submit" class="btn btn-primary fw-bold">إنشاء</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </ul>
    </div>
</div>

<script>
    function toggleNode(node) {
        if (!node.classList.contains('has-children')) return;
        
        const toggleBtn = node.querySelector('.toggle-btn');
        const childrenContainer = node.parentElement.querySelector('.children-container');
        
        if (childrenContainer) {
            childrenContainer.classList.toggle('collapsed');
            toggleBtn.classList.toggle('collapsed');
        }
    }
    
    function expandAll() {
        document.querySelectorAll('.children-container').forEach(container => {
            container.classList.remove('collapsed');
        });
        document.querySelectorAll('.toggle-btn').forEach(btn => {
            btn.classList.remove('collapsed');
        });
    }
    
    function collapseAll() {
        document.querySelectorAll('.children-container').forEach(container => {
            container.classList.add('collapsed');
        });
        document.querySelectorAll('.toggle-btn').forEach(btn => {
            btn.classList.add('collapsed');
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        collapseAll();
    });
</script>
@endsection