<ul class="tree-list">
    @foreach($children as $child)
        <li class="relative m-0 p-0" data-account-id="{{ $child->id }}">
            <div class="account-node {{ $child->children->count() ? 'has-children' : '' }} bg-level-{{$child->level}}" 
                 onclick="toggleNode(this, event)">
                @if($child->children->count())
                    <button class="toggle-btn">
                        <i class="fa-solid fa-angle-down"></i>
                    </button>
                @endif
                <div class="account-info">
                    <div class="d-flex">
                        <div class="account-code">{{ $child->code }}</div>
                        <div class="account-name">{{ $child->name }}</div>
                        <div class="ms-2">({{ $child->level }})</div>
                    </div>
                    <div>
                        <button class="badge bg-danger text-white fs-6 rounded-1 border-0 "
                            type="button" data-bs-toggle="modal" data-bs-target="#deleteRoot{{ $child->id }}"
                            onclick="event.stopPropagation()">
                            -
                        </button>
                        <button class="badge bg-secondary text-white fs-6 rounded-1 px-2 border-0" style="z-index: 1000;" 
                            type="button" data-bs-toggle="modal" data-bs-target="#editRoot{{ $child->id }}"
                            onclick="event.stopPropagation()">
                            <i class="fa-solid fa-pen fa-xs"></i>
                        </button>
                        @if($child->level < 5)
                            <button class="badge bg-dark text-white fs-6 rounded-1 px-2 border-0" style="z-index: 1000;" 
                                type="button" data-bs-toggle="modal" data-bs-target="#addRoot{{ $child->id }}"
                                onclick="event.stopPropagation()">
                                <i class="fa-solid fa-plus fa-xs"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            @if($child->children->count())
                <div class="children-container">
                    @include('pages.accounting.children', ['children' => $child->children])
                </div>
            @endif
        </li>

        <div class="modal fade" id="addRoot{{ $child->id }}" tabindex="-1" aria-labelledby="addRootLabel{{ $child->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-dark fw-bold" id="addRootLabel{{ $child->id }}">إنشاء فرع جديد من {{ $child->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('create.root') }}" method="POST">
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
                                <input type="text" class="form-control border-primary" id="code" name="code" value="{{ $child->children->count() ? $child->children->last()->code + 1 : (int) ($child->code . '00') + 1 }}" required>
                                @error('code')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <input type="hidden" name="parent_id" value={{ $child->id }}>
                            <input type="hidden" name="type_id" value={{ $child->type_id }}>
                            <input type="hidden" name="level" value={{ $child->level + 1 }}>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary fw-bold">إنشاء</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteRoot{{ $child->id }}" tabindex="-1" aria-labelledby="deleteRootLabel{{ $child->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-dark fw-bold" id="deleteRootLabel{{ $child->id }}">تأكيد الحذف</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('delete.root', $child->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-body text-center text-dark">
                            <p>هل انت متاكد من حذف <strong>{{ $child->name }}</strong> من دليل الحسابات؟</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-danger fw-bold">حذف</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="editRoot{{ $child->id }}" tabindex="-1" aria-labelledby="editRootLabel{{ $child->id }}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-dark fw-bold" id="editRootLabel{{ $child->id }}">تعديل مستوى {{ $child->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('update.root', $child->id) }}" method="POST" onsubmit="saveTreeState()">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body text-dark">
                            <div class="mb-3">
                                <label for="name" class="form-label">إسم المستوى</label>
                                <input type="text" class="form-control border-primary" id="name" name="name" value="{{ $child->name }}" required>
                                @error('name')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="code" class="form-label">رقم المستوى</label>
                                <input type="text" class="form-control border-primary" id="code" name="code" value="{{ $child->code }}" required>
                                @error('code')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إالغاء</button>
                            <button type="submit" class="btn btn-primary fw-bold">حفظ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</ul>