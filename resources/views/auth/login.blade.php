<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Blade</title>
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

        .login-container {
            background: white;
            border: 2px solid #0d6efd;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(13, 110, 253, 0.1);
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

        .input-group-text {
            border: 2px solid #0d6efd;
            background-color: #f8f9fa;
            color: #0d6efd;
        }

        .animate-input {
            transition: transform 0.2s ease;
        }

        .animate-input:focus-within {
            transform: scale(1.02);
        }

        @media (max-width: 576px) {
            .brand-title {
                font-size: 2rem;
            }
            
            .login-container {
                margin: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid d-flex align-items-center justify-content-center min-vh-100 py-4">
        <div class="row w-100 justify-content-center">
            <div class="col-12 col-sm-8 col-md-6 col-lg-4">
                <div class="login-container p-4 p-md-5">
                    <!-- Brand Header -->
                    <div class="text-center mb-4">
                        <img src="{{ asset('img/logo.png') }}" width="170px" alt="">
                        <h1 class="fw-bold" style="background: linear-gradient(45deg, #42b3af, #0b56a9); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">DockFlow</h1>
                        {{-- <p class="brand-subtitle mb-0">Welcome back! Please sign in to your account</p> --}}
                    </div>

                    <!-- Login Form -->
                    <form id="loginForm">
                        <!-- Email Input -->
                        <div class="mb-3 animate-input">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="email" placeholder="name@example.com" required>
                                <label for="email"><i class="fa-solid fa-envelope me-2"></i>البريد الإلكتروني</label>
                            </div>
                        </div>

                        <!-- Password Input -->
                        <div class="mb-3 animate-input">
                            <div class="form-floating">
                                <input type="password" class="form-control" id="password" placeholder="Password" required>
                                <label for="password"><i class="fa-solid fa-lock me-2"></i>كلمة السر</label>
                            </div>
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="row mb-4">
                            <div class="col-12 col-sm-6 mb-2 mb-sm-0">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="remember">
                                    <label class="form-check-label text-muted" for="remember">
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

                        <!-- Login Button -->
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fa-solid fa-arrow-right-to-bracket me-2 fw-bold"></i>
                                <span id="loginText" class="fw-bold">تسجيل الدخول</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const loginBtn = document.querySelector('button[type="submit"]');
            const loginText = document.getElementById('loginText');
            
            // Validation
            if (!email || !password) {
                // Bootstrap toast for validation error
                showToast('Please fill in all fields', 'warning');
                return;
            }
            
            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showToast('Please enter a valid email address', 'warning');
                return;
            }
            
            // Loading state
            loginBtn.disabled = true;
            loginText.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Signing In...';
            
            // Simulate API call
            setTimeout(() => {
                // Success simulation
                showToast(`Welcome back! Logged in as ${email}`, 'success');
                
                // Reset button
                loginBtn.disabled = false;
                loginText.innerHTML = 'Sign In';
                
                // Clear form
                document.getElementById('loginForm').reset();
            }, 2000);
        });

        // Toast notification function
        function showToast(message, type = 'info') {
            const toastContainer = document.getElementById('toastContainer') || createToastContainer();
            
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type === 'warning' ? 'warning' : type === 'success' ? 'success' : 'primary'} border-0`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
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
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '1055';
            document.body.appendChild(container);
            return container;
        }

        // Add smooth animations for form inputs
        const formInputs = document.querySelectorAll('.form-control');
        formInputs.forEach(input => {
            input.addEventListener('input', function() {
                if (this.value) {
                    this.classList.add('is-valid');
                    this.classList.remove('is-invalid');
                } else {
                    this.classList.remove('is-valid', 'is-invalid');
                }
            });
        });

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
</body>
</html>