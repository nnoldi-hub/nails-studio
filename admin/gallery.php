<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

require_admin_login();

$page_title = 'Gestionare Galerie';

$success_message = '';
$error_message = '';

// Check for success message from redirect
if (isset($_GET['success'])) {
    $success_message = $_GET['success'];
}

// Simple upload handler
if ($_POST) {
    // Handle delete first (more specific condition)
    if (isset($_POST['delete_gallery']) && isset($_POST['gallery_id'])) {
        $id = (int)$_POST['gallery_id'];
        
        // Get image name first
        $stmt = $conn->prepare("SELECT image FROM gallery WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if ($row) {
            $debug_info .= "DELETE: Found image = " . $row['image'] . "<br>";
            // Delete from database
            $stmt = $conn->prepare("DELETE FROM gallery WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $debug_info .= "DELETE: Database delete successful<br>";
                // Delete file
                $file_path = '../assets/images/gallery/' . $row['image'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                    $debug_info .= "DELETE: File deleted = $file_path<br>";
                } else {
                    $debug_info .= "DELETE: File not found = $file_path<br>";
                }
                $success_message = "Imaginea a fost ștearsă cu succes!";
                header("Location: gallery.php?success=" . urlencode($success_message));
                exit();
            } else {
                $debug_info .= "DELETE: Database error = " . $conn->error . "<br>";
                $error_message = "Eroare la ștergerea imaginii.";
            }
        } else {
            $debug_info .= "DELETE: No row found for ID = $id<br>";
            $error_message = "Imaginea nu a fost găsită.";
        }
    }
    // Handle upload
    elseif (isset($_POST['title'])) { // Schimbat din add_gallery în title
        $title = sanitize_input($_POST['title']);
        $description = sanitize_input($_POST['description']);
        $is_featured = isset($_POST['is_featured']) ? 1 : 0;
        
        // Upload logic
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../assets/images/gallery/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $tmp_name = $_FILES['image']['tmp_name'];
            $original_name = $_FILES['image']['name'];
            
            // Simple name generation
            $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
            $new_name = 'img_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
            $target_path = $upload_dir . $new_name;
            
            // Move the file
            if (move_uploaded_file($tmp_name, $target_path)) {
                // Save to database
                $stmt = $conn->prepare("INSERT INTO gallery (title, description, image, is_featured) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("sssi", $title, $description, $new_name, $is_featured);
                
                if ($stmt->execute()) {
                    $success_message = "Imaginea '$title' a fost adăugată cu succes!";
                    // Redirect pentru a preveni re-submit la refresh
                    header("Location: gallery.php?success=" . urlencode($success_message));
                    exit();
                } else {
                    $error_message = "Eroare la salvarea în baza de date: " . $conn->error;
                    if (file_exists($target_path)) {
                        unlink($target_path);
                    }
                }
            } else {
                $error_message = "Eroare la încărcarea fișierului.";
            }
        } else {
            if (!isset($_FILES['image'])) {
                $error_message = "Nu a fost selectat niciun fișier.";
            } else {
                $error_code = $_FILES['image']['error'];
                $error_message = "Eroare la upload (cod: $error_code)";
            }
        }
    }
}

// Get gallery items directly
$gallery_items = [];
$result = $conn->query("SELECT * FROM gallery ORDER BY created_at DESC");
if ($result) {
    $gallery_items = $result->fetch_all(MYSQLI_ASSOC);
}

include 'includes/admin_header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/admin_sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gestionare Galerie</h1>
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
                                    <i class="fas fa-calendar-alt me-1"></i><?php echo isset($item['created_at']) ? date('d.m.Y', strtotime($item['created_at'])) : ''; ?>
                                </small>
                            </div>
                            <div class="card-footer">
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="gallery_id" value="<?php echo $item['id']; ?>">
                                    <input type="hidden" name="delete_gallery" value="1">
                                    <button type="submit" class="btn btn-sm btn-danger w-100"
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
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descriere</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Imagine *</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        <div class="form-text">Formatele acceptate: JPG, PNG, GIF. Mărimea maximă: <?php echo ini_get('upload_max_filesize'); ?></div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured">
                            <label class="form-check-label" for="is_featured">
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
