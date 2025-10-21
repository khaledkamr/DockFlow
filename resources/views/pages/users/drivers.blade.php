<div class="row mb-4">
    <div class="col-9">
        <form method="GET" action="" class="d-flex flex-column">
            <label for="search" class="form-label text-dark fw-bold">بحث عن سائق:</label>
            <input type="hidden" name="view" value="السائقين">
            <div class="d-flex">
                <input type="text" name="search" class="form-control border-primary"
                    placeholder=" ابحث عن سائق بالاسم او برقم الهوية... " value="{{ request()->query('search') }}">
                <button type="submit" class="btn btn-primary fw-bold ms-2 d-flex align-items-center">
                    <span>بحث</span>
                    <i class="fa-solid fa-magnifying-glass ms-2"></i>
                </button>
            </div>
        </form>
    </div>
    <div class="col d-flex align-items-end">
        <button class="btn btn-primary w-100 fw-bold" type="button" data-bs-toggle="modal"
            data-bs-target="#createDriverModal">
            <i class="fa-solid fa-user-plus pe-1"></i>
            أضف سائق جديد
        </button>
    </div>
</div>

<div class="modal fade" id="createDriverModal" tabindex="-1" aria-labelledby="createDriverModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark fw-bold" id="createDriverModalLabel">إنشاء سائق جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.driver.store') }}" method="POST">
                @csrf
                <div class="modal-body text-dark">
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">إسم السائق</label>
                            <input type="text" class="form-control border-primary" name="name"
                                value="{{ old('name') }}">
                            @error('name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col">
                            <label class="form-label">رقم الهوية</label>
                            <input type="text" class="form-control border-primary" name="NID"
                                value="{{ old('NID') }}">
                            @error('NID')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">رقم الهاتف</label>
                            <input type="text" class="form-control border-primary" name="phone"
                                value="{{ old('phone') }}">
                            @error('phone')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col">
                            <label class="form-label">الشاحنة</label>
                            <select class="form-select border-primary" name="vehicle_id" required>
                                <option value="" disabled>اختر الشاحنة</option>
                                @foreach ($vehiclesWithoutDriver as $vehicle)
                                    <option value="{{ $vehicle->id }}">{{ $vehicle->plate_number . ' - ' . $vehicle->type }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-start">
                    <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary fw-bold">إنشاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="table-container">
    <table class="table table-hover">
        <thead>
            <tr>
                <th class="text-center bg-dark text-white">رقم السائق</th>
                <th class="text-center bg-dark text-white">إسم السائق</th>
                <th class="text-center bg-dark text-white">رقم الهوية</th>
                <th class="text-center bg-dark text-white">رقم الهاتف</th>
                <th class="text-center bg-dark text-white">الشاحنة</th>
                <th class="text-center bg-dark text-white">الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @if ($drivers->isEmpty())
                <tr>
                    <td colspan="7" class="text-center">
                        <div class="status-danger fs-6">لم يتم العثور على اي سائقين!</div>
                    </td>
                </tr>
            @else
                @foreach ($drivers as $driver)
                    <tr>
                        <td class="text-center text-primary fw-bold">{{ $driver->account->code ?? 'N/A' }}</td>
                        <td class="text-center fw-bold">{{ $driver->name }}</td>
                        <td class="text-center">{{ $driver->NID }}</td>
                        <td class="text-center">{{ $driver->phone ?? '-' }}</td>
                        <td class="text-center">{{ $driver->vehicle ? $driver->vehicle->plate_number : '-' }}</td>
                        <td class="action-icons text-center">
                            <button class="btn btn-link p-0 pb-1 me-2" type="button" data-bs-toggle="modal"
                                data-bs-target="#editDriverModal{{ $driver->id }}">
                                <i class="fa-solid fa-pen-to-square text-primary"></i>
                            </button>
                            <button class="btn btn-link p-0 pb-1 m-0" type="button" data-bs-toggle="modal"
                                data-bs-target="#deleteDriverModal{{ $driver->id }}">
                                <i class="fa-solid fa-user-xmark text-danger"></i>
                            </button>
                        </td>
                    </tr>

                    {{-- update modal --}}
                    <div class="modal fade" id="editDriverModal{{ $driver->id }}" tabindex="-1"
                        aria-labelledby="editDriverModalLabel{{ $driver->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-dark fw-bold"
                                        id="editDriverModalLabel{{ $driver->id }}">تعديل بيانات السائق</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form action="{{ route('admin.driver.update', $driver) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body text-dark">
                                        <div class="row mb-3">
                                            <div class="col">
                                                <label class="form-label">إسم السائق</label>
                                                <input type="text" class="form-control border-primary"
                                                    name="name" value="{{ $driver->name }}" required>
                                                @error('name')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col">
                                                <label class="form-label">رقم الهوية</label>
                                                <input type="text" class="form-control border-primary"
                                                    name="NID" value="{{ $driver->NID }}" required>
                                                @error('NID')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col">
                                                <label class="form-label">رقم الهاتف</label>
                                                <input type="text" class="form-control border-primary"
                                                    name="phone" value="{{ $driver->phone }}">
                                                @error('phone')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col">
                                                <label class="form-label">الشاحنة</label>
                                                <select class="form-select border-primary" name="vehicle_id" required>
                                                    <option value="" disabled selected>اختر الشاحنة</option>
                                                    <option value="{{ null }}">لا يوجد</option>
                                                    @foreach ($vehiclesWithoutDriver as $vehicle)
                                                        <option value="{{ $vehicle->id }}" {{ $driver->vehicle && $driver->vehicle->id === $vehicle->id ? 'selected' : '' }}>
                                                            {{ $vehicle->plate_number . ' - ' . $vehicle->type }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer d-flex justify-content-start">
                                        <button type="button" class="btn btn-secondary fw-bold"
                                            data-bs-dismiss="modal">إلغاء</button>
                                        <button type="submit" class="btn btn-primary fw-bold">حفظ التغييرات</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- delete modal --}}
                    <div class="modal fade" id="deleteDriverModal{{ $driver->id }}" tabindex="-1"
                        aria-labelledby="deleteDriverModalLabel{{ $driver->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-dark fw-bold"
                                        id="deleteDriverModalLabel{{ $driver->id }}">تأكيد الحذف</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center text-dark">
                                    هل انت متأكد من حذف السائق <strong>{{ $driver->name }}</strong>؟
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary fw-bold"
                                        data-bs-dismiss="modal">إلغاء</button>
                                    <form action="{{ route('admin.driver.delete', $driver) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger fw-bold">حذف</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </tbody>
    </table>
</div>