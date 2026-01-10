<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: login.php');
    exit();
}

require_once 'db.php';

// Function to get dashboard stats
function getDashboardStats($pdo) {
    $stats = [];

    // Total Users
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $stats['total_users'] = $stmt->fetch()['total'];

    // Total Properties
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM houses");
    $stats['total_properties'] = $stmt->fetch()['total'];

    // Pending Requests
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM requests WHERE status = 'pending'");
    $stats['pending_requests'] = $stmt->fetch()['total'];

    // Total Revenue (placeholder - would need payments table)
    $stats['revenue_today'] = 24800; // Placeholder

    return $stats;
}

// Function to get users
function getUsers($pdo, $type = 'all', $status = 'all') {
    $where = [];
    $params = [];

    if ($type !== 'all') {
        $where[] = "user_type = ?";
        $params[] = $type;
    }

    if ($status === 'pending') {
        $where[] = "email_verified = FALSE";
    } elseif ($status === 'verified') {
        $where[] = "email_verified = TRUE";
    }

    $whereClause = $where ? "WHERE " . implode(" AND ", $where) : "";

    $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, phone, user_type, email_verified, created_at FROM users $whereClause ORDER BY created_at DESC");
    $stmt->execute($params);
    return $stmt->fetchAll();
}

// Function to get properties
function getProperties($pdo) {
    $stmt = $pdo->prepare("
        SELECT h.*, u.first_name, u.last_name, a.name as area_name
        FROM houses h
        LEFT JOIN users u ON h.landlord_id = u.id
        LEFT JOIN areas a ON h.area_id = a.id
        ORDER BY h.created_at DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Function to get movers
function getMovers($pdo) {
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, phone, company_name, service_areas, created_at FROM users WHERE user_type = 'mover' ORDER BY created_at DESC");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Function to get requests
function getRequests($pdo) {
    $stmt = $pdo->prepare("
        SELECT r.*, h.title as house_title, uh.first_name as hunter_first, uh.last_name as hunter_last,
               ul.first_name as landlord_first, ul.last_name as landlord_last
        FROM requests r
        LEFT JOIN houses h ON r.house_id = h.id
        LEFT JOIN users uh ON r.house_hunter_id = uh.id
        LEFT JOIN users ul ON r.landlord_id = ul.id
        ORDER BY r.created_at DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Function to get messages
function getMessages($pdo) {
    $stmt = $pdo->prepare("
        SELECT m.*, u.first_name, u.last_name, u.email
        FROM messages m
        LEFT JOIN users u ON m.sender_id = u.id
        ORDER BY m.created_at DESC
        LIMIT 50
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Function to get contact messages
function getContactMessages($pdo) {
    $stmt = $pdo->prepare("
        SELECT *
        FROM contact_messages
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Function to get categories
function getCategories($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM categories ORDER BY created_at DESC");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Function to get filters
function getFilters($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM filters ORDER BY id ASC");
    $stmt->execute();
    return $stmt->fetchAll();
}

// Function to get property types
function getPropertyTypes($pdo) {
    $stmt = $pdo->prepare("SELECT * FROM property_types ORDER BY created_at DESC");
    $stmt->execute();
    return $stmt->fetchAll();
}

$stats = getDashboardStats($pdo);
$users = getUsers($pdo);
$properties = getProperties($pdo);
$movers = getMovers($pdo);
$requests = getRequests($pdo);
$messages = getMessages($pdo);
$contactMessages = getContactMessages($pdo);
$categories = getCategories($pdo);
$filters = getFilters($pdo);
$propertyTypes = getPropertyTypes($pdo);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Rheaspark</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
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

        /* Animations */
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        .floating {
            animation: float 3s infinite ease-in-out;
        }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(47, 164, 231, 0.1); }
            50% { box-shadow: 0 0 30px rgba(47, 164, 231, 0.2); }
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

        /* Sidebar Animation */
        .sidebar-item {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .sidebar-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 4px;
            height: 0;
            background: linear-gradient(to bottom, #2FA4E7, #3CB371);
            transition: height 0.3s ease;
        }

        .sidebar-item:hover::before,
        .sidebar-item.active::before {
            height: 100%;
        }

        /* Table Row Animation */
        @keyframes fadeInRow {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .fade-in-row {
            animation: fadeInRow 0.3s ease forwards;
        }

        /* Stats Card Animation */
        .stats-card {
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        /* Loading Animation */
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .spin {
            animation: spin 1s linear infinite;
        }

        /* Status Badges */
        .status-badge {
            font-size: 0.75rem;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
        }

        .status-active {
            background-color: rgba(60, 179, 113, 0.1);
            color: #3CB371;
        }

        .status-pending {
            background-color: rgba(255, 152, 0, 0.1);
            color: #FF9800;
        }

        .status-inactive {
            background-color: rgba(158, 158, 158, 0.1);
            color: #9E9E9E;
        }

        .status-verified {
            background-color: rgba(47, 164, 231, 0.1);
            color: #2FA4E7;
        }

        /* Tab Animation */
        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Action Button Animation */
        .action-btn {
            transition: all 0.2s ease;
        }

        .action-btn:hover {
            transform: scale(1.1);
        }

        /* Chart Container */
        .chart-container {
            position: relative;
            height: 300px;
        }

        /* Notification Bell Animation */
        @keyframes ring {
            0% { transform: rotate(0deg); }
            10% { transform: rotate(15deg); }
            20% { transform: rotate(-15deg); }
            30% { transform: rotate(15deg); }
            40% { transform: rotate(-15deg); }
            50% { transform: rotate(0deg); }
            100% { transform: rotate(0deg); }
        }

        .ring-animation {
            animation: ring 2s ease infinite;
        }

        /* Search Input Animation */
        .search-input {
            transition: all 0.3s ease;
        }

        .search-input:focus {
            transform: scale(1.02);
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Admin Layout -->
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div id="sidebar" class="w-64 bg-gradient-to-b from-gray-900 to-gray-800 text-white flex flex-col fixed inset-y-0 left-0 z-50 transform -translate-x-full md:translate-x-0 md:static md:inset-0 transition-transform duration-300 ease-in-out">
            <!-- Logo -->
            <div class="p-6 border-b border-gray-700">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] flex items-center justify-center mr-3">
                        <i class="fas fa-shield-alt text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold">Rheaspark</h1>
                        <p class="text-xs text-gray-400">Admin Panel</p>
                    </div>
                </div>
            </div>

            <!-- Admin Info -->
            <div class="p-6 border-b border-gray-700">
                <div class="flex items-center">
                    <div class="relative">
                        <div class="w-12 h-12 rounded-full bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] flex items-center justify-center border-2 border-[#3CB371]">
                            <i class="fas fa-user text-white text-lg"></i>
                        </div>
                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-gray-900"></div>
                    </div>
                    <div class="ml-4">
                        <h3 class="font-semibold">Admin User</h3>
                        <p class="text-sm text-gray-400">Super Administrator</p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                <a href="#" class="sidebar-item flex items-center p-3 rounded-lg hover:bg-gray-700 active" data-section="dashboard">
                    <i class="fas fa-tachometer-alt w-6 mr-3"></i>
                    <span>Dashboard</span>
                </a>

                <a href="#" class="sidebar-item flex items-center p-3 rounded-lg hover:bg-gray-700" data-section="users">
                    <i class="fas fa-users w-6 mr-3"></i>
                    <span>Users Management</span>
                    <span class="ml-auto bg-red-500 text-xs px-2 py-1 rounded-full"><?php echo count(array_filter($users, function($u) { return !$u['email_verified']; })); ?></span>
                </a>

                <a href="#" class="sidebar-item flex items-center p-3 rounded-lg hover:bg-gray-700" data-section="properties">
                    <i class="fas fa-home w-6 mr-3"></i>
                    <span>Properties</span>
                    <span class="ml-auto bg-blue-500 text-xs px-2 py-1 rounded-full"><?php echo count($properties); ?></span>
                </a>

                <a href="#" class="sidebar-item flex items-center p-3 rounded-lg hover:bg-gray-700" data-section="movers">
                    <i class="fas fa-truck w-6 mr-3"></i>
                    <span>Moving Services</span>
                    <span class="ml-auto bg-green-500 text-xs px-2 py-1 rounded-full"><?php echo count($movers); ?></span>
                </a>

                <a href="#" class="sidebar-item flex items-center p-3 rounded-lg hover:bg-gray-700" data-section="requests">
                    <i class="fas fa-handshake w-6 mr-3"></i>
                    <span>Requests & Bookings</span>
                    <span class="ml-auto bg-yellow-500 text-xs px-2 py-1 rounded-full"><?php echo count(array_filter($requests, function($r) { return $r['status'] === 'pending'; })); ?></span>
                </a>

                <a href="#" class="sidebar-item flex items-center p-3 rounded-lg hover:bg-gray-700" data-section="contacts">
                    <i class="fas fa-envelope w-6 mr-3"></i>
                    <span>Contact Messages</span>
                    <span class="ml-auto bg-purple-500 text-xs px-2 py-1 rounded-full"><?php echo count(array_filter($contactMessages, function($m) { return $m['status'] === 'unread'; })); ?></span>
                </a>

                <a href="#" class="sidebar-item flex items-center p-3 rounded-lg hover:bg-gray-700" data-section="categories">
                    <i class="fas fa-tags w-6 mr-3"></i>
                    <span>CATEGORIES</span>
                </a>

                <a href="#" class="sidebar-item flex items-center p-3 rounded-lg hover:bg-gray-700" data-section="propertytypes">
                    <i class="fas fa-building w-6 mr-3"></i>
                    <span>Property Types</span>
                    <span class="ml-auto bg-blue-500 text-xs px-2 py-1 rounded-full"><?php echo count($propertyTypes); ?></span>
                </a>

                <a href="#" class="sidebar-item flex items-center p-3 rounded-lg hover:bg-gray-700" data-section="settings">
                    <i class="fas fa-cog w-6 mr-3"></i>
                    <span>Settings</span>
                </a>
            </nav>

            <!-- Logout -->
            <div class="p-4 border-t border-gray-700">
                <a href="logout.php" class="flex items-center p-3 rounded-lg hover:bg-gray-700 w-full text-left">
                    <i class="fas fa-sign-out-alt w-6 mr-3"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>

        <!-- Mobile Overlay -->
        <div id="mobileOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 md:hidden hidden"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Bar -->
            <header class="bg-white border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <!-- Mobile Menu Button -->
                    <button id="mobileMenuBtn" class="md:hidden text-gray-600 hover:text-[#2FA4E7] transition-colors duration-300 mr-4">
                        <i class="fas fa-bars text-xl"></i>
                    </button>

                    <!-- Search -->
                    <div class="flex-1 max-w-2xl">
                        <div class="relative">
                            <input
                                type="text"
                                placeholder="Search users, properties, requests..."
                                class="search-input w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] focus:ring-2 focus:ring-blue-100"
                            >
                            <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Right Actions -->
                    <div class="flex items-center space-x-6">
                        <!-- Notifications -->
                        <div class="relative">
                            <button id="notificationsBtn" class="relative text-gray-600 hover:text-[#2FA4E7] transition-colors duration-300">
                                <i class="fas fa-bell text-xl ring-animation"></i>
                                <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                            </button>
                        </div>

                        <!-- Quick Actions -->
                        <!-- <div class="relative">
                            <button id="quickActionsBtn" class="flex items-center space-x-3 px-4 py-2 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white rounded-xl hover:shadow-lg transition-all duration-300">
                                <i class="fas fa-plus"></i>
                                <span>Quick Actions</span>
                                <i class="fas fa-chevron-down ml-2 text-sm"></i>
                            </button>
                        </div> -->

                        <!-- Date & Time -->
                        <div class="text-right">
                            <div class="text-sm text-gray-600" id="currentDate"><?php echo date('l, M j, Y'); ?></div>
                            <div class="text-lg font-semibold text-gray-800" id="currentTime"><?php echo date('H:i A'); ?></div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-6">
                <!-- Dashboard Section (Default) -->
                <section id="dashboard" class="section-content active">
                    <!-- Welcome & Stats -->
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">Welcome back, Admin!</h1>
                        <p class="text-gray-600">Here's what's happening with your platform today.</p>
                    </div>

                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="stats-card bg-white rounded-2xl shadow p-6 border-l-4 border-[#2FA4E7]">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Total Users</p>
                                    <p class="text-3xl font-bold text-gray-800"><?php echo number_format($stats['total_users']); ?></p>
                                </div>
                                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-users text-[#2FA4E7] text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center text-sm text-green-600">
                                    <i class="fas fa-arrow-up mr-1"></i>
                                    <span>12.5% increase</span>
                                </div>
                            </div>
                        </div>

                        <div class="stats-card bg-white rounded-2xl shadow p-6 border-l-4 border-[#3CB371]">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Total Properties</p>
                                    <p class="text-3xl font-bold text-gray-800"><?php echo number_format($stats['total_properties']); ?></p>
                                </div>
                                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-home text-[#3CB371] text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center text-sm text-green-600">
                                    <i class="fas fa-arrow-up mr-1"></i>
                                    <span>8.2% increase</span>
                                </div>
                            </div>
                        </div>

                        <div class="stats-card bg-white rounded-2xl shadow p-6 border-l-4 border-[#FF9800]">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Pending Requests</p>
                                    <p class="text-3xl font-bold text-gray-800"><?php echo number_format($stats['pending_requests']); ?></p>
                                </div>
                                <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                                    <i class="fas fa-clock text-[#FF9800] text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center text-sm text-red-600">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    <span>Requires attention</span>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="stats-card bg-white rounded-2xl shadow p-6 border-l-4 border-[#9C27B0]">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Revenue (Today)</p>
                                    <p class="text-3xl font-bold text-gray-800">KES <?php echo number_format($stats['revenue_today']); ?></p>
                                </div>
                                <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                                    <i class="fas fa-money-bill-wave text-[#9C27B0] text-xl"></i>
                                </div>
                            </div>
                            <div class="mt-4">
                                <div class="flex items-center text-sm text-green-600">
                                    <i class="fas fa-arrow-up mr-1"></i>
                                    <span>KES 8,400 yesterday</span>
                                </div>
                            </div>
                        </div> -->
                    </div>

                    <!-- Charts & Quick Actions -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                        <!-- Chart -->
                        <div class="lg:col-span-2 bg-white rounded-2xl shadow p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-semibold text-gray-800">Platform Activity</h3>
                                <select class="border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:border-[#2FA4E7]">
                                    <option>Last 7 days</option>
                                    <option>Last 30 days</option>
                                    <option>Last 90 days</option>
                                </select>
                            </div>
                            <div class="chart-container">
                                <canvas id="activityChart"></canvas>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-white rounded-2xl shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-6">Quick Actions</h3>
                            <div class="space-y-4">
                                <button id="addPropertyBtn" class="quick-action-btn w-full flex items-center p-4 rounded-xl border border-gray-200 hover:border-[#2FA4E7] hover:bg-blue-50 transition-all duration-300">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                                        <i class="fas fa-plus text-[#2FA4E7]"></i>
                                    </div>
                                    <div class="text-left">
                                        <div class="font-medium text-gray-800">Add New Property</div>
                                        <div class="text-sm text-gray-600">Post a verified listing</div>
                                    </div>
                                </button>

                                <button id="verifyUserBtn" class="quick-action-btn w-full flex items-center p-4 rounded-xl border border-gray-200 hover:border-[#3CB371] hover:bg-green-50 transition-all duration-300">
                                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-4">
                                        <i class="fas fa-user-check text-[#3CB371]"></i>
                                    </div>
                                    <div class="text-left">
                                        <div class="font-medium text-gray-800">Verify Users</div>
                                        <div class="text-sm text-gray-600">Approve pending verifications</div>
                                    </div>
                                </button>

                                <button id="viewRequestsBtn" class="quick-action-btn w-full flex items-center p-4 rounded-xl border border-gray-200 hover:border-[#FF9800] hover:bg-orange-50 transition-all duration-300">
                                    <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center mr-4">
                                        <i class="fas fa-handshake text-[#FF9800]"></i>
                                    </div>
                                    <div class="text-left">
                                        <div class="font-medium text-gray-800">View Requests</div>
                                        <div class="text-sm text-gray-600"><?php echo $stats['pending_requests']; ?> pending requests</div>
                                    </div>
                                </button>

                                <button id="generateReportBtn" class="quick-action-btn w-full flex items-center p-4 rounded-xl border border-gray-200 hover:border-[#9C27B0] hover:bg-purple-50 transition-all duration-300">
                                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-4">
                                        <i class="fas fa-chart-pie text-[#9C27B0]"></i>
                                    </div>
                                    <div class="text-left">
                                        <div class="font-medium text-gray-800">Generate Report</div>
                                        <div class="text-sm text-gray-600">Monthly performance report</div>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-white rounded-2xl shadow p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-800">Recent Activity</h3>
                            <button class="text-[#2FA4E7] font-medium hover:text-[#3CB371] transition-colors duration-300">
                                View All
                            </button>
                        </div>

                        <div class="space-y-4">
                            <?php
                            // Get recent activities (simplified - would need activity log table)
                            $recentActivities = array_slice(array_merge(
                                array_map(function($u) { return ['type' => 'user', 'data' => $u, 'time' => strtotime($u['created_at'])]; }, array_slice($users, 0, 2)),
                                array_map(function($h) { return ['type' => 'house', 'data' => $h, 'time' => strtotime($h['created_at'])]; }, array_slice($properties, 0, 2))
                            ), 0, 4);

                            usort($recentActivities, function($a, $b) { return $b['time'] - $a['time']; });

                            foreach ($recentActivities as $activity):
                                if ($activity['type'] === 'user'):
                            ?>
                            <div class="flex items-center p-4 rounded-xl bg-green-50/50">
                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-4">
                                    <i class="fas fa-user-check text-[#3CB371]"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-800">New User Registered</div>
                                    <div class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($activity['data']['first_name'] . ' ' . $activity['data']['last_name']); ?> joined as <?php echo ucfirst($activity['data']['user_type']); ?></div>
                                </div>
                                <div class="text-sm text-gray-500"><?php echo date('M j, H:i', $activity['time']); ?></div>
                            </div>
                            <?php else: ?>
                            <div class="flex items-center p-4 rounded-xl bg-blue-50/50">
                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                                    <i class="fas fa-home text-[#2FA4E7]"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-800">New Property Listed</div>
                                    <div class="text-sm text-gray-600 mt-1"><?php echo htmlspecialchars($activity['data']['title']); ?> in <?php echo htmlspecialchars($activity['data']['area_name'] ?? $activity['data']['location']); ?></div>
                                </div>
                                <div class="text-sm text-gray-500"><?php echo date('M j, H:i', $activity['time']); ?></div>
                            </div>
                            <?php endif; endforeach; ?>
                        </div>
                    </div>
                </section>

                <!-- Users Management Section -->
                <section id="users" class="section-content hidden">
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">Users Management</h1>
                        <p class="text-gray-600">Manage all users: house seekers, landlords, and movers</p>
                    </div>

                    <!-- Tabs -->
                    <!-- <div class="mb-6">
                        <div class="border-b border-gray-200">
                            <div class="flex space-x-8">
                                <button class="tab-btn py-3 px-1 border-b-2 border-transparent text-gray-600 hover:text-gray-800 font-medium active" data-tab="all-users">
                                    All Users (<?php echo count($users); ?>)
                                </button>
                                <button class="tab-btn py-3 px-1 border-b-2 border-transparent text-gray-600 hover:text-gray-800 font-medium" data-tab="house-seekers">
                                    House Seekers (<?php echo count(array_filter($users, function($u) { return $u['user_type'] === 'seeker'; })); ?>)
                                </button>
                                <button class="tab-btn py-3 px-1 border-b-2 border-transparent text-gray-600 hover:text-gray-800 font-medium" data-tab="landlords">
                                    Landlords (<?php echo count(array_filter($users, function($u) { return $u['user_type'] === 'landlord'; })); ?>)
                                </button>
                                <button class="tab-btn py-3 px-1 border-b-2 border-transparent text-gray-600 hover:text-gray-800 font-medium" data-tab="movers">
                                    Movers (<?php echo count($movers); ?>)
                                </button>
                                <button class="tab-btn py-3 px-1 border-b-2 border-transparent text-gray-600 hover:text-gray-800 font-medium" data-tab="pending">
                                    Pending Verification (<?php echo count(array_filter($users, function($u) { return !$u['email_verified']; })); ?>)
                                </button>
                            </div>
                        </div>
                    </div> -->

                    <!-- Users Table -->
                    <div class="bg-white rounded-2xl shadow overflow-hidden">
                        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Users List</h3>
                                <p class="text-sm text-gray-600">Showing <?php echo count($users); ?> users</p>
                            </div>
                            <div class="flex space-x-3">
                                <button class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-300">
                                    Export
                                </button>
                                <button id="addUserBtn" class="px-4 py-2 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-medium rounded-lg hover:shadow-lg transition-all duration-300">
                                    Add User
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200" id="usersTable">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($users as $index => $user): ?>
                                    <tr class="fade-in-row" style="animation-delay: <?php echo $index * 0.1; ?>s">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                                    <i class="fas fa-user text-gray-600"></i>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></div>
                                                    <div class="text-sm text-gray-500">ID: USER-<?php echo $user['id']; ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 rounded-full <?php
                                                    echo $user['user_type'] === 'landlord' ? 'bg-green-100' :
                                                         ($user['user_type'] === 'mover' ? 'bg-blue-100' : 'bg-gray-100');
                                                ?> flex items-center justify-center mr-2">
                                                    <i class="fas <?php
                                                        echo $user['user_type'] === 'landlord' ? 'fa-home text-green-600' :
                                                             ($user['user_type'] === 'mover' ? 'fa-truck text-blue-600' : 'fa-search text-gray-600');
                                                    ?> text-sm"></i>
                                                </div>
                                                <span class="text-gray-900"><?php echo ucfirst($user['user_type']); ?></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($user['email']); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="status-badge <?php echo $user['email_verified'] ? 'status-verified' : 'status-pending'; ?>">
                                                <i class="fas <?php echo $user['email_verified'] ? 'fa-shield-alt' : 'fa-clock'; ?> mr-1"></i>
                                                <?php echo $user['email_verified'] ? 'Verified' : 'Pending'; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button class="action-btn text-[#2FA4E7] hover:text-blue-700" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="action-btn text-[#3CB371] hover:text-green-700" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="action-btn text-[#FF9800] hover:text-orange-700" title="Suspend" onclick="showConfirmation('suspendUser', <?php echo $user['id']; ?>, 'Are you sure you want to suspend this user?')">
                                                    <i class="fas fa-user-slash"></i>
                                                </button>
                                                <button class="action-btn text-[#F44336] hover:text-red-700" title="Delete" onclick="if(confirm('Are you sure you want to delete this user? This action cannot be undone.')) { deleteUser(<?php echo $user['id']; ?>) }">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- Properties Management Section -->
                <section id="properties" class="section-content hidden">
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">Properties Management</h1>
                        <p class="text-gray-600">Manage all rental properties and listings</p>
                    </div>

                    <!-- Properties Table -->
                    <div class="bg-white rounded-2xl shadow overflow-hidden">
                        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">All Properties</h3>
                                <p class="text-sm text-gray-600"><?php echo count($properties); ?> properties listed</p>
                            </div>
                            <div class="flex space-x-3">
                                <button class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-300">
                                    Filter
                                </button>
                                <button id="addPropertyModalBtn" class="px-4 py-2 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-medium rounded-lg hover:shadow-lg transition-all duration-300">
                                    Add Property
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200" id="propertiesTable">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Landlord</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($properties as $index => $property): ?>
                                    <tr class="fade-in-row" style="animation-delay: <?php echo $index * 0.1; ?>s">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center mr-3">
                                                    <i class="fas fa-home text-gray-600"></i>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900"><?php echo htmlspecialchars($property['title']); ?></div>
                                                    <div class="text-sm text-gray-500">ID: PROP-<?php echo $property['id']; ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <i class="fas fa-map-marker-alt text-[#3CB371] mr-2"></i>
                                                <span class="text-gray-900"><?php echo htmlspecialchars($property['area_name'] ?? $property['location']); ?></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-900 font-semibold">
                                            KES <?php echo number_format($property['price']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="status-badge <?php echo $property['verified'] ? 'status-verified' : 'status-pending'; ?>">
                                                <i class="fas <?php echo $property['verified'] ? 'fa-check-circle' : 'fa-clock'; ?> mr-1"></i>
                                                <?php echo $property['verified'] ? 'Verified' : 'Pending'; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars(($property['first_name'] ?? '') . ' ' . ($property['last_name'] ?? '')); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button class="action-btn text-[#2FA4E7] hover:text-blue-700" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="action-btn text-[#3CB371] hover:text-green-700" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="action-btn text-[#F44336] hover:text-red-700" title="Delete" onclick="showConfirmation('deleteProperty', <?php echo $property['id']; ?>, 'Are you sure you want to delete this property? This action cannot be undone.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- Moving Services Section -->
                <section id="movers" class="section-content hidden">
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">Moving Services Management</h1>
                        <p class="text-gray-600">Manage all registered moving service providers</p>
                    </div>

                    <!-- Movers Table -->
                    <div class="bg-white rounded-2xl shadow overflow-hidden">
                        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Moving Services</h3>
                                <p class="text-sm text-gray-600"><?php echo count($movers); ?> registered movers</p>
                            </div>
                            <button id="addMoverBtn" class="px-4 py-2 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-medium rounded-lg hover:shadow-lg transition-all duration-300">
                                Add Mover
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200" id="moversTable">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service Areas</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($movers as $index => $mover): ?>
                                    <tr class="fade-in-row" style="animation-delay: <?php echo $index * 0.1; ?>s">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                                    <i class="fas fa-truck text-blue-600"></i>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900"><?php echo htmlspecialchars($mover['company_name'] ?? $mover['first_name'] . ' ' . $mover['last_name']); ?></div>
                                                    <div class="text-sm text-gray-500">ID: MOVER-<?php echo $mover['id']; ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($mover['email']); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($mover['phone'] ?? 'N/A'); ?></div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <?php echo htmlspecialchars($mover['service_areas'] ?? 'Nairobi Area'); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="status-badge status-active">
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Active
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button class="action-btn text-[#2FA4E7] hover:text-blue-700" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="action-btn text-[#3CB371] hover:text-green-700" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="action-btn text-[#F44336] hover:text-red-700" title="Delete" onclick="showConfirmation('deleteMover', <?php echo $mover['id']; ?>, 'Are you sure you want to delete this mover? This action cannot be undone.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- Requests & Bookings Section -->
                <section id="requests" class="section-content hidden">
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">Requests & Bookings</h1>
                        <p class="text-gray-600">Manage all viewing requests and moving service bookings</p>
                    </div>

                    <!-- Requests Table -->
                    <div class="bg-white rounded-2xl shadow overflow-hidden">
                        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">All Requests</h3>
                                <p class="text-sm text-gray-600"><?php echo count($requests); ?> total requests</p>
                            </div>
                            <div class="flex space-x-3">
                                <button class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-300">
                                    Filter
                                </button>
                                <button class="px-4 py-2 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-medium rounded-lg hover:shadow-lg transition-all duration-300">
                                    Mark All as Read
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200" id="requestsTable">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($requests as $index => $request): ?>
                                    <tr class="fade-in-row" style="animation-delay: <?php echo $index * 0.1; ?>s">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-full <?php echo $request['request_type'] === 'visit' ? 'bg-blue-100' : 'bg-gray-100'; ?> flex items-center justify-center mr-3">
                                                    <i class="fas <?php echo $request['request_type'] === 'visit' ? 'fa-calendar-check text-blue-600' : 'fa-envelope text-gray-600'; ?>"></i>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900"><?php echo ucfirst($request['request_type']); ?> Request</div>
                                                    <div class="text-sm text-gray-500">ID: REQ-<?php echo $request['id']; ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($request['hunter_first'] . ' ' . $request['hunter_last']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($request['landlord_first'] . ' ' . $request['landlord_last']); ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <?php echo htmlspecialchars($request['house_title'] ?? 'N/A'); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="status-badge <?php
                                                echo $request['status'] === 'accepted' ? 'status-active' :
                                                     ($request['status'] === 'rejected' ? 'status-inactive' : 'status-pending');
                                            ?>">
                                                <i class="fas <?php
                                                    echo $request['status'] === 'accepted' ? 'fa-check-circle' :
                                                         ($request['status'] === 'rejected' ? 'fa-times-circle' : 'fa-clock');
                                                ?> mr-1"></i>
                                                <?php echo ucfirst($request['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button class="action-btn text-[#2FA4E7] hover:text-blue-700" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <?php if ($request['status'] === 'pending'): ?>
                                                <button class="action-btn text-[#3CB371] hover:text-green-700" title="Accept">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="action-btn text-[#F44336] hover:text-red-700" title="Reject">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- Contact Messages Section -->
                <section id="contacts" class="section-content hidden">
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">Contact Messages</h1>
                        <p class="text-gray-600">Manage all customer inquiries and support messages</p>
                    </div>

                    <!-- Contact Table -->
                    <div class="bg-white rounded-2xl shadow overflow-hidden">
                        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Contact Messages</h3>
                                <p class="text-sm text-gray-600"><?php echo count($contactMessages); ?> total messages</p>
                            </div>
                            <div class="flex space-x-3">
                                <button class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-300">
                                    Filter
                                </button>
                                <button class="px-4 py-2 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-medium rounded-lg hover:shadow-lg transition-all duration-300">
                                    Mark All as Read
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200" id="contactsTable">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Message</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (empty($contactMessages)): ?>
                                    <tr>
                                        <td colspan="6" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center">
                                                <i class="fas fa-envelope text-gray-300 text-4xl mb-4"></i>
                                                <h3 class="text-lg font-medium text-gray-900 mb-2">No contact messages yet</h3>
                                                <p class="text-gray-500">Contact messages from the website will appear here.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($contactMessages as $index => $message): ?>
                                    <tr class="fade-in-row" style="animation-delay: <?php echo $index * 0.1; ?>s">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                                    <i class="fas fa-user text-gray-600"></i>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900"><?php echo htmlspecialchars($message['name']); ?></div>
                                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($message['email']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($message['subject']); ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                                            <?php echo htmlspecialchars(substr($message['message'], 0, 100)) . (strlen($message['message']) > 100 ? '...' : ''); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo date('M j, H:i', strtotime($message['created_at'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="status-badge <?php
                                                echo $message['status'] === 'read' ? 'status-active' :
                                                     ($message['status'] === 'replied' ? 'status-verified' : 'status-pending');
                                            ?>">
                                                <i class="fas <?php
                                                    echo $message['status'] === 'read' ? 'fa-check-circle' :
                                                         ($message['status'] === 'replied' ? 'fa-reply' : 'fa-envelope');
                                                ?> mr-1"></i>
                                                <?php echo ucfirst($message['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button class="action-btn text-[#2FA4E7] hover:text-blue-700" title="View" onclick="viewContactMessage(<?php echo $message['id']; ?>)">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="action-btn text-[#3CB371] hover:text-green-700" title="Reply" onclick="replyToContactMessage(<?php echo $message['id']; ?>)">
                                                    <i class="fas fa-reply"></i>
                                                </button>
                                                <button class="action-btn text-[#F44336] hover:text-red-700" title="Delete" onclick="showConfirmation('deleteContactMessage', <?php echo $message['id']; ?>, 'Are you sure you want to delete this contact message?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- Property Types Section -->
                <section id="propertytypes" class="section-content hidden">
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">Property Types Management</h1>
                        <p class="text-gray-600">Manage property types for property listings</p>
                    </div>

                    <div class="bg-white rounded-2xl shadow overflow-hidden">
                        <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-800">Property Types</h3>
                                <p class="text-sm text-gray-600">Manage property types</p>
                            </div>
                            <button id="addPropertyTypeBtn" class="px-4 py-2 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-medium rounded-lg hover:shadow-lg transition-all duration-300">
                                Add Property Type
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200" id="propertyTypesTable">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($propertyTypes as $index => $propertyType): ?>
                                    <tr class="fade-in-row" style="animation-delay: <?php echo $index * 0.1; ?>s">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 rounded-full mr-3" style="background-color: <?php echo htmlspecialchars($propertyType['color']); ?>"></div>
                                                <div class="font-medium text-gray-900"><?php echo htmlspecialchars($propertyType['name']); ?></div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($propertyType['slug']); ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                                            <?php echo htmlspecialchars($propertyType['description'] ?? ''); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($propertyType['color']); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <button class="action-btn text-[#2FA4E7] hover:text-blue-700" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="action-btn text-[#F44336] hover:text-red-700" title="Delete" onclick="showConfirmation('deletePropertyType', <?php echo $propertyType['id']; ?>, 'Are you sure you want to delete this property type? This action cannot be undone.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <!-- Settings Section -->
                <section id="settings" class="section-content hidden">
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">Settings</h1>
                        <p class="text-gray-600">Manage your admin account settings</p>
                    </div>

                    <!-- Settings Content -->
                    <div class="max-w-2xl">
                        <div class="bg-white rounded-2xl shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-6">Change Password</h3>

                            <form id="changePasswordForm" class="space-y-6">
                                <div>
                                    <label for="currentPassword" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                                    <input
                                        type="password"
                                        id="currentPassword"
                                        name="currentPassword"
                                        required
                                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] focus:ring-2 focus:ring-blue-100"
                                        placeholder="Enter your current password"
                                    >
                                </div>

                                <div>
                                    <label for="newPassword" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                                    <input
                                        type="password"
                                        id="newPassword"
                                        name="newPassword"
                                        required
                                        minlength="8"
                                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] focus:ring-2 focus:ring-blue-100"
                                        placeholder="Enter your new password"
                                    >
                                </div>

                                <div>
                                    <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                                    <input
                                        type="password"
                                        id="confirmPassword"
                                        name="confirmPassword"
                                        required
                                        minlength="8"
                                        class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] focus:ring-2 focus:ring-blue-100"
                                        placeholder="Confirm your new password"
                                    >
                                </div>

                                <div class="flex space-x-4">
                                    <button
                                        type="submit"
                                        id="changePasswordBtn"
                                        class="px-6 py-3 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold rounded-xl hover:shadow-lg transition-all duration-300"
                                    >
                                        Change Password
                                    </button>
                                    <button
                                        type="button"
                                        id="cancelChangePassword"
                                        class="px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-colors duration-300"
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </section>

                <!-- Categories Section -->
                <section id="categories" class="section-content hidden">
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">Categories & Filters Management</h1>
                        <p class="text-gray-600">Manage categories and filters for property listings</p>
                    </div>

                    <!-- Tabs for Categories and Filters -->
                    <div class="mb-6">
                        <div class="border-b border-gray-200">
                            <div class="flex space-x-8">
                                <button class="tab-btn py-3 px-1 border-b-2 border-[#2FA4E7] text-[#2FA4E7] font-medium active" data-tab="categories-tab">
                                    Categories (<?php echo count($categories); ?>)
                                </button>
                                <button class="tab-btn py-3 px-1 border-b-2 border-transparent text-gray-600 hover:text-gray-800 font-medium" data-tab="filters-tab">
                                    Filters (<?php echo count($filters); ?>)
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Categories Tab Content -->
                    <div id="categories-tab" class="tab-content hidden">
                        <div class="bg-white rounded-2xl shadow overflow-hidden">
                            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Categories</h3>
                                    <p class="text-sm text-gray-600">Manage property categories</p>
                                </div>
                                <button id="addCategoryBtn" class="px-4 py-2 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-medium rounded-lg hover:shadow-lg transition-all duration-300">
                                    Add Category
                                </button>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200" id="categoriesTable">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php foreach ($categories as $index => $category): ?>
                                        <tr class="fade-in-row" style="animation-delay: <?php echo $index * 0.1; ?>s">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 rounded-full mr-3" style="background-color: <?php echo htmlspecialchars($category['color']); ?>"></div>
                                                    <div class="font-medium text-gray-900"><?php echo htmlspecialchars($category['name']); ?></div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?php echo htmlspecialchars($category['slug']); ?>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900 max-w-xs truncate">
                                                <?php echo htmlspecialchars($category['description'] ?? ''); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?php echo htmlspecialchars($category['color']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <button class="action-btn text-[#2FA4E7] hover:text-blue-700" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="action-btn text-[#F44336] hover:text-red-700" title="Delete" onclick="showConfirmation('deleteCategory', <?php echo $category['id']; ?>, 'Are you sure you want to delete this category? This action cannot be undone.')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Filters Tab Content -->
                    <div id="filters-tab" class="tab-content hidden">
                        <div class="bg-white rounded-2xl shadow overflow-hidden">
                            <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">Filters</h3>
                                    <p class="text-sm text-gray-600">Manage property filters</p>
                                </div>
                                <button id="addFilterBtn" class="px-4 py-2 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-medium rounded-lg hover:shadow-lg transition-all duration-300">
                                    Add Filter
                                </button>
                            </div>

                            <p>Property Types Content Here</p>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200" id="filtersTable">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Filter</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php foreach ($filters as $index => $filter): ?>
                                        <tr class="fade-in-row" style="animation-delay: <?php echo $index * 0.1; ?>s">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center mr-3">
                                                        <i class="fas fa-filter text-gray-600"></i>
                                                    </div>
                                                    <div class="font-medium text-gray-900"><?php echo htmlspecialchars($filter['name']); ?></div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?php echo htmlspecialchars($filter['slug']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?php echo htmlspecialchars($filter['category']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <button class="action-btn text-[#2FA4E7] hover:text-blue-700" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="action-btn text-[#F44336] hover:text-red-700" title="Delete" onclick="showConfirmation('deleteFilter', <?php echo $filter['id']; ?>, 'Are you sure you want to delete this filter? This action cannot be undone.')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmationModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="modal-enter bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 id="confirmTitle" class="text-xl font-bold text-gray-800">Confirm Action</h3>
                        <button id="cancelConfirm" class="text-gray-500 hover:text-gray-800 text-2xl">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="mb-6">
                        <p id="confirmMessage" class="text-gray-600">Are you sure you want to proceed with this action?</p>
                    </div>

                    <div class="flex space-x-3">
                        <button id="confirmAction" class="flex-1 bg-gradient-to-r from-[#F44336] to-[#E53935] text-white font-semibold py-3 px-6 rounded-xl hover:shadow-lg transition-all duration-300">
                            Confirm
                        </button>
                        <button id="cancelConfirmBtn" class="flex-1 border border-gray-300 text-gray-700 font-semibold py-3 px-6 rounded-xl hover:bg-gray-50 transition-colors duration-300">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div id="addCategoryModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="modal-enter bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-800">Add New Category</h3>
                        <button id="closeAddCategoryModal" class="text-gray-500 hover:text-gray-800 text-2xl">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form id="addCategoryForm" class="space-y-4">
                        <div>
                            <label for="categoryName" class="block text-sm font-medium text-gray-700 mb-2">Category Name</label>
                            <input
                                type="text"
                                id="categoryName"
                                name="name"
                                required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] focus:ring-2 focus:ring-blue-100"
                                placeholder="Enter category name"
                            >
                        </div>

                        <div>
                            <label for="categorySlug" class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                            <input
                                type="text"
                                id="categorySlug"
                                name="slug"
                                required
                                pattern="[a-z0-9-]+"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] focus:ring-2 focus:ring-blue-100"
                                placeholder="enter-slug-here"
                            >
                        </div>

                        <div>
                            <label for="categoryDescription" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea
                                id="categoryDescription"
                                name="description"
                                rows="3"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] focus:ring-2 focus:ring-blue-100"
                                placeholder="Enter category description"
                            ></textarea>
                        </div>

                        <div>
                            <label for="categoryColor" class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                            <input
                                type="color"
                                id="categoryColor"
                                name="color"
                                value="#2FA4E7"
                                class="w-full h-12 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] focus:ring-2 focus:ring-blue-100"
                            >
                        </div>

                        <div class="flex space-x-3 pt-4">
                            <button
                                type="submit"
                                id="saveCategoryBtn"
                                class="flex-1 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold py-3 px-6 rounded-xl hover:shadow-lg transition-all duration-300"
                            >
                                Add Category
                            </button>
                            <button
                                type="button"
                                id="cancelAddCategory"
                                class="flex-1 border border-gray-300 text-gray-700 font-semibold py-3 px-6 rounded-xl hover:bg-gray-50 transition-colors duration-300"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Filter Modal -->
    <div id="addFilterModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="modal-enter bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-800">Add New Filter</h3>
                        <button id="closeAddFilterModal" class="text-gray-500 hover:text-gray-800 text-2xl">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form id="addFilterForm" class="space-y-4">
                        <div>
                            <label for="filterName" class="block text-sm font-medium text-gray-700 mb-2">Filter Name</label>
                            <input
                                type="text"
                                id="filterName"
                                name="name"
                                required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] focus:ring-2 focus:ring-blue-100"
                                placeholder="Enter filter name"
                            >
                        </div>

                        <div>
                            <label for="filterSlug" class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                            <input
                                type="text"
                                id="filterSlug"
                                name="slug"
                                required
                                pattern="[a-z0-9-]+"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] focus:ring-2 focus:ring-blue-100"
                                placeholder="enter-slug-here"
                            >
                        </div>

                        <div>
                            <label for="filterCategory" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select
                                id="filterCategory"
                                name="category"
                                required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] focus:ring-2 focus:ring-blue-100"
                            >
                                <option value="">Select category</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['slug']); ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="flex space-x-3 pt-4">
                            <button
                                type="submit"
                                id="saveFilterBtn"
                                class="flex-1 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold py-3 px-6 rounded-xl hover:shadow-lg transition-all duration-300"
                            >
                                Add Filter
                            </button>
                            <button
                                type="button"
                                id="cancelAddFilter"
                                class="flex-1 border border-gray-300 text-gray-700 font-semibold py-3 px-6 rounded-xl hover:bg-gray-50 transition-colors duration-300"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Property Type Modal -->
    <div id="addPropertyTypeModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/70 backdrop-blur-sm"></div>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="modal-enter bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-800">Add New Property Type</h3>
                        <button id="closeAddPropertyTypeModal" class="text-gray-500 hover:text-gray-800 text-2xl">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form id="addPropertyTypeForm" class="space-y-4">
                        <div>
                            <label for="propertyTypeName" class="block text-sm font-medium text-gray-700 mb-2">Property Type Name</label>
                            <input
                                type="text"
                                id="propertyTypeName"
                                name="name"
                                required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] focus:ring-2 focus:ring-blue-100"
                                placeholder="Enter property type name"
                            >
                        </div>

                        <div>
                            <label for="propertyTypeSlug" class="block text-sm font-medium text-gray-700 mb-2">Slug</label>
                            <input
                                type="text"
                                id="propertyTypeSlug"
                                name="slug"
                                required
                                pattern="[a-z0-9-]+"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] focus:ring-2 focus:ring-blue-100"
                                placeholder="enter-slug-here"
                            >
                        </div>

                        <div>
                            <label for="propertyTypeDescription" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea
                                id="propertyTypeDescription"
                                name="description"
                                rows="3"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] focus:ring-2 focus:ring-blue-100"
                                placeholder="Enter property type description"
                            ></textarea>
                        </div>

                        <div>
                            <label for="propertyTypeColor" class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                            <input
                                type="color"
                                id="propertyTypeColor"
                                name="color"
                                value="#2FA4E7"
                                class="w-full h-12 border border-gray-200 rounded-xl focus:outline-none focus:border-[#2FA4E7] focus:ring-2 focus:ring-blue-100"
                            >
                        </div>

                        <div class="flex space-x-3 pt-4">
                            <button
                                type="submit"
                                id="savePropertyTypeBtn"
                                class="flex-1 bg-gradient-to-r from-[#2FA4E7] to-[#3CB371] text-white font-semibold py-3 px-6 rounded-xl hover:shadow-lg transition-all duration-300"
                            >
                                Add Property Type
                            </button>
                            <button
                                type="button"
                                id="cancelAddPropertyType"
                                class="flex-1 border border-gray-300 text-gray-700 font-semibold py-3 px-6 rounded-xl hover:bg-gray-50 transition-colors duration-300"
                            >
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    // DOM Elements (defined inside ready)
    let sections, sidebarItems, sidebar, mobileMenuBtn, mobileOverlay, addPropertyModal, addUserModal, viewPropertyModal, confirmationModal, notificationsModal, tabBtns, quickActionBtns;

    // State Variables
    let currentAction = null;
    let currentItemId = null;

    // Initialize DataTables
    $(document).ready(function() {
        // DOM Elements
        sections = document.querySelectorAll('.section-content');
        sidebarItems = document.querySelectorAll('.sidebar-item');
        sidebar = document.getElementById('sidebar');
        mobileMenuBtn = document.getElementById('mobileMenuBtn');
        mobileOverlay = document.getElementById('mobileOverlay');
        addPropertyModal = document.getElementById('addPropertyModal');
        addUserModal = document.getElementById('addUserModal');
        viewPropertyModal = document.getElementById('viewPropertyModal');
        confirmationModal = document.getElementById('confirmationModal');
        notificationsModal = document.getElementById('notificationsModal');
        tabBtns = document.querySelectorAll('.tab-btn');
        quickActionBtns = document.querySelectorAll('.quick-action-btn');

        // Initialize users table
        $('#usersTable').DataTable({
            paging: true,
            pageLength: 5,
            searching: true,
            info: true,
            ordering: true
        });

        // Initialize properties table
        $('#propertiesTable').DataTable({
            paging: true,
            pageLength: 5,
            searching: true,
            info: true,
            ordering: true
        });

        // Initialize movers table
        $('#moversTable').DataTable({
            paging: false,
            searching: false,
            info: false,
            ordering: false
        });

        // Initialize requests table
        $('#requestsTable').DataTable({
            paging: false,
            searching: false,
            info: false,
            ordering: false
        });

        // Initialize contacts table
        $('#contactsTable').DataTable({
            paging: false,
            searching: false,
            info: false,
            ordering: false
        });

        // Mobile Menu Toggle - FIXED VERSION
        if (mobileMenuBtn) {
            mobileMenuBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.toggle('-translate-x-full');
                if (mobileOverlay) {
                    mobileOverlay.classList.toggle('hidden');
                }
            });
        }

        // Close sidebar when clicking overlay
        if (mobileOverlay) {
            mobileOverlay.addEventListener('click', function() {
                sidebar.classList.add('-translate-x-full');
                this.classList.add('hidden');
            });
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth < 768 && sidebar && mobileOverlay) {
                if (!sidebar.contains(e.target) && 
                    e.target !== mobileMenuBtn && 
                    !mobileOverlay.contains(e.target) &&
                    !sidebar.classList.contains('-translate-x-full')) {
                    sidebar.classList.add('-translate-x-full');
                    mobileOverlay.classList.add('hidden');
                }
            }
        });
    });

    // Initialize Chart
    const ctx = document.getElementById('activityChart').getContext('2d');
    const activityChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Property Views',
                data: [120, 190, 300, 500, 200, 300, 450],
                borderColor: '#2FA4E7',
                backgroundColor: 'rgba(47, 164, 231, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'User Signups',
                data: [50, 80, 120, 180, 90, 120, 160],
                borderColor: '#3CB371',
                backgroundColor: 'rgba(60, 179, 113, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Update Date and Time
    function updateDateTime() {
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('currentDate').textContent = now.toLocaleDateString('en-US', options);
        document.getElementById('currentTime').textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    }

    // Update every minute
    updateDateTime();
    setInterval(updateDateTime, 60000);

    // Sidebar Navigation
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarItems = document.querySelectorAll('.sidebar-item');
        const sections = document.querySelectorAll('.section-content');
        const sidebar = document.getElementById('sidebar');
        const mobileOverlay = document.getElementById('mobileOverlay');

        sidebarItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();

                // Get target section
                const targetSection = this.dataset.section;

                // Update active state
                sidebarItems.forEach(i => i.classList.remove('active'));
                this.classList.add('active');

                // Show target section
                sections.forEach(section => {
                    section.classList.remove('active');
                    section.classList.add('hidden');
                });

                const targetElement = document.getElementById(targetSection);
                if (targetElement) {
                    targetElement.classList.remove('hidden');
                    targetElement.classList.add('active');
                }

                // Close sidebar on mobile after navigation
                if (window.innerWidth < 768 && sidebar && mobileOverlay) {
                    sidebar.classList.add('-translate-x-full');
                    mobileOverlay.classList.add('hidden');
                }

                // Update page title
                document.title = `${targetSection.charAt(0).toUpperCase() + targetSection.slice(1)} | Rheaspark Admin`;
            });
        });

        // Tab Navigation
        const tabBtns = document.querySelectorAll('.tab-btn');
        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Update active tab
                tabBtns.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                // Add border bottom
                tabBtns.forEach(b => {
                    b.classList.remove('border-[#2FA4E7]', 'text-[#2FA4E7]');
                });
                this.classList.add('border-[#2FA4E7]', 'text-[#2FA4E7]');

                // Show corresponding tab content
                const tabId = this.dataset.tab;
                const tabContents = document.querySelectorAll('.tab-content');
                tabContents.forEach(content => {
                    content.classList.remove('active');
                    content.classList.add('hidden');
                });
                const targetContent = document.getElementById(tabId);
                if (targetContent) {
                    targetContent.classList.remove('hidden');
                    targetContent.classList.add('active');
                }
            });
        });
    });

    // Quick Actions
    document.addEventListener('DOMContentLoaded', function() {
        const quickActionBtns = document.querySelectorAll('.quick-action-btn');
        
        quickActionBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const action = this.id;

                switch(action) {
                    case 'addPropertyBtn':
                        openModal(addPropertyModal);
                        break;
                    case 'verifyUserBtn':
                        // Navigate to users section
                        document.querySelector('[data-section="users"]').click();
                        break;
                    case 'viewRequestsBtn':
                        // Navigate to requests section
                        document.querySelector('[data-section="requests"]').click();
                        break;
                    case 'generateReportBtn':
                        // Navigate to reports section
                        document.querySelector('[data-section="reports"]').click();
                        break;
                }
            });
        });
    });

    // Add Property Button (from properties section)
    document.addEventListener('DOMContentLoaded', function() {
        const addPropertyModalBtn = document.getElementById('addPropertyModalBtn');
        if (addPropertyModalBtn) {
            addPropertyModalBtn.addEventListener('click', () => {
                openModal(addPropertyModal);
            });
        }
    });

    // Add User Button
    document.addEventListener('DOMContentLoaded', function() {
        const addUserBtn = document.getElementById('addUserBtn');
        if (addUserBtn) {
            addUserBtn.addEventListener('click', () => {
                openModal(addUserModal);
            });
        }
    });

    // Add Mover Button
    document.addEventListener('DOMContentLoaded', function() {
        const addMoverBtn = document.getElementById('addMoverBtn');
        if (addMoverBtn) {
            addMoverBtn.addEventListener('click', () => {
                // For now, use add user modal
                openModal(addUserModal);
                // Pre-select mover type
                const moverRadio = document.querySelector('input[name="userType"][value="mover"]');
                if (moverRadio) {
                    moverRadio.checked = true;
                }
            });
        }
    });

    // Add Category Button
    document.addEventListener('DOMContentLoaded', function() {
        const addCategoryBtn = document.getElementById('addCategoryBtn');
        const addCategoryModal = document.getElementById('addCategoryModal');
        if (addCategoryBtn && addCategoryModal) {
            addCategoryBtn.addEventListener('click', () => {
                openModal(addCategoryModal);
            });
        }
    });

    // Add Filter Button
    document.addEventListener('DOMContentLoaded', function() {
        const addFilterBtn = document.getElementById('addFilterBtn');
        const addFilterModal = document.getElementById('addFilterModal');
        if (addFilterBtn && addFilterModal) {
            addFilterBtn.addEventListener('click', () => {
                openModal(addFilterModal);
            });
        }
    });

    // Add Property Type Button
    document.addEventListener('DOMContentLoaded', function() {
        const addPropertyTypeBtn = document.getElementById('addPropertyTypeBtn');
        const addPropertyTypeModal = document.getElementById('addPropertyTypeModal');
        if (addPropertyTypeBtn && addPropertyTypeModal) {
            addPropertyTypeBtn.addEventListener('click', () => {
                openModal(addPropertyTypeModal);
            });
        }
    });

    // Quick Actions Button
    document.addEventListener('DOMContentLoaded', function() {
        const quickActionsBtn = document.getElementById('quickActionsBtn');
        if (quickActionsBtn) {
            quickActionsBtn.addEventListener('click', function() {
                // Create quick actions dropdown
                const dropdown = document.createElement('div');
                dropdown.className = 'absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-2xl border border-gray-200 z-50';
                dropdown.innerHTML = `
                    <div class="p-4">
                        <div class="space-y-2">
                            <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-50 text-gray-700">
                                <i class="fas fa-home mr-3 text-[#2FA4E7]"></i>
                                <span>Add Property</span>
                            </a>
                            <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-50 text-gray-700">
                                <i class="fas fa-user-plus mr-3 text-[#3CB371]"></i>
                                <span>Add User</span>
                            </a>
                            <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-50 text-gray-700">
                                <i class="fas fa-truck mr-3 text-[#2FA4E7]"></i>
                                <span>Add Mover</span>
                            </a>
                            <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-50 text-gray-700">
                                <i class="fas fa-file-invoice mr-3 text-[#FF9800]"></i>
                                <span>Generate Report</span>
                            </a>
                            <a href="#" class="flex items-center p-3 rounded-lg hover:bg-gray-50 text-gray-700">
                                <i class="fas fa-cog mr-3 text-gray-600"></i>
                                <span>Settings</span>
                            </a>
                        </div>
                    </div>
                `;

                // Position and show dropdown
                this.parentNode.appendChild(dropdown);

                // Close on outside click
                document.addEventListener('click', function closeDropdown(e) {
                    if (!dropdown.contains(e.target) && e.target !== this) {
                        dropdown.remove();
                        document.removeEventListener('click', closeDropdown);
                    }
                }.bind(this));
            });
        }
    });

    // Notifications Button
    document.addEventListener('DOMContentLoaded', function() {
        const notificationsBtn = document.getElementById('notificationsBtn');
        if (notificationsBtn) {
            notificationsBtn.addEventListener('click', () => {
                openModal(notificationsModal);
                loadNotifications();
            });
        }
    });

    // Load Notifications
    function loadNotifications() {
        const container = notificationsModal?.querySelector('.p-4');
        if (!container) return;
        
        container.innerHTML = `
            <div class="space-y-4">
                <div class="p-4 rounded-xl bg-blue-50 border border-blue-100">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-home text-blue-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-800">New Property Needs Review</div>
                            <div class="text-sm text-gray-600 mt-1">Property #P-1245 needs verification</div>
                            <div class="text-xs text-gray-500 mt-2">10 minutes ago</div>
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-green-50 border border-green-100">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-user-check text-green-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-800">User Verification Complete</div>
                            <div class="text-sm text-gray-600 mt-1">Sarah Johnson verified as landlord</div>
                            <div class="text-xs text-gray-500 mt-2">1 hour ago</div>
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-red-50 border border-red-100">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-red-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-800">Reported Content</div>
                            <div class="text-sm text-gray-600 mt-1">Property #P-0482 reported for misleading info</div>
                            <div class="text-xs text-gray-500 mt-2">2 hours ago</div>
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-purple-50 border border-purple-100">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-money-check-alt text-purple-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="font-medium text-gray-800">Payment Issue</div>
                            <div class="text-sm text-gray-600 mt-1">Refund request #R-784 needs approval</div>
                            <div class="text-xs text-gray-500 mt-2">3 hours ago</div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Save Property
    document.addEventListener('DOMContentLoaded', function() {
        const savePropertyBtn = document.getElementById('saveProperty');
        if (savePropertyBtn) {
            savePropertyBtn.addEventListener('click', function() {
                const form = document.getElementById('propertyForm');
                const isValid = validateForm(form);

                if (isValid) {
                    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
                    this.disabled = true;

                    // Simulate API call
                    setTimeout(() => {
                        showNotification('Property added successfully!', 'success');
                        closeModal(addPropertyModal);
                        this.innerHTML = 'Save Property';
                        this.disabled = false;

                        // Refresh properties table
                        loadSampleProperties();
                    }, 1500);
                }
            });
        }
    });

    // Save User
    document.addEventListener('DOMContentLoaded', function() {
        const saveUserBtn = document.getElementById('saveUser');
        if (saveUserBtn) {
            saveUserBtn.addEventListener('click', function() {
                const form = document.getElementById('userForm');
                const isValid = validateForm(form);

                if (isValid) {
                    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Creating...';
                    this.disabled = true;

                    // Simulate API call
                    setTimeout(() => {
                        showNotification('User created successfully!', 'success');
                        closeModal(addUserModal);
                        this.innerHTML = 'Create User';
                        this.disabled = false;

                        // Refresh users table
                        loadSampleUsers();
                    }, 1500);
                }
            });
        }
    });

    // Cancel Buttons
    document.addEventListener('DOMContentLoaded', function() {
        const cancelButtons = {
            'cancelProperty': addPropertyModal,
            'cancelUser': addUserModal,
            'closeAddPropertyModal': addPropertyModal,
            'closeAddUserModal': addUserModal,
            'closeViewPropertyModal': viewPropertyModal,
            'closeNotificationsModal': notificationsModal,
            'cancelConfirm': confirmationModal,
            'cancelConfirmBtn': confirmationModal,
            'closeAddCategoryModal': document.getElementById('addCategoryModal'),
            'cancelAddCategory': document.getElementById('addCategoryModal'),
            'closeAddFilterModal': document.getElementById('addFilterModal'),
            'cancelAddFilter': document.getElementById('addFilterModal'),
            'closeAddPropertyTypeModal': document.getElementById('addPropertyTypeModal'),
            'cancelAddPropertyType': document.getElementById('addPropertyTypeModal')
        };

        for (const [id, modal] of Object.entries(cancelButtons)) {
            const button = document.getElementById(id);
            if (button && modal) {
                button.addEventListener('click', () => closeModal(modal));
            }
        }
    });

    // Open Modal Helper
    function openModal(modal) {
        if (!modal) return;
        modal.classList.remove('hidden');
        setTimeout(() => {
            const modalEnter = modal.querySelector('.modal-enter');
            if (modalEnter) {
                modalEnter.classList.add('modal-enter-active');
            }
        }, 10);

        document.body.style.overflow = 'hidden';
    }

    // Close Modal Helper
    function closeModal(modal) {
        if (!modal) return;
        const modalEnter = modal.querySelector('.modal-enter');
        if (modalEnter) {
            modalEnter.classList.remove('modal-enter-active');
        }
        setTimeout(() => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }, 300);
    }

    // Show Confirmation Modal
    function showConfirmation(action, itemId, message) {
        currentAction = action;
        currentItemId = itemId;

        const confirmTitle = document.getElementById('confirmTitle');
        const confirmMessage = document.getElementById('confirmMessage');
        
        if (confirmTitle) confirmTitle.textContent = 'Confirm Action';
        if (confirmMessage) confirmMessage.textContent = message;

        openModal(confirmationModal);
    }

    // Confirm Action
    document.addEventListener('DOMContentLoaded', function() {
        const confirmActionBtn = document.getElementById('confirmAction');
        if (confirmActionBtn) {
            confirmActionBtn.addEventListener('click', function() {
                switch(currentAction) {
                    case 'deleteProperty':
                        // Delete property via API
                        fetch('api/admin_delete_property.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'property_id=' + currentItemId
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showNotification('Property deleted successfully!', 'success');
                                location.reload();
                            } else {
                                showNotification('Error: ' + data.error, 'error');
                            }
                        })
                        .catch(error => {
                            showNotification('Error deleting property', 'error');
                        });
                        break;
                    case 'deleteUser':
                        // Actual delete via API
                        fetch('api/admin_delete_user.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'user_id=' + currentItemId
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showNotification('User deleted successfully!', 'success');
                                location.reload();
                            } else {
                                showNotification('Error: ' + data.error, 'error');
                            }
                        })
                        .catch(error => {
                            showNotification('Error deleting user', 'error');
                        });
                        break;
                    case 'suspendUser':
                        // Simulate suspend
                        showNotification('User suspended successfully!', 'success');
                        break;
                    case 'deleteContactMessage':
                        // Delete contact message via API
                        fetch('api/delete_contact_message.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'message_id=' + currentItemId
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showNotification('Contact message deleted successfully!', 'success');
                                location.reload();
                            } else {
                                showNotification('Error: ' + data.error, 'error');
                            }
                        })
                        .catch(error => {
                            showNotification('Error deleting contact message', 'error');
                        });
                        break;
                    case 'deleteCategory':
                        // Delete category via API
                        fetch('api/delete_category.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'category_id=' + currentItemId
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showNotification('Category deleted successfully!', 'success');
                                location.reload();
                            } else {
                                showNotification('Error: ' + data.error, 'error');
                            }
                        })
                        .catch(error => {
                            showNotification('Error deleting category', 'error');
                        });
                        break;
                    case 'deleteFilter':
                        // Delete filter via API
                        fetch('api/delete_filter.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'filter_id=' + currentItemId
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showNotification('Filter deleted successfully!', 'success');
                                location.reload();
                            } else {
                                showNotification('Error: ' + data.error, 'error');
                            }
                        })
                        .catch(error => {
                            showNotification('Error deleting filter', 'error');
                        });
                        break;
                    case 'deletePropertyType':
                        // Delete property type via API
                        fetch('api/delete_property_type.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: 'property_type_id=' + currentItemId
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                showNotification('Property type deleted successfully!', 'success');
                                location.reload();
                            } else {
                                showNotification('Error: ' + data.error, 'error');
                            }
                        })
                        .catch(error => {
                            showNotification('Error deleting property type', 'error');
                        });
                        break;
                }

                closeModal(confirmationModal);
            });
        }
    });

    // Validate Form
    function validateForm(form) {
        if (!form) return false;
        
        const requiredInputs = form.querySelectorAll('[required]');
        let isValid = true;

        requiredInputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.style.borderColor = '#F87171';
                setTimeout(() => {
                    input.style.borderColor = '';
                }, 3000);
            }
        });

        if (!isValid) {
            showNotification('Please fill all required fields.', 'error');
        }

        return isValid;
    }

    // Delete User
    function deleteUser(userId) {
        fetch('api/admin_delete_user.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'user_id=' + userId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('User deleted successfully!', 'success');
                location.reload();
            } else {
                showNotification('Error: ' + data.error, 'error');
            }
        })
        .catch(error => {
            showNotification('Error deleting user', 'error');
        });
    }

    // View Contact Message
    function viewContactMessage(messageId) {
        // For now, just show an alert. In a real implementation, this would open a modal
        showNotification('View message functionality would open a detailed view modal', 'info');
    }

    // Reply to Contact Message
    function replyToContactMessage(messageId) {
        // For now, just show an alert. In a real implementation, this would open a reply modal
        showNotification('Reply functionality would open an email composer', 'info');
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

    function loadSampleProperties() {
        const propertiesTable = document.querySelector('#propertiesTable tbody');
        if (!propertiesTable) return;
        
        const properties = [
            { title: 'Spacious 3-Bedroom Apartment', location: 'Westlands', price: 'KES 85,000', status: 'Active', views: 124 },
            { title: 'Modern 2-Bedroom Studio', location: 'Kilimani', price: 'KES 65,000', status: 'Active', views: 89 },
            { title: 'Luxury Villa', location: 'Karen', price: 'KES 250,000', status: 'Pending', views: 45 },
            { title: 'Cozy Bedsitter', location: 'South B', price: 'KES 25,000', status: 'Active', views: 156 },
            { title: 'Executive Apartment', location: 'Kileleshwa', price: 'KES 120,000', status: 'Inactive', views: 67 }
        ];

        propertiesTable.innerHTML = '';

        properties.forEach((property, index) => {
            const statusClass = property.status === 'Active' ? 'status-active' :
                                property.status === 'Pending' ? 'status-pending' : 'status-inactive';

            const row = document.createElement('tr');
            row.className = 'fade-in-row';
            row.style.animationDelay = `${index * 0.1}s`;

            row.innerHTML = `
                <td class="px-6 py-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-lg bg-gray-200 flex items-center justify-center mr-3">
                            <i class="fas fa-home text-gray-600"></i>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">${property.title}</div>
                            <div class="text-sm text-gray-500">ID: PROP-${2000 + index}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <i class="fas fa-map-marker-alt text-[#3CB371] mr-2"></i>
                        <span class="text-gray-900">${property.location}</span>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-900 font-semibold">
                    ${property.price}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="status-badge ${statusClass}">
                        <i class="fas ${property.status === 'Active' ? 'fa-check-circle' : property.status === 'Pending' ? 'fa-clock' : 'fa-ban'} mr-1"></i>
                        ${property.status}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                    ${property.views}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                        <button class="action-btn text-[#2FA4E7] hover:text-blue-700" title="View">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="action-btn text-[#3CB371] hover:text-green-700" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn text-[#F44336] hover:text-red-700" title="Delete" onclick="showConfirmation('deleteProperty', ${index}, 'Are you sure you want to delete this property? This action cannot be undone.')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            `;

            propertiesTable.appendChild(row);
        });
    }

    // Change Password Form
    document.addEventListener('DOMContentLoaded', function() {
        const changePasswordForm = document.getElementById('changePasswordForm');
        const changePasswordBtn = document.getElementById('changePasswordBtn');
        const cancelChangePassword = document.getElementById('cancelChangePassword');

        if (changePasswordForm) {
            changePasswordForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const currentPassword = document.getElementById('currentPassword').value;
                const newPassword = document.getElementById('newPassword').value;
                const confirmPassword = document.getElementById('confirmPassword').value;

                // Client-side validation
                if (newPassword !== confirmPassword) {
                    showNotification('New password and confirmation do not match', 'error');
                    return;
                }

                if (newPassword.length < 8) {
                    showNotification('New password must be at least 8 characters long', 'error');
                    return;
                }

                // Disable button
                changePasswordBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Changing...';
                changePasswordBtn.disabled = true;

                // Send request
                fetch('api/admin_change_password.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        currentPassword: currentPassword,
                        newPassword: newPassword,
                        confirmPassword: confirmPassword
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Password changed successfully!', 'success');
                        changePasswordForm.reset();
                    } else {
                        showNotification('Error: ' + data.error, 'error');
                    }
                })
                .catch(error => {
                    showNotification('Error changing password', 'error');
                })
                .finally(() => {
                    changePasswordBtn.innerHTML = 'Change Password';
                    changePasswordBtn.disabled = false;
                });
            });
        }

        if (cancelChangePassword) {
            cancelChangePassword.addEventListener('click', function() {
                changePasswordForm.reset();
            });
        }
    });

    // Add Category Form
    document.addEventListener('DOMContentLoaded', function() {
        const addCategoryForm = document.getElementById('addCategoryForm');
        const saveCategoryBtn = document.getElementById('saveCategoryBtn');
        const addCategoryModal = document.getElementById('addCategoryModal');

        if (addCategoryForm) {
            addCategoryForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                // Disable button
                saveCategoryBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Adding...';
                saveCategoryBtn.disabled = true;

                // Send request
                fetch('api/add_category.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Category added successfully!', 'success');
                        addCategoryForm.reset();
                        closeModal(addCategoryModal);
                        location.reload();
                    } else {
                        showNotification('Error: ' + data.error, 'error');
                    }
                })
                .catch(error => {
                    showNotification('Error adding category', 'error');
                })
                .finally(() => {
                    saveCategoryBtn.innerHTML = 'Add Category';
                    saveCategoryBtn.disabled = false;
                });
            });
        }
    });

    // Add Filter Form
    document.addEventListener('DOMContentLoaded', function() {
        const addFilterForm = document.getElementById('addFilterForm');
        const saveFilterBtn = document.getElementById('saveFilterBtn');
        const addFilterModal = document.getElementById('addFilterModal');

        if (addFilterForm) {
            addFilterForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                // Disable button
                saveFilterBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Adding...';
                saveFilterBtn.disabled = true;

                // Send request
                fetch('api/add_filter.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Filter added successfully!', 'success');
                        addFilterForm.reset();
                        closeModal(addFilterModal);
                        location.reload();
                    } else {
                        showNotification('Error: ' + data.error, 'error');
                    }
                })
                .catch(error => {
                    showNotification('Error adding filter', 'error');
                })
                .finally(() => {
                    saveFilterBtn.innerHTML = 'Add Filter';
                    saveFilterBtn.disabled = false;
                });
            });
        }
    });

    // Add Property Type Form
    document.addEventListener('DOMContentLoaded', function() {
        const addPropertyTypeForm = document.getElementById('addPropertyTypeForm');
        const savePropertyTypeBtn = document.getElementById('savePropertyTypeBtn');
        const addPropertyTypeModal = document.getElementById('addPropertyTypeModal');

        if (addPropertyTypeForm) {
            addPropertyTypeForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                // Disable button
                savePropertyTypeBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Adding...';
                savePropertyTypeBtn.disabled = true;

                // Send request
                fetch('api/add_property_type.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Property type added successfully!', 'success');
                        addPropertyTypeForm.reset();
                        closeModal(addPropertyTypeModal);
                        location.reload();
                    } else {
                        showNotification('Error: ' + data.error, 'error');
                    }
                })
                .catch(error => {
                    showNotification('Error adding property type', 'error');
                })
                .finally(() => {
                    savePropertyTypeBtn.innerHTML = 'Add Property Type';
                    savePropertyTypeBtn.disabled = false;
                });
            });
        }
    });

    // Close modals on background click
    document.addEventListener('DOMContentLoaded', function() {
        const modals = [addPropertyModal, addUserModal, viewPropertyModal, confirmationModal, notificationsModal, document.getElementById('addCategoryModal'), document.getElementById('addFilterModal'), document.getElementById('addPropertyTypeModal')];

        modals.forEach(modal => {
            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        const closeBtn = this.querySelector('button[id^="close"], button[id^="cancel"]');
                        if (closeBtn) closeBtn.click();
                    }
                });
            }
        });
    });
</script>
   
</body>
</html>