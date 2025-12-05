<ul class="tree-list">
    @foreach ($children as $child)
        <li class="relative m-0 p-0" data-cost-center-id="{{ $child->id }}">
            <div class="cost-center-node {{ $child->children->count() ? 'has-children' : '' }} bg-level-{{ $child->level }}"
                onclick="toggleNode(this, event)">
                @if ($child->children->count())
                    <button class="toggle-btn">
                        <i class="fa-solid fa-angle-down"></i>
                    </button>
                @endif
                <div class="cost-center-info">
                    <div class="d-flex">
                        <div class="cost-center-code">{{ $child->code }}</div>
                        <div class="cost-center-name">{{ $child->name }}</div>
                        <div class="ms-2">({{ $child->level }})</div>
                    </div>
                    <div>
                        <button class="badge bg-danger text-white fs-6 rounded-1 border-0 " type="button"
                            data-bs-toggle="modal" data-bs-target="#deleteCostCenter{{ $child->id }}"
                            onclick="event.stopPropagation()">
                            -
                        </button>
                        <button class="badge bg-secondary text-white fs-6 rounded-1 px-2 border-0"
                            style="z-index: 1000;" type="button" data-bs-toggle="modal"
                            data-bs-target="#editCostCenter{{ $child->id }}" onclick="event.stopPropagation()">
                            <i class="fa-solid fa-pen fa-xs"></i>
                        </button>
                        @if ($child->level < 5)
                            <button class="badge bg-dark text-white fs-6 rounded-1 px-2 border-0" style="z-index: 1000;"
                                type="button" data-bs-toggle="modal" data-bs-target="#addCostCenter{{ $child->id }}"
                                onclick="event.stopPropagation()">
                                <i class="fa-solid fa-plus fa-xs"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @if ($child->children->count())
                <div class="children-container">
                    @include('pages.accounting.cost_centers.children', ['children' => $child->children])
                </div>
            @endif
        </li>

        <div class="modal fade" id="addCostCenter{{ $child->id }}" tabindex="-1"
            aria-labelledby="addCostCenterLabel{{ $child->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white fw-bold" id="addCostCenterLabel{{ $child->id }}">إنشاء مركز
                            تكلفة فرعي من {{ $child->name }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('cost.centers.store') }}" method="POST">
                        @csrf
                        <div class="modal-body text-dark">
                            <div class="mb-3">
                                <label for="name" class="form-label">اسم مركز التكلفة</label>
                                <input type="text" class="form-control border-primary" id="name" name="name"
                                    value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="code" class="form-label">رقم مركز التكلفة</label>
                                <input type="text" class="form-control border-primary" id="code" name="code"
                                    value="{{ $child->children->count() ? $child->children->last()->code + 1 : (int) ($child->code . '00') + 1 }}"
                                    required>
                                @error('code')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <input type="hidden" name="parent_id" value={{ $child->id }}>
                            <input type="hidden" name="level" value={{ $child->level + 1 }}>
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

        <div class="modal fade" id="deleteCostCenter{{ $child->id }}" tabindex="-1"
            aria-labelledby="deleteCostCenterLabel{{ $child->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <h5 class="modal-title text-white fw-bold" id="deleteCostCenterLabel{{ $child->id }}">تأكيد
                            الحذف</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('cost.centers.delete', $child->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-body text-center text-dark">
                            <p>هل أنت متأكد من حذف <strong>{{ $child->name }}</strong> من مراكز التكلفة؟</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary fw-bold"
                                data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-danger fw-bold">حذف</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editCostCenter{{ $child->id }}" tabindex="-1"
            aria-labelledby="editCostCenterLabel{{ $child->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title text-white fw-bold" id="editCostCenterLabel{{ $child->id }}">تعديل
                            مركز التكلفة {{ $child->name }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="{{ route('cost.centers.update', $child->id) }}" method="POST"
                        onsubmit="saveTreeState()">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body text-dark">
                            <div class="mb-3">
                                <label for="name" class="form-label">اسم مركز التكلفة</label>
                                <input type="text" class="form-control border-primary" id="name"
                                    name="name" value="{{ $child->name }}" required>
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="code" class="form-label">رقم مركز التكلفة</label>
                                <input type="text" class="form-control border-primary" id="code"
                                    name="code" value="{{ $child->code }}" required>
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
