<?php
require_once '../includes/config.php';
$sql = "SELECT id, name, image FROM services";
$res = $conn->query($sql);
echo "<table border=1 cellpadding=5><tr><th>ID</th><th>Nume</th><th>Imagine</th></tr>";
while($row = $res->fetch_assoc()) {
    $imgPath = '../assets/images/services/' . $row['image'];
    if (!empty($row['image']) && file_exists(__DIR__ . '/../assets/images/services/' . $row['image'])) {
        $thumb = '../assets/images/services/' . $row['image'];
    } else {
        $thumb = '../assets/images/default-service.jpg';
    }
    echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['image']}<br><img src='$thumb' style='max-width:80px;max-height:80px'></td></tr>";
}
echo "</table>";
?>