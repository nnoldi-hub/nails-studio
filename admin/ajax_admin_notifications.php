<?php
require_once '../includes/config.php';
// Număr programări neprocesate (pending)
$stmt = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE status = 'pending'");
$stmt->execute();
$stmt->bind_result($pending_appointments);
$stmt->fetch();
$stmt->close();
// Număr mesaje necitite
$stmt = $conn->prepare("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0");
$stmt->execute();
$stmt->bind_result($unread_messages);
$stmt->fetch();
$stmt->close();
header('Content-Type: application/json');
echo json_encode([
    'appointments' => $pending_appointments,
    'messages' => $unread_messages
]);
?>