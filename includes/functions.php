<?php
require_once 'config.php';

// Function to check if shop module is enabled
function get_shop_enabled() {
    global $conn;
    // Try to get from settings table if exists, else default true
    if ($conn->query("SHOW TABLES LIKE 'settings'")->num_rows) {
        $result = $conn->query("SELECT value FROM settings WHERE name = 'shop_enabled' LIMIT 1");
        if ($row = $result->fetch_assoc()) {
            return $row['value'] == '1';
        }
    }
    return true; // default enabled
}

// Function to sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to check if user is logged in as admin
function is_admin_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Function to redirect to login if not admin
function require_admin_login() {
    if (!is_admin_logged_in()) {
        header('Location: ' . SITE_URL . '/admin/login.php');
        exit();
    }
}

// Function to get all services
function get_all_services($active_only = true) {
    global $conn;
    $where = $active_only ? "WHERE is_active = 1" : "";
    $sql = "SELECT * FROM services $where ORDER BY name";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to get service by ID
function get_service_by_id($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

// Function to add appointment
function add_appointment($client_name, $client_email, $client_phone, $service_id, $appointment_date, $appointment_time, $notes = '') {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO appointments (client_name, client_email, client_phone, service_id, appointment_date, appointment_time, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssisss", $client_name, $client_email, $client_phone, $service_id, $appointment_date, $appointment_time, $notes);
    return $stmt->execute();
}

// Function to get all appointments
function get_all_appointments($status = null) {
    global $conn;
    $where = $status ? "WHERE a.status = '$status'" : "";
    $sql = "SELECT a.*, s.name as service_name, s.price, s.duration 
            FROM appointments a 
            JOIN services s ON a.service_id = s.id 
            $where 
            ORDER BY a.appointment_date DESC, a.appointment_time DESC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to get gallery items
function get_gallery_items($featured_only = false) {
    global $conn;
    $where = $featured_only ? "WHERE is_featured = 1" : "";
    $sql = "SELECT * FROM gallery $where ORDER BY created_at DESC";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to get coaching sessions
function get_coaching_sessions($active_only = true) {
    global $conn;
    $where = $active_only ? "WHERE is_active = 1" : "";
    $sql = "SELECT * FROM coaching_sessions $where ORDER BY session_name";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to add coaching booking
function add_coaching_booking($client_name, $client_email, $client_phone, $session_id, $booking_date, $booking_time, $notes = '') {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO coaching_bookings (client_name, client_email, client_phone, session_id, booking_date, booking_time, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssisss", $client_name, $client_email, $client_phone, $session_id, $booking_date, $booking_time, $notes);
    return $stmt->execute();
}

// Function to add contact message
function add_contact_message($name, $email, $subject, $message) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);
    return $stmt->execute();
}

// Function to authenticate admin
function authenticate_admin($username, $password) {
    global $conn;
    $stmt = $conn->prepare("SELECT id, username, password, full_name FROM admin_users WHERE username = ? AND is_active = 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            $_SESSION['admin_name'] = $user['full_name'];
            return true;
        }
    }
    return false;
}

// Function to format date
function format_date($date) {
    return date('d.m.Y', strtotime($date));
}

// Function to format time
function format_time($time) {
    return date('H:i', strtotime($time));
}
?>
