<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

require_admin_login();

$page_title = 'Gestionare Galerie - Simplu';

$success_message = '';
$error_message = '';

// Simple upload handler - no complex validation, just basic checks
if ($_POST) {
    if (isset($_POST['add_gallery'])) {
        $title = sanitize_input($_POST['title']);
        $description = sanitize_input($_POST['description']);
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        
        // DEBUG: Log what we received
        $debug_info = "POST DATA: " . print_r($_POST, true) . "\n";
        $debug_info .= "FILES DATA: " . print_r($_FILES, true) . "\n";
        file_put_contents('../debug_upload.log', date('Y-m-d H:i:s') . " " . $debug_info . "\n", FILE_APPEND);
        
        if (isset($_FILES['image'])) {
            $debug_info .= "File error code: " . $_FILES['image']['error'] . "\n";
            file_put_contents('../debug_upload.log', $debug_info, FILE_APPEND);
            
            if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../assets/images/gallery/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $tmp_name = $_FILES['image']['tmp_name'];
            $original_name = $_FILES['image']['name'];
            $file_size = $_FILES['image']['size'];
            
            // DEBUG: More logging
            file_put_contents('../debug_upload.log', "Upload dir: $upload_dir, exists: " . (is_dir($upload_dir) ? 'YES' : 'NO') . "\n", FILE_APPEND);
            file_put_contents('../debug_upload.log', "Tmp file: $tmp_name, exists: " . (file_exists($tmp_name) ? 'YES' : 'NO') . "\n", FILE_APPEND);
            
            // Simple name generation
            $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
            $new_name = 'img_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            $target_path = $upload_dir . $new_name;
            
            file_put_contents('../debug_upload.log', "Target path: $target_path\n", FILE_APPEND);
            
            // Just move the file - no complex processing
            if (move_uploaded_file($tmp_name, $target_path)) {
                file_put_contents('../debug_upload.log', "File moved successfully!\n", FILE_APPEND);
                
                // Save to database
                $stmt = $conn->prepare("INSERT INTO gallery (title, description, image, is_featured) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sssi", $title, $description, $new_name, $is_featured);
                
                if ($stmt->execute()) {
                    $success_message = "Imaginea a fost adăugată cu succes!";
                    file_put_contents('../debug_upload.log', "Database insert successful!\n", FILE_APPEND);
                } else {
                    $error_message = "Eroare la salvarea în baza de date: " . $conn->error;
                    file_put_contents('../debug_upload.log', "Database error: " . $conn->error . "\n", FILE_APPEND);
                    unlink($target_path); // Remove uploaded file if DB fails
                }
            } else {
                $error_message = "Eroare la încărcarea fișierului.";
                file_put_contents('../debug_upload.log', "move_uploaded_file FAILED!\n", FILE_APPEND);
            }
        } else {
            $error_message = "Eroare fișier: " . $_FILES['image']['error'];
            file_put_contents('../debug_upload.log', "File error: " . $_FILES['image']['error'] . "\n", FILE_APPEND);
        }
        } else {
            $error_message = "Vă rugăm să selectați o imagine.";
            file_put_contents('../debug_upload.log', "No file selected\n", FILE_APPEND);
        }
    }
    
    if (isset($_POST['delete_gallery'])) {
        $id = (int)$_POST['gallery_id'];
        
        // Get image name first
        $stmt = $conn->prepare("SELECT image FROM gallery WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row) {
            // Delete from database
            $stmt = $conn->prepare("DELETE FROM gallery WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                // Delete file
                $file_path = '../assets/images/gallery/' . $row['image'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
                $success_message = "Imaginea a fost ștearsă cu succes!";
            } else {
                $error_message = "Eroare la ștergerea imaginii.";
            }
        }
    }
}

// Get gallery items
$gallery_items = get_gallery_items(false);

include 'includes/admin_header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/admin_sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gestionare Galerie - Simplu</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        <i class="fas fa-plus me-1"></i>Adaugă Imagine
                    </button>
                </div>
            </div>

            <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error_message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <!-- Gallery Grid -->
            <div class="row">
                <?php if (!empty($gallery_items)): ?>
                    <?php foreach ($gallery_items as $item): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card">
                            <div class="position-relative">
                                <?php 
                                $image_path = '../assets/images/gallery/' . $item['image'];
                                $display_path = file_exists($image_path) ? $image_path : '../assets/images/placeholder.png';
                                ?>
                                <img src="<?php echo htmlspecialchars($display_path); ?>"
                                     class="card-img-top" alt="<?php echo htmlspecialchars($item['title']); ?>"
                                     style="height:200px;object-fit:cover;">
                                <?php if ($item['is_featured']): ?>
                                <span class="position-absolute top-0 end-0 badge bg-warning m-2">
                                    <i class="fas fa-star"></i> Featured
                                </span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">
                                <h6 class="card-title"><?php echo htmlspecialchars($item['title']); ?></h6>
                                <p class="card-text text-muted small"><?php echo htmlspecialchars($item['description']); ?></p>
                                <small class="text-muted">
                                    <i class="fas fa-calendar-alt me-1"></i><?php echo format_date($item['created_at']); ?>
                                </small>
                            </div>
                            <div class="card-footer">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="gallery_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" name="delete_gallery" class="btn btn-sm btn-danger w-100"
                                            onclick="return confirm('Sigur vrei să ștergi această imagine?')">
                                        <i class="fas fa-trash"></i> Șterge
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                <div class="col-12">
                    <div class="text-center py-4">
                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                        <h5>Nu există imagini în galerie</h5>
                        <p class="text-muted">Adaugă prima imagine pentru a începe galeria.</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adaugă Imagine Nouă</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label">Titlu *</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descriere</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Imagine *</label>
                        <input type="file" class="form-control" name="image" accept="image/*" required>
                        <div class="form-text">Formatele acceptate: JPG, PNG, GIF. Mărimea maximă: <?php echo ini_get('upload_max_filesize'); ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_featured">
                            <label class="form-check-label">
                                Imagine principală (Featured)
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulează</button>
                    <button type="submit" name="add_gallery" class="btn btn-primary">Adaugă Imagine</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>
