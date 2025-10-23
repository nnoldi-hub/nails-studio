<?php
// Test simplu upload - debug maxim
if ($_POST) {
    echo "<h1>FORM SUBMITTED!</h1>";
    echo "<h2>POST Data:</h2>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
    echo "<h2>FILES Data:</h2>";
    echo "<pre>" . print_r($_FILES, true) . "</pre>";
    
    if (isset($_FILES['image'])) {
        echo "<h2>File Details:</h2>";
        echo "Error code: " . $_FILES['image']['error'] . "<br>";
        echo "Tmp name: " . $_FILES['image']['tmp_name'] . "<br>";
        echo "Original name: " . $_FILES['image']['name'] . "<br>";
        echo "Size: " . $_FILES['image']['size'] . " bytes<br>";
        echo "Type: " . $_FILES['image']['type'] . "<br>";
        
        if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
            echo "<h3 style='color: green;'>FILE UPLOAD OK!</h3>";
            
            $target = '../assets/images/gallery/test_' . time() . '.jpg';
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                echo "<h3 style='color: green;'>FILE MOVED TO: $target</h3>";
            } else {
                echo "<h3 style='color: red;'>FAILED TO MOVE FILE!</h3>";
            }
        } else {
            echo "<h3 style='color: red;'>UPLOAD ERROR CODE: " . $_FILES['image']['error'] . "</h3>";
        }
    } else {
        echo "<h3 style='color: red;'>NO FILE IN _FILES ARRAY!</h3>";
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Upload</title>
</head>
<body>
    <h1>Test Upload Simple</h1>
    
    <div style="background: #f0f0f0; padding: 20px; margin: 20px 0;">
        <h3>PHP Settings:</h3>
        <strong>file_uploads:</strong> <?php echo ini_get('file_uploads') ? 'ON' : 'OFF'; ?><br>
        <strong>upload_max_filesize:</strong> <?php echo ini_get('upload_max_filesize'); ?><br>
        <strong>post_max_size:</strong> <?php echo ini_get('post_max_size'); ?><br>
        <strong>max_file_uploads:</strong> <?php echo ini_get('max_file_uploads'); ?><br>
        <strong>upload_tmp_dir:</strong> <?php echo ini_get('upload_tmp_dir'); ?><br>
    </div>
    
    <form method="POST" enctype="multipart/form-data" style="border: 2px solid #333; padding: 20px;">
        <h3>Test Form</h3>
        <label>Title:</label><br>
        <input type="text" name="title" value="Test Image" required><br><br>
        
        <label>Image:</label><br>
        <input type="file" name="image" accept="image/*" required><br><br>
        
        <button type="submit" style="background: #007cba; color: white; padding: 10px 20px; border: none;">
            UPLOAD TEST
        </button>
    </form>
</body>
</html>
