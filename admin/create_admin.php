<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Setează aici datele noului admin
$new_username = 'Andreea_Studio';
$new_email = 'Andreea_Studio@gmail.com';
$new_password = 'A_SB2025sector3'; // va fi hash-uit
$new_full_name = 'Andreea Strat-Balta';

// Hash parola
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// Creează admin
$conn->set_charset('utf8');
$stmt = $conn->prepare("INSERT INTO admin_users (username, email, password, full_name) VALUES (?, ?, ?, ?)");
$stmt->bind_param('ssss', $new_username, $new_email, $hashed_password, $new_full_name);
if ($stmt->execute()) {
    echo "Cont admin creat cu succes!<br>Username: $new_username<br>Email: $new_email<br>Parola: $new_password";
} else {
    echo "Eroare la crearea contului admin: " . $stmt->error;
}
$stmt->close();
$conn->close();
?>
