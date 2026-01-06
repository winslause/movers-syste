<?php
require_once 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

// Get user data
$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];
$user_name = $_SESSION['first_name'] . ' ' . $_SESSION['last_name'];
$user_email = $_SESSION['email'];

// Get user details from database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Get user stats based on type
if ($user_type === 'landlord') {
    // Count properties
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM houses WHERE landlord_id = ?");
    $stmt->execute([$user_id]);
    $property_count = $stmt->fetch()['count'];

    // Count viewings (requests)
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM requests WHERE landlord_id = ?");
    $stmt->execute([$user_id]);
    $viewing_count = $stmt->fetch()['count'];
} else {
    // For house hunters, count saved properties and viewings
    $property_count = 5; // Placeholder
    $viewing_count = 12; // Placeholder
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Rheaspark</title>
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
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #2FA4E7, #3CB371);
            border-radius: 10px;
        }
        
        /* Sidebar Animation */
        .sidebar-collapse {
            transition: all 0.3s ease;
        }
        
        /* Card Hover Effects */
        .dashboard-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
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
        
        /* Profile Image Upload */
        .profile-image-container {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .profile-image-container:hover .profile-overlay {
            opacity: 1;
        }
        
        .profile-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        /* Statistics Cards */
        .stat-card {
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #2FA4E7 0%, #3CB371 100%);
        }
        
        /* Notification Badge */
        .notification-badge {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        /* Loading Skeleton */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200px 100%;
            animation: shimmer 1.5s infinite;
        }
        
        @keyframes shimmer {
            0% { background-position: -200px 0; }
            100% { background-position: calc(200px + 100%) 0; }
        }
        
        /* Switch Toggle */
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
            background-color: #ccc;
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
            background: linear-gradient(90deg, #2FA4E7 0%, #3CB371 100%);
        }
        
        input:checked + .slider:before {
            transform: translateX(30px);
        }
        
        /* Activity Timeline */
        .timeline-item {
            position: relative;
            padding-left: 30px;
            margin-bottom: 20px;
        }
        
        .timeline-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 5px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #2FA4E7;
        }
        
        .timeline-item::after {
            content: '';
            position: absolute;
            left: 5px;
            top: 17px;
            width: 2px;
            height: calc(100% + 3px);
            background: #e5e7eb;
        }
        
        .timeline-item:last-child::after {
            display: none;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    
    <!-- Dashboard Container -->
    <div class="flex">
        <!-- Sidebar -->
        <aside id="sidebar" class="sidebar-collapse w-64 bg-white shadow-xl min-h-screen fixed lg:relative lg:mr-4 z-30">
            <!-- Sidebar Header -->
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold brand-font">
                        <span class="brand-gradient">Rheaspark</span>
                    </h2>
                    <button id="sidebarToggle" class="lg:hidden text-gray-600 hover:text-gray-800">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- User Profile -->
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center space-x-4">
                    <div class="profile-image-container w-16 h-16 rounded-full overflow-hidden cursor-pointer">
                        <img
                            src="<?php echo htmlspecialchars($user['profile_image'] ? 'uploads/profiles/' . $user['profile_image'] : 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'); ?>"
                            alt="Profile"
                            class="w-full h-full object-cover"
                        >
                        <div class="profile-overlay">
                            <i class="fas fa-camera text-white text-lg"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800"><?php echo htmlspecialchars($user_name); ?></h3>
                        <div class="flex items-center mt-1">
                            <span class="px-2 py-1 bg-gradient-to-r from-[#2FA4E7]/10 to-[#3CB371]/10 text-[#3CB371] text-xs font-semibold rounded-full">
                                <i class="fas fa-user-tie mr-1"></i> <?php echo ucfirst($user_type); ?>
                            </span>
                            <span class="ml-2 px-2 py-1 bg-blue-50 text-blue-600 text-xs font-semibold rounded-full">
                                <i class="fas fa-shield-alt mr-1"></i> Verified
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Stats -->
                <div class="grid grid-cols-2 gap-3 mt-6">
                    <div class="text-center p-3 bg-blue-50 rounded-xl">
                        <div class="text-2xl font-bold text-[#2FA4E7]"><?php echo $property_count; ?></div>
                        <div class="text-xs text-gray-600"><?php echo $user_type === 'landlord' ? 'Properties' : 'Saved'; ?></div>
                    </div>
                    <div class="text-center p-3 bg-green-50 rounded-xl">
                        <div class="text-2xl font-bold text-[#3CB371]"><?php echo $viewing_count; ?></div>
                        <div class="text-xs text-gray-600"><?php echo $user_type === 'landlord' ? 'Requests' : 'Viewings'; ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Navigation Menu -->
            <nav class="p-4 overflow-y-auto flex-1">
                <div class="space-y-2">
                    <a href="#" class="tab-link flex items-center px-4 py-3 text-gray-700 hover:text-[#2FA4E7] hover:bg-blue-50 rounded-xl transition-all duration-300 active" data-tab="dashboard">
                        <i class="fas fa-tachometer-alt mr-3 text-lg"></i>
                        <span>Dashboard</span>
                    </a>

                    <?php if ($user_type === 'seeker'): ?>
                    <!-- House Hunter Menu -->
                    <div class="mb-2">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 mb-2">House Hunter</h4>
                        <a href="#" class="tab-link flex items-center px-4 py-3 text-gray-700 hover:text-[#2FA4E7] hover:bg-blue-50 rounded-xl transition-all duration-300" data-tab="saved">
                            <i class="fas fa-heart mr-3 text-lg"></i>
                            <span>Saved Properties</span>
                            <span class="ml-auto bg-red-100 text-red-600 text-xs px-2 py-1 rounded-full">5</span>
                        </a>
                        <a href="#" class="tab-link flex items-center px-4 py-3 text-gray-700 hover:text-[#2FA4E7] hover:bg-blue-50 rounded-xl transition-all duration-300" data-tab="viewings">
                            <i class="fas fa-calendar-alt mr-3 text-lg"></i>
                            <span>My Viewings</span>
                        </a>
                        <a href="#" class="tab-link flex items-center px-4 py-3 text-gray-700 hover:text-[#2FA4E7] hover:bg-blue-50 rounded-xl transition-all duration-300" data-tab="requests">
                            <i class="fas fa-paper-plane mr-3 text-lg"></i>
                            <span>My Requests</span>
                        </a>
                        <a href="#" class="tab-link flex items-center px-4 py-3 text-gray-700 hover:text-[#2FA4E7] hover:bg-blue-50 rounded-xl transition-all duration-300" data-tab="messages">
                            <i class="fas fa-envelope mr-3 text-lg"></i>
                            <span>Messages</span>
                        </a>
                        <a href="#" class="tab-link flex items-center px-4 py-3 text-gray-700 hover:text-[#2FA4E7] hover:bg-blue-50 rounded-xl transition-all duration-300" data-tab="payments">
                            <i class="fas fa-receipt mr-3 text-lg"></i>
                            <span>Payment History</span>
                        </a>
                    </div>
                    <?php else: ?>
                    <!-- Landlord Menu -->
                    <div class="mb-2">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 mb-2">Landlord</h4>
                        <a href="#" class="tab-link flex items-center px-4 py-3 text-gray-700 hover:text-[#2FA4E7] hover:bg-blue-50 rounded-xl transition-all duration-300" data-tab="properties">
                            <i class="fas fa-home mr-3 text-lg"></i>
                            <span>My Properties</span>
                            <span class="ml-auto bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full"><?php echo $property_count; ?></span>
                        </a>
                        <button id="addListingBtn" class="w-full text-left flex items-center px-4 py-3 text-gray-700 hover:text-[#2FA4E7] hover:bg-blue-50 rounded-xl transition-all duration-300">
                            <i class="fas fa-plus-circle mr-3 text-lg"></i>
                            <span>Add New Listing</span>
                        </button>
                        <a href="#" class="tab-link flex items-center px-4 py-3 text-gray-700 hover:text-[#2FA4E7] hover:bg-blue-50 rounded-xl transition-all duration-300" data-tab="inquiries">
                            <i class="fas fa-inbox mr-3 text-lg"></i>
                            <span>Inquiries</span>
                            <span class="ml-auto bg-green-100 text-green-600 text-xs px-2 py-1 rounded-full notification-badge"><?php echo $viewing_count; ?></span>
                        </a>
                        <a href="#" class="tab-link flex items-center px-4 py-3 text-gray-700 hover:text-[#2FA4E7] hover:bg-blue-50 rounded-xl transition-all duration-300" data-tab="reports">
                            <i class="fas fa-chart-bar mr-3 text-lg"></i>
                            <span>Reports & Analytics</span>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Account Settings -->
                    <div>
                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider px-4 mb-2">Account</h4>
                        <a href="#" class="tab-link flex items-center px-4 py-3 text-gray-700 hover:text-[#2FA4E7] hover:bg-blue-50 rounded-xl transition-all duration-300" data-tab="profile">
                            <i class="fas fa-user-cog mr-3 text-lg"></i>
                            <span>Profile Settings</span>
                        </a>
                        <a href="#" class="tab-link flex items-center px-4 py-3 text-gray-700 hover:text-[#2FA4E7] hover:bg-blue-50 rounded-xl transition-all duration-300" data-tab="security">
                            <i class="fas fa-shield-alt mr-3 text-lg"></i>
                            <span>Security</span>
                        </a>
                        <a href="#" class="tab-link flex items-center px-4 py-3 text-gray-700 hover:text-[#2FA4E7] hover:bg-blue-50 rounded-xl transition-all duration-300" data-tab="notifications">
                            <i class="fas fa-bell mr-3 text-lg"></i>
                            <span>Notifications</span>
                        </a>
                    </div>
                </div>
            </nav>
            
            <!-- Sidebar Footer -->
            <!-- <div class="absolute bottom-0 left-0 right-0 p-6 border-t border-gray-100">
                <a href="index.php?page=logout" class="flex items-center text-gray-700 hover:text-red-600 transition-colors duration-300">
                    <i class="fas fa-sign-out-alt mr-3"></i>
                    <span>Log Out</span>
                </a>
            </div> -->
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 lg:ml-0 p-6 lg:pl-0">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm sticky top-0 z-20">
                <div class="px-6 py-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <button id="mobileSidebarToggle" class="lg:hidden text-gray-600 hover:text-gray-800 mr-4">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h1 class="text-xl font-bold text-gray-800" id="pageTitle">Dashboard Overview</h1>
                    </div>
                    
                    <div class="flex items-center space-x-6">
                        <!-- Notification Bell -->
                        <div class="relative">
                            <button id="notificationBtn" class="text-gray-600 hover:text-gray-800 relative">
                                <i class="fas fa-bell text-xl"></i>
                                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                            </button>
                            
                            <!-- Notification Dropdown -->
                            <div id="notificationDropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-2xl border border-gray-100 hidden z-30">
                                <div class="p-4 border-b border-gray-100">
                                    <div class="flex justify-between items-center">
                                        <h3 class="font-bold text-gray-800">Notifications</h3>
                                        <button class="text-sm text-[#2FA4E7] font-semibold">Mark all as read</button>
                                    </div>
                                </div>
                                <div class="max-h-96 overflow-y-auto">
                                    <a href="#" class="block p-4 border-b border-gray-100 hover:bg-gray-50">
                                        <div class="flex">
                                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3 flex-shrink-0">
                                                <i class="fas fa-calendar-check text-blue-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800">New viewing request</p>
                                                <p class="text-sm text-gray-600">For your property in Westlands</p>
                                                <p class="text-xs text-gray-500 mt-1">2 hours ago</p>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#" class="block p-4 border-b border-gray-100 hover:bg-gray-50">
                                        <div class="flex">
                                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3 flex-shrink-0">
                                                <i class="fas fa-home text-green-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800">Property verified</p>
                                                <p class="text-sm text-gray-600">Your Kilimani apartment is now verified</p>
                                                <p class="text-xs text-gray-500 mt-1">1 day ago</p>
                                            </div>
                                        </div>
                                    </a>
                                    <a href="#" class="block p-4 border-b border-gray-100 hover:bg-gray-50">
                                        <div class="flex">
                                            <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center mr-3 flex-shrink-0">
                                                <i class="fas fa-star text-yellow-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-800">New review received</p>
                                                <p class="text-sm text-gray-600">Someone reviewed your property</p>
                                                <p class="text-xs text-gray-500 mt-1">2 days ago</p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <div class="p-4 text-center border-t border-gray-100">
                                    <a href="#" class="text-[#2FA4E7] font-semibold text-sm">View all notifications</a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- User Menu -->
                        <div class="relative">
                            <button id="userMenuBtn" class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full overflow-hidden">
                                    <img
                                        src="<?php echo htmlspecialchars($user['profile_image'] ? 'uploads/profiles/' . $user['profile_image'] : 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'); ?>"
                                        alt="Profile"
                                        class="w-full h-full object-cover"
                                    >
                                </div>
                                <div class="hidden md:block text-left">
                                    <p class="font-medium text-gray-800"><?php echo htmlspecialchars($user_name); ?></p>
                                    <p class="text-sm text-gray-600"><?php echo ucfirst($user_type); ?></p>
                                </div>
                                <i class="fas fa-chevron-down text-gray-500"></i>
                            </button>
                            
                            <!-- User Dropdown -->
                            <div id="userDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-2xl border border-gray-100 hidden z-30">
                                <div class="p-4 border-b border-gray-100">
                                    <p class="font-medium text-gray-800"><?php echo htmlspecialchars($user_name); ?></p>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($user_email); ?></p>
                                </div>
                                <div class="p-2">
                                    <a href="#" class="tab-link flex items-center px-3 py-2 text-gray-700 hover:bg-blue-50 rounded-lg transition-colors duration-300" data-tab="profile">
                                        <i class="fas fa-user-circle mr-3"></i>
                                        <span>My Profile</span>
                                    </a>
                                    <a href="#" class="tab-link flex items-center px-3 py-2 text-gray-700 hover:bg-blue-50 rounded-lg transition-colors duration-300" data-tab="settings">
                                        <i class="fas fa-cog mr-3"></i>
                                        <span>Settings</span>
                                    </a>
                                    <a href="#" class="flex items-center px-3 py-2 text-gray-700 hover:bg-blue-50 rounded-lg transition-colors duration-300">
                                        <i class="fas fa-question-circle mr-3"></i>
                                        <span>Help & Support</span>
                                    </a>
                                </div>
                                <div class="p-2 border-t border-gray-100">
                                    <a href="index.php?page=logout" class="flex items-center px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-300">
                                        <i class="fas fa-sign-out-alt mr-3"></i>
                                        <span>Log Out</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Main Content Area -->
            <main class="p-6 lg:pl-0">
                <!-- Tab Content: Dashboard -->
                <div id="dashboard" class="tab-content active">
                    <!-- Welcome Card -->
                    <div class="bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] rounded-2xl p-8 text-white mb-8 shadow-lg">
                        <div class="flex flex-col md:flex-row justify-between items-center">
                            <div class="mb-6 md:mb-0">
                                <h2 class="text-3xl font-bold mb-2">Welcome back, <?php echo htmlspecialchars($user['first_name']); ?>!</h2>
                                <p class="opacity-90">Here's what's happening with your Rheaspark account today.</p>
                            </div>
                            <div class="flex space-x-4">
                                <button class="px-6 py-3 bg-white/20 backdrop-blur-sm rounded-xl hover:bg-white/30 transition-colors duration-300">
                                    <i class="fas fa-plus mr-2"></i> Add Property
                                </button>
                                <button class="px-6 py-3 bg-white text-[#2FA4E7] font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
                                    <i class="fas fa-chart-line mr-2"></i> View Analytics
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6 mb-8">
                        <div class="dashboard-card stat-card bg-white rounded-2xl p-6 shadow-md">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-gray-600 text-sm"><?php echo $user_type === 'landlord' ? 'Total Properties' : 'Saved Properties'; ?></p>
                                    <h3 class="text-3xl font-bold text-gray-800 mt-2"><?php echo $property_count; ?></h3>
                                </div>
                                <div class="w-14 h-14 rounded-xl bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-home text-2xl text-[#2FA4E7]"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <span class="text-green-600 text-sm font-semibold"><i class="fas fa-arrow-up mr-1"></i> <?php echo $user_type === 'landlord' ? '2 this month' : '3 this week'; ?></span>
                            </div>
                        </div>

                        <div class="dashboard-card stat-card bg-white rounded-2xl p-6 shadow-md">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-gray-600 text-sm"><?php echo $user_type === 'landlord' ? 'Viewing Requests' : 'Scheduled Viewings'; ?></p>
                                    <h3 class="text-3xl font-bold text-gray-800 mt-2"><?php echo $viewing_count; ?></h3>
                                </div>
                                <div class="w-14 h-14 rounded-xl bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-calendar-alt text-2xl text-[#3CB371]"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <span class="text-green-600 text-sm font-semibold"><i class="fas fa-arrow-up mr-1"></i> <?php echo $user_type === 'landlord' ? '3 pending' : '2 confirmed'; ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent Activity & Quick Actions -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Recent Activity -->
                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-2xl shadow-md p-6 mb-8">
                                <div class="flex justify-between items-center mb-6">
                                    <h3 class="text-xl font-bold text-gray-800">Recent Activity</h3>
                                    <a href="#" class="text-[#2FA4E7] font-semibold text-sm">View all</a>
                                </div>
                                
                                <div class="space-y-1">
                                    <div class="timeline-item">
                                        <div class="bg-gray-50 rounded-xl p-4">
                                            <div class="flex justify-between">
                                                <p class="font-medium text-gray-800">New viewing request received</p>
                                                <span class="text-xs text-gray-500">Today, 10:30 AM</span>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1">For your 3-bedroom apartment in Westlands</p>
                                        </div>
                                    </div>
                                    
                                    <div class="timeline-item">
                                        <div class="bg-gray-50 rounded-xl p-4">
                                            <div class="flex justify-between">
                                                <p class="font-medium text-gray-800">Property verified by admin</p>
                                                <span class="text-xs text-gray-500">Yesterday, 3:15 PM</span>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1">Your Kilimani apartment is now verified</p>
                                        </div>
                                    </div>
                                    
                                    <div class="timeline-item">
                                        <div class="bg-gray-50 rounded-xl p-4">
                                            <div class="flex justify-between">
                                                <p class="font-medium text-gray-800">Payment received</p>
                                                <span class="text-xs text-gray-500">2 days ago</span>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1">KES 200 for property access from a house hunter</p>
                                        </div>
                                    </div>
                                    
                                    <div class="timeline-item">
                                        <div class="bg-gray-50 rounded-xl p-4">
                                            <div class="flex justify-between">
                                                <p class="font-medium text-gray-800">New property listed</p>
                                                <span class="text-xs text-gray-500">3 days ago</span>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1">You added a new property in Kileleshwa</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Properties Overview -->
                            <div class="bg-white rounded-2xl shadow-md p-6">
                                <div class="flex justify-between items-center mb-6">
                                    <h3 class="text-xl font-bold text-gray-800">My Properties</h3>
                                    <a href="#" class="text-[#2FA4E7] font-semibold text-sm">Manage all</a>
                                </div>

                                <div id="userPropertiesTable" class="overflow-x-auto">
                                    <!-- Properties table will be loaded here -->
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Actions & Stats -->
                        <div>
                            <!-- Quick Actions -->
                            <div class="bg-white rounded-2xl shadow-md p-6 mb-8">
                                <h3 class="text-xl font-bold text-gray-800 mb-6">Quick Actions</h3>
                                
                                <div class="space-y-4">
                                    <a href="#" class="tab-link flex items-center p-4 bg-blue-50 text-[#2FA4E7] rounded-xl hover:bg-blue-100 transition-all duration-300" data-tab="listings">
                                        <div class="w-12 h-12 rounded-lg bg-white flex items-center justify-center mr-4">
                                            <i class="fas fa-plus text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold">Add New Listing</p>
                                            <p class="text-sm text-gray-600">List a property for free</p>
                                        </div>
                                    </a>
                                    
                                    <a href="#" class="tab-link flex items-center p-4 bg-green-50 text-[#3CB371] rounded-xl hover:bg-green-100 transition-all duration-300" data-tab="inquiries">
                                        <div class="w-12 h-12 rounded-lg bg-white flex items-center justify-center mr-4">
                                            <i class="fas fa-inbox text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold">View Inquiries</p>
                                            <p class="text-sm text-gray-600">3 new messages</p>
                                        </div>
                                    </a>
                                    
                                    <a href="#" class="tab-link flex items-center p-4 bg-blue-50 text-[#2FA4E7] rounded-xl hover:bg-blue-100 transition-all duration-300" data-tab="reports">
                                        <div class="w-12 h-12 rounded-lg bg-white flex items-center justify-center mr-4">
                                            <i class="fas fa-chart-pie text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold">Generate Report</p>
                                            <p class="text-sm text-gray-600">View performance analytics</p>
                                        </div>
                                    </a>
                                    
                                    <a href="#" class="tab-link flex items-center p-4 bg-green-50 text-[#3CB371] rounded-xl hover:bg-green-100 transition-all duration-300" data-tab="profile">
                                        <div class="w-12 h-12 rounded-lg bg-white flex items-center justify-center mr-4">
                                            <i class="fas fa-user-edit text-xl"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold">Edit Profile</p>
                                            <p class="text-sm text-gray-600">Update your information</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Account Status -->
                            <div class="bg-gradient-to-br from-[#2FA4E7] to-[#3CB371] rounded-2xl shadow-md p-6 text-white">
                                <h3 class="text-xl font-bold mb-6">Account Status</h3>
                                
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center">
                                        <span>Verification Status</span>
                                        <span class="font-bold">Verified ✓</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span>Member Since</span>
                                        <span class="font-bold">Jan 2023</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span>Subscription</span>
                                        <span class="font-bold">Free Plan</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span>Properties Listed</span>
                                        <span class="font-bold">5/∞ Free</span>
                                    </div>
                                </div>
                                
                                <div class="mt-8">
                                    <div class="flex items-center justify-between mb-2">
                                        <span>Profile Completeness</span>
                                        <span class="font-bold">85%</span>
                                    </div>
                                    <div class="h-2 bg-white/30 rounded-full overflow-hidden">
                                        <div class="h-full bg-white rounded-full" style="width: 85%"></div>
                                    </div>
                                    <p class="text-sm opacity-90 mt-2">Complete your profile to get better visibility</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tab Content: Profile Settings -->
                <div id="profile" class="tab-content">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-800">Profile Settings</h2>
                        <p class="text-gray-600">Manage your personal information and account settings</p>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Left Column -->
                        <div class="lg:col-span-2">
                            <!-- Personal Information Form -->
                            <div class="bg-white rounded-2xl shadow-md p-6 mb-8">
                                <h3 class="text-xl font-bold text-gray-800 mb-6">Personal Information</h3>

                                <form id="personalInfoForm">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                            <input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                            <input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300">
                                        </div>
                                    </div>

                                    <div class="mb-6">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300">
                                    </div>

                                    <div class="mb-6">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                        <input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300">
                                    </div>

                                    <div class="mb-6">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                                        <textarea name="bio" rows="4" class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                                    </div>
                                    
                                    <div class="flex justify-end">
                                        <button type="button" class="px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all duration-300 mr-4">
                                            Cancel
                                        </button>
                                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
                                            Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Address Information -->
                            <div class="bg-white rounded-2xl shadow-md p-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-6">Address Information</h3>

                                <form id="addressForm">
                                    <div class="mb-6">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Primary Address</label>
                                        <input type="text" name="address" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300">
                                    </div>
       
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                            <input type="text" name="city" value="<?php echo htmlspecialchars($user['city'] ?? ''); ?>" class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                                            <input type="text" name="postal_code" value="<?php echo htmlspecialchars($user['postal_code'] ?? ''); ?>" class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300">
                                        </div>
                                    </div>
                                    
                                    <div class="flex justify-end">
                                        <button type="submit" class="px-6 py-3 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
                                            Update Address
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        
                        <!-- Right Column -->
                        <div>
                            <!-- Profile Photo -->
                            <div class="bg-white rounded-2xl shadow-md p-6 mb-8">
                                <h3 class="text-xl font-bold text-gray-800 mb-6">Profile Photo</h3>
                                
                                <div class="text-center">
                                    <div class="w-48 h-48 rounded-full overflow-hidden mx-auto mb-6 relative">
                                        <img
                                            src="<?php echo htmlspecialchars($user['profile_image'] ? 'uploads/profiles/' . $user['profile_image'] : 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'); ?>"
                                            alt="Profile"
                                            class="w-full h-full object-cover"
                                        >
                                        <div class="absolute inset-0 bg-black/50 opacity-0 hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                            <i class="fas fa-camera text-white text-2xl"></i>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <button class="upload-photo-btn w-full py-3 bg-blue-50 text-[#2FA4E7] font-semibold rounded-xl hover:bg-blue-100 transition-colors duration-300">
                                            Upload New Photo
                                        </button>
                                        <button class="remove-photo-btn w-full py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors duration-300">
                                            Remove Photo
                                        </button>
                                        <input type="file" id="photoInput" accept="image/*" style="display:none">
                                    </div>
                                    
                                    <p class="text-sm text-gray-600 mt-4">Recommended: Square image, at least 400x400 pixels</p>
                                </div>
                            </div>
                            
                            <!-- Account Type -->
                            <div class="bg-white rounded-2xl shadow-md p-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-6">Account Type</h3>
                                
                                <div class="space-y-4">
                                    <div class="p-4 border-2 border-[#3CB371] rounded-xl bg-green-50">
                                        <div class="flex items-center">
                                            <div class="w-12 h-12 rounded-full bg-[#3CB371] flex items-center justify-center mr-4">
                                                <i class="fas fa-user-tie text-white text-xl"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-800">Landlord Account</p>
                                                <p class="text-sm text-gray-600">Manage properties and tenants</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="p-4 border border-gray-200 rounded-xl">
                                        <div class="flex items-center">
                                            <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center mr-4">
                                                <i class="fas fa-search text-gray-600 text-xl"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-800">House Hunter Account</p>
                                                <p class="text-sm text-gray-600">Search and book properties</p>
                                            </div>
                                        </div>
                                        <button class="switch-account-btn w-full mt-4 py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors duration-300">
                                            Switch Profile
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tab Content: My Properties -->
                <div id="properties" class="tab-content">
                    <div class="mb-8">
                        <div class="flex justify-between items-center">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800">My Properties</h2>
                                <p class="text-gray-600">Manage all your listed properties</p>
                            </div>
                            <button id="addPropertyBtn" class="px-6 py-3 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
                                <i class="fas fa-plus mr-2"></i> Add New Property
                            </button>
                        </div>
                    </div>
                    
                    <!-- Properties Grid -->
                    <div id="userPropertiesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        <!-- Properties will be loaded dynamically -->
                    </div>
                    
                    <!-- Property Statistics -->
                    <div class="bg-white rounded-2xl shadow-md p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-6">Property Statistics</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div class="text-center p-6 bg-blue-50 rounded-2xl">
                                <div class="text-3xl font-bold text-[#2FA4E7] mb-2">5</div>
                                <p class="text-gray-700 font-medium">Total Properties</p>
                            </div>
                            <div class="text-center p-6 bg-green-50 rounded-2xl">
                                <div class="text-3xl font-bold text-[#3CB371] mb-2">3</div>
                                <p class="text-gray-700 font-medium">Active Listings</p>
                            </div>
                            <div class="text-center p-6 bg-blue-50 rounded-2xl">
                                <div class="text-3xl font-bold text-[#2FA4E7] mb-2">12</div>
                                <p class="text-gray-700 font-medium">Total Viewings</p>
                            </div>
                            <!-- <div class="text-center p-6 bg-green-50 rounded-2xl">
                                <div class="text-3xl font-bold text-[#3CB371] mb-2">KES 425K</div>
                                <p class="text-gray-700 font-medium">Total Revenue</p>
                            </div> -->
                        </div>
                    </div>
                </div>
                
                
                <!-- Tab Content: Saved Properties (House Hunter) -->
                <div id="saved" class="tab-content">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-800">Saved Properties</h2>
                        <p class="text-gray-600">Properties you've saved for later viewing</p>
                    </div>

                    <div id="savedPropertiesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Saved properties will be loaded here -->
                        <div class="text-center col-span-1 md:col-span-2 lg:col-span-3 py-12">
                            <div class="w-24 h-24 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-6">
                                <i class="fas fa-heart text-3xl text-gray-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">No Saved Properties</h3>
                            <p class="text-gray-600 mb-6">You haven't saved any properties yet</p>
                            <button onclick="switchTab('dashboard')" class="px-6 py-3 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
                                Browse Properties
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Tab Content: My Requests (House Hunter) -->
                <div id="requests" class="tab-content">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-800">My Requests</h2>
                        <p class="text-gray-600">Requests you've sent to landlords</p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-md p-6">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-100">
                                        <th class="text-left py-3 text-gray-600 font-medium">Property</th>
                                        <th class="text-left py-3 text-gray-600 font-medium">Landlord</th>
                                        <th class="text-left py-3 text-gray-600 font-medium">Status</th>
                                        <th class="text-left py-3 text-gray-600 font-medium">Date Sent</th>
                                        <th class="text-left py-3 text-gray-600 font-medium">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                                        <td class="py-4">
                                            <div class="flex items-center">
                                                <div class="w-12 h-12 rounded-lg overflow-hidden mr-3">
                                                    <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80"
                                                         alt="Property" class="w-full h-full object-cover">
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-800">Westlands Apartment</p>
                                                    <p class="text-sm text-gray-600">KES 85,000/month</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4">
                                            <p class="font-medium">John Mwangi</p>
                                            <p class="text-sm text-gray-600">john@example.com</p>
                                        </td>
                                        <td class="py-4">
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-600 text-xs font-semibold rounded-full">Pending</span>
                                        </td>
                                        <td class="py-4">
                                            <p class="font-medium">2 days ago</p>
                                        </td>
                                        <td class="py-4">
                                            <div class="flex space-x-2">
                                                <button class="px-4 py-2 bg-blue-50 text-[#2FA4E7] font-semibold rounded-lg hover:bg-blue-100 transition-colors duration-300 text-sm">
                                                    Message
                                                </button>
                                                <button class="px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors duration-300 text-sm">
                                                    Cancel
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="text-center mt-8">
                            <p class="text-gray-600">No requests yet? <a href="#" class="text-[#2FA4E7] font-semibold">Browse properties</a> to send your first request.</p>
                        </div>
                    </div>
                </div>

                <!-- Tab Content: Messages (House Hunter) -->
                <div id="messages" class="tab-content">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-800">Messages</h2>
                        <p class="text-gray-600">Conversations with landlords</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Conversations List -->
                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                                <div class="max-h-[600px] overflow-y-auto">
                                    <a href="#" class="block p-6 border-b border-gray-100 hover:bg-blue-50 transition-colors duration-300">
                                        <div class="flex">
                                            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mr-4 flex-shrink-0">
                                                <span class="font-bold text-green-600">JM</span>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex justify-between mb-2">
                                                    <h4 class="font-bold text-gray-800">John Mwangi</h4>
                                                    <span class="text-sm text-gray-500">Today, 10:30 AM</span>
                                                </div>
                                                <p class="text-gray-700 mb-2">Yes, the apartment is still available. Would you like to schedule a viewing?</p>
                                                <div class="flex items-center">
                                                    <span class="px-2 py-1 bg-blue-100 text-blue-600 text-xs font-semibold rounded mr-3">Westlands Apartment</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Message Thread -->
                        <div>
                            <div class="bg-white rounded-2xl shadow-md p-6 sticky top-24">
                                <h3 class="text-xl font-bold text-gray-800 mb-6">Conversation</h3>

                                <div class="space-y-4 mb-6 max-h-96 overflow-y-auto">
                                    <!-- Messages would be loaded here -->
                                    <div class="text-center text-gray-500 py-8">
                                        Select a conversation to view messages
                                    </div>
                                </div>

                                <form>
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Send Message</label>
                                        <textarea rows="4" placeholder="Type your message here..." class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300"></textarea>
                                    </div>

                                    <button type="submit" class="w-full py-3 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
                                        Send Message
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Content: My Viewings (House Hunter) -->
                <div id="viewings" class="tab-content">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-800">My Viewings</h2>
                        <p class="text-gray-600">Schedule and manage property viewings</p>
                    </div>

                    <div class="bg-white rounded-2xl shadow-md p-6">
                        <div id="viewingsTableContainer" class="overflow-x-auto">
                            <!-- Viewings table will be loaded here -->
                            <div class="text-center py-12">
                                <div class="w-24 h-24 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-6">
                                    <i class="fas fa-calendar-alt text-3xl text-gray-400"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 mb-2">No Viewing Requests</h3>
                                <p class="text-gray-600 mb-6">You haven't requested any property viewings yet</p>
                                <button onclick="switchTab('dashboard')" class="px-6 py-3 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
                                    Browse Properties
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tab Content: Inquiries (Landlord) -->
                <div id="inquiries" class="tab-content">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-800">Inquiries & Messages</h2>
                        <p class="text-gray-600">Manage requests and messages from potential tenants</p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Inquiries List -->
                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-2xl shadow-md overflow-hidden">
                                <!-- Filter Tabs -->
                                <div class="border-b border-gray-100">
                                    <div class="flex">
                                        <button class="inquiry-filter px-6 py-4 font-semibold border-b-2 border-[#2FA4E7] text-[#2FA4E7]" data-filter="all">
                                            All <span class="ml-2 bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full"><?php echo $viewing_count; ?></span>
                                        </button>
                                        <button class="inquiry-filter px-6 py-4 font-semibold text-gray-600 hover:text-gray-800" data-filter="requests">
                                            Requests <span class="ml-2 bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">2</span>
                                        </button>
                                        <button class="inquiry-filter px-6 py-4 font-semibold text-gray-600 hover:text-gray-800" data-filter="messages">
                                            Messages <span class="ml-2 bg-green-100 text-green-600 text-xs px-2 py-1 rounded-full">1</span>
                                        </button>
                                        <button class="inquiry-filter px-6 py-4 font-semibold text-gray-600 hover:text-gray-800" data-filter="unread">
                                            Unread <span class="ml-2 bg-red-500 text-white text-xs px-2 py-1 rounded-full">2</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Messages/Requests List -->
                                <div class="max-h-[600px] overflow-y-auto">
                                    <!-- Viewing Request -->
                                    <a href="#" class="block p-6 border-b border-gray-100 hover:bg-blue-50 transition-colors duration-300">
                                        <div class="flex">
                                            <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mr-4 flex-shrink-0">
                                                <span class="font-bold text-green-600">JM</span>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex justify-between mb-2">
                                                    <h4 class="font-bold text-gray-800">Jane Muthoni</h4>
                                                    <span class="text-sm text-gray-500">Today, 10:30 AM</span>
                                                </div>
                                                <p class="text-gray-700 mb-2">I'd like to schedule a viewing for your Westlands apartment this Friday.</p>
                                                <div class="flex items-center">
                                                    <span class="px-2 py-1 bg-blue-100 text-blue-600 text-xs font-semibold rounded mr-3">Viewing Request</span>
                                                    <span class="text-sm text-gray-600">For: Westlands Apartment</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>

                                    <!-- Message -->
                                    <a href="#" class="block p-6 border-b border-gray-100 hover:bg-blue-50 transition-colors duration-300 bg-blue-50/50">
                                        <div class="flex">
                                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mr-4 flex-shrink-0">
                                                <span class="font-bold text-blue-600">PO</span>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex justify-between mb-2">
                                                    <h4 class="font-bold text-gray-800">Peter Omondi</h4>
                                                    <span class="text-sm text-gray-500">Yesterday, 3:15 PM</span>
                                                </div>
                                                <p class="text-gray-700 mb-2">Can I get more details about the parking and security features?</p>
                                                <div class="flex items-center">
                                                    <span class="px-2 py-1 bg-green-100 text-green-600 text-xs font-semibold rounded mr-3">Message</span>
                                                    <span class="text-sm text-gray-600">For: Kilimani Suite</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div>
                            <div class="bg-white rounded-2xl shadow-md p-6 sticky top-24">
                                <h3 class="text-xl font-bold text-gray-800 mb-6">Quick Actions</h3>

                                <div class="space-y-4">
                                    <button class="w-full py-3 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
                                        <i class="fas fa-reply mr-2"></i> Reply to Message
                                    </button>

                                    <button class="w-full py-3 border border-[#2FA4E7] text-[#2FA4E7] font-semibold rounded-xl hover:bg-blue-50 transition-all duration-300">
                                        <i class="fas fa-calendar-check mr-2"></i> Approve Viewing
                                    </button>

                                    <button class="w-full py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors duration-300">
                                        <i class="fas fa-envelope mr-2"></i> Send Message
                                    </button>

                                    <button class="w-full py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors duration-300">
                                        <i class="fas fa-user-plus mr-2"></i> Add to Contacts
                                    </button>
                                </div>

                                <div class="mt-8 pt-6 border-t border-gray-100">
                                    <h4 class="font-bold text-gray-800 mb-4">Response Templates</h4>
                                    <div class="space-y-2">
                                        <button class="w-full text-left py-2 px-3 text-sm text-gray-600 hover:bg-gray-50 rounded-lg transition-colors duration-300">
                                            ✓ Viewing confirmed
                                        </button>
                                        <button class="w-full text-left py-2 px-3 text-sm text-gray-600 hover:bg-gray-50 rounded-lg transition-colors duration-300">
                                            ✗ Property not available
                                        </button>
                                        <button class="w-full text-left py-2 px-3 text-sm text-gray-600 hover:bg-gray-50 rounded-lg transition-colors duration-300">
                                            💬 More information needed
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tab Content: Payment History -->
                <div id="payments" class="tab-content">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-800">Payment History</h2>
                        <p class="text-gray-600">Track all your payments and transactions</p>
                    </div>
                    
                    <div class="bg-white rounded-2xl shadow-md p-6">
                        <!-- Payment Filters -->
                        <div class="flex flex-col md:flex-row justify-between items-center mb-8">
                            <div class="mb-4 md:mb-0">
                                <div class="flex space-x-4">
                                    <button class="px-4 py-2 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-lg">All</button>
                                    <button class="px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50">Access Fees</button>
                                    <button class="px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50">Subscriptions</button>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4">
                                <input type="month" class="p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300" value="2023-11">
                                <button class="px-4 py-3 bg-blue-50 text-[#2FA4E7] font-semibold rounded-xl hover:bg-blue-100 transition-colors duration-300">
                                    <i class="fas fa-download mr-2"></i> Export
                                </button>
                            </div>
                        </div>
                        
                        <!-- Payments Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b border-gray-100">
                                        <th class="text-left py-3 text-gray-600 font-medium">Date</th>
                                        <th class="text-left py-3 text-gray-600 font-medium">Description</th>
                                        <th class="text-left py-3 text-gray-600 font-medium">Amount</th>
                                        <th class="text-left py-3 text-gray-600 font-medium">Status</th>
                                        <th class="text-left py-3 text-gray-600 font-medium">Receipt</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                                        <td class="py-4">
                                            <p class="font-medium">Nov 15, 2023</p>
                                            <p class="text-sm text-gray-600">10:30 AM</p>
                                        </td>
                                        <td class="py-4">
                                            <p class="font-medium text-gray-800">Property Access Fee</p>
                                            <p class="text-sm text-gray-600">Westlands Apartment</p>
                                        </td>
                                        <td class="py-4">
                                            <p class="font-bold text-[#2FA4E7]">KES 200</p>
                                        </td>
                                        <td class="py-4">
                                            <span class="px-3 py-1 bg-green-100 text-green-600 text-xs font-semibold rounded-full">Completed</span>
                                        </td>
                                        <td class="py-4">
                                            <button class="px-4 py-2 bg-blue-50 text-[#2FA4E7] font-semibold rounded-lg hover:bg-blue-100 transition-colors duration-300 text-sm">
                                                Download
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="border-b border-gray-50 hover:bg-gray-50">
                                        <td class="py-4">
                                            <p class="font-medium">Nov 10, 2023</p>
                                            <p class="text-sm text-gray-600">2:15 PM</p>
                                        </td>
                                        <td class="py-4">
                                            <p class="font-medium text-gray-800">Property Access Fee</p>
                                            <p class="text-sm text-gray-600">Kilimani Suite</p>
                                        </td>
                                        <td class="py-4">
                                            <p class="font-bold text-[#2FA4E7]">KES 200</p>
                                        </td>
                                        <td class="py-4">
                                            <span class="px-3 py-1 bg-green-100 text-green-600 text-xs font-semibold rounded-full">Completed</span>
                                        </td>
                                        <td class="py-4">
                                            <button class="px-4 py-2 bg-blue-50 text-[#2FA4E7] font-semibold rounded-lg hover:bg-blue-100 transition-colors duration-300 text-sm">
                                                Download
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Payment Summary -->
                        <div class="mt-8 pt-8 border-t border-gray-100">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-gray-600">Total this month</p>
                                    <h3 class="text-2xl font-bold text-gray-800">KES 400</h3>
                                </div>
                                <div class="text-right">
                                    <p class="text-gray-600">All-time total</p>
                                    <h3 class="text-2xl font-bold text-[#3CB371]">KES 1,400</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tab Content: Security -->
                <div id="security" class="tab-content">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-800">Security Settings</h2>
                        <p class="text-gray-600">Manage your account security and privacy</p>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Change Password -->
                        <div class="bg-white rounded-2xl shadow-md p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-6">Change Password</h3>
                            
                            <form>
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                    <input type="password" class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300" required>
                                </div>
                                
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                    <input type="password" class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300" required>
                                </div>
                                
                                <div class="mb-8">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                    <input type="password" class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300" required>
                                </div>
                                
                                <button type="submit" class="w-full py-4 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
                                    Update Password
                                </button>
                            </form>
                        </div>
                        
                        <!-- Two-Factor Authentication -->
                        <div class="bg-white rounded-2xl shadow-md p-6">
                            <h3 class="text-xl font-bold text-gray-800 mb-6">Two-Factor Authentication</h3>
                            
                            <div class="mb-8">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <p class="font-medium text-gray-800">SMS Authentication</p>
                                        <p class="text-sm text-gray-600">Receive codes via SMS</p>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" checked>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                                
                                <div class="flex items-center justify-between mb-8">
                                    <div>
                                        <p class="font-medium text-gray-800">Authenticator App</p>
                                        <p class="text-sm text-gray-600">Use Google Authenticator or similar</p>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox">
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Security Activity -->
                            <div>
                                <h4 class="font-bold text-gray-800 mb-4">Recent Security Activity</h4>
                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                            <i class="fas fa-check text-green-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">Login from Nairobi</p>
                                            <p class="text-xs text-gray-600">Today, 10:30 AM • This device</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                            <i class="fas fa-mobile-alt text-blue-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">Password changed</p>
                                            <p class="text-xs text-gray-600">2 weeks ago</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tab Content: Notifications -->
                <div id="notifications" class="tab-content">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-800">Notification Settings</h2>
                        <p class="text-gray-600">Control how and when you receive notifications</p>
                    </div>
                    
                    <div class="bg-white rounded-2xl shadow-md p-6">
                        <div class="space-y-8">
                            <!-- Email Notifications -->
                            <div>
                                <h3 class="text-lg font-bold text-gray-800 mb-6">Email Notifications</h3>
                                <div class="space-y-6">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-800">New viewing requests</p>
                                            <p class="text-sm text-gray-600">When someone requests to view your property</p>
                                        </div>
                                        <label class="switch">
                                            <input type="checkbox" checked>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-800">New messages</p>
                                            <p class="text-sm text-gray-600">When you receive new inquiries</p>
                                        </div>
                                        <label class="switch">
                                            <input type="checkbox" checked>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-800">Payment receipts</p>
                                            <p class="text-sm text-gray-600">When payments are processed</p>
                                        </div>
                                        <label class="switch">
                                            <input type="checkbox" checked>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Push Notifications -->
                            <div>
                                <h3 class="text-lg font-bold text-gray-800 mb-6">Push Notifications</h3>
                                <div class="space-y-6">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-800">Property alerts</p>
                                            <p class="text-sm text-gray-600">New properties matching your search</p>
                                        </div>
                                        <label class="switch">
                                            <input type="checkbox">
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-800">Viewing reminders</p>
                                            <p class="text-sm text-gray-600">Reminders for scheduled viewings</p>
                                        </div>
                                        <label class="switch">
                                            <input type="checkbox" checked>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- SMS Notifications -->
                            <div>
                                <h3 class="text-lg font-bold text-gray-800 mb-6">SMS Notifications</h3>
                                <div class="space-y-6">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-800">Urgent alerts</p>
                                            <p class="text-sm text-gray-600">Critical updates about your account</p>
                                        </div>
                                        <label class="switch">
                                            <input type="checkbox" checked>
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-800">Viewing confirmations</p>
                                            <p class="text-sm text-gray-600">When viewings are confirmed or cancelled</p>
                                        </div>
                                        <label class="switch">
                                            <input type="checkbox">
                                            <span class="slider"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="pt-6 border-t border-gray-100">
                                <button class="px-8 py-3 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
                                    Save Notification Settings
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tab Content: Reports & Analytics -->
                <div id="reports" class="tab-content">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-800">Reports & Analytics</h2>
                        <p class="text-gray-600">Track your property performance and generate reports</p>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Analytics Summary -->
                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-2xl shadow-md p-6 mb-8">
                                <div class="flex justify-between items-center mb-8">
                                    <h3 class="text-xl font-bold text-gray-800">Performance Overview</h3>
                                    <select class="p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300">
                                        <option>Last 30 days</option>
                                        <option>Last 90 days</option>
                                        <option>Last 6 months</option>
                                        <option>Last year</option>
                                    </select>
                                </div>
                                
                                <!-- Stats Grid -->
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                                    <div class="text-center p-6 bg-blue-50 rounded-2xl">
                                        <div class="text-3xl font-bold text-[#2FA4E7] mb-2">85%</div>
                                        <p class="text-gray-700 font-medium">View Rate</p>
                                    </div>
                                    <div class="text-center p-6 bg-green-50 rounded-2xl">
                                        <div class="text-3xl font-bold text-[#3CB371] mb-2">12</div>
                                        <p class="text-gray-700 font-medium">Total Views</p>
                                    </div>
                                    <div class="text-center p-6 bg-blue-50 rounded-2xl">
                                        <div class="text-3xl font-bold text-[#2FA4E7] mb-2">5</div>
                                        <p class="text-gray-700 font-medium">Applications</p>
                                    </div>
                                    <div class="text-center p-6 bg-green-50 rounded-2xl">
                                        <div class="text-3xl font-bold text-[#3CB371] mb-2">3.2K</div>
                                        <p class="text-gray-700 font-medium">Impressions</p>
                                    </div>
                                </div>
                                
                                <!-- Chart Placeholder -->
                                <div class="bg-gray-50 rounded-2xl p-8 text-center">
                                    <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-br from-blue-100 to-green-100 flex items-center justify-center mb-6">
                                        <i class="fas fa-chart-line text-3xl text-gray-400"></i>
                                    </div>
                                    <h4 class="text-xl font-bold text-gray-800 mb-2">Analytics Chart</h4>
                                    <p class="text-gray-600">Performance charts will appear here with real data</p>
                                </div>
                            </div>
                            
                            <!-- Report Generation -->
                            <div class="bg-white rounded-2xl shadow-md p-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-6">Generate Reports</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="p-6 border border-gray-200 rounded-2xl hover:border-[#2FA4E7] transition-colors duration-300">
                                        <div class="w-14 h-14 rounded-xl bg-blue-100 flex items-center justify-center mb-4">
                                            <i class="fas fa-file-invoice-dollar text-2xl text-[#2FA4E7]"></i>
                                        </div>
                                        <h4 class="font-bold text-gray-800 mb-2">Financial Report</h4>
                                        <p class="text-gray-600 text-sm mb-4">Generate detailed financial reports for tax purposes</p>
                                        <button class="px-4 py-2 bg-blue-50 text-[#2FA4E7] font-semibold rounded-lg hover:bg-blue-100 transition-colors duration-300 text-sm">
                                            Generate
                                        </button>
                                    </div>
                                    
                                    <div class="p-6 border border-gray-200 rounded-2xl hover:border-[#3CB371] transition-colors duration-300">
                                        <div class="w-14 h-14 rounded-xl bg-green-100 flex items-center justify-center mb-4">
                                            <i class="fas fa-chart-bar text-2xl text-[#3CB371]"></i>
                                        </div>
                                        <h4 class="font-bold text-gray-800 mb-2">Performance Report</h4>
                                        <p class="text-gray-600 text-sm mb-4">View detailed analytics of your property performance</p>
                                        <button class="px-4 py-2 bg-green-50 text-[#3CB371] font-semibold rounded-lg hover:bg-green-100 transition-colors duration-300 text-sm">
                                            Generate
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Stats -->
                        <div>
                            <div class="bg-white rounded-2xl shadow-md p-6 mb-8">
                                <h3 class="text-xl font-bold text-gray-800 mb-6">Top Performing</h3>
                                
                                <div class="space-y-6">
                                    <div>
                                        <div class="flex justify-between mb-2">
                                            <span class="font-medium text-gray-800">Westlands Apartment</span>
                                            <span class="font-bold text-[#3CB371]">85%</span>
                                        </div>
                                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-[#3CB371] rounded-full" style="width: 85%"></div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <div class="flex justify-between mb-2">
                                            <span class="font-medium text-gray-800">Kilimani Suite</span>
                                            <span class="font-bold text-[#2FA4E7]">72%</span>
                                        </div>
                                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-[#2FA4E7] rounded-full" style="width: 72%"></div>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <div class="flex justify-between mb-2">
                                            <span class="font-medium text-gray-800">Kileleshwa House</span>
                                            <span class="font-bold text-gray-600">58%</span>
                                        </div>
                                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-gray-400 rounded-full" style="width: 58%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Export Options -->
                            <div class="bg-white rounded-2xl shadow-md p-6">
                                <h3 class="text-xl font-bold text-gray-800 mb-6">Export Data</h3>
                                
                                <div class="space-y-4">
                                    <button class="w-full py-4 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors duration-300 flex items-center justify-center">
                                        <i class="fas fa-file-excel text-green-600 text-xl mr-3"></i>
                                        Export as Excel
                                    </button>
                                    <button class="w-full py-4 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors duration-300 flex items-center justify-center">
                                        <i class="fas fa-file-pdf text-red-600 text-xl mr-3"></i>
                                        Export as PDF
                                    </button>
                                    <button class="w-full py-4 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors duration-300 flex items-center justify-center">
                                        <i class="fas fa-file-csv text-blue-600 text-xl mr-3"></i>
                                        Export as CSV
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Property Modal -->
    <div id="addPropertyModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-3xl w-full max-w-4xl max-h-[90vh] overflow-y-auto modal-enter">
                <!-- Modal Header -->
                <div class="sticky top-0 z-10 bg-white border-b border-gray-100 p-6 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-plus-circle text-[#2FA4E7] mr-3"></i>
                        Add New Property Listing
                    </h2>
                    <button id="closePropertyModal" class="text-gray-500 hover:text-gray-800 text-2xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Modal Content -->
                <div class="p-6 overflow-y-auto">
                    <!-- Listing Form -->
                    <form id="propertyForm">
                        <!-- Step Indicator -->
                        <div class="mb-10">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] flex items-center justify-center text-white font-bold mr-3">1</div>
                                    <span class="font-semibold">Basic Information</span>
                                </div>
                                <div class="h-1 flex-1 bg-gray-200 mx-4"></div>
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold mr-3">2</div>
                                    <span class="font-semibold text-gray-500">Property Details</span>
                                </div>
                                <div class="h-1 flex-1 bg-gray-200 mx-4"></div>
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold mr-3">3</div>
                                    <span class="font-semibold text-gray-500">Photos & Pricing</span>
                                </div>
                            </div>
                        </div>

                        <!-- Basic Information -->
                        <div class="mb-10">
                            <h3 class="text-xl font-bold text-gray-800 mb-6">Basic Information</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Property Title*</label>
                                    <input type="text" name="title" placeholder="e.g., Spacious 3-Bedroom Apartment" class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Property Type*</label>
                                    <select id="propertyTypeSelect" name="property_type" class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300" required>
                                        <option value="">Select type</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description*</label>
                                <textarea name="description" rows="4" placeholder="Describe your property in detail..." class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300" required></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Location*</label>
                                    <input type="text" name="location" placeholder="e.g., Westlands, Nairobi" class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Neighborhood</label>
                                    <input type="text" name="neighborhood" placeholder="e.g., Near shopping mall" class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300">
                                </div>
                            </div>
                        </div>

                        <!-- Property Details -->
                        <div class="mb-10">
                            <h3 class="text-xl font-bold text-gray-800 mb-6">Property Details</h3>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Bedrooms*</label>
                                    <select name="bedrooms" class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300" required>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3" selected>3</option>
                                        <option value="4">4</option>
                                        <option value="5+">5+</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Bathrooms*</label>
                                    <select name="bathrooms" class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300" required>
                                        <option value="1">1</option>
                                        <option value="2" selected>2</option>
                                        <option value="3">3</option>
                                        <option value="4+">4+</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Square Feet</label>
                                    <input type="number" name="size_sqft" placeholder="e.g., 1200" class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Parking Spots</label>
                                    <select name="parking_spaces" class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300">
                                        <option value="0">None</option>
                                        <option value="1" selected>1</option>
                                        <option value="2">2</option>
                                        <option value="3+">3+</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Amenities -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-4">Amenities</label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" id="wifi" name="wifi" class="mr-3">
                                        <label for="wifi" class="text-gray-700">WiFi</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="parking" name="parking" class="mr-3" checked>
                                        <label for="parking" class="text-gray-700">Parking</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="security" name="security" class="mr-3" checked>
                                        <label for="security" class="text-gray-700">Security</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="pool" name="pool" class="mr-3">
                                        <label for="pool" class="text-gray-700">Swimming Pool</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="gym" name="gym" class="mr-3">
                                        <label for="gym" class="text-gray-700">Gym</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="pets" name="pets" class="mr-3" checked>
                                        <label for="pets" class="text-gray-700">Pet Friendly</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="furnished" name="furnished" class="mr-3">
                                        <label for="furnished" class="text-gray-700">Furnished</label>
                                    </div>
                                    <div class="flex items-center">
                                        <input type="checkbox" id="balcony" name="balcony" class="mr-3" checked>
                                        <label for="balcony" class="text-gray-700">Balcony</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing & Photos -->
                        <div class="mb-10">
                            <h3 class="text-xl font-bold text-gray-800 mb-6">Pricing & Photos</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Rent (KES)*</label>
                                    <input type="number" name="price" placeholder="e.g., 85000" class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Security Deposit (KES)</label>
                                    <input type="number" name="security_deposit" placeholder="e.g., 85000" class="w-full p-3 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#2FA4E7] focus:border-transparent transition-all duration-300">
                                </div>
                            </div>

                            <!-- Photo Upload -->
                            <div class="mb-8">
                                <label class="block text-sm font-medium text-gray-700 mb-4">Property Photos</label>
                                <div id="photoUploadArea" class="border-2 border-dashed border-gray-300 rounded-2xl p-8 text-center hover:border-[#2FA4E7] transition-colors duration-300 cursor-pointer">
                                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                                    <h4 class="text-lg font-bold text-gray-800 mb-2">Upload Property Photos</h4>
                                    <p class="text-gray-600 mb-4">Drag & drop images here or click to browse</p>
                                    <p class="text-sm text-gray-500">Recommended: High-quality photos, minimum 3 images</p>
                                </div>
                                <input type="file" id="propertyPhotoInput" accept="image/*" multiple style="display:none">

                                <!-- Photo Preview -->
                                <div id="photoPreview" class="mt-4 hidden">
                                    <div class="flex items-center justify-between mb-4">
                                        <span id="photoCount" class="text-sm text-gray-600">0 photos selected</span>
                                        <button type="button" id="clearPhotos" class="text-sm text-red-600 hover:text-red-800">Clear all</button>
                                    </div>
                                    <div id="photoGrid" class="grid grid-cols-2 md:grid-cols-3 gap-4"></div>
                                </div>
                            </div>

                            <!-- Listing Promotion -->
                            <div class="bg-gradient-to-r from-green-50 to-blue-50 border border-green-100 rounded-2xl p-6">
                                <div class="flex items-center mb-4">
                                    <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mr-4">
                                        <i class="fas fa-gift text-green-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800">Free Listing Promotion</h4>
                                        <p class="text-gray-700">During Year One, all property listings are completely free! No charges until 2025.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-between pt-6 border-t border-gray-100">
                            <button type="button" id="closePropertyModalBtn" class="px-8 py-4 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all duration-300">
                                Cancel
                            </button>
                            <div class="space-x-4">
                                <button type="button" class="px-8 py-4 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all duration-300">
                                    Save as Draft
                                </button>
                                <button type="submit" class="px-8 py-4 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
                                    <i class="fas fa-paper-plane mr-2"></i> Publish Listing
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // DOM Elements
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
        const notificationBtn = document.getElementById('notificationBtn');
        const notificationDropdown = document.getElementById('notificationDropdown');
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userDropdown = document.getElementById('userDropdown');
        const pageTitle = document.getElementById('pageTitle');
        
        // Tab titles mapping
        const tabTitles = {
            dashboard: 'Dashboard Overview',
            profile: 'Profile Settings',
            properties: 'My Properties',
            listings: 'Add New Listing',
            saved: 'Saved Properties',
            viewings: 'My Viewings',
            requests: 'My Requests',
            messages: 'Messages',
            payments: 'Payment History',
            inquiries: 'Inquiries & Messages',
            security: 'Security Settings',
            notifications: 'Notification Settings',
            reports: 'Reports & Analytics'
        };
        
        // Modal Elements
        const addPropertyModal = document.getElementById('addPropertyModal');
        const addListingBtn = document.getElementById('addListingBtn');
        const addPropertyBtn = document.getElementById('addPropertyBtn');
        const closePropertyModal = document.getElementById('closePropertyModal');
        const closePropertyModalBtn = document.getElementById('closePropertyModalBtn');

        // Modal functions
        function openModal(modal) {
            modal.classList.remove('hidden');
        }

        function closeModal(modal) {
            modal.classList.add('hidden');
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Set active tab from URL hash if present
            const hash = window.location.hash.substring(1);
            if (hash && tabTitles[hash]) {
                switchTab(hash);
            }

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                // Close notification dropdown
                if (!notificationBtn.contains(e.target) && !notificationDropdown.contains(e.target)) {
                    notificationDropdown.classList.add('hidden');
                }

                // Close user dropdown
                if (!userMenuBtn.contains(e.target) && !userDropdown.contains(e.target)) {
                    userDropdown.classList.add('hidden');
                }
            });

            // Modal functionality
            if (addListingBtn) {
                addListingBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    openModal(addPropertyModal);
                    loadPropertyTypes();
                });
            }

            if (addPropertyBtn) {
                addPropertyBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    openModal(addPropertyModal);
                    loadPropertyTypes();
                });
            }

            if (closePropertyModal) {
                closePropertyModal.addEventListener('click', function() {
                    closeModal(addPropertyModal);
                });
            }

            if (closePropertyModalBtn) {
                closePropertyModalBtn.addEventListener('click', function() {
                    closeModal(addPropertyModal);
                });
            }

            // Close modal on background click
            addPropertyModal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal(addPropertyModal);
                }
            });
        });
        
        // Toggle sidebar on mobile
        mobileSidebarToggle.addEventListener('click', function() {
            sidebar.classList.remove('hidden', 'lg:hidden');
            sidebar.classList.add('block');
        });
        
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.add('hidden');
        });
        
        // Toggle notification dropdown
        notificationBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('hidden');
            userDropdown.classList.add('hidden');
        });
        
        // Toggle user dropdown
        userMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('hidden');
            notificationDropdown.classList.add('hidden');
        });
        
        // Tab switching functionality
        document.querySelectorAll('.tab-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const tabId = this.dataset.tab;
                switchTab(tabId);
                
                // Close sidebar on mobile after selecting a tab
                if (window.innerWidth < 1024) {
                    sidebar.classList.add('hidden');
                }
            });
        });
        
        // Switch tab function
        function switchTab(tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Remove active class from all tab links
            document.querySelectorAll('.tab-link').forEach(link => {
                link.classList.remove('active');
            });
            
            // Show selected tab content
            const selectedTab = document.getElementById(tabId);
            if (selectedTab) {
                selectedTab.classList.add('active');
                
                // Update page title
                if (tabTitles[tabId]) {
                    pageTitle.textContent = tabTitles[tabId];
                }
                
                // Add active class to clicked tab link
                document.querySelectorAll(`.tab-link[data-tab="${tabId}"]`).forEach(link => {
                    link.classList.add('active');
                });
                
                // Update URL hash
                window.history.pushState(null, null, `#${tabId}`);
                
                // Scroll to top of main content
                document.querySelector('main').scrollTop = 0;
            }
        }
        
        // Inquiry filter functionality
        document.querySelectorAll('.inquiry-filter').forEach(filter => {
            filter.addEventListener('click', function() {
                // Remove active class from all filters
                document.querySelectorAll('.inquiry-filter').forEach(f => {
                    f.classList.remove('border-b-2', 'border-[#2FA4E7]', 'text-[#2FA4E7]');
                    f.classList.add('text-gray-600');
                });
                
                // Add active class to clicked filter
                this.classList.add('border-b-2', 'border-[#2FA4E7]', 'text-[#2FA4E7]');
                this.classList.remove('text-gray-600');
            });
        });
        
        // Property form submission
        document.getElementById('propertyForm')?.addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Publishing...';
            submitBtn.disabled = true;

            // Collect form data
            const formData = new FormData(this);

            // Add photos
            const files = propertyPhotoInput?.files || [];
            for (let i = 0; i < files.length && i < 3; i++) {
                formData.append('photo_' + (i + 1), files[i]);
            }

            // Add amenities
            formData.append('wifi', document.getElementById('wifi')?.checked ? '1' : '0');
            formData.append('parking', document.getElementById('parking')?.checked ? '1' : '0');
            formData.append('security', document.getElementById('security')?.checked ? '1' : '0');
            formData.append('pool', document.getElementById('pool')?.checked ? '1' : '0');
            formData.append('gym', document.getElementById('gym')?.checked ? '1' : '0');
            formData.append('pets', document.getElementById('pets')?.checked ? '1' : '0');
            formData.append('furnished', document.getElementById('furnished')?.checked ? '1' : '0');
            formData.append('balcony', document.getElementById('balcony')?.checked ? '1' : '0');

            fetch('api/add_house.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    submitBtn.innerHTML = '<i class="fas fa-check mr-2"></i> Published!';
                    showNotification('Property listing published successfully!', 'success');

                    // Reset form
                    this.reset();
                    if (propertyPhotoInput) propertyPhotoInput.value = '';

                    // Switch to properties tab after delay
                    setTimeout(() => {
                        switchTab('properties');
                        // Reload page to show new property
                        window.location.reload();
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

        // Personal info form submission
        document.getElementById('personalInfoForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('api/update_profile.php', {
                method: 'POST',
                body: formData
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    showNotification('Personal information updated successfully!', 'success');
                } else {
                    showNotification(data.error || 'Failed to update profile', 'error');
                }
            }).catch(() => {
                showNotification('Network error. Please try again.', 'error');
            });
        });

        // Address form submission
        document.getElementById('addressForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('api/update_profile.php', {
                method: 'POST',
                body: formData
            }).then(res => res.json()).then(data => {
                if (data.success) {
                    showNotification('Address updated successfully!', 'success');
                } else {
                    showNotification(data.error || 'Failed to update address', 'error');
                }
            }).catch(() => {
                showNotification('Network error. Please try again.', 'error');
            });
        });

        // Show notification function
        function showNotification(message, type) {
            // Remove existing notification
            const existingNotification = document.querySelector('.notification-toast');
            if (existingNotification) {
                existingNotification.remove();
            }
            
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification-toast fixed top-6 right-6 z-50 px-6 py-4 rounded-xl shadow-2xl transform transition-all duration-500 translate-x-full`;
            
            // Set notification style based on type
            let bgColor, icon;
            if (type === 'success') {
                bgColor = 'linear-gradient(90deg, #3CB371, #4CAF50)';
                icon = 'check-circle';
            } else if (type === 'error') {
                bgColor = 'linear-gradient(90deg, #F44336, #E53935)';
                icon = 'exclamation-circle';
            } else if (type === 'warning') {
                bgColor = 'linear-gradient(90deg, #FF9800, #FF5722)';
                icon = 'exclamation-triangle';
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
        
        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                sidebar.classList.remove('hidden');
            }
        });

        // Load saved properties
        function loadSavedProperties() {
            fetch('api/favorites.php')
            .then(response => response.json())
            .then(data => {
                const grid = document.getElementById('savedPropertiesGrid');
                if (data.success && data.data.length > 0) {
                    grid.innerHTML = '';
                    data.data.forEach(favorite => {
                        const card = createSavedPropertyCard(favorite);
                        grid.appendChild(card);
                    });
                } else {
                    grid.innerHTML = `
                        <div class="text-center col-span-1 md:col-span-2 lg:col-span-3 py-12">
                            <div class="w-24 h-24 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-6">
                                <i class="fas fa-heart text-3xl text-gray-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">No Saved Properties</h3>
                            <p class="text-gray-600 mb-6">You haven't saved any properties yet</p>
                            <button onclick="switchTab('dashboard')" class="px-6 py-3 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
                                Browse Properties
                            </button>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading saved properties:', error);
            });
        }

        // Create saved property card
        function createSavedPropertyCard(favorite) {
            const card = document.createElement('div');
            card.className = 'dashboard-card bg-white rounded-2xl shadow-md overflow-hidden';

            const features = [];
            if (favorite.bedrooms) features.push(`<div class="flex items-center"><i class="fas fa-bed text-[#2FA4E7] mr-1"></i><span class="text-sm">${favorite.bedrooms} Bed</span></div>`);
            if (favorite.bathrooms) features.push(`<div class="flex items-center"><i class="fas fa-bath text-[#2FA4E7] mr-1"></i><span class="text-sm">${favorite.bathrooms} Bath</span></div>`);

            card.innerHTML = `
                <div class="relative h-48">
                    <img src="${favorite.image_url_1}" alt="Property" class="w-full h-full object-cover">
                    <button onclick="removeFavorite(${favorite.house_id})" class="absolute top-4 right-4 w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-md hover:shadow-lg transition-shadow duration-300 text-red-500">
                        <i class="fas fa-heart"></i>
                    </button>
                    <div class="absolute bottom-4 left-4">
                        <span class="px-3 py-1 bg-white text-gray-800 font-bold rounded-lg">KES ${favorite.price.toLocaleString()}</span>
                    </div>
                </div>

                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">${favorite.title}</h3>
                    <div class="flex items-center text-gray-600 mb-4">
                        <i class="fas fa-map-marker-alt text-[#3CB371] mr-2"></i>
                        <span>${favorite.location}</span>
                    </div>

                    <div class="flex flex-wrap gap-2 mb-6">
                        ${features.join('')}
                    </div>

                    <div class="space-y-3">
                        <button onclick="viewProperty(${favorite.house_id})" class="w-full py-3 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
                            View Details
                        </button>
                        <button onclick="requestViewing(${favorite.house_id})" class="w-full py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors duration-300">
                            Request Viewing
                        </button>
                    </div>
                </div>
            `;
            return card;
        }

        // Remove favorite
        function removeFavorite(houseId) {
            fetch('api/favorites.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    house_id: houseId,
                    action: 'remove'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadSavedProperties(); // Reload the list
                    showNotification('Property removed from favorites!', 'info');
                } else {
                    showNotification(data.error || 'Failed to remove favorite', 'error');
                }
            })
            .catch(error => {
                showNotification('Network error. Please try again.', 'error');
            });
        }

        // Load viewings
        function loadViewings() {
            fetch('api/viewing_requests.php')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('viewingsTableContainer');
                if (data.success && data.data.length > 0) {
                    const tableHTML = `
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="text-left py-3 text-gray-600 font-medium">Property</th>
                                    <th class="text-left py-3 text-gray-600 font-medium">Date & Time</th>
                                    <th class="text-left py-3 text-gray-600 font-medium">Status</th>
                                    <th class="text-left py-3 text-gray-600 font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.data.map(request => createViewingRow(request)).join('')}
                            </tbody>
                        </table>
                    `;
                    container.innerHTML = tableHTML;
                } else {
                    container.innerHTML = `
                        <div class="text-center py-12">
                            <div class="w-24 h-24 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-6">
                                <i class="fas fa-calendar-alt text-3xl text-gray-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">No Viewing Requests</h3>
                            <p class="text-gray-600 mb-6">You haven't requested any property viewings yet</p>
                            <button onclick="switchTab('dashboard')" class="px-6 py-3 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
                                Browse Properties
                            </button>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading viewings:', error);
            });
        }

        // Load user requests (for My Requests tab)
        function loadUserRequests() {
            fetch('api/viewing_requests.php')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('requestsTableContainer');
                if (data.success && data.data.length > 0) {
                    const tableHTML = `
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-100">
                                    <th class="text-left py-3 text-gray-600 font-medium">Property</th>
                                    <th class="text-left py-3 text-gray-600 font-medium">Landlord</th>
                                    <th class="text-left py-3 text-gray-600 font-medium">Status</th>
                                    <th class="text-left py-3 text-gray-600 font-medium">Date Sent</th>
                                    <th class="text-left py-3 text-gray-600 font-medium">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.data.map(request => createRequestRow(request)).join('')}
                            </tbody>
                        </table>
                    `;
                    container.innerHTML = tableHTML;
                } else {
                    container.innerHTML = `
                        <div class="text-center py-12">
                            <div class="w-24 h-24 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-6">
                                <i class="fas fa-paper-plane text-3xl text-gray-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">No Requests Sent</h3>
                            <p class="text-gray-600 mb-6">You haven't sent any requests yet</p>
                            <button onclick="switchTab('dashboard')" class="px-6 py-3 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
                                Browse Properties
                            </button>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading requests:', error);
            });
        }

        // Load inquiries for landlord
        function loadInquiries() {
            fetch('api/viewing_requests.php?landlord=1')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('inquiriesList');
                if (data.success && data.data.length > 0) {
                    container.innerHTML = data.data.map(request => createInquiryItem(request)).join('');
                } else {
                    container.innerHTML = `
                        <div class="text-center py-12">
                            <div class="w-24 h-24 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-6">
                                <i class="fas fa-inbox text-3xl text-gray-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mb-2">No Inquiries Yet</h3>
                            <p class="text-gray-600">When potential tenants send viewing requests, they'll appear here.</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading inquiries:', error);
            });
        }

        // Create viewing row
        function createViewingRow(request) {
            const statusColors = {
                'pending': 'yellow',
                'accepted': 'green',
                'rejected': 'red'
            };
            const color = statusColors[request.status] || 'gray';

            return `
                <tr class="border-b border-gray-50 hover:bg-gray-50">
                    <td class="py-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-lg overflow-hidden mr-3">
                                <img src="${request.image_url_1 || 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'}" alt="Property" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">${request.title}</p>
                                <p class="text-sm text-gray-600">KES ${request.price.toLocaleString()}/month</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-4">
                        <p class="font-medium">${new Date(request.created_at).toLocaleDateString()}</p>
                        <p class="text-sm text-gray-600">${request.status === 'pending' ? 'Awaiting confirmation' : 'Confirmed'}</p>
                    </td>
                    <td class="py-4">
                        <span class="px-3 py-1 bg-${color}-100 text-${color}-600 text-xs font-semibold rounded-full capitalize">${request.status}</span>
                    </td>
                    <td class="py-4">
                        <div class="flex space-x-2">
                            <button onclick="viewProperty(${request.house_id})" class="px-4 py-2 bg-blue-50 text-[#2FA4E7] font-semibold rounded-lg hover:bg-blue-100 transition-colors duration-300 text-sm">
                                View
                            </button>
                            ${request.status === 'pending' ? `<button onclick="cancelRequest(${request.id})" class="px-4 py-2 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors duration-300 text-sm">Cancel</button>` : ''}
                        </div>
                    </td>
                </tr>
            `;
        }

        // Create request row for My Requests tab
        function createRequestRow(request) {
            const statusColors = {
                'pending': 'yellow',
                'accepted': 'green',
                'rejected': 'red'
            };
            const color = statusColors[request.status] || 'gray';

            return `
                <tr class="border-b border-gray-50 hover:bg-gray-50">
                    <td class="py-4">
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-lg overflow-hidden mr-3">
                                <img src="${request.image_url_1 || 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'}" alt="Property" class="w-full h-full object-cover">
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">${request.title}</p>
                                <p class="text-sm text-gray-600">KES ${request.price.toLocaleString()}/month</p>
                            </div>
                        </div>
                    </td>
                    <td class="py-4">
                        <p class="font-medium">Landlord</p>
                        <p class="text-sm text-gray-600">Contact info available</p>
                    </td>
                    <td class="py-4">
                        <span class="px-3 py-1 bg-${color}-100 text-${color}-600 text-xs font-semibold rounded-full capitalize">${request.status}</span>
                    </td>
                    <td class="py-4">
                        <p class="font-medium">${new Date(request.created_at).toLocaleDateString()}</p>
                    </td>
                    <td class="py-4">
                        <div class="flex space-x-2">
                            <button onclick="viewProperty(${request.house_id})" class="px-4 py-2 bg-blue-50 text-[#2FA4E7] font-semibold rounded-lg hover:bg-blue-100 transition-colors duration-300 text-sm">
                                View
                            </button>
                            <button onclick="deleteRequest(${request.id})" class="px-4 py-2 bg-red-50 text-red-600 font-semibold rounded-lg hover:bg-red-100 transition-colors duration-300 text-sm">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }

        // Create inquiry item for landlord
        function createInquiryItem(request) {
            const statusColors = {
                'pending': 'yellow',
                'accepted': 'green',
                'rejected': 'red'
            };
            const color = statusColors[request.status] || 'gray';

            return `
                <a href="#" class="block p-6 border-b border-gray-100 hover:bg-blue-50 transition-colors duration-300">
                    <div class="flex">
                        <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mr-4 flex-shrink-0">
                            <span class="font-bold text-green-600">${(request.first_name + ' ' + request.last_name).split(' ').map(n => n[0]).join('').toUpperCase()}</span>
                        </div>
                        <div class="flex-1">
                            <div class="flex justify-between mb-2">
                                <h4 class="font-bold text-gray-800">${request.first_name} ${request.last_name}</h4>
                                <span class="text-sm text-gray-500">${new Date(request.created_at).toLocaleDateString()}</span>
                            </div>
                            <p class="text-gray-700 mb-2">${request.message || 'Viewing request for your property'}</p>
                            <div class="flex items-center">
                                <span class="px-2 py-1 bg-blue-100 text-blue-600 text-xs font-semibold rounded mr-3">Viewing Request</span>
                                <span class="text-sm text-gray-600">For: ${request.title}</span>
                            </div>
                        </div>
                    </div>
                </a>
            `;
        }

        // View property (redirect to houses page with modal)
        function viewProperty(houseId) {
            window.location.href = `index.php?page=houses&property=${houseId}`;
        }

        // Request viewing (redirect to houses page with modal)
        function requestViewing(houseId) {
            window.location.href = `index.php?page=houses&property=${houseId}&action=viewing`;
        }

        // Delete request
        function deleteRequest(requestId) {
            if (confirm('Are you sure you want to delete this request?')) {
                fetch('api/viewing_requests.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ request_id: requestId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        loadUserRequests(); // Reload the requests
                        showNotification('Request deleted successfully!', 'success');
                    } else {
                        showNotification(data.error || 'Failed to delete request', 'error');
                    }
                })
                .catch(error => {
                    showNotification('Network error. Please try again.', 'error');
                });
            }
        }

        // Cancel request (same as delete for pending)
        function cancelRequest(requestId) {
            deleteRequest(requestId);
        }

        // Load data when tabs are clicked
        document.addEventListener('click', function(e) {
            if (e.target.closest('.tab-link[data-tab="saved"]')) {
                setTimeout(loadSavedProperties, 100);
            } else if (e.target.closest('.tab-link[data-tab="viewings"]')) {
                setTimeout(loadViewings, 100);
            } else if (e.target.closest('.tab-link[data-tab="properties"]')) {
                setTimeout(loadUserProperties, 100);
            }
        });

        // Property management
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-property-btn')) {
                e.preventDefault();
                const btn = e.target.closest('.delete-property-btn');
                const propertyId = btn.dataset.propertyId;

                if (confirm('Are you sure you want to delete this property? This action cannot be undone.')) {
                    fetch('api/delete_house.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({id: propertyId})
                    }).then(res => res.json()).then(data => {
                        if (data.success) {
                            loadUserProperties();
                            showNotification('Property deleted successfully', 'success');
                        } else {
                            showNotification(data.error || 'Failed to delete property', 'error');
                        }
                    }).catch(() => {
                        showNotification('Network error. Please try again.', 'error');
                    });
                }
            } else if (e.target.closest('.edit-property-btn')) {
                const btn = e.target.closest('.edit-property-btn');
                const propertyId = btn.dataset.propertyId;
                // For now, just show a message
                showNotification('Edit functionality coming soon!', 'info');
            }
        });

        // Load user properties
        function loadUserProperties() {
            fetch('api/houses.php?landlord_id=<?php echo $_SESSION['user_id']; ?>')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderUserPropertiesGrid(data.data);
                    renderUserPropertiesTable(data.data);
                }
            })
            .catch(error => {
                console.error('Error loading user properties:', error);
            });
        }

        // Render user properties grid
        function renderUserPropertiesGrid(properties) {
            const grid = document.getElementById('userPropertiesGrid');
            grid.innerHTML = '';

            if (properties.length === 0) {
                grid.innerHTML = `
                    <div class="col-span-full text-center py-12">
                        <div class="w-24 h-24 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-6">
                            <i class="fas fa-home text-3xl text-gray-400"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">No Properties Listed</h3>
                        <p class="text-gray-600 mb-6">You haven't listed any properties yet</p>
                        <button id="addFirstPropertyBtn" class="px-6 py-3 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300">
                            Add Your First Property
                        </button>
                    </div>
                `;
                document.getElementById('addFirstPropertyBtn')?.addEventListener('click', () => {
                    openModal(addPropertyModal);
                    loadPropertyTypes();
                });
                return;
            }

            properties.forEach(property => {
                const card = document.createElement('div');
                card.className = 'dashboard-card bg-white rounded-2xl shadow-md overflow-hidden';

                const features = [];
                if (property.bedrooms) features.push(`<div class="flex items-center"><i class="fas fa-bed text-[#2FA4E7] mr-1"></i><span class="text-sm">${property.bedrooms} Bed</span></div>`);
                if (property.bathrooms) features.push(`<div class="flex items-center"><i class="fas fa-bath text-[#2FA4E7] mr-1"></i><span class="text-sm">${property.bathrooms} Bath</span></div>`);
                if (property.size_sqft) features.push(`<div class="flex items-center"><i class="fas fa-ruler-combined text-[#2FA4E7] mr-1"></i><span class="text-sm">${property.size_sqft} sqft</span></div>`);

                card.innerHTML = `
                    <div class="relative h-48">
                        <img src="${property.image_url_1 || 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'}" alt="Property" class="w-full h-full object-cover">
                        <div class="absolute top-4 left-4">
                            <span class="px-3 py-1 bg-green-100 text-green-600 text-xs font-semibold rounded-full">Active</span>
                        </div>
                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1 bg-white text-gray-800 font-bold rounded-lg">KES ${property.price.toLocaleString()}</span>
                        </div>
                    </div>

                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">${property.title}</h3>
                        <div class="flex items-center text-gray-600 mb-4">
                            <i class="fas fa-map-marker-alt text-[#3CB371] mr-2"></i>
                            <span>${property.location}</span>
                        </div>

                        <div class="flex flex-wrap gap-2 mb-6">
                            ${features.join('')}
                        </div>

                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-600">Viewings</p>
                                <p class="font-bold">0 scheduled</p>
                            </div>
                            <div class="space-x-2">
                                <button class="edit-property-btn px-4 py-2 bg-blue-50 text-[#2FA4E7] font-semibold rounded-lg hover:bg-blue-100 transition-colors duration-300 text-sm" data-property-id="${property.id}">
                                    Edit
                                </button>
                                <button class="delete-property-btn px-4 py-2 bg-red-50 text-red-600 font-semibold rounded-lg hover:bg-red-100 transition-colors duration-300 text-sm" data-property-id="${property.id}">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                grid.appendChild(card);
            });
        }

        // Render user properties table
        function renderUserPropertiesTable(properties) {
            const container = document.getElementById('userPropertiesTable');

            if (properties.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-12">
                        <div class="w-24 h-24 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-6">
                            <i class="fas fa-home text-3xl text-gray-400"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">No Properties Listed</h3>
                        <p class="text-gray-600">Start by adding your first property listing.</p>
                    </div>
                `;
                return;
            }

            const tableHTML = `
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="text-left py-3 text-gray-600 font-medium">Property</th>
                            <th class="text-left py-3 text-gray-600 font-medium">Status</th>
                            <th class="text-left py-3 text-gray-600 font-medium">Viewings</th>
                            <th class="text-left py-3 text-gray-600 font-medium">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${properties.map(property => `
                            <tr class="border-b border-gray-50 hover:bg-gray-50">
                                <td class="py-4">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 rounded-lg overflow-hidden mr-3">
                                            <img src="${property.image_url_1 || 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80'}" alt="Property" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800">${property.title}</p>
                                            <p class="text-sm text-gray-600">KES ${property.price.toLocaleString()}/month</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4">
                                    <span class="px-3 py-1 bg-green-100 text-green-600 text-xs font-semibold rounded-full">Active</span>
                                </td>
                                <td class="py-4">
                                    <span class="font-medium">0 scheduled</span>
                                </td>
                                <td class="py-4">
                                    <div class="space-x-2">
                                        <button class="edit-property-btn px-3 py-2 bg-blue-50 text-[#2FA4E7] font-semibold rounded-lg hover:bg-blue-100 transition-colors duration-300 text-sm" data-property-id="${property.id}">
                                            Edit
                                        </button>
                                        <button class="delete-property-btn px-3 py-2 bg-red-50 text-red-600 font-semibold rounded-lg hover:bg-red-100 transition-colors duration-300 text-sm" data-property-id="${property.id}">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
            container.innerHTML = tableHTML;
        }

        // Photo upload functionality
        const uploadBtn = document.querySelector('.upload-photo-btn');
        const removeBtn = document.querySelector('.remove-photo-btn');
        const photoInput = document.getElementById('photoInput');
        const profileImgs = document.querySelectorAll('img[alt="Profile"]');

        // Property photo upload
        const photoUploadArea = document.getElementById('photoUploadArea');
        const propertyPhotoInput = document.getElementById('propertyPhotoInput');
        const photoPreview = document.getElementById('photoPreview');
        const photoCount = document.getElementById('photoCount');
        const photoGrid = document.getElementById('photoGrid');
        const clearPhotos = document.getElementById('clearPhotos');

        if (photoUploadArea && propertyPhotoInput) {
            photoUploadArea.addEventListener('click', () => {
                propertyPhotoInput.click();
            });

            // Handle drag and drop
            photoUploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                photoUploadArea.classList.add('border-[#2FA4E7]');
            });

            photoUploadArea.addEventListener('dragleave', () => {
                photoUploadArea.classList.remove('border-[#2FA4E7]');
            });

            photoUploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                photoUploadArea.classList.remove('border-[#2FA4E7]');
                const files = e.dataTransfer.files;
                handleFiles(files);
            });
        }

        // Handle file selection
        propertyPhotoInput?.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });

        // Clear all photos
        clearPhotos?.addEventListener('click', () => {
            propertyPhotoInput.value = '';
            photoGrid.innerHTML = '';
            photoPreview.classList.add('hidden');
            updatePhotoCount();
        });

        function handleFiles(files) {
            // Limit to 3 files
            const validFiles = Array.from(files).filter(file => file.type.startsWith('image/')).slice(0, 3);

            // Set files to input
            const dt = new DataTransfer();
            validFiles.forEach(file => dt.items.add(file));
            propertyPhotoInput.files = dt.files;

            updatePhotoCount();
            displayPreviews(validFiles);
        }

        function updatePhotoCount() {
            const count = propertyPhotoInput?.files.length || 0;
            photoCount.textContent = `${count} photo${count !== 1 ? 's' : ''} selected`;
            photoPreview.classList.toggle('hidden', count === 0);
        }

        function displayPreviews(files) {
            photoGrid.innerHTML = '';
            files.forEach((file, index) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const previewDiv = document.createElement('div');
                    previewDiv.className = 'relative';
                    previewDiv.innerHTML = `
                        <img src="${e.target.result}" alt="Preview" class="w-full h-24 object-cover rounded-lg">
                        <button type="button" class="absolute top-1 right-1 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600" onclick="removePhoto(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    photoGrid.appendChild(previewDiv);
                };
                reader.readAsDataURL(file);
            });
        }

        // Remove individual photo
        window.removePhoto = function(index) {
            const dt = new DataTransfer();
            const files = Array.from(propertyPhotoInput.files);
            files.splice(index, 1);
            files.forEach(file => dt.items.add(file));
            propertyPhotoInput.files = dt.files;
            updatePhotoCount();
            displayPreviews(files);
        };

        // Load property types
        function loadPropertyTypes() {
            const select = document.getElementById('propertyTypeSelect');
            if (!select) return;

            fetch('api/property_types.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    select.innerHTML = '<option value="">Select type</option>';
                    data.data.forEach(type => {
                        const option = document.createElement('option');
                        option.value = type.slug;
                        option.textContent = type.name;
                        select.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error loading property types:', error);
            });
        }

        if (uploadBtn) {
            uploadBtn.addEventListener('click', () => {
                photoInput.click();
            });
        }

        if (photoInput) {
            photoInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    // Check file size (2MB max)
                    if (file.size > 2 * 1024 * 1024) {
                        showNotification('File too large. Please select an image under 2MB.', 'error');
                        return;
                    }

                    // Check file type
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    if (!allowedTypes.includes(file.type)) {
                        showNotification('Please select a valid image file (JPEG, PNG, GIF, WebP).', 'error');
                        return;
                    }

                    // Show loading
                    uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Uploading...';
                    uploadBtn.disabled = true;

                    const formData = new FormData();
                    formData.append('photo', file);

                    fetch('api/upload_photo.php', {
                        method: 'POST',
                        body: formData
                    }).then(res => res.json()).then(data => {
                        uploadBtn.innerHTML = 'Upload New Photo';
                        uploadBtn.disabled = false;

                        if (data.success) {
                            profileImgs.forEach(img => img.src = data.photo_url);
                            showNotification('Profile photo updated successfully!', 'success');
                        } else {
                            showNotification(data.error || 'Upload failed', 'error');
                        }
                    }).catch(() => {
                        uploadBtn.innerHTML = 'Upload New Photo';
                        uploadBtn.disabled = false;
                        showNotification('Upload failed', 'error');
                    });
                }
            });
        }

        if (removeBtn) {
            removeBtn.addEventListener('click', () => {
                if (confirm('Are you sure you want to remove your profile photo?')) {
                    fetch('api/remove_photo.php', {
                        method: 'POST'
                    }).then(res => res.json()).then(data => {
                        if (data.success) {
                            profileImg.src = 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80';
                            showNotification('Profile photo removed', 'info');
                        } else {
                            showNotification(data.error || 'Failed to remove photo', 'error');
                        }
                    });
                }
            });
        }

        // Switch account type
        const switchBtn = document.querySelector('.switch-account-btn');
        if (switchBtn) {
            switchBtn.addEventListener('click', () => {
                const currentType = '<?php echo $user_type; ?>';
                const newType = currentType === 'landlord' ? 'seeker' : 'landlord';
                const confirmMsg = `Are you sure you want to switch to ${newType === 'landlord' ? 'Landlord' : 'House Hunter'} account?`;

                if (confirm(confirmMsg)) {
                    switchBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Switching...';
                    switchBtn.disabled = true;

                    fetch('api/update_user_type.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify({user_type: newType})
                    }).then(res => res.json()).then(data => {
                        if (data.success) {
                            showNotification('Account type updated successfully!', 'success');
                            setTimeout(() => location.reload(), 1500);
                        } else {
                            switchBtn.innerHTML = 'Switch to ' + (newType === 'landlord' ? 'Landlord' : 'House Hunter');
                            switchBtn.disabled = false;
                            showNotification(data.error || 'Failed to update account type', 'error');
                        }
                    }).catch(() => {
                        switchBtn.innerHTML = 'Switch to ' + (newType === 'landlord' ? 'Landlord' : 'House Hunter');
                        switchBtn.disabled = false;
                        showNotification('Network error', 'error');
                    });
                }
            });
        }
    </script>
</body>
</html>