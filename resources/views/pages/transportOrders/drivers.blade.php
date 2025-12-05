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
        min-width: 700px;
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
            min-width: 750px;
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
            <label for="search" class="form-label text-dark fw-bold mb-2">بحث عن سائق:</label>
            <input type="hidden" name="view" value="السائقين">
            <div class="d-flex gap-2">
                <input type="text" name="search" class="form-control border-primary flex-grow-1"
                    placeholder="ابحث عن سائق بالاسم او برقم الهوية..."
                    value="{{ request()->query('search') }}">
                <button type="submit" class="btn btn-primary fw-bold d-flex align-items-center px-3 px-md-4">
                    <span class="d-none d-sm-inline">بحث</span>
                    <i class="fa-solid fa-magnifying-glass ms-0 ms-sm-2"></i>
                </button>
            </div>
        </form>
    </div>
    
    <!-- Add Driver Button -->
    <div class="col-12 col-lg-3">
        <label class="form-label d-none d-lg-block opacity-0">.</label>
        <button class="btn btn-primary w-100 fw-bold d-flex align-items-center justify-content-center" 
            type="button" data-bs-toggle="modal" data-bs-target="#createDriverModal">
            <i class="fa-solid fa-user-plus ms-2"></i>
            <span>أضف سائق جديد</span>
        </button>
    </div>
</div>

<!-- Create Driver Modal -->
<div class="modal fade" id="createDriverModal" tabindex="-1" aria-labelledby="createDriverModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h5 class="modal-title text-white fw-bold" id="createDriverModalLabel">إنشاء سائق جديد</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('relation.driver.store') }}" method="POST">
                @csrf
                <div class="modal-body text-dark">
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label">إسم السائق</label>
                            <input type="text" class="form-control border-primary" name="name"
                                value="{{ old('name') }}">
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">رقم الهوية</label>
                            <input type="text" class="form-control border-primary" name="NID"
                                value="{{ old('NID') }}">
                            @error('NID')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label">رقم الهاتف</label>
                            <input type="text" class="form-control border-primary" name="phone"
                                value="{{ old('phone') }}">
                            @error('phone')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label">الشاحنة</label>
                            <select class="form-select border-primary" name="vehicle_id" required>
                                <option value="" disabled selected>اختر الشاحنة</option>
                                @foreach ($vehiclesWithoutDriver as $vehicle)
                                    <option value="{{ $vehicle->id }}">
                                        {{ $vehicle->plate_number . ' - ' . $vehicle->type }}
                                    </option>
                                @endforeach
                            </select>
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

<!-- Drivers Table -->
<div class="table-container" id="tableContainer">
    <table class="table table-hover mb-0">
        <thead>
            <tr>
                <th class="text-center bg-dark text-white">مركز التكلفة</th>
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
                    <td colspan="6" class="text-center py-4">
                        <div class="status-danger fs-6">لم يتم العثور على اي سائقين!</div>
                    </td>
                </tr>
            @else
                @foreach ($drivers as $driver)
                    <tr>
                        <td class="text-center text-primary fw-bold">{{ $driver->costCenter->code ?? 'N/A' }}</td>
                        <td class="text-center fw-bold">{{ $driver->name }}</td>
                        <td class="text-center">{{ $driver->NID }}</td>
                        <td class="text-center">{{ $driver->phone ?? '-' }}</td>
                        <td class="text-center">{{ $driver->vehicle ? $driver->vehicle->plate_number : '-' }}</td>
                        <td class="action-icons text-center">
                            <button class="btn btn-link p-0 pb-1 me-1 me-md-2" type="button" data-bs-toggle="modal"
                                data-bs-target="#editDriverModal{{ $driver->id }}">
                                <i class="fa-solid fa-pen-to-square text-primary" title="تعديل السائق"></i>
                            </button>
                            <button class="btn btn-link p-0 pb-1 m-0" type="button" data-bs-toggle="modal"
                                data-bs-target="#deleteDriverModal{{ $driver->id }}">
                                <i class="fa-solid fa-user-xmark text-danger" title="حذف السائق"></i>
                            </button>
                        </td>
                    </tr>

                    <!-- Edit Driver Modal -->
                    <div class="modal fade" id="editDriverModal{{ $driver->id }}" tabindex="-1"
                        aria-labelledby="editDriverModalLabel{{ $driver->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header bg-primary">
                                    <h5 class="modal-title text-white fw-bold"
                                        id="editDriverModalLabel{{ $driver->id }}">تعديل بيانات السائق</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form action="{{ route('relation.driver.update', $driver) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body text-dark">
                                        <div class="row g-3 mb-3">
                                            <div class="col-12 col-md-6">
                                                <label class="form-label">إسم السائق</label>
                                                <input type="text" class="form-control border-primary"
                                                    name="name" value="{{ $driver->name }}" required>
                                                @error('name')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label class="form-label">رقم الهوية</label>
                                                <input type="text" class="form-control border-primary"
                                                    name="NID" value="{{ $driver->NID }}" required>
                                                @error('NID')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row g-3 mb-3">
                                            <div class="col-12 col-md-6">
                                                <label class="form-label">رقم الهاتف</label>
                                                <input type="text" class="form-control border-primary"
                                                    name="phone" value="{{ $driver->phone }}">
                                                @error('phone')
                                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12 col-md-6">
                                                <label class="form-label">الشاحنة</label>
                                                <select class="form-select border-primary" name="vehicle_id" required>
                                                    <option value="" disabled>اختر الشاحنة</option>
                                                    <option value="{{ null }}">لا يوجد</option>
                                                    @if($driver->vehicle)
                                                        <option value="{{ $driver->vehicle->id }}" selected>
                                                            {{ $driver->vehicle->plate_number . ' - ' . $driver->vehicle->type }}
                                                        </option>
                                                    @endif
                                                    @foreach ($vehiclesWithoutDriver as $vehicle)
                                                        <option value="{{ $vehicle->id }}" {{ $driver->vehicle && $driver->vehicle->id === $vehicle->id ? 'selected' : '' }}>
                                                            {{ $vehicle->plate_number . ' - ' . $vehicle->type }}
                                                        </option>
                                                    @endforeach
                                                </select>
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

                    <!-- Delete Driver Modal -->
                    <div class="modal fade" id="deleteDriverModal{{ $driver->id }}" tabindex="-1"
                        aria-labelledby="deleteDriverModalLabel{{ $driver->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-danger">
                                    <h5 class="modal-title text-white fw-bold fs-6"
                                        id="deleteDriverModalLabel{{ $driver->id }}">تأكيد الحذف</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center text-dark px-4 py-4">
                                    هل انت متأكد من حذف السائق <strong>{{ $driver->name }}</strong>؟
                                </div>
                                <div class="modal-footer d-flex flex-column flex-sm-row justify-content-center gap-2">
                                    <button type="button" class="btn btn-secondary fw-bold order-1"
                                        data-bs-dismiss="modal">إلغاء</button>
                                    <form action="{{ route('relation.driver.delete', $driver) }}" method="POST" class="order-2">
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