<div class="row mb-4">
    <div class="col-9">
        <form method="GET" action="" class="d-flex flex-column">
            <label for="search" class="form-label text-dark fw-bold">بحث عن شاحنة:</label>
            <input type="hidden" name="view" value="الشاحنات">
            <div class="d-flex">
                <input type="text" name="search" class="form-control border-primary"
                    placeholder=" ابحث عن شاحنة برقم اللوحة... " value="{{ request()->query('search') }}">
                <button type="submit" class="btn btn-primary fw-bold ms-2 d-flex align-items-center">
                    <span>بحث</span>
                    <i class="fa-solid fa-magnifying-glass ms-2"></i>
                </button>
            </div>
        </form>
    </div>
    <div class="col d-flex align-items-end">
        <button class="btn btn-primary w-100 fw-bold" type="button" data-bs-toggle="modal"
            data-bs-target="#createVehicleModal">
            <i class="fa-solid fa-user-plus pe-1"></i>
            أضف شاحنة جديدة
        </button>
    </div>
</div>

<div class="modal fade" id="createVehicleModal" tabindex="-1" aria-labelledby="createVehicleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-dark fw-bold" id="createVehicleModalLabel">إنشاء شاحنة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.vehicle.store') }}" method="POST">
                @csrf
                <div class="modal-body text-dark">
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label">رقم اللوحة</label>
                            <input type="text" class="form-control border-primary" name="plate_number"
                                value="{{ old('plate_number') }}">
                            @error('plate_number')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col">
                            <label class="form-label">نوع الشاحنة</label>
                            <input type="text" class="form-control border-primary" name="type"
                                value="{{ old('type') }}">
                            @error('type')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
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
                <th class="text-center bg-dark text-white">رقم الشاحنة</th>
                <th class="text-center bg-dark text-white">رقم اللوحة</th>
                <th class="text-center bg-dark text-white">النوع</th>
                <th class="text-center bg-dark text-white">السائق</th>
                <th class="text-center bg-dark text-white">الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @if ($vehicles->isEmpty())
                <tr>
                    <td colspan="7" class="text-center">
                        <div class="status-danger fs-6">لم يتم العثور على اي شاحنات!</div>
                    </td>
                </tr>
            @else
                @foreach ($vehicles as $vehicle)
                    <tr>
                        <td class="text-center text-primary fw-bold">{{ $vehicle->account->code ?? 'N/A' }}</td>
                        <td class="text-center">{{ $vehicle->plate_number }}</td>
                        <td class="text-center">{{ $vehicle->type }}</td>
                        <td class="text-center">{{ $vehicle->driver->name ?? 'N/A' }}</td>
                        <td class="action-icons text-center">
                            <button class="btn btn-link p-0 pb-1 me-2" type="button" data-bs-toggle="modal"
                                data-bs-target="#editVehicleModal{{ $vehicle->id }}">
                                <i class="fa-solid fa-pen-to-square text-primary"></i>
                            </button>
                            <button class="btn btn-link p-0 pb-1 m-0" type="button" data-bs-toggle="modal"
                                data-bs-target="#deleteVehicleModal{{ $vehicle->id }}">
                                <i class="fa-solid fa-trash-can text-danger"></i>
                            </button>
                        </td>
                    </tr>

                    {{-- update modal --}}
                    <div class="modal fade" id="editVehicleModal{{ $vehicle->id }}" tabindex="-1"
                        aria-labelledby="editVehicleModalLabel{{ $vehicle->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-dark fw-bold"
                                        id="editVehicleModalLabel{{ $vehicle->id }}">تعديل بيانات الشاحنة</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form action="{{ route('admin.vehicle.update', $vehicle) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body text-dark">
                                        <div class="row mb-3">
                                            <div class="col">
                                                <label class="form-label">رقم اللوحة</label>
                                                <input type="text" class="form-control border-primary"
                                                    name="plate_number" value="{{ $vehicle->plate_number }}" required>
                                                @error('plate_number')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col">
                                                <label class="form-label">النوع</label>
                                                <input type="text" class="form-control border-primary"
                                                    name="type" value="{{ $vehicle->type }}" required>
                                                @error('type')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
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
                    <div class="modal fade" id="deleteVehicleModal{{ $vehicle->id }}" tabindex="-1"
                        aria-labelledby="deleteVehicleModalLabel{{ $vehicle->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title text-dark fw-bold" id="deleteVehicleModalLabel{{ $vehicle->id }}">تأكيد الحذف</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center text-dark">
                                    هل انت متأكد من حذف الشاحنة <strong>{{ $vehicle->plate_number }}</strong>؟
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary fw-bold"
                                        data-bs-dismiss="modal">إلغاء</button>
                                    <form action="{{ route('admin.vehicle.delete', $vehicle) }}" method="POST">
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