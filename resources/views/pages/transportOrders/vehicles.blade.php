<style>
    /* Minimal custom styles - mostly using Bootstrap */
    .table-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        position: relative;
    }
    
    /* Add scroll indicator shadow */
    .table-container::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        width: 30px;
        background: linear-gradient(to left, rgba(0,0,0,0.1), transparent);
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .table-container.has-scroll::after {
        opacity: 1;
    }
    
    .table {
        min-width: 600px;
    }
    
    .table thead th,
    .table tbody td {
        white-space: nowrap;
    }
    
    @media (max-width: 768px) {
        .table thead th {
            font-size: 13px;
            padding: 12px 8px;
        }
        
        .table tbody td {
            font-size: 13px;
            padding: 12px 8px;
        }
        
        .action-icons i {
            font-size: 18px;
        }
    }
    
    @media (max-width: 576px) {
        .table thead th {
            font-size: 12px;
            padding: 10px 6px;
        }
        
        .table tbody td {
            font-size: 12px;
            padding: 10px 6px;
        }
        
        .table {
            min-width: 650px;
        }
    }
    
    /* Scroll hint */
    .scroll-hint {
        display: none;
        text-align: center;
        color: #6c757d;
        font-size: 14px;
        margin-top: 10px;
    }
    
    @media (max-width: 768px) {
        .scroll-hint {
            display: block;
        }
    }
</style>

<!-- Search and Add Section -->
<div class="row g-3 mb-4">
    <!-- Search Form -->
    <div class="col-12 col-lg-9">
        <form method="GET" action="" class="d-flex flex-column">
            <label for="search" class="form-label text-dark fw-bold mb-2">بحث عن شاحنة:</label>
            <input type="hidden" name="view" value="الشاحنات">
            <div class="d-flex gap-2">
                <input type="text" name="search" class="form-control border-primary flex-grow-1"
                    placeholder="ابحث عن شاحنة برقم اللوحة..."
                    value="{{ request()->query('search') }}">
                <button type="submit" class="btn btn-primary fw-bold d-flex align-items-center px-3 px-md-4">
                    <span class="d-none d-sm-inline">بحث</span>
                    <i class="fa-solid fa-magnifying-glass ms-0 ms-sm-2"></i>
                </button>
            </div>
        </form>
    </div>
    
    <!-- Add Vehicle Button -->
    <div class="col-12 col-lg-3">
        <label class="form-label d-none d-lg-block opacity-0">.</label>
        <button class="btn btn-primary w-100 fw-bold d-flex align-items-center justify-content-center" 
            type="button" data-bs-toggle="modal" data-bs-target="#createVehicleModal">
            <i class="fa-solid fa-plus me-2"></i>
            <span>أضف شاحنة جديدة</span>
        </button>
    </div>
</div>

<!-- Create Vehicle Modal -->
<div class="modal fade" id="createVehicleModal" tabindex="-1" aria-labelledby="createVehicleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white fw-bold" id="createVehicleModalLabel">إنشاء شاحنة جديدة</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('relation.vehicle.store') }}" method="POST">
                @csrf
                <div class="modal-body text-dark">
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label">رقم اللوحة</label>
                            <input type="text" class="form-control border-primary" name="plate_number"
                                value="{{ old('plate_number') }}">
                            @error('plate_number')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">نوع الشاحنة</label>
                            <input type="text" class="form-control border-primary" name="type"
                                value="{{ old('type') }}">
                            @error('type')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer d-flex flex-column flex-sm-row justify-content-start gap-2">
                    <button type="submit" class="btn btn-primary fw-bold order-2 order-sm-1">إنشاء</button>
                    <button type="button" class="btn btn-secondary fw-bold order-1 order-sm-2" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Vehicles Table -->
<div class="table-container" id="tableContainer">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th class="text-center bg-dark text-white">#</th>
                <th class="text-center bg-dark text-white">مركز التكلفة</th>
                <th class="text-center bg-dark text-white">رقم اللوحة</th>
                <th class="text-center bg-dark text-white">النوع</th>
                <th class="text-center bg-dark text-white">السائق</th>
                <th class="text-center bg-dark text-white">الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @if ($vehicles->isEmpty())
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <div class="status-danger fs-6">لم يتم العثور على اي شاحنات!</div>
                    </td>
                </tr>
            @else
                @foreach ($vehicles as $vehicle)
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        <td class="text-center text-primary fw-bold">{{ $vehicle->costCenter->code ?? 'N/A' }}</td>
                        <td class="text-center">{{ $vehicle->plate_number }}</td>
                        <td class="text-center">{{ $vehicle->type }}</td>
                        <td class="text-center">{{ $vehicle->driver->name ?? 'N/A' }}</td>
                        <td class="action-icons text-center">
                            <button class="btn btn-link p-0 pb-1 me-1 me-md-2" type="button" data-bs-toggle="modal"
                                data-bs-target="#editVehicleModal{{ $vehicle->id }}">
                                <i class="fa-solid fa-pen-to-square text-primary" title="تعديل الشاحنة"></i>
                            </button>
                            <button class="btn btn-link p-0 pb-1 m-0" type="button" data-bs-toggle="modal"
                                data-bs-target="#deleteVehicleModal{{ $vehicle->id }}">
                                <i class="fa-solid fa-trash-can text-danger" title="حذف الشاحنة"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Vehicle Modal -->
                    <div class="modal fade" id="editVehicleModal{{ $vehicle->id }}" tabindex="-1"
                        aria-labelledby="editVehicleModalLabel{{ $vehicle->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h5 class="modal-title text-white fw-bold"
                                        id="editVehicleModalLabel{{ $vehicle->id }}">تعديل بيانات الشاحنة</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form action="{{ route('relation.vehicle.update', $vehicle) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body text-dark">
                                        <div class="row g-3 mb-3">
                                            <div class="col-12 col-md-6">
                                                <label class="form-label">رقم اللوحة</label>
                                                <input type="text" class="form-control border-primary"
                                                    name="plate_number" value="{{ $vehicle->plate_number }}" required>
                                                @error('plate_number')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label class="form-label">النوع</label>
                                                <input type="text" class="form-control border-primary"
                                                    name="type" value="{{ $vehicle->type }}" required>
                                                @error('type')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer d-flex flex-column flex-sm-row justify-content-start gap-2">
                                        <button type="submit" class="btn btn-primary fw-bold order-2 order-sm-1">حفظ التغييرات</button>
                                        <button type="button" class="btn btn-secondary fw-bold order-1 order-sm-2"
                                            data-bs-dismiss="modal">إلغاء</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Delete Vehicle Modal -->
                    <div class="modal fade" id="deleteVehicleModal{{ $vehicle->id }}" tabindex="-1"
                        aria-labelledby="deleteVehicleModalLabel{{ $vehicle->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <h5 class="modal-title text-white fw-bold fs-6" 
                                        id="deleteVehicleModalLabel{{ $vehicle->id }}">تأكيد الحذف</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center text-dark px-4 py-4">
                                    هل انت متأكد من حذف الشاحنة <strong>{{ $vehicle->plate_number }}</strong>؟
                                </div>
                                <div class="modal-footer d-flex flex-column flex-sm-row justify-content-center gap-2">
                                    <button type="button" class="btn btn-secondary fw-bold order-1"
                                        data-bs-dismiss="modal">إلغاء</button>
                                    <form action="{{ route('relation.vehicle.delete', $vehicle) }}" method="POST" class="order-2">
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
<div class="scroll-hint">
    <i class="fa-solid fa-arrows-left-right me-1"></i>
    اسحب الجدول لليمين أو اليسار لرؤية المزيد
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tableContainer = document.getElementById('tableContainer');
        
        // Check if table needs scrolling
        function checkScroll() {
            if (tableContainer && tableContainer.scrollWidth > tableContainer.clientWidth) {
                tableContainer.classList.add('has-scroll');
            } else if (tableContainer) {
                tableContainer.classList.remove('has-scroll');
            }
        }
        
        // Check on load and resize
        checkScroll();
        window.addEventListener('resize', checkScroll);
        
        // Remove scroll hint after first interaction
        const scrollHint = document.querySelector('.scroll-hint');
        if (scrollHint && tableContainer) {
            tableContainer.addEventListener('scroll', function() {
                scrollHint.style.display = 'none';
            }, { once: true });
        }
    });
</script>