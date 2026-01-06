<?php
session_start();

// Handle API requests
if (isset($_GET['page']) && strpos($_GET['page'], 'api/') === 0) {
    $apiPath = str_replace('api/', '', $_GET['page']);
    $apiFile = 'api/' . $apiPath . '.php';
    if (file_exists($apiFile)) {
        include $apiFile;
        exit;
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'API endpoint not found']);
        exit;
    }
}

$page = $_GET['page'] ?? 'home';

// Pages that don't require authentication
$public_pages = ['home', 'login', 'register', 'reset_password', 'logout'];

// Check if user is logged in for protected pages
if (!isset($_SESSION['user_id']) && !in_array($page, $public_pages)) {
    header('Location: index.php?page=login');
    exit;
}

include 'header.php';

switch ($page) {
    case 'home':
        include 'home.php';
        break;
    case 'house':
        include 'house.php';
        break;
    case 'houses':
        include 'houses.php';
        break;
    case 'profile':
        include 'profile.php';
        break;
    case 'login':
        include 'login.php';
        break;
    case 'register':
        include 'register.php';
        break;
    case 'reset_password':
        include 'reset_password.php';
        break;
    case 'logout':
        session_destroy();
        header('Location: index.php');
        exit;
        break;
    default:
        include 'home.php';
        break;
}

include 'footer.php';
?>