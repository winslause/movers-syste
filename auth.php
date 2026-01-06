<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication | Rheaspark</title>
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
        
        /* Tab Animation */
        .tab-content {
            display: none;
            animation: fadeIn 0.5s ease;
        }
        
        .tab-content.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        /* User Type Selection */
        .user-type-card {
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }
        
        .user-type-card:hover {
            transform: translateY(-5px);
            border-color: rgba(47, 164, 231, 0.2);
        }
        
        .user-type-card.selected {
            border-color: #2FA4E7;
            background: linear-gradient(135deg, rgba(47, 164, 231, 0.05), rgba(60, 179, 113, 0.05));
        }
        
        /* Social Login Buttons */
        .social-btn {
            transition: all 0.3s ease;
            border: 1px solid #e5e7eb;
        }
        
        .social-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        /* Form Step Indicator */
        .form-step {
            position: relative;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .form-step.active {
            background: linear-gradient(90deg, #2FA4E7, #3CB371);
            color: white;
            transform: scale(1.1);
        }
        
        .form-step.completed {
            background: #3CB371;
            color: white;
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
                    <span class="block text-gray-800">Welcome to</span>
                    <span class="brand-gradient">Rheaspark</span>
                </h2>
                
                <p class="text-xl text-gray-600 mb-12 leading-relaxed">
                    Join Kenya's most trusted housing and relocation platform. 
                    Whether you're looking for a home, listing a property, or providing moving services, 
                    create your account to get started.
                </p>
                
                <!-- Features -->
                <div class="space-y-6 mb-12">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-4">
                            <i class="fas fa-shield-alt text-green-600"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Verified Properties</h4>
                            <p class="text-gray-600">100% manually verified listings</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                            <i class="fas fa-handshake text-blue-600"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Trusted Partners</h4>
                            <p class="text-gray-600">Vetted landlords and moving services</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-4">
                            <i class="fas fa-lock text-green-600"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Secure & Private</h4>
                            <p class="text-gray-600">Your data is protected with encryption</p>
                        </div>
                    </div>
                </div>
                
                <!-- Auth Stats -->
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-sm">
                    <div class="grid grid-cols-3 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-[#2FA4E7]">10K+</div>
                            <div class="text-sm text-gray-600">Users</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-[#3CB371]">5K+</div>
                            <div class="text-sm text-gray-600">Properties</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-[#2FA4E7]">98%</div>
                            <div class="text-sm text-gray-600">Satisfaction</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Auth Cards -->
            <div class="space-y-8">
                <!-- Login Card -->
                <div class="bg-white rounded-3xl shadow-xl p-8 pulse-glow">
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Welcome Back</h3>
                    <p class="text-gray-600 mb-6">Sign in to your account to continue</p>
                    
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
                        <button class="social-btn p-4 rounded-xl flex items-center justify-center hover:bg-gray-50">
                            <i class="fab fa-google text-red-500 text-xl mr-3"></i>
                            <span class="font-medium">Google</span>
                        </button>
                        <button class="social-btn p-4 rounded-xl flex items-center justify-center hover:bg-gray-50">
                            <i class="fab fa-facebook text-blue-600 text-xl mr-3"></i>
                            <span class="font-medium">Facebook</span>
                        </button>
                    </div>
                    
                    <!-- Sign Up Link -->
                    <div class="text-center">
                        <span class="text-gray-600">Don't have an account?</span>
                        <button id="showRegisterBtn" class="ml-2 text-[#2FA4E7] font-semibold hover:text-[#3CB371] transition-colors duration-300">
                            Sign up here
                        </button>
                    </div>
                </div>
                
                <!-- Guest Actions -->
                <div class="text-center">
                    <p class="text-gray-600 mb-4">Or continue as a guest to</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <button class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all duration-300">
                            <i class="fas fa-search mr-2"></i> Browse Properties
                        </button>
                        <button class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all duration-300">
                            <i class="fas fa-info-circle mr-2"></i> Learn More
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Register Modal -->
    <div id="registerModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-3xl w-full max-w-2xl max-h-[90vh] overflow-y-auto modal-enter">
                <!-- Modal Header -->
                <div class="sticky top-0 z-10 bg-white border-b border-gray-100 p-6 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-user-plus text-[#2FA4E7] mr-3"></i>
                        Create Your Account
                    </h2>
                    <button id="closeRegisterModal" class="text-gray-500 hover:text-gray-800 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <!-- Modal Content -->
                <div class="p-6 overflow-y-auto">
                    <!-- Step Indicator -->
                    <div class="flex justify-center mb-10">
                        <div class="flex items-center">
                            <div class="flex items-center">
                                <div class="form-step active" data-step="1">1</div>
                                <div class="w-16 h-1 bg-gray-200 mx-2"></div>
                            </div>
                            <div class="flex items-center">
                                <div class="form-step" data-step="2">2</div>
                                <div class="w-16 h-1 bg-gray-200 mx-2"></div>
                            </div>
                            <div class="flex items-center">
                                <div class="form-step" data-step="3">3</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 1: User Type Selection -->
                    <div id="step1" class="tab-content active">
                        <h3 class="text-xl font-bold text-gray-800 mb-6 text-center">Who are you joining as?</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                            <!-- House Seeker -->
                            <div class="user-type-card bg-white rounded-2xl p-6 border-2 cursor-pointer text-center" data-user-type="seeker">
                                <div class="w-20 h-20 rounded-full bg-gradient-to-r from-[#2FA4E7] to-blue-100 flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-search text-3xl text-[#2FA4E7]"></i>
                                </div>
                                <h4 class="font-bold text-gray-800 text-lg mb-2">House Seeker</h4>
                                <p class="text-gray-600 text-sm mb-4">Looking for rental properties</p>
                                <ul class="text-left text-sm text-gray-600 space-y-2">
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-[#3CB371] mr-2 mt-1 text-xs"></i>
                                        <span>Browse verified listings</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-[#3CB371] mr-2 mt-1 text-xs"></i>
                                        <span>Access landlord contacts</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-[#3CB371] mr-2 mt-1 text-xs"></i>
                                        <span>Find moving services</span>
                                    </li>
                                </ul>
                            </div>
                            
                            <!-- Landlord -->
                            <div class="user-type-card bg-white rounded-2xl p-6 border-2 cursor-pointer text-center" data-user-type="landlord">
                                <div class="w-20 h-20 rounded-full bg-gradient-to-r from-[#3CB371] to-green-100 flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-home text-3xl text-[#3CB371]"></i>
                                </div>
                                <h4 class="font-bold text-gray-800 text-lg mb-2">Landlord</h4>
                                <p class="text-gray-600 text-sm mb-4">Listing rental properties</p>
                                <ul class="text-left text-sm text-gray-600 space-y-2">
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-[#2FA4E7] mr-2 mt-1 text-xs"></i>
                                        <span>Free listing (Year One)</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-[#2FA4E7] mr-2 mt-1 text-xs"></i>
                                        <span>Verified badge for trust</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-[#2FA4E7] mr-2 mt-1 text-xs"></i>
                                        <span>Property management tools</span>
                                    </li>
                                </ul>
                            </div>
                            
                            <!-- Mover -->
                            <div class="user-type-card bg-white rounded-2xl p-6 border-2 cursor-pointer text-center" data-user-type="mover">
                                <div class="w-20 h-20 rounded-full bg-gradient-to-r from-[#2FA4E7] to-blue-100 flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-truck text-3xl text-[#2FA4E7]"></i>
                                </div>
                                <h4 class="font-bold text-gray-800 text-lg mb-2">Moving Service</h4>
                                <p class="text-gray-600 text-sm mb-4">Providing relocation services</p>
                                <ul class="text-left text-sm text-gray-600 space-y-2">
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-[#3CB371] mr-2 mt-1 text-xs"></i>
                                        <span>Get moving job requests</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-[#3CB371] mr-2 mt-1 text-xs"></i>
                                        <span>Verified service badge</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-[#3CB371] mr-2 mt-1 text-xs"></i>
                                        <span>10% commission per job</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <button id="nextStep1" class="px-8 py-4 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-xl hover:shadow-xl transition-all duration-300 hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                                Next: Account Details <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Step 2: Account Details -->
                    <div id="step2" class="tab-content">
                        <h3 class="text-xl font-bold text-gray-800 mb-6 text-center">Create Your Account</h3>
                        
                        <form id="registerForm" class="space-y-6">
                            <!-- User Type Display -->
                            <div class="text-center mb-6">
                                <div id="selectedUserTypeBadge" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold">
                                    <!-- Will be filled by JavaScript -->
                                </div>
                            </div>
                            
                            <!-- Two Column Layout -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                    <input 
                                        type="text" 
                                        name="firstName"
                                        required
                                        class="input-focus w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] transition-all duration-300"
                                        placeholder="John"
                                    >
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                    <input 
                                        type="text" 
                                        name="lastName"
                                        required
                                        class="input-focus w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] transition-all duration-300"
                                        placeholder="Doe"
                                    >
                                </div>
                            </div>
                            
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
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input 
                                    type="tel" 
                                    name="phone"
                                    required
                                    class="input-focus w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] transition-all duration-300"
                                    placeholder="+254 712 345 678"
                                >
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Create Password</label>
                                <div class="relative">
                                    <input 
                                        type="password" 
                                        name="password"
                                        id="registerPassword"
                                        required
                                        class="input-focus w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] transition-all duration-300 pr-12"
                                        placeholder="••••••••"
                                    >
                                    <button 
                                        type="button" 
                                        id="toggleRegisterPassword"
                                        class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                
                                <!-- Password Strength -->
                                <div class="mt-3">
                                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                                        <span>Password strength</span>
                                        <span id="passwordStrengthText">Weak</span>
                                    </div>
                                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div id="passwordStrengthBar" class="password-strength h-full bg-red-500 w-1/4"></div>
                                    </div>
                                    <div class="mt-2 text-xs text-gray-500">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Use at least 8 characters with a mix of letters, numbers & symbols
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                                <div class="relative">
                                    <input 
                                        type="password" 
                                        name="confirmPassword"
                                        id="confirmPassword"
                                        required
                                        class="input-focus w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] transition-all duration-300 pr-12"
                                        placeholder="••••••••"
                                    >
                                    <button 
                                        type="button" 
                                        id="toggleConfirmPassword"
                                        class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div id="passwordMatch" class="mt-2 text-sm hidden">
                                    <!-- Will show match status -->
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <input 
                                    type="checkbox" 
                                    id="agreeTerms"
                                    name="agreeTerms"
                                    required
                                    class="mt-1 mr-3 w-4 h-4 text-[#2FA4E7] border-gray-300 rounded focus:ring-[#2FA4E7]"
                                >
                                <label for="agreeTerms" class="text-sm text-gray-700">
                                    I agree to the <a href="#" class="text-[#2FA4E7] font-medium">Terms of Service</a> and <a href="#" class="text-[#2FA4E7] font-medium">Privacy Policy</a>. I understand that my data will be processed in accordance with Rheaspark's data protection policies.
                                </label>
                            </div>
                            
                            <div class="flex items-start">
                                <input 
                                    type="checkbox" 
                                    id="marketingConsent"
                                    name="marketingConsent"
                                    class="mt-1 mr-3 w-4 h-4 text-[#3CB371] border-gray-300 rounded focus:ring-[#3CB371]"
                                >
                                <label for="marketingConsent" class="text-sm text-gray-700">
                                    I'd like to receive updates about new features, properties, and special offers from Rheaspark. (Optional)
                                </label>
                            </div>
                        </form>
                        
                        <div class="flex justify-between mt-10">
                            <button id="prevStep2" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all duration-300">
                                <i class="fas fa-arrow-left mr-2"></i> Back
                            </button>
                            <button id="nextStep2" class="px-8 py-3 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-xl hover:shadow-xl transition-all duration-300 hover:scale-105">
                                Next: Complete Profile <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Step 3: Profile Completion -->
                    <div id="step3" class="tab-content">
                        <h3 class="text-xl font-bold text-gray-800 mb-6 text-center">Complete Your Profile</h3>
                        
                        <div class="text-center mb-10">
                            <div class="w-32 h-32 rounded-full bg-gradient-to-r from-blue-100 to-green-100 flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-user-circle text-6xl text-gray-400"></i>
                            </div>
                            <button class="text-[#2FA4E7] font-medium hover:text-[#3CB371] transition-colors duration-300">
                                <i class="fas fa-camera mr-2"></i> Upload Profile Photo
                            </button>
                            <p class="text-sm text-gray-500 mt-2">Optional - You can add this later</p>
                        </div>
                        
                        <!-- Additional Info Based on User Type -->
                        <div id="additionalInfo" class="space-y-6 mb-10">
                            <!-- Will be filled by JavaScript based on user type -->
                        </div>
                        
                        <!-- Referral Code -->
                        
                        
                        <!-- Verification Note -->
                        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6 mb-10">
                            <div class="flex items-start">
                                <i class="fas fa-shield-alt text-[#2FA4E7] text-xl mr-4 mt-1"></i>
                                <div>
                                    <h4 class="font-bold text-gray-800 mb-2">Account Verification</h4>
                                    <p class="text-gray-700 text-sm">
                                        After registration, you'll receive a verification email. 
                                        You must verify your email address within 24 hours to activate your account 
                                        and access all platform features.
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between">
                            <button id="prevStep3" class="px-6 py-3 border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all duration-300">
                                <i class="fas fa-arrow-left mr-2"></i> Back
                            </button>
                            <button id="submitRegistration" class="px-8 py-3 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-xl hover:shadow-xl transition-all duration-300 hover:scale-105">
                                <i class="fas fa-user-plus mr-2"></i> Create Account
                            </button>
                        </div>
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
    
    <!-- Reset Password Modal -->
    <div id="resetPasswordModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl w-full max-w-md overflow-y-auto modal-enter">
                <!-- Modal Header -->
                <div class="p-6 border-b border-gray-100">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                <i class="fas fa-lock-open text-green-600"></i>
                            </div>
                            Create New Password
                        </h2>
                        <button id="closeResetPasswordModal" class="text-gray-500 hover:text-gray-800 text-2xl">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Modal Content -->
                <div class="p-6">
                    <p class="text-gray-600 mb-8">
                        Please enter your new password. Make sure it's strong and different from your previous passwords.
                    </p>
                    
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
                        Your account has been created successfully.
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
        const registerModal = document.getElementById('registerModal');
        const forgotPasswordModal = document.getElementById('forgotPasswordModal');
        const resetPasswordModal = document.getElementById('resetPasswordModal');
        const successModal = document.getElementById('successModal');
        const showRegisterBtn = document.getElementById('showRegisterBtn');
        const closeRegisterModal = document.getElementById('closeRegisterModal');
        const forgotPasswordBtn = document.getElementById('forgotPasswordBtn');
        const closeForgotPasswordModal = document.getElementById('closeForgotPasswordModal');
        const closeResetPasswordModal = document.getElementById('closeResetPasswordModal');
        const closeSuccessModal = document.getElementById('closeSuccessModal');
        const backToLogin = document.getElementById('backToLogin');
        const loginForm = document.getElementById('loginForm');
        const forgotPasswordForm = document.getElementById('forgotPasswordForm');
        const resetPasswordForm = document.getElementById('resetPasswordForm');
        
        // Registration Step Elements
        const nextStep1 = document.getElementById('nextStep1');
        const prevStep2 = document.getElementById('prevStep2');
        const nextStep2 = document.getElementById('nextStep2');
        const prevStep3 = document.getElementById('prevStep3');
        const submitRegistration = document.getElementById('submitRegistration');
        const userTypeCards = document.querySelectorAll('.user-type-card');
        const formSteps = document.querySelectorAll('.form-step');
        
        // Password Toggle Elements
        const toggleLoginPassword = document.getElementById('toggleLoginPassword');
        const toggleRegisterPassword = document.getElementById('toggleRegisterPassword');
        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const toggleNewPassword = document.getElementById('toggleNewPassword');
        const toggleConfirmNewPassword = document.getElementById('toggleConfirmNewPassword');
        
        // State Variables
        let selectedUserType = null;
        let currentStep = 1;
        
        // Open Register Modal
        showRegisterBtn.addEventListener('click', () => {
            openModal(registerModal);
            resetRegistrationForm();
        });
        
        // Close Register Modal
        closeRegisterModal.addEventListener('click', () => {
            closeModal(registerModal);
        });
        
        // Open Forgot Password Modal
        forgotPasswordBtn.addEventListener('click', () => {
            closeModal(registerModal); // Close any open register modal
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
        
        // Close Reset Password Modal
        closeResetPasswordModal.addEventListener('click', () => {
            closeModal(resetPasswordModal);
        });
        
        // Close Success Modal
        closeSuccessModal.addEventListener('click', () => {
            closeModal(successModal);
        });
        
        // User Type Selection
        userTypeCards.forEach(card => {
            card.addEventListener('click', function() {
                // Remove selection from all cards
                userTypeCards.forEach(c => {
                    c.classList.remove('selected');
                    c.style.borderColor = '';
                });
                
                // Add selection to clicked card
                this.classList.add('selected');
                this.style.borderColor = this.dataset.userType === 'landlord' ? '#3CB371' : '#2FA4E7';
                
                // Set selected user type
                selectedUserType = this.dataset.userType;
                
                // Enable next button
                nextStep1.disabled = false;
                
                // Update badge for step 2
                updateUserTypeBadge();
                
                // Update additional info for step 3
                updateAdditionalInfo();
            });
        });
        
        // Update User Type Badge
        function updateUserTypeBadge() {
            const badge = document.getElementById('selectedUserTypeBadge');
            let badgeContent = '';
            
            switch(selectedUserType) {
                case 'seeker':
                    badgeContent = `
                        <i class="fas fa-search mr-2"></i>
                        <span>House Seeker</span>
                    `;
                    badge.style.background = 'linear-gradient(90deg, #2FA4E7, #3CB371)';
                    badge.style.color = 'white';
                    break;
                case 'landlord':
                    badgeContent = `
                        <i class="fas fa-home mr-2"></i>
                        <span>Landlord</span>
                    `;
                    badge.style.background = 'linear-gradient(90deg, #3CB371, #2FA4E7)';
                    badge.style.color = 'white';
                    break;
                case 'mover':
                    badgeContent = `
                        <i class="fas fa-truck mr-2"></i>
                        <span>Moving Service</span>
                    `;
                    badge.style.background = 'linear-gradient(90deg, #2FA4E7, #3CB371)';
                    badge.style.color = 'white';
                    break;
            }
            
            badge.innerHTML = badgeContent;
        }
        
        // Update Additional Info Based on User Type
        function updateAdditionalInfo() {
            const additionalInfo = document.getElementById('additionalInfo');
            let content = '';
            
            switch(selectedUserType) {
                case 'seeker':
                    content = `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Locations</label>
                            <input 
                                type="text" 
                                name="preferredLocations"
                                class="input-focus w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] transition-all duration-300"
                                placeholder="e.g., Westlands, Kilimani, Kileleshwa"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Budget (KES/month)</label>
                            <input 
                                type="number" 
                                name="maxBudget"
                                class="input-focus w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] transition-all duration-300"
                                placeholder="e.g., 50000"
                            >
                        </div>
                    `;
                    break;
                    
                case 'landlord':
                    content = `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Property Count</label>
                            <select name="propertyCount" class="input-focus w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:border-[#3CB371] transition-all duration-300">
                                <option value="">Select number of properties</option>
                                <option value="1">1 Property</option>
                                <option value="2-5">2-5 Properties</option>
                                <option value="6-10">6-10 Properties</option>
                                <option value="10+">10+ Properties</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Primary Location</label>
                            <input 
                                type="text" 
                                name="primaryLocation"
                                class="input-focus w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:border-[#3CB371] transition-all duration-300"
                                placeholder="e.g., Westlands, Nairobi"
                            >
                        </div>
                    `;
                    break;
                    
                case 'mover':
                    content = `
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                            <input 
                                type="text" 
                                name="companyName"
                                class="input-focus w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] transition-all duration-300"
                                placeholder="e.g., SafeMove Relocations"
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Service Areas</label>
                            <input 
                                type="text" 
                                name="serviceAreas"
                                class="input-focus w-full p-4 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] transition-all duration-300"
                                placeholder="e.g., Nairobi, Kiambu, Machakos"
                            >
                        </div>
                    `;
                    break;
            }
            
            additionalInfo.innerHTML = content;
        }
        
        // Registration Step Navigation
        nextStep1.addEventListener('click', () => {
            navigateToStep(2);
        });
        
        prevStep2.addEventListener('click', () => {
            navigateToStep(1);
        });
        
        nextStep2.addEventListener('click', () => {
            // Validate step 2 form before proceeding
            if (validateStep2()) {
                navigateToStep(3);
            }
        });
        
        prevStep3.addEventListener('click', () => {
            navigateToStep(2);
        });
        
        // Navigate to Step
        function navigateToStep(step) {
            // Hide all steps
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Show target step
            document.getElementById(`step${step}`).classList.add('active');
            
            // Update step indicators
            formSteps.forEach(formStep => {
                const stepNum = parseInt(formStep.dataset.step);
                
                if (stepNum < step) {
                    formStep.classList.remove('active');
                    formStep.classList.add('completed');
                } else if (stepNum === step) {
                    formStep.classList.add('active');
                    formStep.classList.remove('completed');
                } else {
                    formStep.classList.remove('active', 'completed');
                }
            });
            
            currentStep = step;
        }
        
        // Validate Step 2
        function validateStep2() {
            const form = document.getElementById('registerForm');
            const inputs = form.querySelectorAll('input[required]');
            let isValid = true;
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.style.borderColor = '#F87171';
                    setTimeout(() => {
                        input.style.borderColor = '';
                    }, 3000);
                }
            });
            
            // Check password match
            const password = document.getElementById('registerPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (password !== confirmPassword) {
                isValid = false;
                showPasswordMatch(false);
            }
            
            // Check terms agreement
            if (!document.getElementById('agreeTerms').checked) {
                isValid = false;
                document.getElementById('agreeTerms').nextElementSibling.style.color = '#F87171';
                setTimeout(() => {
                    document.getElementById('agreeTerms').nextElementSibling.style.color = '';
                }, 3000);
            }
            
            if (!isValid) {
                showNotification('Please fill all required fields correctly.', 'error');
            }
            
            return isValid;
        }
        
        // Submit Registration
        submitRegistration.addEventListener('click', () => {
            const submitBtn = submitRegistration;
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Creating Account...';
            submitBtn.disabled = true;

            // Collect form data
            const registerForm = document.getElementById('registerForm');
            const formData = new FormData(registerForm);
            const data = {
                firstName: formData.get('firstName'),
                lastName: formData.get('lastName'),
                email: formData.get('email'),
                phone: formData.get('phone'),
                password: formData.get('password'),
                userType: selectedUserType
            };

            // Add user type specific data
            switch(selectedUserType) {
                case 'seeker':
                    data.preferredLocations = formData.get('preferredLocations');
                    data.maxBudget = formData.get('maxBudget');
                    break;
                case 'landlord':
                    data.propertyCount = formData.get('propertyCount');
                    data.primaryLocation = formData.get('primaryLocation');
                    break;
                case 'mover':
                    data.companyName = formData.get('companyName');
                    data.serviceAreas = formData.get('serviceAreas');
                    break;
            }

            fetch('api/register.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    submitBtn.innerHTML = '<i class="fas fa-check mr-2"></i> Account Created!';

                    // Close register modal
                    setTimeout(() => {
                        closeModal(registerModal);

                        // Show success modal
                        document.getElementById('successTitle').textContent = 'Account Created!';
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

                        // Redirect to profile
                        window.location.href = 'index.php?page=profile';
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
        
        // Reset Password Form Submission
        resetPasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Updating...';
            submitBtn.disabled = true;

            const formData = new FormData(this);
            const data = {
                token: formData.get('token'), // Hidden field
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
                        closeModal(resetPasswordModal);

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
        setupPasswordToggle('toggleLoginPassword', 'loginPassword');
        setupPasswordToggle('toggleRegisterPassword', 'registerPassword');
        setupPasswordToggle('toggleConfirmPassword', 'confirmPassword');
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
        document.getElementById('registerPassword').addEventListener('input', function() {
            checkPasswordStrength(this.value, 'passwordStrengthBar', 'passwordStrengthText');
            checkPasswordMatch('registerPassword', 'confirmPassword', 'passwordMatch');
        });
        
        document.getElementById('confirmPassword').addEventListener('input', function() {
            checkPasswordMatch('registerPassword', 'confirmPassword', 'passwordMatch');
        });
        
        document.getElementById('newPassword').addEventListener('input', function() {
            checkPasswordStrength(this.value, 'newPasswordStrengthBar', 'newPasswordStrengthText');
            checkPasswordMatch('newPassword', 'confirmNewPassword', 'newPasswordMatch');
        });
        
        document.getElementById('confirmNewPassword').addEventListener('input', function() {
            checkPasswordMatch('newPassword', 'confirmNewPassword', 'newPasswordMatch');
        });
        
        // Show password match helper
        function showPasswordMatch(isMatch) {
            const matchElement = document.getElementById('passwordMatch');
            
            if (isMatch) {
                matchElement.innerHTML = '<i class="fas fa-check text-green-500 mr-2"></i> <span class="text-green-600">Passwords match</span>';
                matchElement.classList.remove('hidden');
            } else {
                matchElement.innerHTML = '<i class="fas fa-times text-red-500 mr-2"></i> <span class="text-red-600">Passwords do not match</span>';
                matchElement.classList.remove('hidden');
            }
        }
        
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
        
        // Reset Registration Form
        function resetRegistrationForm() {
            // Reset steps
            navigateToStep(1);
            
            // Reset user type selection
            userTypeCards.forEach(card => {
                card.classList.remove('selected');
                card.style.borderColor = '';
            });
            selectedUserType = null;
            nextStep1.disabled = true;
            
            // Reset form
            document.getElementById('registerForm').reset();
            document.getElementById('selectedUserTypeBadge').innerHTML = '';
            document.getElementById('selectedUserTypeBadge').style.background = '';
            document.getElementById('selectedUserTypeBadge').style.color = '';
            
            // Reset password strength
            document.getElementById('passwordStrengthBar').style.width = '25%';
            document.getElementById('passwordStrengthBar').style.backgroundColor = '#EF4444';
            document.getElementById('passwordStrengthText').textContent = 'Weak';
            document.getElementById('passwordStrengthText').style.color = '#EF4444';
            document.getElementById('passwordMatch').classList.add('hidden');
            
            // Reset additional info
            document.getElementById('additionalInfo').innerHTML = '';
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
        [registerModal, forgotPasswordModal, resetPasswordModal, successModal].forEach(modal => {
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
                // Add hidden token field to reset form
                const tokenField = document.createElement('input');
                tokenField.type = 'hidden';
                tokenField.name = 'token';
                tokenField.value = resetToken;
                resetPasswordForm.appendChild(tokenField);

                // Open reset password modal
                openModal(resetPasswordModal);
            }

            // Add click effect to social buttons
            document.querySelectorAll('.social-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    showNotification(`Sign in with ${this.querySelector('span').textContent} coming soon!`, 'info');
                });
            });

            // Add click effect to guest buttons
            document.querySelectorAll('.guest-actions button').forEach(btn => {
                btn.addEventListener('click', function() {
                    showNotification('You can browse properties without an account!', 'info');
                });
            });
        });
    </script>
</body>
</html>