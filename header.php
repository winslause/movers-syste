<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rheaspark | Simplified House Hunting & Relocation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css" />
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        .brand-font {
            font-family: 'Playfair Display', serif;
        }

        /* Navigation Link Styling */
        .nav-link {
            position: relative;
            padding: 8px 0;
            color: #333;
            font-weight: 500;
            transition: all 0.3s ease;
            letter-spacing: 0.3px;
        }

        /* Animated Underline Effect */
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background: linear-gradient(90deg, #2FA4E7 0%, #3CB371 100%);
            transition: width 0.4s cubic-bezier(0.65, 0, 0.35, 1);
            border-radius: 2px;
        }

        .nav-link:hover {
            color: #2FA4E7;
        }

        .nav-link:hover::after {
            width: 100%;
        }

        /* Dropdown Styling */
        .dropdown-container {
            position: relative;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            width: 280px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(15px) scale(0.95);
            background: white;
            border-radius: 12px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            padding: 0;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.2);
            z-index: 100;
            border: 1px solid rgba(47, 164, 231, 0.1);
        }

        .dropdown-container:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(8px) scale(1);
        }

        .dropdown-item {
            padding: 16px 24px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            color: #444;
            transition: all 0.3s ease;
            position: relative;
            display: block;
        }

        .dropdown-item:last-child {
            border-bottom: none;
        }

        .dropdown-item:hover {
            background: linear-gradient(90deg, rgba(47, 164, 231, 0.05) 0%, rgba(60, 179, 113, 0.05) 100%);
            padding-left: 32px;
            color: #2FA4E7;
        }

        .dropdown-item::before {
            content: '';
            position: absolute;
            left: 16px;
            top: 50%;
            width: 6px;
            height: 6px;
            background: #3CB371;
            border-radius: 50%;
            transform: translateY(-50%) scale(0);
            transition: transform 0.3s ease;
        }

        .dropdown-item:hover::before {
            transform: translateY(-50%) scale(1);
        }

        /* Logo Animation */
        .logo-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(47, 164, 231, 0.2);
            transition: all 0.5s ease;
            width: 50px;
            height: 50px;
        }

        .logo-wrapper:hover {
            transform: rotate(5deg) scale(1.05);
            box-shadow: 0 12px 30px rgba(47, 164, 231, 0.3);
        }

        .logo-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 12px;
            transition: transform 0.5s ease;
        }

        .logo-wrapper:hover .logo-image {
            transform: scale(1.1);
        }

        /* Mobile Menu */
        .mobile-menu {
            position: fixed;
            top: 0;
            right: -100%;
            width: 100%;
            max-width: 320px;
            height: 100vh;
            background: white;
            box-shadow: -5px 0 30px rgba(0, 0, 0, 0.1);
            transition: right 0.5s cubic-bezier(0.77, 0.2, 0.05, 1);
            z-index: 1000;
            overflow-y: auto;
        }

        .mobile-menu.open {
            right: 0;
        }

        .mobile-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 999;
        }

        .mobile-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .mobile-dropdown {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s cubic-bezier(0.77, 0.2, 0.05, 1);
            background-color: #f9fafb;
        }

        .mobile-dropdown.open {
            max-height: 500px;
        }

        .mobile-dropdown-toggle {
            position: relative;
            padding-right: 40px;
            width: 100%;
            text-align: left;
        }

        .mobile-dropdown-toggle::after {
            content: '+';
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            font-weight: 300;
            transition: all 0.3s ease;
        }

        .mobile-dropdown-toggle.active::after {
            content: '-';
        }

        /* Mobile dropdown items spacing */
        .mobile-dropdown a {
            display: block;
            padding: 12px 20px;
            padding-left: 30px;
            color: #4b5563;
            font-size: 14px;
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.2s ease;
        }

        .mobile-dropdown a:last-child {
            border-bottom: none;
        }

        .mobile-dropdown a:hover {
            color: #2FA4E7;
            background-color: #f0f9ff;
            padding-left: 35px;
        }

        /* CTA Button Styling */
        .cta-button {
            background: linear-gradient(90deg, #2FA4E7 0%, #3CB371 100%);
            color: white;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            position: relative;
            overflow: hidden;
            transition: all 0.4s ease;
            box-shadow: 0 4px 15px rgba(47, 164, 231, 0.3);
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(47, 164, 231, 0.4);
        }

        .cta-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.7s ease;
        }

        .cta-button:hover::before {
            left: 100%;
        }

        /* Header Animation */
        .header-shadow {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        /* Brand Gradient Text */
        .brand-gradient {
            background: linear-gradient(90deg, #2FA4E7 0%, #3CB371 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Mobile menu items */
        .mobile-nav-item {
            display: block;
            padding: 16px 20px;
            color: #374151;
            font-weight: 500;
            border-bottom: 1px solid #f3f4f6;
            transition: all 0.2s ease;
        }

        .mobile-nav-item:hover {
            background-color: #f0f9ff;
            color: #2FA4E7;
        }

        /* Fix for mobile menu spacing */
        .mobile-menu-content {
            padding-bottom: 20px;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-white to-green-50 min-h-screen">
    <!-- Header -->
    <header class="sticky top-0 z-50 transition-all duration-300 header-shadow">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo and Brand -->
                <div class="flex items-center space-x-4">
                    <div class="logo-wrapper">
                        <!-- Real logo image from online source -->
                        <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80"
                             alt="Rheaspark Logo"
                             class="logo-image"
                             onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAiIGhlaWdodD0iNTAiIHZpZXdCb3g9IjAgMCA1MCA1MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjUwIiBoZWlnaHQ9IjUwIiByeD0iMTIiIGZpbGw9InVybCgjZ3JhZGllbnQwX2xpbmVhcl8xMjBfNzQpIi8+CjxwYXRoIGQ9Ik0xNiAyMEgzNFYzNEgxNlYyMFoiIGZpbGw9IndoaXRlIi8+CjxwYXRoIGQ9Ik0yMCAxNkgzNFYyMEgyMFYxNloiIGZpbGw9IndoaXRlIi8+CjxkZWZzPgo8bGluZWFyR3JhZGllbnQgaWQ9ImdyYWRpZW50MF9saW5lYXJfMTIwXzc0IiB4MT0iMCIgeTE9IjAiIHgyPSI1MCIgeTI9IjUwIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+CjxzdG9wIHN0b3AtY29sb3I9IiMyRkE0RTciLz4KPHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjM0NCMzcxIi8+CjwvbGluZWFyR3JhZGllbnQ+CjwvZGVmcz4KPC9zdmc+Cg=='">
                    </div>
                    <a href="index.php" class="text-2xl font-bold brand-font">
                        <span class="brand-gradient">Rheaspark</span>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <nav class="hidden lg:flex items-center space-x-16">
                    <!-- Home -->
                    <a href="index.php" class="nav-link">
                        Home
                    </a>

                    <!-- Find a House -->
                    <div class="dropdown-container">
                        <a href="index.php?page=houses" class="nav-link">
                            Find a House
                        </a>
                        <div class="dropdown-menu">
                            <a href="index.php?page=houses" class="dropdown-item">
                                <div class="font-medium">Browse by Area</div>
                                <div class="text-sm text-gray-500 mt-1">Search houses by location with exact mapping</div>
                            </a>
                            <a href="index.php?page=houses" class="dropdown-item">
                                <div class="font-medium">Advanced Filters</div>
                                <div class="text-sm text-gray-500 mt-1">Filter by price, house type, and size</div>
                            </a>
                            <a href="index.php?page=houses" class="dropdown-item">
                                <div class="font-medium">Verified Listings</div>
                                <div class="text-sm text-gray-500 mt-1">100% verified properties with honest disclosures</div>
                            </a>
                            <a href="index.php?page=houses" class="dropdown-item">
                                <div class="font-medium">Payment Access</div>
                                <div class="text-sm text-gray-500 mt-1">One-time fee for full property details</div>
                            </a>
                        </div>
                    </div>

                    <!-- Moving Services -->
                    <div class="dropdown-container">
                        <a href="index.php?page=moving_services" class="nav-link">
                            Moving Services
                        </a>
                        <div class="dropdown-menu">
                            <a href="index.php?page=moving_services" class="dropdown-item">
                                <div class="font-medium">Find Registered Movers</div>
                                <div class="text-sm text-gray-500 mt-1">Browse trusted relocation partners</div>
                            </a>
                            <a href="index.php?page=moving_services" class="dropdown-item">
                                <div class="font-medium">Request a Move</div>
                                <div class="text-sm text-gray-500 mt-1">Direct contact or job request option</div>
                            </a>
                            <a href="index.php?page=moving_services" class="dropdown-item">
                                <div class="font-medium">Mover Profiles</div>
                                <div class="text-sm text-gray-500 mt-1">View services, capacity, and coverage areas</div>
                            </a>
                            <a href="index.php?page=moving_services" class="dropdown-item">
                                <div class="font-medium">Commission System</div>
                                <div class="text-sm text-gray-500 mt-1">10% per successful relocation job</div>
                            </a>
                        </div>
                    </div>

                    <!-- About -->
                    <div class="dropdown-container">
                        <a href="index.php?page=about" class="nav-link">
                            About
                        </a>
                        <div class="dropdown-menu">
                            <a href="index.php?page=about" class="dropdown-item">
                                <div class="font-medium">Our Mission</div>
                                <div class="text-sm text-gray-500 mt-1">Simplifying house hunting and relocation</div>
                            </a>
                            <a href="index.php?page=about" class="dropdown-item">
                                <div class="font-medium">Business Plan</div>
                                <div class="text-sm text-gray-500 mt-1">Platform roadmap and vision</div>
                            </a>
                            <a href="index.php?page=about" class="dropdown-item">
                                <div class="font-medium">How It Works</div>
                                <div class="text-sm text-gray-500 mt-1">Platform functionalities and features</div>
                            </a>
                            <a href="index.php?page=about" class="dropdown-item">
                                <div class="font-medium">Contact Support</div>
                                <div class="text-sm text-gray-500 mt-1">WhatsApp, email, and inquiry system</div>
                            </a>
                        </div>
                    </div>
                </nav>

                <!-- CTA Buttons -->
                <div class="hidden lg:flex items-center space-x-6">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- User Dropdown -->
                        <div class="dropdown-container">
                            <button class="flex items-center space-x-2 text-gray-700 font-medium hover:text-[#2FA4E7] transition-colors duration-300">
                                <span><?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            <div class="dropdown-menu" style="width: 200px; left: -100px;">
                                <a href="index.php?page=profile" class="dropdown-item">
                                    <div class="font-medium">My Profile</div>
                                    <div class="text-sm text-gray-500 mt-1">View and edit your profile</div>
                                </a>
                                <a href="index.php?page=dashboard" class="dropdown-item">
                                    <div class="font-medium">Dashboard</div>
                                    <div class="text-sm text-gray-500 mt-1">Manage your account</div>
                                </a>
                                <a href="index.php?page=settings" class="dropdown-item">
                                    <div class="font-medium">Settings</div>
                                    <div class="text-sm text-gray-500 mt-1">Account preferences</div>
                                </a>
                                <div class="border-t border-gray-100 my-2"></div>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="index.php?page=login" class="text-gray-700 font-medium hover:text-[#2FA4E7] transition-colors duration-300">
                            Log In
                        </a>
                    <?php endif; ?>
                    <a href="index.php?page=houses" class="cta-button">
                        Find Houses
                    </a>
                </div>

                <!-- Mobile Menu Toggle -->
                <button id="mobile-menu-toggle" class="lg:hidden text-gray-800 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Menu Overlay -->
    <div id="mobile-overlay" class="mobile-overlay"></div>

    <!-- Mobile Menu Sidebar -->
    <div id="mobile-menu" class="mobile-menu">
        <div class="mobile-menu-content">
            <!-- Mobile Menu Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="logo-wrapper" style="width: 40px; height: 40px;">
                        <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80"
                         alt="Rheaspark Logo"
                         class="logo-image"
                         onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAiIGhlaWdodD0iNTAiIHZpZXdCb3g9IjAgMCA1MCA1MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjUwIiBoZWlnaHQ9IjUwIiByeD0iMTIiIGZpbGw9InVybCgjZ3JhZGllbnQwX2xpbmVhcl8xMjBfNzQpIi8+CjxwYXRoIGQ9Ik0xNiAyMEgzNFYzNEgxNlYyMFoiIGZpbGw9IndoaXRlIi8+CjxwYXRoIGQ9Ik0yMCAxNkgzNFYyMEgyMFYxNloiIGZpbGw9IndoaXRlIi8+CjxkZWZzPgo8bGluZWFyR3JhZGllbnQgaWQ9ImdyYWRpZW50MF9saW5lYXJfMTIwXzc0IiB4MT0iMCIgeTE9IjAiIHgyPSI1MCIgeTI9IjUwIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+CjxzdG9wIHN0b3AtY29sb3I9IiMyRkE0RTciLz4KPHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjM0NCMzcxIi8+CjwvbGluZWFyR3JhZGllbnQ+CjwvZGVmcz4KPC9zdmc+Cg=='">
                    </div>
                    <span class="text-xl font-bold brand-font brand-gradient">Rheaspark</span>
                </div>
                <button id="mobile-menu-close" class="text-gray-500 hover:text-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Navigation -->
            <div class="space-y-0">
                <a href="index.php" class="mobile-nav-item">
                    Home
                </a>

                <!-- Find a House Dropdown -->
                <div class="border-b border-gray-100">
                    <button class="mobile-dropdown-toggle mobile-nav-item">
                        Find a House
                    </button>
                    <div class="mobile-dropdown">
                        <a href="index.php?page=houses" class="mobile-dropdown-item">
                            Browse by Area
                        </a>
                        <a href="index.php?page=houses" class="mobile-dropdown-item">
                            Advanced Filters
                        </a>
                        <a href="index.php?page=houses" class="mobile-dropdown-item">
                            Verified Listings
                        </a>
                        <a href="index.php?page=houses" class="mobile-dropdown-item">
                            Payment Access
                        </a>
                    </div>
                </div>

                <!-- Moving Services Dropdown -->
                <div class="border-b border-gray-100">
                    <button class="mobile-dropdown-toggle mobile-nav-item">
                        Moving Services
                    </button>
                    <div class="mobile-dropdown">
                        <a href="index.php?page=moving_services" class="mobile-dropdown-item">
                            Find Registered Movers
                        </a>
                        <a href="index.php?page=moving_services" class="mobile-dropdown-item">
                            Request a Move
                        </a>
                        <a href="index.php?page=moving_services" class="mobile-dropdown-item">
                            Mover Profiles
                        </a>
                        <a href="index.php?page=moving_services" class="mobile-dropdown-item">
                            Commission System
                        </a>
                    </div>
                </div>

                <!-- About Dropdown -->
                <div class="border-b border-gray-100">
                    <button class="mobile-dropdown-toggle mobile-nav-item">
                        About
                    </button>
                    <div class="mobile-dropdown">
                        <a href="index.php?page=about" class="mobile-dropdown-item">
                            Our Mission
                        </a>
                        <a href="index.php?page=about" class="mobile-dropdown-item">
                            Business Plan
                        </a>
                        <a href="index.php?page=about" class="mobile-dropdown-item">
                            How It Works
                        </a>
                        <a href="index.php?page=about" class="mobile-dropdown-item">
                            Contact Support
                        </a>
                    </div>
                </div>
            </div>

            <!-- Mobile CTA Buttons -->
            <div class="p-6 space-y-4">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="text-center py-3 text-gray-600 font-medium border border-gray-200 rounded-lg bg-gray-50">
                        Welcome, <?php echo htmlspecialchars($_SESSION['first_name'] . ' ' . $_SESSION['last_name']); ?>
                    </div>
                    <a href="index.php?page=profile" class="block w-full py-3 text-center text-gray-700 font-medium border border-gray-300 rounded-lg hover:border-[#2FA4E7] hover:text-[#2FA4E7] transition-all duration-300">
                        My Profile
                    </a>
                    <a href="index.php?page=dashboard" class="block w-full py-3 text-center text-gray-700 font-medium border border-gray-300 rounded-lg hover:border-[#2FA4E7] hover:text-[#2FA4E7] transition-all duration-300">
                        Dashboard
                    </a>
                    <a href="index.php?page=logout" class="block w-full py-3 text-center text-gray-700 font-medium border border-gray-300 rounded-lg hover:border-red-500 hover:text-red-500 transition-all duration-300">
                        Log Out
                    </a>
                <?php else: ?>
                    <a href="index.php?page=login" class="block w-full py-3 text-center text-gray-700 font-medium border border-gray-300 rounded-lg hover:border-[#2FA4E7] hover:text-[#2FA4E7] transition-all duration-300">
                        Log In
                    </a>
                <?php endif; ?>
                <a href="index.php?page=houses" class="block w-full py-3 text-center cta-button">
                    Find Houses
                </a>
            </div>
        </div>
    </div>

    <script>
        // Mobile menu functionality
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const mobileMenuClose = document.getElementById('mobile-menu-close');
        const mobileMenu = document.getElementById('mobile-menu');
        const mobileOverlay = document.getElementById('mobile-overlay');

        // Toggle mobile menu
        mobileMenuToggle.addEventListener('click', () => {
            mobileMenu.classList.add('open');
            mobileOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });

        // Close mobile menu
        mobileMenuClose.addEventListener('click', () => {
            mobileMenu.classList.remove('open');
            mobileOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });

        mobileOverlay.addEventListener('click', () => {
            mobileMenu.classList.remove('open');
            mobileOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });

        // Mobile dropdown functionality
        document.querySelectorAll('.mobile-dropdown-toggle').forEach(toggle => {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const dropdown = this.nextElementSibling;
                const isOpen = dropdown.classList.contains('open');

                // Close all other dropdowns
                document.querySelectorAll('.mobile-dropdown').forEach(d => {
                    if (d !== dropdown) {
                        d.classList.remove('open');
                        d.previousElementSibling.classList.remove('active');
                    }
                });

                // Toggle current dropdown
                dropdown.classList.toggle('open');
                this.classList.toggle('active');
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.mobile-dropdown-toggle') && !e.target.closest('.mobile-dropdown')) {
                document.querySelectorAll('.mobile-dropdown').forEach(d => {
                    d.classList.remove('open');
                });
                document.querySelectorAll('.mobile-dropdown-toggle').forEach(t => {
                    t.classList.remove('active');
                });
            }
        });

        // Add scroll effect to header
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header');
            if (window.scrollY > 10) {
                header.classList.add('header-shadow');
            } else {
                header.classList.remove('header-shadow');
            }
        });

        // Fallback for logo image if it fails to load
        function logoFallback(img) {
            img.onerror = null;
            img.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAiIGhlaWdodD0iNTAiIHZpZXdCb3g9IjAgMCA1MCA1MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjUwIiBoZWlnaHQ9IjUwIiByeD0iMTIiIGZpbGw9InVybCgjZ3JhZGllbnQwX2xpbmVhcl8xMjBfNzQpIi8+CjxwYXRoIGQ9Ik0xNiAyMEgzNFYzNEgxNlYyMFoiIGZpbGw9IndoaXRlIi8+CjxwYXRoIGQ9Ik0yMCAxNkgzNFYyMEgyMFYxNloiIGZpbGw9IndoaXRlIi8+CjxkZWZzPgo8bGluZWFyR3JhZGllbnQgaWQ9ImdyYWRpZW50MF9saW5lYXJfMTIwXzc0IiB4MT0iMCIgeTE9IjAiIHgyPSI1MCIgeTI9IjUwIiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+CjxzdG9wIHN0b3AtY29sb3I9IiMyRkE0RTciLz4KPHN0b3Agb2Zmc2V0PSIxIiBzdG9wLWNvbG9yPSIjM0NCMzcxIi8+CjwvbGluZWFyR3JhZGllbnQ+CjwvZGVmcz4KPC9zdmc+Cg==';
        }
    </script>
