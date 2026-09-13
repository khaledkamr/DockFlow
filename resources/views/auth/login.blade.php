<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - DockFlow</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-teal: #42b3af;
            --brand-blue: #0b56a9;
            --brand-focus: rgba(11, 86, 169, 0.25);
        }

        body {
            font-family: "Cairo", sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            min-height: 100vh;
            background: linear-gradient(135deg, #f8f9fc 0%, #e9ecef 100%);
        }

        .brand-title {
            background: linear-gradient(45deg, var(--brand-teal), var(--brand-blue));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .form-control {
            border: 2px solid #d7dce2;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 16px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .form-control:focus {
            border-color: var(--brand-blue);
            box-shadow: 0 0 0 0.25rem var(--brand-focus);
        }

        .form-control.is-invalid {
            border-color: #dc3545;
        }

        .form-control::placeholder {
            color: #6c757d;
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--brand-teal), var(--brand-blue));
            border: none;
            padding: 12px 24px;
            font-weight: 600;
            border-radius: 8px;
            transition: transform 0.15s ease, box-shadow 0.15s ease, opacity 0.15s ease;
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(11, 86, 169, 0.3);
        }

        .btn-primary:active:not(:disabled) {
            transform: translateY(0);
        }

        .btn-primary:disabled {
            opacity: 0.75;
        }

        .btn-primary:focus-visible,
        .password-toggle-btn:focus-visible,
        a:focus-visible {
            outline: 3px solid var(--brand-blue);
            outline-offset: 2px;
        }

        .form-check-input:checked {
            background-color: var(--brand-blue);
            border-color: var(--brand-blue);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 0.25rem var(--brand-focus);
        }

        .text-brand {
            color: var(--brand-blue) !important;
        }

        .text-brand:hover {
            color: var(--brand-teal) !important;
            text-decoration: underline;
        }

        .form-floating>.form-control {
            padding: 1rem 0.75rem;
        }

        .form-floating>label {
            color: #6c757d;
            font-weight: 500;
        }

        .password-wrapper .form-control {
            padding-inline-end: 3rem;
        }

        .password-toggle-btn {
            position: absolute;
            inset-inline-end: 8px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: color 0.2s ease, background-color 0.2s ease;
            z-index: 10;
        }

        .password-toggle-btn:hover {
            color: var(--brand-blue);
            background-color: rgba(11, 86, 169, 0.1);
        }

        .password-wrapper {
            position: relative;
        }

        .field-error {
            display: none;
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.35rem;
        }

        .field-error.show {
            display: block;
        }

        @media (prefers-reduced-motion: reduce) {

            .form-control,
            .btn-primary,
            .password-toggle-btn {
                transition: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid d-flex align-items-center justify-content-center min-vh-100 py-4">
        <div class="row w-100 justify-content-center">
            <div class="col-12 col-sm-8 col-md-6 col-lg-4">
                <div class="bg-white border border-2 rounded-4 shadow p-4 p-md-5"
                    style="border-color: var(--brand-blue) !important;">
                    <div class="text-center mb-4">
                        <img src="{{ asset('img/logo.png') }}" width="170" alt="DockFlow">
                        <h1 class="fw-bold brand-title">DockFlow</h1>
                    </div>

                    <!-- Login Form -->
                    <form action="{{ route('login') }}" method="POST" id="loginForm" novalidate>
                        @csrf

                        <div class="mb-3">
                            <div class="form-floating">
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                    id="email" name="email" placeholder="name@example.com"
                                    value="{{ old('email') }}" autocomplete="email" autofocus required
                                    aria-describedby="emailError">
                                <label for="email"><i class="fa-solid fa-envelope me-2" aria-hidden="true"></i>البريد
                                    الإلكتروني</label>
                            </div>
                            <div class="field-error" id="emailError">
                                @error('email')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-floating password-wrapper">
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" placeholder="••••••••"
                                    autocomplete="current-password" required aria-describedby="passwordError">
                                <label for="password"><i class="fa-solid fa-lock me-2" aria-hidden="true"></i>كلمة
                                    السر</label>
                                <button type="button" class="password-toggle-btn" id="togglePassword"
                                    aria-label="إظهار كلمة السر" aria-pressed="false">
                                    <i class="fa-solid fa-eye" id="toggleIcon" aria-hidden="true"></i>
                                </button>
                            </div>
                            <div class="field-error" id="passwordError">
                                @error('password')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                <div class="form-check">
                                    <input class="form-check-input border border-2"
                                        style="border-color: var(--brand-blue) !important;" type="checkbox"
                                        name="remember" id="remember">
                                    <label class="form-check-label fw-bold small text-muted" for="remember">
                                        تذكرني
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 text-sm-end">
                                <a href="#" class="text-brand text-decoration-none">
                                    <small class="fw-bold">هل نسيت كلمة السر؟</small>
                                </a>
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <span id="loginText" class="fw-bold">تسجيل الدخول</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="toastContainer" class="toast-container position-fixed top-0 start-50 translate-middle-x p-4"
        style="z-index: 1055;" aria-live="polite" aria-atomic="true"></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            const form = document.getElementById('loginForm');
            const submitBtn = form.querySelector('button[type="submit"]');
            const loginText = document.getElementById('loginText');
            const emailInput = document.getElementById('email');
            const emailError = document.getElementById('emailError');
            const passwordError = document.getElementById('passwordError');

            togglePassword.addEventListener('click', function() {
                const isHidden = passwordInput.getAttribute('type') === 'password';
                passwordInput.setAttribute('type', isHidden ? 'text' : 'password');
                toggleIcon.classList.toggle('fa-eye', !isHidden);
                toggleIcon.classList.toggle('fa-eye-slash', isHidden);
                togglePassword.setAttribute('aria-pressed', String(isHidden));
                togglePassword.setAttribute('aria-label', isHidden ? 'إخفاء كلمة السر' : 'إظهار كلمة السر');
            });

            function setFieldError(input, errorEl, message) {
                if (message) {
                    input.classList.add('is-invalid');
                    errorEl.textContent = message;
                    errorEl.classList.add('show');
                } else {
                    input.classList.remove('is-invalid');
                    errorEl.textContent = '';
                    errorEl.classList.remove('show');
                }
            }

            form.addEventListener('submit', function(e) {
                const email = emailInput.value.trim();
                const password = passwordInput.value;
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                let hasError = false;

                if (!email) {
                    setFieldError(emailInput, emailError, 'البريد الإلكتروني مطلوب');
                    hasError = true;
                } else if (!emailRegex.test(email)) {
                    setFieldError(emailInput, emailError, 'البريد الإلكتروني غير صالح');
                    hasError = true;
                } else {
                    setFieldError(emailInput, emailError, '');
                }

                if (!password) {
                    setFieldError(passwordInput, passwordError, 'كلمة السر مطلوبة');
                    hasError = true;
                } else {
                    setFieldError(passwordInput, passwordError, '');
                }

                if (hasError) {
                    e.preventDefault();
                    showToast('بالرجاء تصحيح الأخطاء الموضحة أدناه', 'danger');
                    return;
                }

                // Let the form submit normally; show a loading state while the
                // browser navigates. No need to intercept or re-submit manually.
                submitBtn.disabled = true;
                loginText.innerHTML =
                    '<span class="spinner-border spinner-border-sm me-2" role="status"></span> جاري تسجيل الدخول...';
            });

            function showToast(message, type) {
                type = type || 'info';
                const toastContainer = document.getElementById('toastContainer');

                const iconByType = {
                    success: 'fa-circle-check',
                    warning: 'fa-triangle-exclamation',
                    danger: 'fa-circle-exclamation',
                    info: 'fa-circle-info'
                };

                const toast = document.createElement('div');
                toast.className = `toast align-items-center text-white bg-${type} border-0`;
                toast.setAttribute('role', 'alert');
                toast.setAttribute('aria-live', 'assertive');
                toast.setAttribute('aria-atomic', 'true');
                toast.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fa-solid ${iconByType[type] || iconByType.info} me-2" aria-hidden="true"></i>
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white m-auto" data-bs-dismiss="toast" aria-label="إغلاق"></button>
                    </div>
                `;

                toastContainer.appendChild(toast);
                const bsToast = new bootstrap.Toast(toast, {
                    delay: 15000
                });
                bsToast.show();
                toast.addEventListener('hidden.bs.toast', () => toast.remove());
            }

            window.__dockflowShowToast = showToast;
        })();
    </script>

    @if (session('error'))
        <script>
            window.__dockflowShowToast("{{ session('error') }}", "danger");
        </script>
    @endif
    @if (session('success'))
        <script>
            window.__dockflowShowToast("{{ session('success') }}", "success");
        </script>
    @endif
</body>

</html>
