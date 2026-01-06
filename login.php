<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Rheaspark</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        .brand-font {
            font-family: 'Playfair Display', serif;
        }

        .brand-gradient {
            background: linear-gradient(90deg, #2FA4E7 0%, #3CB371 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #2FA4E7, #3CB371);
            border-radius: 10px;
        }

        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .floating {
            animation: float 3s infinite ease-in-out;
        }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(47, 164, 231, 0.2); }
            50% { box-shadow: 0 0 30px rgba(47, 164, 231, 0.4); }
        }

        .pulse-glow {
            animation: pulse-glow 3s infinite;
        }

        /* Input Focus Effects */
        .input-focus {
            transition: all 0.3s ease;
        }

        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(47, 164, 231, 0.1);
            border-color: #2FA4E7;
        }

        /* Toggle Switch */
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(90deg, #ccc, #ddd);
            transition: .4s;
            border-radius: 34px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background: linear-gradient(90deg, #2FA4E7, #3CB371);
        }

        input:checked + .slider:before {
            transform: translateX(30px);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-green-50 min-h-screen flex items-center justify-center p-4">

    <!-- Main Container -->
    <div class="w-full max-w-6xl">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <!-- Left Column - Branding & Info -->
            <div class="text-center lg:text-left">
                <!-- Logo -->
                <div class="flex items-center justify-center lg:justify-start mb-8">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] flex items-center justify-center shadow-xl floating">
                        <i class="fas fa-home text-3xl text-white"></i>
                    </div>
                    <div class="ml-4">
                        <h1 class="text-4xl font-bold brand-font">
                            <span class="brand-gradient">Rheaspark</span>
                        </h1>
                        <p class="text-gray-600">Trusted Housing Platform</p>
                    </div>
                </div>

                <!-- Hero Text -->
                <h2 class="text-5xl lg:text-6xl font-bold mb-8 brand-font">
                    <span class="block text-gray-800">Welcome Back to</span>
                    <span class="brand-gradient">Rheaspark</span>
                </h2>

                <p class="text-xl text-gray-600 mb-12 leading-relaxed">
                    Sign in to access your account and continue your house hunting journey.
                </p>

                <!-- Features -->
                <div class="space-y-6 mb-12">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-4">
                            <i class="fas fa-shield-alt text-green-600"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Secure Login</h4>
                            <p class="text-gray-600">Your account is protected</p>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                            <i class="fas fa-search text-blue-600"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Continue Searching</h4>
                            <p class="text-gray-600">Pick up where you left off</p>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-4">
                            <i class="fas fa-heart text-green-600"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Saved Properties</h4>
                            <p class="text-gray-600">Access your favorites</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Login Card -->
            <div class="space-y-8">
                <!-- Login Card -->
                <div class="bg-white rounded-3xl shadow-xl p-8 pulse-glow">
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Sign In</h3>
                    <p class="text-gray-600 mb-6">Enter your credentials to access your account</p>

                    <!-- Login Form -->
                    <form id="loginForm" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input
                                type="email"
                                name="email"
                                required
                                class="input-focus w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] transition-all duration-300"
                                placeholder="you@example.com"
                            >
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-sm font-medium text-gray-700">Password</label>
                                <button type="button" id="forgotPasswordBtn" class="text-sm text-[#2FA4E7] font-medium hover:text-[#3CB371] transition-colors duration-300">
                                    Forgot Password?
                                </button>
                            </div>
                            <div class="relative">
                                <input
                                    type="password"
                                    name="password"
                                    id="loginPassword"
                                    required
                                    class="input-focus w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] transition-all duration-300 pr-12"
                                    placeholder="••••••••"
                                >
                                <button
                                    type="button"
                                    id="toggleLoginPassword"
                                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                >
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input
                                    type="checkbox"
                                    id="rememberMe"
                                    class="w-4 h-4 text-[#2FA4E7] border-gray-300 rounded focus:ring-[#2FA4E7]"
                                >
                                <label for="rememberMe" class="ml-2 text-sm text-gray-700">Remember me</label>
                            </div>

                            <div class="flex items-center">
                                <span class="text-sm text-gray-600 mr-2">Stay signed in</span>
                                <label class="switch">
                                    <input type="checkbox" id="staySignedIn">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>

                        <button
                            type="submit"
                            class="w-full py-4 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-xl hover:shadow-xl transition-all duration-300 hover:scale-105"
                        >
                            <span id="loginBtnText">Sign In</span>
                        </button>
                    </form>

                    <!-- Divider -->
                    <div class="flex items-center my-8">
                        <div class="flex-1 border-t border-gray-200"></div>
                        <div class="px-4 text-gray-500 text-sm">Or continue with</div>
                        <div class="flex-1 border-t border-gray-200"></div>
                    </div>

                    <!-- Social Login -->
                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <button class="social-btn p-4 rounded-xl flex items-center justify-center hover:bg-gray-50 transition-all duration-300 border border-gray-300">
                            <i class="fab fa-google text-red-500 text-xl mr-3"></i>
                            <span class="font-medium">Google</span>
                        </button>
                        <button class="social-btn p-4 rounded-xl flex items-center justify-center hover:bg-gray-50 transition-all duration-300 border border-gray-300">
                            <i class="fab fa-facebook text-blue-600 text-xl mr-3"></i>
                            <span class="font-medium">Facebook</span>
                        </button>
                    </div>

                    <!-- Sign Up Link -->
                    <div class="text-center">
                        <span class="text-gray-600">Don't have an account?</span>
                        <a href="index.php?page=register" class="ml-2 text-[#2FA4E7] font-semibold hover:text-[#3CB371] transition-colors duration-300">
                            Sign up now
                        </a>
                    </div>
                </div>

                <!-- Guest Actions -->
                <div class="text-center">
                    <p class="text-gray-600 mb-4">Or continue as a guest to</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="index.php?page=houses" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all duration-300">
                            <i class="fas fa-search mr-2"></i> Browse Properties
                        </a>
                        <a href="index.php" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all duration-300">
                            <i class="fas fa-home mr-2"></i> Go to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div id="forgotPasswordModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl w-full max-w-md overflow-y-auto modal-enter">
                <!-- Modal Header -->
                <div class="p-6 border-b border-gray-100">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <i class="fas fa-key text-blue-600"></i>
                            </div>
                            Reset Password
                        </h2>
                        <button id="closeForgotPasswordModal" class="text-gray-500 hover:text-gray-800 text-2xl">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Content -->
                <div class="p-6">
                    <p class="text-gray-600 mb-8">
                        Enter your email address and we'll send you a link to reset your password.
                    </p>

                    <form id="forgotPasswordForm" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                            <input
                                type="email"
                                name="email"
                                required
                                class="input-focus w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] transition-all duration-300"
                                placeholder="you@example.com"
                            >
                        </div>

                        <button
                            type="submit"
                            class="w-full py-4 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-xl hover:shadow-xl transition-all duration-300 hover:scale-105"
                        >
                            <span id="resetBtnText">Send Reset Link</span>
                        </button>
                    </form>

                    <div class="mt-8 text-center">
                        <button id="backToLogin" class="text-[#2FA4E7] font-medium hover:text-[#3CB371] transition-colors duration-300">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Sign In
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl w-full max-w-md overflow-y-auto modal-enter">
                <div class="p-8 text-center">
                    <!-- Success Icon -->
                    <div class="w-32 h-32 mx-auto mb-6 rounded-full bg-gradient-to-br from-green-100 to-green-50 flex items-center justify-center">
                        <svg class="w-20 h-20 text-green-500" viewBox="0 0 52 52">
                            <circle cx="26" cy="26" r="25" fill="none" stroke="currentColor" stroke-width="2"/>
                            <path class="checkmark" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" d="M14 27l7 7 17-17"/>
                        </svg>
                    </div>

                    <h3 class="text-2xl font-bold text-gray-800 mb-4" id="successTitle">Success!</h3>
                    <p class="text-gray-600 mb-8" id="successMessage">
                        Your action was completed successfully.
                    </p>

                    <button
                        id="closeSuccessModal"
                        class="w-full py-4 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-xl hover:shadow-xl transition-all duration-300 hover:scale-105"
                    >
                        Continue
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // DOM Elements
        const forgotPasswordModal = document.getElementById('forgotPasswordModal');
        const successModal = document.getElementById('successModal');
        const showRegisterBtn = document.getElementById('showRegisterBtn');
        const closeForgotPasswordModal = document.getElementById('closeForgotPasswordModal');
        const closeSuccessModal = document.getElementById('closeSuccessModal');
        const backToLogin = document.getElementById('backToLogin');
        const loginForm = document.getElementById('loginForm');
        const forgotPasswordForm = document.getElementById('forgotPasswordForm');

        // Password Toggle Elements
        const toggleLoginPassword = document.getElementById('toggleLoginPassword');
        const forgotPasswordBtn = document.getElementById('forgotPasswordBtn');

        // Open Forgot Password Modal
        forgotPasswordBtn.addEventListener('click', () => {
            openModal(forgotPasswordModal);
        });

        // Close Forgot Password Modal
        closeForgotPasswordModal.addEventListener('click', () => {
            closeModal(forgotPasswordModal);
        });

        // Back to Login from Forgot Password
        backToLogin.addEventListener('click', () => {
            closeModal(forgotPasswordModal);
        });

        // Close Success Modal
        closeSuccessModal.addEventListener('click', () => {
            closeModal(successModal);
        });

        // Login Form Submission
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Signing In...';
            submitBtn.disabled = true;

            const formData = new FormData(this);
            const data = {
                email: formData.get('email'),
                password: formData.get('password')
            };

            fetch('api/login.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    submitBtn.innerHTML = '<i class="fas fa-check mr-2"></i> Success!';
                    showNotification('Login successful! Redirecting...', 'success');

                    // Reset button after delay
                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;

                        // Redirect based on user type
                        window.location.href = result.redirect_url || 'index.php';
                    }, 2000);
                } else {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                    showNotification(result.error, 'error');
                }
            })
            .catch(error => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                showNotification('An error occurred. Please try again.', 'error');
            });
        });

        // Forgot Password Form Submission
        forgotPasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Sending...';
            submitBtn.disabled = true;

            const formData = new FormData(this);
            const data = {
                email: formData.get('email')
            };

            fetch('api/forgot_password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                submitBtn.innerHTML = '<i class="fas fa-check mr-2"></i> Sent!';

                // Close forgot password modal
                setTimeout(() => {
                    closeModal(forgotPasswordModal);

                    // Show success modal
                    document.getElementById('successTitle').textContent = 'Check Your Email';
                    document.getElementById('successMessage').textContent = result.message;
                    openModal(successModal);

                    // Reset button
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 1000);
            })
            .catch(error => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                showNotification('An error occurred. Please try again.', 'error');
            });
        });

        // Password Toggle Functions
        function setupPasswordToggle(toggleBtnId, passwordFieldId) {
            const toggleBtn = document.getElementById(toggleBtnId);
            const passwordField = document.getElementById(passwordFieldId);

            toggleBtn.addEventListener('click', () => {
                const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordField.setAttribute('type', type);

                // Toggle eye icon
                const icon = toggleBtn.querySelector('i');
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });
        }

        // Setup password toggles
        setupPasswordToggle('toggleLoginPassword', 'loginPassword');

        // Open Modal Helper
        function openModal(modal) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.querySelector('.modal-enter').classList.add('modal-enter-active');
            }, 10);

            document.body.style.overflow = 'hidden';
        }

        // Close Modal Helper
        function closeModal(modal) {
            modal.querySelector('.modal-enter').classList.remove('modal-enter-active');
            setTimeout(() => {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }, 300);
        }

        // Show Notification
        function showNotification(message, type) {
            // Remove existing notification
            const existingNotification = document.querySelector('.notification');
            if (existingNotification) {
                existingNotification.remove();
            }

            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification fixed top-6 right-6 z-50 px-6 py-4 rounded-xl shadow-2xl transform transition-all duration-500 translate-x-full`;

            // Set notification style based on type
            let bgColor, icon;
            if (type === 'success') {
                bgColor = 'linear-gradient(90deg, #3CB371, #4CAF50)';
                icon = 'check-circle';
            } else if (type === 'error') {
                bgColor = 'linear-gradient(90deg, #F44336, #E53935)';
                icon = 'exclamation-circle';
            } else {
                bgColor = 'linear-gradient(90deg, #2FA4E7, #2196F3)';
                icon = 'info-circle';
            }

            notification.style.background = bgColor;

            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-${icon} text-white text-xl mr-3"></i>
                    <span class="text-white font-semibold">${message}</span>
                </div>
            `;

            // Add to body
            document.body.appendChild(notification);

            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 10);

            // Remove after 3 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    notification.remove();
                }, 500);
            }, 3000);
        }

        // Close modals on background click
        [forgotPasswordModal, successModal].forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    const closeBtn = this.querySelector('button[id^="close"]');
                    if (closeBtn) closeBtn.click();
                }
            });
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            // Check URL for reset token
            const urlParams = new URLSearchParams(window.location.search);
            const resetToken = urlParams.get('token');
            if (resetToken) {
                // Redirect to reset password page
                window.location.href = 'index.php?page=reset_password&token=' + resetToken;
            }

            // Add click effect to social buttons
            document.querySelectorAll('.social-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    showNotification(`Sign in with ${this.querySelector('span').textContent} coming soon!`, 'info');
                });
            });
        });
    </script>
</body>
</html>