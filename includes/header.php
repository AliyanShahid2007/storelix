    <?php
ob_start();
require_once 'config.php';
require_once 'functions.php';

$is_admin_page = strpos($_SERVER['PHP_SELF'], '/admin/') !== false;
$is_employee_page = strpos($_SERVER['PHP_SELF'], '/employee/') !== false;

$cart_count = 0;
$notification_count = 0;
if (isLoggedIn()) {
    $cart_count = getCartCount($_SESSION['user_id']);
    $notification_count = getUnreadNotificationsCount($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #001f3f;
            --secondary-color: #003366;
            --dark-color: #333;
            --light-color: #f8f9fa;
            --bg-color: #ffffff;
            --text-color: #000000;
            --card-bg: #ffffff;
            --navbar-bg: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            --navbar-text: white;
            --footer-bg: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            --footer-text: white;
            --hero-bg: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        }

        [data-theme="dark"] {
            --bg-color: #1a1a1a;
            --text-color: #ffffff;
            --card-bg: #2d2d2d;
            --navbar-bg: #2d2d2d;
            --navbar-text: #ffffff;
            --footer-bg: linear-gradient(135deg, #2c3e50, #34495e);
            --footer-text: #ffffff;
            --hero-bg: linear-gradient(135deg, #2c3e50, #34495e);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.3s, color 0.3s;
        }
        
        .navbar {
            background: var(--navbar-bg);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            color: var(--navbar-text) !important;
        }

        .navbar-nav .nav-link {
            color: var(--navbar-text) !important;
            margin: 0 10px;
            transition: all 0.3s;
        }
        
        .navbar-nav .nav-link:hover {
            color: white !important;
            transform: translateY(-2px);
        }
        
        .badge-cart {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #ff9800;
        }

        .badge-notification {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            font-size: 0.7rem;
        }

        .notification-icon {
            position: relative;
        }

        .notification-dropdown {
            min-width: 300px;
            max-width: 400px;
            left: 0;
            right: auto;
        }

        /* Hero Section */
        .hero-section {
            color: white;
            padding: 0 0 100px 0;
            text-align: center;
            position: relative;
            margin-top: 0;
        }

        .hero-section .carousel-item {
            height: 500px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            display: flex;
            align-items: center;
        }

        .hero-section .carousel-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 31, 63, 0.4);
            z-index: 1;
        }

        .hero-section .container {
            position: relative;
            z-index: 2;
        }

        .hero-section .carousel-control-prev,
        .hero-section .carousel-control-next {
            z-index: 3;
        }

        /* Featured Products Section */
        .bg-light {
            background-color: var(--bg-color) !important;
        }

        /* Cards */
        .card {
            background-color: var(--card-bg);
            color: var(--text-color);
            border: 1px solid rgba(0,0,0,0.1);
        }

        .card .card-title {
            color: var(--text-color);
        }

        .card .card-text {
            color: var(--text-color);
        }

        .card .text-muted {
            color: rgba(0,0,0,0.6) !important;
        }

        /* Buttons */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Theme Toggle Button */
        #theme-toggle {
            background: none;
            border: none;
            color: var(--navbar-text);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 0.25rem;
            transition: all 0.3s;
        }

        #theme-toggle:hover {
            background-color: rgba(255,255,255,0.1);
        }

        /* Footer */
        .footer {
            background: var(--footer-bg);
        }

        .notification-dropdown .dropdown-item {
            white-space: normal;
        }

        .notification-icon .dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            right: auto;
            transform: none !important;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(233, 30, 99, 0.3);
        }
        
        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            background-color: var(--card-bg);
            color: var(--text-color);
        }
        
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .card-img-top {
            height: 250px;
            object-fit: cover;
        }
        
        .product-price {
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: bold;
        }
        

        
        .category-card {
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .category-card:hover {
            transform: scale(1.05);
        }
        
        footer {
            background: var(--footer-bg);
            color: var(--text-color);
            padding: 30px 0;
            margin-top: auto;
        }
        
        .search-form {
            max-width: 400px;
        }
        
        .cart-icon {
            position: relative;
        }

        /* Admin Sidebar Styles */
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background: var(--navbar-bg);
            color: var(--navbar-text);
            z-index: 1000;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            overflow-y: auto;
        }

        .admin-sidebar.show {
            transform: translateX(0);
        }

        .admin-sidebar .sidebar-header {
            padding: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .admin-sidebar .sidebar-brand {
            font-size: 1.25rem;
            font-weight: bold;
            color: var(--navbar-text);
        }

        .admin-sidebar .sidebar-nav {
            padding: 1rem 0;
        }

        .admin-sidebar .nav-item {
            margin: 0.25rem 0;
        }

        .admin-sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            transition: all 0.3s;
        }

        .admin-sidebar .nav-link:hover,
        .admin-sidebar .nav-link.active {
            color: var(--navbar-text);
            background-color: rgba(255,255,255,0.1);
            text-decoration: none;
        }

        .admin-sidebar .nav-link i {
            margin-right: 0.75rem;
            width: 20px;
        }

        .sidebar-toggle {
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1001;
            background: var(--navbar-bg);
            border: none;
            color: var(--navbar-text);
            padding: 0.5rem;
            border-radius: 0.25rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: all 0.3s;
            cursor: pointer;
        }

        .sidebar-toggle:hover {
            background-color: rgba(255,255,255,0.1);
        }

        .admin-sidebar .sidebar-toggle {
            position: relative;
            top: auto;
            left: auto;
            z-index: auto;
            background: transparent;
            border: none;
            color: inherit;
            padding: 0;
            border-radius: 0;
            box-shadow: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .admin-sidebar .sidebar-toggle:hover {
            background-color: rgba(255,255,255,0.1);
        }

        .admin-content {
            margin-left: 0;
            transition: margin-left 0.3s ease;
        }

        .admin-sidebar.show ~ .admin-content {
            margin-left: 250px;
        }

        @media (max-width: 768px) {
            .admin-sidebar.show ~ .admin-content {
                margin-left: 0;
            }

            .admin-sidebar {
                width: 100%;
                z-index: 1050;
            }
        }

        /* Star Rating Styles */
        .rating-stars {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }

        .rating-stars input[type="radio"] {
            display: none;
        }

        .rating-stars label {
            cursor: pointer;
            font-size: 1.5rem;
            color: #ddd;
            transition: color 0.3s;
        }

        .rating-stars label:hover,
        .rating-stars label:hover ~ label,
        .rating-stars input[type="radio"]:checked ~ label {
            color: #ffc107;
        }

        /* Special Offer Countdown Timer Styles */
        .special-offer-section {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            margin: 20px 0;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .special-offer-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg,
                transparent 30%,
                rgba(255, 255, 255, 0.02) 50%,
                transparent 70%);
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .special-offer-banner {
            padding: 20px;
            background: linear-gradient(135deg, var(--primary-color, #007bff) 0%, var(--secondary-color, #6c757d) 100%);
            border-radius: 12px;
            color: white;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        .special-offer-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.2)"/><circle cx="10" cy="50" r="0.5" fill="rgba(255,255,255,0.2)"/><circle cx="90" cy="30" r="0.5" fill="rgba(255,255,255,0.2)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
            pointer-events: none;
        }

        .countdown-timer {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1;
        }

        .timer-label {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 15px;
            color: rgba(255, 255, 255, 0.9);
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: center;
        }

        .timer-display {
            display: flex;
            justify-content: space-around;
            gap: 10px;
        }

        .timer-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 60px;
            position: relative;
        }

        .timer-item::before {
            content: '';
            position: absolute;
            top: -5px;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            animation: glow 2s ease-in-out infinite;
            box-shadow: 0 0 5px rgba(255, 255, 255, 0.8);
        }

         @keyframes glow {
            0% { box-shadow: 0 0 5px rgba(255, 255, 255, 0.8); }
            50% { box-shadow: 0 0 20px rgba(255, 255, 255, 1), 0 0 30px rgba(255, 255, 255, 0.5); }
            100% { box-shadow: 0 0 5px rgba(255, 255, 255, 0.8); }
        } 

        .timer-value {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
            margin-bottom: 5px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            padding: 8px 12px;
            min-width: 50px;
            text-align: center;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }

        .timer-value:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.05);
        }

        .timer-unit {
            font-size: 0.75rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.8);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: center;
        }

        .discount-badge {
            margin-top: 10px;
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
            40% { transform: translateY(-5px); }
            60% { transform: translateY(-3px); }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .special-offer-banner {
                padding: 15px;
            }

            .timer-display {
                gap: 5px;
            }

            .timer-item {
                min-width: 50px;
            }

            .timer-value {
                font-size: 1.5rem;
                padding: 6px 8px;
                min-width: 40px;
            }

            .timer-unit {
                font-size: 0.7rem;
            }
        }

        @media (max-width: 576px) {
            .special-offer-section .row > div {
                margin-bottom: 15px;
            }

            .countdown-timer {
                padding: 15px;
            }

            .timer-display {
                flex-wrap: wrap;
                justify-content: center;
            }

            .timer-item {
                min-width: 45px;
                margin: 5px;
            }
        }

        /* Chat Widget Toggle Button */
        .chat-toggle {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            z-index: 1001;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            animation: bounceIn 0.8s ease-out;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.1);
        }

        @keyframes bounceIn {
            0% {
                transform: scale(0.3);
                opacity: 0;
            }
            50% {
                transform: scale(1.05);
            }
            70% {
                transform: scale(0.9);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .chat-toggle:hover {
            background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(0,123,255,0.5);
        }

        .chat-toggle.unread {
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* Chat Widget Styles */
        .chat-widget {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 350px;
            height: 500px;
            background: linear-gradient(135deg, rgba(255,255,255,0.95) 0%, rgba(248,249,250,0.95) 50%, rgba(240,242,245,0.95) 100%);
            border-radius: 28px;
            box-shadow:
                0 25px 80px rgba(0,0,0,0.15),
                0 0 0 1px rgba(255,255,255,0.2),
                inset 0 1px 0 rgba(255,255,255,0.6);
            display: none;
            flex-direction: column;
            z-index: 1000;
            backdrop-filter: blur(25px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.3);
            overflow: hidden;
            animation: widgetAppear 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes widgetAppear {
            from {
                transform: translateY(30px) scale(0.95);
                opacity: 0;
            }
            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }

        .chat-widget.minimized {
            height: 60px;
            width: 200px;
            border-radius: 30px;
            cursor: pointer;
        }

        .chat-widget.minimized .chat-messages,
        .chat-widget.minimized .chat-input,
        .chat-widget.minimized .guest-form {
            display: none !important;
        }

        .chat-widget.slide-down {
            transform: translateY(100px);
            opacity: 0;
            transition: all 0.3s ease;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .chat-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            color: white;
            padding: 20px 24px;
            border-radius: 28px 28px 0 0;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow:
                0 8px 32px rgba(102, 126, 234, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.15);
            position: relative;
            overflow: hidden;
        }

        .chat-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        .chat-header .header-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chat-header .header-info span {
            background: linear-gradient(135deg, #ffffff 0%, #e0e7ff 50%, #f0f9ff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
            font-size: 18px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            letter-spacing: 0.5px;
        }

        .chat-header .status-indicator {
            width: 8px;
            height: 8px;
            background: #28a745;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: #f8f9fa;
            background-image: radial-gradient(circle at 20px 80px, rgba(120, 119, 198, 0.1) 0%, transparent 50%),
                              radial-gradient(circle at 80px 20px, rgba(255, 119, 198, 0.1) 0%, transparent 50%),
                              radial-gradient(circle at 40px 40px, rgba(120, 219, 255, 0.1) 0%, transparent 50%);
            background-size: 100px 100px;
        }

        .chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        .message {
            margin-bottom: 16px;
            padding: 14px 18px;
            border-radius: 20px;
            max-width: 75%;
            word-wrap: break-word;
            position: relative;
            animation: messageSlideIn 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.2s ease;
        }

        .message:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }

        @keyframes messageSlideIn {
            from {
                transform: translateY(15px) scale(0.95);
                opacity: 0;
            }
            to {
                transform: translateY(0) scale(1);
                opacity: 1;
            }
        }

        .message.user {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            margin-left: auto;
            border-bottom-right-radius: 6px;
            align-self: flex-end;
        }

        .message.user::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: -8px;
            width: 0;
            height: 0;
            border-left: 8px solid #764ba2;
            border-bottom: 8px solid transparent;
            border-top: 8px solid transparent;
        }

        .message.admin {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            color: #2d3748;
            border-bottom-left-radius: 6px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            align-self: flex-start;
        }

        .message.admin::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: -8px;
            width: 0;
            height: 0;
            border-right: 8px solid #ffffff;
            border-bottom: 8px solid transparent;
            border-top: 8px solid transparent;
        }

        .message .timestamp {
            font-size: 11px;
            opacity: 0.7;
            margin-top: 4px;
            display: block;
        }

        .chat-input {
            padding: 20px;
            border-top: 1px solid #dee2e6;
            background: white;
            border-radius: 0 0 20px 20px;
        }

        .chat-input .input-group {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 25px;
            overflow: hidden;
            position: relative;
        }

        .emoji-picker {
            position: absolute;
            bottom: 100%;
            right: 0;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            padding: 10px;
            display: none;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            width: 300px;
        }

        .emoji-picker.show {
            display: block;
        }

        .emoji-grid {
            display: grid;
            grid-template-columns: repeat(8, 1fr);
            gap: 5px;
        }

        .emoji-btn {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            padding: 5px;
            border-radius: 5px;
            transition: background-color 0.2s;
        }

        .emoji-btn:hover {
            background-color: #f8f9fa;
        }

        .attachment-btn {
            background: none;
            border: none;
            color: #6c757d;
            font-size: 18px;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.2s;
            margin-right: 5px;
        }

        .attachment-btn:hover {
            background-color: #f8f9fa;
            color: #007bff;
        }

        .emoji-toggle-btn {
            background: none;
            border: none;
            color: #6c757d;
            font-size: 18px;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.2s;
            margin-right: 5px;
        }

        .emoji-toggle-btn:hover {
            background-color: #f8f9fa;
            color: #007bff;
        }

        .chat-input input {
            border: none;
            padding: 12px 20px;
            font-size: 14px;
        }

        .chat-input input:focus {
            box-shadow: none;
        }

        .chat-input button {
            border: none;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 12px 20px;
            border-radius: 0 25px 25px 0;
            transition: all 0.3s ease;
        }

        .chat-input button:hover {
            background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
            transform: scale(1.05);
        }

        .minimize-btn {
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .minimize-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: scale(1.1);
        }

        .guest-form {
            padding: 20px;
            text-align: center;
        }

        .guest-form h6 {
            color: #333;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .guest-form .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 16px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .guest-form .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }

        .guest-form .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 14px 32px;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            color: white;
        }

        .guest-form .btn:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .typing-indicator {
            display: none;
            padding: 12px 20px;
            color: #666;
            font-style: italic;
            animation: typing 1.5s infinite;
        }

        @keyframes typing {
            0%, 60%, 100% { opacity: 1; }
            30% { opacity: 0.5; }
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .chat-widget {
                width: calc(100vw - 16px);
                height: calc(100vh - 32px);
                bottom: 8px;
                right: 8px;
                left: 8px;
                border-radius: 16px;
                max-width: 100vw;
                max-height: 100vh;
            }

            .chat-header {
                padding: 16px 20px;
                border-radius: 16px 16px 0 0;
            }

            .chat-header .header-info span {
                font-size: 16px;
                font-weight: 600;
            }

            .chat-toggle {
                bottom: 16px;
                right: 16px;
                width: 60px;
                height: 60px;
                font-size: 24px;
                box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            }

            .chat-messages {
                padding: 16px;
                -webkit-overflow-scrolling: touch;
            }

            .message {
                max-width: 90%;
                font-size: 15px;
                padding: 12px 16px;
                margin-bottom: 12px;
                touch-action: manipulation;
            }

            .message.user {
                margin-left: auto;
                margin-right: 8px;
            }

            .message.admin {
                margin-left: 8px;
                margin-right: auto;
            }

            .chat-input {
                padding: 16px;
            }

            .chat-input input {
                padding: 14px 18px;
                font-size: 16px; /* Prevents zoom on iOS */
                border-radius: 20px;
            }

            .chat-input button {
                padding: 14px 18px;
                border-radius: 0 20px 20px 0;
            }

            .guest-form {
                padding: 20px 16px;
            }

            .guest-form .form-control {
                padding: 14px 16px;
                font-size: 16px;
                border-radius: 12px;
            }

            .guest-form .btn {
                padding: 16px 24px;
                font-size: 16px;
                border-radius: 12px;
            }

            .minimize-btn {
                padding: 10px;
                font-size: 16px;
                min-width: 40px;
                min-height: 40px;
            }
        }

        /* Small mobile devices */
        @media (max-width: 480px) {
            .chat-widget {
                width: calc(100vw - 12px);
                height: calc(100vh - 24px);
                bottom: 6px;
                right: 6px;
                left: 6px;
                border-radius: 12px;
            }

            .chat-toggle {
                bottom: 12px;
                right: 12px;
                width: 56px;
                height: 56px;
                font-size: 22px;
            }

            .chat-messages {
                padding: 12px;
            }

            .message {
                max-width: 95%;
                font-size: 14px;
                padding: 10px 14px;
            }

            .chat-input {
                padding: 12px;
            }

            .chat-input input {
                padding: 12px 16px;
            }

            .chat-input button {
                padding: 12px 16px;
            }
        }

        /* Dark mode support */
        @media (prefers-color-scheme: dark) {
            .chat-widget {
                background: linear-gradient(135deg, #2d3748 0%, #1a202c 100%);
                border-color: rgba(255,255,255,0.1);
            }

            .chat-messages {
                background: #2d3748;
            }

            .message.admin {
                background: #4a5568;
                color: white;
                border-color: #718096;
            }

            .chat-input {
                background: #2d3748;
                border-color: #718096;
            }
        }

        /* Chat Preview Styles */
        .chat-preview {
            max-width: 400px;
            margin: 0 auto;
        }

        .chat-preview-window {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
            border: 2px solid #e9ecef;
        }

        .chat-preview-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            font-weight: 600;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.2);
        }

        .chat-preview-messages {
            padding: 20px;
            background: #f8f9fa;
            max-height: 200px;
            overflow-y: auto;
        }

        .message-preview {
            margin-bottom: 12px;
            padding: 10px 15px;
            border-radius: 18px;
            max-width: 80%;
            font-size: 14px;
            line-height: 1.4;
        }

        .message-preview.user {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            margin-left: auto;
            text-align: right;
        }

        .message-preview.admin {
            background: white;
            color: #333;
            border: 1px solid #dee2e6;
            margin-right: auto;
        }

        .chat-preview-input {
            padding: 15px 20px;
            background: white;
            border-top: 1px solid #dee2e6;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .chat-preview-input input {
            flex: 1;
            border: 1px solid #dee2e6;
            border-radius: 20px;
            padding: 8px 15px;
            font-size: 14px;
            background: #f8f9fa;
        }

        .chat-preview-input button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .status-indicator {
            width: 10px;
            height: 10px;
            background: #28a745;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo ($is_admin_page || $is_employee_page) ? '../index.php' : 'index.php'; ?>">
                <i class="fas fa-store"></i> <?php echo SITE_NAME; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo ($is_admin_page || $is_employee_page) ? '../index.php' : 'index.php'; ?>"><i class="fas fa-home"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo ($is_admin_page || $is_employee_page) ? '../products.php' : 'products.php'; ?>"><i class="fas fa-box"></i> Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo ($is_admin_page || $is_employee_page) ? '../categories.php' : 'categories.php'; ?>"><i class="fas fa-th-large"></i> Categories</a>
                    </li>

                </ul>
                
                <form class="d-flex search-form me-3" action="<?php echo ($is_admin_page || $is_employee_page) ? '../products.php' : 'products.php'; ?>" method="GET">
                    <input class="form-control me-2" type="search" name="search" placeholder="Search products..."
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button class="btn btn-outline-light" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>

                <!-- Theme Toggle Button -->
                <button id="theme-toggle" class="btn btn-outline-light me-3" title="Toggle Dark Mode">
                    <i class="fas fa-moon"></i>
                </button>

                

                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link notification-icon dropdown-toggle" href="#" id="notificationDropdown" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-bell"></i>
                                <?php if ($notification_count > 0): ?>
                                    <span class="badge bg-danger badge-notification"><?php echo $notification_count; ?></span>
                                <?php endif; ?>
                            </a>
                            <ul class="dropdown-menu notification-dropdown" aria-labelledby="notificationDropdown">
                                <li><h6 class="dropdown-header">Notifications</h6></li>
                                <?php
                                $notifications = getNotifications($_SESSION['user_id'], 5);
                                if (empty($notifications)): ?>
                                    <li><a class="dropdown-item" href="#">No new notifications</a></li>
                                <?php else: ?>
                                    <?php foreach ($notifications as $notification): ?>
                                        <?php
                                        $order_id = null;
                                        if (preg_match('/#(\d+)/', $notification['message'], $matches)) {
                                            $order_id = $matches[1];
                                        }
                                        ?>
                                        <li>
                                            <a class="dropdown-item <?php echo $notification['is_read'] ? '' : 'fw-bold'; ?>"
                                               href="#" onclick="markAsRead(<?php echo $notification['id']; ?>, <?php echo $order_id ? $order_id : 'null'; ?>)">
                                                <small class="text-muted"><?php echo date('M d', strtotime($notification['created_at'])); ?></small><br>
                                                <strong><?php echo htmlspecialchars($notification['title']); ?></strong><br>
                                                <?php echo htmlspecialchars(substr($notification['message'], 0, 50)); ?><?php echo strlen($notification['message']) > 50 ? '...' : ''; ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-center" href="<?php echo ($is_admin_page || $is_employee_page) ? '../notifications.php' : 'notifications.php'; ?>">View All</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link cart-icon" href="<?php echo ($is_admin_page || $is_employee_page) ? '../cart.php' : 'cart.php'; ?>">
                                <i class="fas fa-shopping-cart"></i>
                                <?php if ($cart_count > 0): ?>
                                    <span class="badge bg-warning badge-cart"><?php echo $cart_count; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                               data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?php echo $_SESSION['username']; ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="orders.php">
                                    <i class="fas fa-box"></i> My Orders</a>
                                </li>
                                <?php if (isAdmin()): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo $is_admin_page ? 'dashboard.php' : 'admin/dashboard.php'; ?>">
                                        <i class="fas fa-tachometer-alt"></i> Admin Panel</a>
                                    </li>
                                <?php elseif (isEmployee()): ?>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?php echo $is_employee_page ? 'dashboard.php' : 'employee/dashboard.php'; ?>">
                                        <i class="fas fa-tachometer-alt"></i> Employee Dashboard</a>
                                    </li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo ($is_admin_page || $is_employee_page) ? '../logout.php' : 'logout.php'; ?>">
                                    <i class="fas fa-sign-out-alt"></i> Logout</a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php"><i class="fas fa-user-plus"></i> Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Admin Sidebar -->
    <?php if ($is_admin_page): ?>
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <i class="fas fa-cog"></i> Admin Panel
            </div>
            <button class="sidebar-toggle d-md-none" onclick="toggleSidebar()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>" href="products.php">
                        <i class="fas fa-box"></i> Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>" href="categories.php">
                        <i class="fas fa-th-large"></i> Categories
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>" href="orders.php">
                        <i class="fas fa-shopping-cart"></i> Orders
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>" href="users.php">
                        <i class="fas fa-users"></i> Users
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <a class="nav-link" href="../index.php">
                        <i class="fas fa-store"></i> Back to Store
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Employee Sidebar -->
    <?php elseif ($is_employee_page): ?>
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <i class="fas fa-briefcase"></i> Employee Panel
            </div>
            <button class="sidebar-toggle d-md-none" onclick="toggleSidebar()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'active' : ''; ?>" href="orders.php">
                        <i class="fas fa-shopping-cart"></i> Orders
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>" href="products.php">
                        <i class="fas fa-box"></i> Products
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <a class="nav-link" href="../index.php">
                        <i class="fas fa-store"></i> Back to Store
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </nav>
    </aside>
    <?php endif; ?>

    <!-- Sidebar Toggle Button for Mobile -->
    <button class="sidebar-toggle d-md-none" onclick="toggleSidebar()" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <main class="<?php echo ($is_admin_page || $is_employee_page) ? 'admin-content' : ''; ?>">
