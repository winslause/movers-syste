<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Rheaspark</title>
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

        /* Modal Animations */
        .modal-enter {
            opacity: 0;
            transform: scale(0.9) translateY(20px);
        }

        .modal-enter-active {
            opacity: 1;
            transform: scale(1) translateY(0);
            transition: opacity 300ms, transform 300ms;
        }

        /* Input Focus Effects */
        .input-focus {
            transition: all 0.3s ease;
        }

        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(47, 164, 231, 0.1);
            border-color: #2FA4E7;
        }

        /* Password Strength Indicator */
        .password-strength {
            height: 4px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }

        /* Loading Animation */
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .spin {
            animation: spin 1s linear infinite;
        }

        /* Success Check Animation */
        @keyframes checkmark {
            0% { stroke-dashoffset: 100; }
            100% { stroke-dashoffset: 0; }
        }

        .checkmark {
            stroke-dasharray: 100;
            stroke-dashoffset: 100;
            animation: checkmark 0.5s ease-in-out forwards;
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
                        <i class="fas fa-lock-open text-3xl text-white"></i>
                    </div>
                    <div class="ml-4">
                        <h1 class="text-4xl font-bold brand-font">
                            <span class="brand-gradient">Rheaspark</span>
                        </h1>
                        <p class="text-gray-600">Reset Your Password</p>
                    </div>
                </div>

                <!-- Hero Text -->
                <h2 class="text-5xl lg:text-6xl font-bold mb-8 brand-font">
                    <span class="block text-gray-800">Secure Your</span>
                    <span class="brand-gradient">Account</span>
                </h2>

                <p class="text-xl text-gray-600 mb-12 leading-relaxed">
                    Create a new, strong password to keep your Rheaspark account secure.
                </p>

                <!-- Features -->
                <div class="space-y-6 mb-12">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-4">
                            <i class="fas fa-shield-alt text-green-600"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Enhanced Security</h4>
                            <p class="text-gray-600">Your new password will be encrypted</p>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                            <i class="fas fa-key text-blue-600"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Password Recovery</h4>
                            <p class="text-gray-600">Easy reset process for future needs</p>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-4">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Account Access</h4>
                            <p class="text-gray-600">Regain full access to your account</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Reset Password Card -->
            <div class="space-y-8">
                <!-- Reset Password Card -->
                <div class="bg-white rounded-3xl shadow-xl p-8 pulse-glow">
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Create New Password</h3>
                    <p class="text-gray-600 mb-6">Please enter your new password. Make sure it's strong and different from your previous passwords.</p>

                    <!-- Reset Password Form -->
                    <form id="resetPasswordForm" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                            <div class="relative">
                                <input
                                    type="password"
                                    name="newPassword"
                                    id="newPassword"
                                    required
                                    class="input-focus w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] transition-all duration-300 pr-12"
                                    placeholder="••••••••"
                                >
                                <button
                                    type="button"
                                    id="toggleNewPassword"
                                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                >
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>

                            <!-- Password Strength -->
                            <div class="mt-3">
                                <div class="flex justify-between text-sm text-gray-600 mb-1">
                                    <span>Password strength</span>
                                    <span id="newPasswordStrengthText">Weak</span>
                                </div>
                                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div id="newPasswordStrengthBar" class="password-strength h-full bg-red-500 w-1/4"></div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                            <div class="relative">
                                <input
                                    type="password"
                                    name="confirmNewPassword"
                                    id="confirmNewPassword"
                                    required
                                    class="input-focus w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] transition-all duration-300 pr-12"
                                    placeholder="••••••••"
                                >
                                <button
                                    type="button"
                                    id="toggleConfirmNewPassword"
                                    class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                >
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div id="newPasswordMatch" class="mt-2 text-sm hidden">
                                <!-- Will show match status -->
                            </div>
                        </div>

                        <button
                            type="submit"
                            class="w-full py-4 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-xl hover:shadow-xl transition-all duration-300 hover:scale-105"
                        >
                            <span id="savePasswordBtnText">Update Password</span>
                        </button>
                    </form>

                    <!-- Security Tips -->
                    <div class="mt-8 bg-blue-50 border border-blue-100 rounded-2xl p-6">
                        <div class="flex items-start">
                            <i class="fas fa-lightbulb text-[#2FA4E7] text-xl mr-4 mt-1"></i>
                            <div>
                                <h4 class="font-bold text-gray-800 mb-2">Password Tips</h4>
                                <ul class="text-gray-700 text-sm space-y-1">
                                    <li>• Use at least 8 characters</li>
                                    <li>• Include uppercase and lowercase letters</li>
                                    <li>• Add numbers and special characters</li>
                                    <li>• Avoid using personal information</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Guest Actions -->
                <div class="text-center">
                    <p class="text-gray-600 mb-4">Remember your password?</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="index.php?page=login" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all duration-300">
                            <i class="fas fa-sign-in-alt mr-2"></i> Sign In
                        </a>
                        <a href="index.php" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all duration-300">
                            <i class="fas fa-home mr-2"></i> Go to Home
                        </a>
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

                    <h3 class="text-2xl font-bold text-gray-800 mb-4" id="successTitle">Password Updated!</h3>
                    <p class="text-gray-600 mb-8" id="successMessage">
                        Your password has been updated successfully. You can now sign in with your new password.
                    </p>

                    <button
                        id="closeSuccessModal"
                        class="w-full py-4 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-xl hover:shadow-xl transition-all duration-300 hover:scale-105"
                    >
                        Go to Login
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // DOM Elements
        const successModal = document.getElementById('successModal');
        const closeSuccessModal = document.getElementById('closeSuccessModal');
        const resetPasswordForm = document.getElementById('resetPasswordForm');

        // Password Toggle Elements
        const toggleNewPassword = document.getElementById('toggleNewPassword');
        const toggleConfirmNewPassword = document.getElementById('toggleConfirmNewPassword');

        // Get token from URL
        const urlParams = new URLSearchParams(window.location.search);
        const resetToken = urlParams.get('token');

        // Close Success Modal
        closeSuccessModal.addEventListener('click', () => {
            closeModal(successModal);
            window.location.href = 'index.php?page=login';
        });

        // Reset Password Form Submission
        resetPasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!resetToken) {
                showNotification('Invalid reset link. Please request a new password reset.', 'error');
                return;
            }

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Updating...';
            submitBtn.disabled = true;

            const formData = new FormData(this);
            const data = {
                token: resetToken,
                password: formData.get('newPassword')
            };

            fetch('api/reset_password.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    submitBtn.innerHTML = '<i class="fas fa-check mr-2"></i> Updated!';

                    // Close reset password modal
                    setTimeout(() => {
                        // Show success modal
                        document.getElementById('successTitle').textContent = 'Password Updated!';
                        document.getElementById('successMessage').textContent = result.message;
                        openModal(successModal);

                        // Reset button
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }, 1000);
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
        setupPasswordToggle('toggleNewPassword', 'newPassword');
        setupPasswordToggle('toggleConfirmNewPassword', 'confirmNewPassword');

        // Password Strength Checker
        function checkPasswordStrength(password, strengthBarId, strengthTextId) {
            const strengthBar = document.getElementById(strengthBarId);
            const strengthText = document.getElementById(strengthTextId);

            let strength = 0;

            // Length check
            if (password.length >= 8) strength += 25;

            // Contains lowercase
            if (/[a-z]/.test(password)) strength += 25;

            // Contains uppercase
            if (/[A-Z]/.test(password)) strength += 25;

            // Contains numbers or symbols
            if (/[0-9]/.test(password) || /[^A-Za-z0-9]/.test(password)) strength += 25;

            // Update UI
            strengthBar.style.width = `${strength}%`;

            if (strength <= 25) {
                strengthBar.style.backgroundColor = '#EF4444';
                strengthText.textContent = 'Weak';
                strengthText.style.color = '#EF4444';
            } else if (strength <= 50) {
                strengthBar.style.backgroundColor = '#F59E0B';
                strengthText.textContent = 'Fair';
                strengthText.style.color = '#F59E0B';
            } else if (strength <= 75) {
                strengthBar.style.backgroundColor = '#3B82F6';
                strengthText.textContent = 'Good';
                strengthText.style.color = '#3B82F6';
            } else {
                strengthBar.style.backgroundColor = '#10B981';
                strengthText.textContent = 'Strong';
                strengthText.style.color = '#10B981';
            }
        }

        // Password Match Checker
        function checkPasswordMatch(passwordId, confirmPasswordId, matchElementId) {
            const password = document.getElementById(passwordId).value;
            const confirmPassword = document.getElementById(confirmPasswordId).value;
            const matchElement = document.getElementById(matchElementId);

            if (!password || !confirmPassword) {
                matchElement.classList.add('hidden');
                return;
            }

            if (password === confirmPassword) {
                matchElement.innerHTML = '<i class="fas fa-check text-green-500 mr-2"></i> <span class="text-green-600">Passwords match</span>';
                matchElement.classList.remove('hidden');
                return true;
            } else {
                matchElement.innerHTML = '<i class="fas fa-times text-red-500 mr-2"></i> <span class="text-red-600">Passwords do not match</span>';
                matchElement.classList.remove('hidden');
                return false;
            }
        }

        // Setup password strength and match checking
        document.getElementById('newPassword').addEventListener('input', function() {
            checkPasswordStrength(this.value, 'newPasswordStrengthBar', 'newPasswordStrengthText');
            checkPasswordMatch('newPassword', 'confirmNewPassword', 'newPasswordMatch');
        });

        document.getElementById('confirmNewPassword').addEventListener('input', function() {
            checkPasswordMatch('newPassword', 'confirmNewPassword', 'newPasswordMatch');
        });

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
        [successModal].forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    const closeBtn = this.querySelector('button[id^="close"]');
                    if (closeBtn) closeBtn.click();
                }
            });
        });

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            if (!resetToken) {
                showNotification('Invalid reset link. Please request a new password reset.', 'error');
                setTimeout(() => {
                    window.location.href = 'index.php?page=login';
                }, 3000);
            }
        });
    </script>
</body>
</html>