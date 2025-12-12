<nav class="navbar navbar-expand-lg bg-white shadow-sm" style="min-height: 70px;">
    <div class="container-fluid">
        @if (auth()->user()->company->logo)
            <img src="{{ asset('storage/' . auth()->user()->company->logo) }}" alt="Logo"
                class="me-2" style="height: 40px; width: auto;">
        @endif
        <a class="navbar-brand text-dark fw-bold" href="{{ route('company', auth()->user()->company) }}">
            {{ auth()->user()->company->name }}
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <form class="d-flex mx-auto" style="width: 40%; max-width: 500px;">
                <div class="input-group">
                    <input class="form-control border-primary" type="search" placeholder="بحث..."
                        aria-label="Search">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <div class="d-flex align-items-center gap-3 ms-auto">
                <!-- Language Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-link p-0 border-0" type="button" id="languageDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-globe fa-xl text-secondary"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="languageDropdown">
                        <li>
                            <h6 class="dropdown-header">اختر اللغة</h6>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item d-flex align-items-center" href="#">
                                <img src="https://flagcdn.com/w20/sa.png" alt="Arabic" class="me-2"
                                    style="width: 20px;">
                                العربية
                                <i class="fa-solid fa-check text-success ms-auto"
                                    style="display: {{ app()->getLocale() == 'ar' ? 'inline' : 'none' }}"></i>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Notifications Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-link p-0 border-0 position-relative" type="button"
                        id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-bell fa-xl text-secondary"></i>
                        <!-- Notification Badge -->
                        {{-- <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                            5
                        </span> --}}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationsDropdown"
                        style="width: 350px; max-height: 400px; overflow-y: auto;">
                        <li>
                            <h6 class="dropdown-header d-flex justify-content-between align-items-center">
                                الإشعارات
                                <span class="badge bg-primary rounded-pill">0</span>
                            </h6>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>

                        <!-- Notification Items -->
                        {{-- <li>
                            <a class="dropdown-item py-2 px-3" href="#">
                                <div class="d-flex align-items-start">
                                    <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; min-width: 35px;">
                                        <i class="fa-solid fa-file-invoice text-white" style="font-size: 14px;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-bold" style="font-size: 13px;">فاتورة جديدة</div>
                                        <div class="text-muted" style="font-size: 12px;">تم إنشاء فاتورة رقم #1234</div>
                                        <small class="text-muted">منذ 5 دقائق</small>
                                    </div>
                                </div>
                            </a>
                        </li> --}}

                        <li>
                            <div class="dropdown-item text-center py-4">
                                <i class="fa-solid fa-bell-slash text-muted fa-2x mb-2"></i>
                                <div class="text-muted">لا توجد إشعارات</div>
                            </div>
                        </li>

                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item text-center py-2 fw-bold text-primary" href="#">
                                <i class="fa-solid fa-eye me-1"></i>
                                عرض جميع الإشعارات
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Settings Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-link p-0 border-0" type="button" id="settingsDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-gear fa-xl text-secondary"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsDropdown">
                        <li class="dropdown-hover"><a class="dropdown-item" href="#">الإعدادات</a></li>
                        <li class="dropdown-hover"><a class="dropdown-item"
                                href="{{ route('company', auth()->user()->company) }}">بيانات الشركة</a></li>
                        <li class="dropdown-hover"><a class="dropdown-item"
                                href="{{ route('user.profile', auth()->user()) }}">الملف الشخصي</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li class="dropdown-hover">
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger"
                                    style="border: none; background: transparent;">
                                    <i class="fa-solid fa-arrow-right-from-bracket me-1"></i>
                                    تسجيل خروج
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>

                <div class="d-flex align-items-center text-dark me-0 me-md-3">
                    <a href="{{ route('user.profile', Auth::user()) }}">
                        <img src="{{ Auth::user()->avatar ?? asset('img/user-profile.jpg') }}"
                            alt="Profile Photo" class="rounded-circle me-2"
                            style="width: 40px; height: 40px;">
                    </a>
                    <div class="d-flex flex-column">
                        <span class="fw-bold" style="font-size: 14px;">{{ Auth::user()->name }}</span>
                        <span class="text-secondary"
                            style="font-size: 12px;">{{ Auth::user()->roles->first()->name }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>