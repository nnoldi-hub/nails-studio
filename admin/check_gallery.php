<?php
require_once '../includes/config.php';

echo "Gallery items in database:\n";
$result = $conn->query('SELECT id, title, image FROM gallery ORDER BY id');
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . " - Title: " . $row['title'] . " - Image: " . $row['image'] . "\n";
    }
} else {
    echo "Error: " . $conn->error;
}
?>
