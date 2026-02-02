@extends('layouts.app')

@section('title', 'مراكز التكلفة')

@section('content')
    <style>
        .tree-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .cost-center-node {
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
            background: rgba(255, 255, 255, 0.2);
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
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        .toggle-btn.collapsed {
            transform: rotate(90deg);
        }

        .toggle-btn.collapsed i {
            transform: translateY(2px);
        }

        .cost-center-info {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .cost-center-code {
            background: rgba(255, 255, 255, 0.9);
            color: #1f2937;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            margin-left: 12px;
            font-size: 0.875rem;
            min-width: 60px;
            text-align: center;
        }

        .cost-center-node:not(.has-children) .cost-center-code {
            background: #e5e7eb;
            color: #374151;
        }

        .cost-center-name {
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

    <div class="cost-centers-tree">
        <h1 class="mb-4">مراكز التكلفة</h1>

        <div class="bg-white rounded-3 shadow-sm p-3 mt-3">
            <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                <div>
                    <button class="btn btn-sm btn-outline-primary" onclick="expandAll()">
                        <i class="fa-solid fa-maximize"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="collapseAll()">
                        <i class="fa-solid fa-minimize"></i>
                    </button>
                </div>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#createParentCostCenter">
                    <i class="fa-solid fa-plus me-1"></i>
                    إنشاء مركز تكلفة رئيسي
                </button>
            </div>
            <ul class="tree-list">
                @foreach ($costCenters as $costCenter)
                    <li class="relative m-0 p-0 mb-3" data-cost-center-id="{{ $costCenter->id }}">
                        <div class="cost-center-node {{ $costCenter->children->count() ? 'has-children' : '' }} bg-level-{{ $costCenter->level }}"
                            onclick="toggleNode(this, event)">
                            @if ($costCenter->children->count())
                                <button class="toggle-btn">
                                    <i class="fa-solid fa-angle-down"></i>
                                </button>
                            @endif
                            <div class="cost-center-info">
                                <div class="d-flex align-items-center">
                                    <div class="cost-center-code">{{ $costCenter->code }}</div>
                                    <div class="cost-center-name">{{ $costCenter->name }}</div>
                                    <div class="ms-2">({{ $costCenter->level }})</div>
                                </div>
                                <div>
                                    <button class="badge bg-secondary text-white fs-6 rounded-1 px-2 border-0"
                                        style="z-index: 1000;" type="button" data-bs-toggle="modal"
                                        data-bs-target="#editCostCenter{{ $costCenter->id }}"
                                        onclick="event.stopPropagation()">
                                        <i class="fa-solid fa-pen fa-xs"></i>
                                    </button>
                                    <button class="badge bg-dark text-white fs-6 rounded-1 px-2 border-0"
                                        style="z-index: 1000;" type="button" data-bs-toggle="modal"
                                        data-bs-target="#addCostCenter{{ $costCenter->id }}"
                                        onclick="event.stopPropagation()">
                                        <i class="fa-solid fa-plus fa-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @if ($costCenter->children->count())
                            <div class="children-container">
                                @include('pages.accounting.cost_centers.children', [
                                    'children' => $costCenter->children,
                                ])
                            </div>
                        @endif
                    </li>

                    <div class="modal fade" id="addCostCenter{{ $costCenter->id }}" tabindex="-1"
                        aria-labelledby="addCostCenterLabel{{ $costCenter->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h5 class="modal-title text-white fw-bold" id="addCostCenterLabel{{ $costCenter->id }}">
                                        إنشاء مركز تكلفة فرعي من {{ $costCenter->name }}</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form action="{{ route('cost.centers.store') }}" method="POST" onsubmit="saveTreeState()">
                                    @csrf
                                    <div class="modal-body text-dark">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">اسم مركز التكلفة</label>
                                            <input type="text" class="form-control border-primary" id="name"
                                                name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="code" class="form-label">رقم مركز التكلفة</label>
                                            <input type="text" class="form-control border-primary" id="code"
                                                name="code"
                                                value="{{ $costCenter->children->count() ? $costCenter->children->last()->code + 1 : (int) ($costCenter->code . '00') + 1 }}"
                                                required>
                                            @error('code')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <input type="hidden" name="parent_id" value={{ $costCenter->id }}>
                                        <input type="hidden" name="level" value={{ $costCenter->level + 1 }}>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary fw-bold"
                                            data-bs-dismiss="modal">إلغاء</button>
                                        <button type="submit" class="btn btn-primary fw-bold">إنشاء</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="editCostCenter{{ $costCenter->id }}" tabindex="-1"
                        aria-labelledby="editCostCenterLabel{{ $costCenter->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h5 class="modal-title text-white fw-bold"
                                        id="editCostCenterLabel{{ $costCenter->id }}">تعديل مركز التكلفة
                                        {{ $costCenter->name }}</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form action="{{ route('cost.centers.update', $costCenter->id) }}" method="POST"
                                    onsubmit="saveTreeState()">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body text-dark">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">اسم مركز التكلفة</label>
                                            <input type="text" class="form-control border-primary" id="name"
                                                name="name" value="{{ $costCenter->name }}" required>
                                            @error('name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="code" class="form-label">رقم مركز التكلفة</label>
                                            <input type="text" class="form-control border-primary" id="code"
                                                name="code" value="{{ $costCenter->code }}" required>
                                            @error('code')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary fw-bold"
                                            data-bs-dismiss="modal">إلغاء</button>
                                        <button type="submit" class="btn btn-primary fw-bold">حفظ</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Create Parent Cost Center Modal -->
    <div class="modal fade" id="createParentCostCenter" tabindex="-1" aria-labelledby="createParentCostCenterLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="createParentCostCenterLabel">
                        <i class="fa-solid fa-plus-circle me-2"></i>
                        إنشاء مركز تكلفة رئيسي
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('cost.centers.store') }}" method="POST" onsubmit="saveTreeState()">
                    @csrf
                    <div class="modal-body text-dark">
                        <div class="mb-3">
                            <label for="parent_name" class="form-label fw-bold">اسم مركز التكلفة الرئيسي</label>
                            <input type="text" class="form-control border-primary" id="parent_name" name="name"
                                value="{{ old('name') }}" required>
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="parent_code" class="form-label fw-bold">رقم مركز التكلفة</label>
                            <input type="text" class="form-control border-primary" id="parent_code" name="code"
                                value="{{ old('code', $costCenters->count() ? $costCenters->last()->code + 10 : 1) }}"
                                required>
                            @error('code')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <input type="hidden" name="parent_id" value="">
                        <input type="hidden" name="level" value="1">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary fw-bold">
                            <i class="fa-solid fa-check me-1"></i>
                            إنشاء
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleNode(node, event) {
            if (!node.classList.contains('has-children')) return;

            const toggleBtn = node.querySelector('.toggle-btn');
            const childrenContainer = node.parentElement.querySelector('.children-container');

            if (childrenContainer) {
                childrenContainer.classList.toggle('collapsed');
                toggleBtn.classList.toggle('collapsed');
                saveTreeState();
            }
        }

        function expandAll() {
            document.querySelectorAll('.children-container').forEach(container => {
                container.classList.remove('collapsed');
            });
            document.querySelectorAll('.toggle-btn').forEach(btn => {
                btn.classList.remove('collapsed');
            });
            saveTreeState();
        }

        function collapseAll() {
            document.querySelectorAll('.children-container').forEach(container => {
                container.classList.add('collapsed');
            });
            document.querySelectorAll('.toggle-btn').forEach(btn => {
                btn.classList.add('collapsed');
            });
            saveTreeState();
        }

        function saveTreeState() {
            const expandedNodes = [];
            document.querySelectorAll('.children-container:not(.collapsed)').forEach(container => {
                const costCenterNode = container.closest('li[data-cost-center-id]');
                if (costCenterNode) {
                    expandedNodes.push(costCenterNode.getAttribute('data-cost-center-id'));
                }
            });
            sessionStorage.setItem('expandedCostCenters', JSON.stringify(expandedNodes));
        }

        function restoreTreeState() {
            const expandedNodes = JSON.parse(sessionStorage.getItem('expandedCostCenters') || '[]');

            if (expandedNodes.length === 0) {
                collapseAll();
                return;
            }

            // First collapse all
            document.querySelectorAll('.children-container').forEach(container => {
                container.classList.add('collapsed');
            });
            document.querySelectorAll('.toggle-btn').forEach(btn => {
                btn.classList.add('collapsed');
            });

            // Then expand the saved nodes
            expandedNodes.forEach(costCenterId => {
                const costCenterNode = document.querySelector(`li[data-cost-center-id="${costCenterId}"]`);
                if (costCenterNode) {
                    const childrenContainer = costCenterNode.querySelector('.children-container');
                    const toggleBtn = costCenterNode.querySelector('.toggle-btn');
                    if (childrenContainer) {
                        childrenContainer.classList.remove('collapsed');
                    }
                    if (toggleBtn) {
                        toggleBtn.classList.remove('collapsed');
                    }
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            restoreTreeState();
        });
    </script>
@endsection
