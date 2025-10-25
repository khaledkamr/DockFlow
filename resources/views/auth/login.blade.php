<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DockFlow - تسجيل دخول</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200..1000&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Cairo", sans-serif;
            font-optical-sizing: auto;
            font-weight: 400;
            font-style: normal;
            font-variation-settings: "slnt" 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #f8f9fc 0%, #e9ecef 100%);
        }
        .form-control {
            border: 2px solid #0d6efd;
            border-radius: 8px;
            padding: 12px 16px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #0b5ed7;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .form-control::placeholder {
            color: #6c757d;
        }
        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
            padding: 12px 24px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
        }
        .btn-primary:active {
            transform: translateY(0);
        }
        .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        .text-primary {
            color: #0d6efd !important;
        }
        .text-primary:hover {
            color: #0b5ed7 !important;
            text-decoration: underline;
        }
        .form-floating > .form-control {
            padding: 1rem 0.75rem;
        }
        .form-floating > label {
            color: #6c757d;
            font-weight: 500;
        }
        .animate-input {
            transition: transform 0.2s ease;
        }
        .animate-input:focus-within {
            transform: scale(1.02);
        }
        .password-toggle-btn {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: all 0.3s ease;
            z-index: 10;
        }
        .password-toggle-btn:hover {
            color: #0d6efd;
            background-color: rgba(13, 110, 253, 0.1);
        }
        .password-toggle-btn:active {
            transform: translateY(-50%) scale(0.95);
        }
        .password-wrapper {
            position: relative;
        }
    </style>
</head>
<body>
    <div class="container-fluid d-flex align-items-center justify-content-center min-vh-100 py-4">
        <div class="row w-100 justify-content-center">
            <div class="col-12 col-sm-8 col-md-6 col-lg-4">
                <div class="bg-white border border-2 border-primary rounded-4 shadow p-4 p-md-5">
                    <div class="text-center mb-4">
                        <img src="{{ asset('img/logo.png') }}" width="170px" alt="">
                        <h1 class="fw-bold" style="background: linear-gradient(45deg, #42b3af, #0b56a9); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">DockFlow</h1>
                    </div>

                    <!-- Login Form -->
                    <form action="{{ route('login') }}" method="POST" id="loginForm">
                        @csrf
                        <div class="mb-3 animate-input">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="email" name="email" placeholder="" value="{{ old('email') }}">
                                <label for="email"><i class="fa-solid fa-envelope me-2"></i>البريد الإلكتروني</label>
                            </div>
                        </div>
                        <div class="mb-3 animate-input">
                            <div class="form-floating password-wrapper">
                                <input type="password" class="form-control" id="password" name="password" placeholder="">
                                <label for="password"><i class="fa-solid fa-lock me-2"></i>كلمة السر</label>
                                <button type="button" class="password-toggle-btn" id="togglePassword" aria-label="Toggle password visibility">
                                    <i class="fa-solid fa-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row mb-4">
                            <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                <div class="form-check">
                                    <input class="form-check-input border border-2 border-primary" type="checkbox" id="remember">
                                    <label class="form-check-label fw-bold small text-muted" for="remember">
                                        تذكرني
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6 text-sm-end">
                                <a href="#" class="text-primary text-decoration-none">
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password toggle functionality
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

        togglePassword.addEventListener('click', function() {
            // Toggle password visibility
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle icon
            if (type === 'password') {
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            } else {
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            }
        });

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const loginBtn = document.querySelector('button[type="submit"]');
            const loginText = document.getElementById('loginText');
            
            // Validation
            if (!email || !password) {
                showToast('بالرجاء إدخال جميع البيانات', 'danger');
                return;
            }
            
            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showToast('البريد الإلكتروني غير صالح', 'danger');
                return;
            }
            
            // Loading state
            loginText.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> يتم تسجيل دخول';
            
            // Simulate API call
            setTimeout(() => {
                loginText.innerHTML = 'تسجيل الدخول';
            }, 2000);

            document.getElementById('loginForm').submit();
        });

        // Toast notification function
        function showToast(message, type = 'info') {
            const toastContainer = document.getElementById('toastContainer') || createToastContainer();
            
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type} border-0`;
            toast.setAttribute('role', 'alert');
            toast.style.direction = 'rtl';
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} ms-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-3 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            
            toastContainer.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            // Remove toast after it's hidden
            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        }

        // Create toast container
        function createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container position-fixed top-0 end-0 p-4';
            container.style.zIndex = '1055';
            document.body.appendChild(container);
            return container;
        }

        // Add ripple effect to button
        document.querySelector('.btn-primary').addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                background: rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                transform: scale(0);
                animation: ripple 0.6s ease-out;
                pointer-events: none;
            `;
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });

        // Add CSS animation for ripple effect
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(2);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>

    @if (session('error'))
        <script>
            showToast("{{ session('error') }}", "danger");
        </script>
    @endif
    @if (session('success'))
        <script>
            showToast("{{ session('success') }}", "success");
        </script>
    @endif
</body>
</html>