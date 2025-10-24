<?php
require_once 'includes/admin_header.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_admin_login();

$page_title = 'Administrare Articole Blog';
$error_message = '';
$success_message = '';

// Handle add new article
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_article'])) {
    $slug = sanitize_input($_POST['slug']);
    $title = sanitize_input($_POST['title']);
    $content = $_POST['content'];
    $meta_title = sanitize_input($_POST['meta_title']);
    $meta_description = sanitize_input($_POST['meta_description']);
    $meta_keywords = sanitize_input($_POST['meta_keywords']);

    $image = '';
    if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] === UPLOAD_ERR_OK) {
        $target_dir = '../assets/images/gallery/';
        $file_name = time() . '_' . basename($_FILES['image_upload']['name']);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES['image_upload']['tmp_name'], $target_file)) {
            $image = 'assets/images/gallery/' . $file_name;
        }
    } elseif (!empty($_POST['image_select'])) {
        $image = sanitize_input($_POST['image_select']);
    }

    $status = sanitize_input($_POST['status']);

    $check = $conn->prepare("SELECT id FROM articles WHERE slug = ? LIMIT 1");
    $check->bind_param('s', $slug);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error_message = 'Slug-ul există deja!';
    } else {
        $stmt = $conn->prepare("INSERT INTO articles (slug, title, content, meta_title, meta_description, meta_keywords, image, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssssss', $slug, $title, $content, $meta_title, $meta_description, $meta_keywords, $image, $status);
        if ($stmt->execute()) {
            $success_message = 'Articolul a fost adăugat!';
        } else {
            $error_message = 'Eroare la adăugare articol!';
        }
        $stmt->close();
    }
    $check->close();
}

// Handle delete article
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_article']) && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $stmt = $conn->prepare("DELETE FROM articles WHERE id = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $success_message = 'Articolul a fost șters!';
    } else {
        $error_message = 'Eroare la ștergerea articolului!';
    }
    $stmt->close();
}

// Handle edit/save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_article']) && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $title = sanitize_input($_POST['title']);
    $content = $_POST['content'];
    $meta_title = sanitize_input($_POST['meta_title']);
    $meta_description = sanitize_input($_POST['meta_description']);
    $meta_keywords = sanitize_input($_POST['meta_keywords']);
    $status = sanitize_input($_POST['status']);

    // Imagine actualizată
    $image = sanitize_input($_POST['current_image']);
    if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] === UPLOAD_ERR_OK) {
        $target_dir = '../assets/images/gallery/';
        $file_name = time() . '_' . basename($_FILES['image_upload']['name']);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES['image_upload']['tmp_name'], $target_file)) {
            $image = 'assets/images/gallery/' . $file_name;
        }
    } elseif (!empty($_POST['image_select'])) {
        $image = sanitize_input($_POST['image_select']);
    }

    $stmt = $conn->prepare("UPDATE articles SET title=?, content=?, meta_title=?, meta_description=?, meta_keywords=?, image=?, status=? WHERE id=?");
    $stmt->bind_param('sssssssi', $title, $content, $meta_title, $meta_description, $meta_keywords, $image, $status, $id);
    $stmt->execute();
    $stmt->close();
    $success_message = 'Articolul a fost actualizat!';
}

// Get all articles
$articles = $conn->query("SELECT * FROM articles ORDER BY created_at DESC");
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/admin_sidebar.php'; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Administrare Articole Blog</h1>
            </div>

            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"><?= $success_message; ?></div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?= $error_message; ?></div>
            <?php endif; ?>

            <div class="card mb-4 shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Articole existente</span>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addArticleModal">
                        <i class="fas fa-plus"></i> Adaugă articol
                    </button>
                </div>
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Slug</th>
                                <th>Titlu</th>
                                <th>Status</th>
                                <th>Imagine</th>
                                <th>Acțiuni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $articles->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id']; ?></td>
                                <td><?= htmlspecialchars($row['slug']); ?></td>
                                <td><?= htmlspecialchars($row['title']); ?></td>
                                <td><span class="badge bg-<?= $row['status'] === 'published' ? 'success' : 'secondary'; ?>"><?= $row['status']; ?></span></td>
                                <td class="text-center">
                                    <?php if (!empty($row['image'])): ?>
                                        <img src="../<?= htmlspecialchars($row['image']); ?>" alt="" style="height:60px; border-radius:5px;">
                                    <?php else: ?>
                                        <small class="text-muted">Fără imagine</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id']; ?>">Editează</button>
                                    <form method="POST" style="display:inline-block;" onsubmit="return confirm('Sigur vrei să ștergi acest articol?');">
                                        <input type="hidden" name="delete_article" value="1">
                                        <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger ms-1">Șterge</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modal Adaugă Articol Nou -->
            <div class="modal fade" id="addArticleModal" tabindex="-1" aria-labelledby="addArticleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="add_article" value="1">
                            <div class="modal-header">
                                <h5 class="modal-title">Adaugă Articol Nou</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Slug (unic, fără spații)</label>
                                    <input type="text" name="slug" class="form-control" required pattern="^[a-zA-Z0-9_-]+$">
                                </div>
                                <div class="mb-3">
                                    <label>Titlu</label>
                                    <input type="text" name="title" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label>Conținut</label>
                                    <textarea name="content" class="form-control" rows="8" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label>Meta Title</label>
                                    <input type="text" name="meta_title" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label>Meta Description</label>
                                    <input type="text" name="meta_description" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label>Meta Keywords</label>
                                    <input type="text" name="meta_keywords" class="form-control">
                                </div>

                                <!-- Imagine -->
                                <div class="mb-3">
                                    <label>Imagine pentru articol</label>
                                    <input type="file" name="image_upload" class="form-control mb-2" accept="image/*" onchange="previewImage(event, 'preview_add')">
                                    <label class="mt-2">Sau selectează imagine existentă:</label>
                                    <select name="image_select" class="form-select mb-2" onchange="updatePreviewFromSelect(this, 'preview_add')">
                                        <option value="">-- Selectează --</option>
                                        <?php
                                        $img_dir = '../assets/images/gallery/';
                                        foreach (glob($img_dir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE) as $img) {
                                            $img_rel = str_replace('../', '', $img);
                                            echo '<option value="' . htmlspecialchars($img_rel) . '">' . htmlspecialchars(basename($img)) . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <div class="mt-3 text-center">
                                        <img id="preview_add" src="" alt="Previzualizare imagine" style="max-height:150px; display:none; border-radius:10px; border:1px solid #ddd; padding:5px;">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label>Status</label>
                                    <select name="status" class="form-select">
                                        <option value="draft">Draft</option>
                                        <option value="published">Publicat</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Adaugă</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulează</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Modals pentru editare -->
            <?php $articles->data_seek(0); while ($row = $articles->fetch_assoc()): ?>
            <div class="modal fade" id="editModal<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="edit_article" value="1">
                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                            <input type="hidden" name="current_image" value="<?= htmlspecialchars($row['image']); ?>">

                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">Editează Articol: <?= htmlspecialchars($row['title']); ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Titlu</label>
                                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($row['title']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label>Conținut</label>
                                    <textarea name="content" class="form-control" rows="8" required><?= htmlspecialchars($row['content']); ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label>Meta Title</label>
                                    <input type="text" name="meta_title" class="form-control" value="<?= htmlspecialchars($row['meta_title']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label>Meta Description</label>
                                    <input type="text" name="meta_description" class="form-control" value="<?= htmlspecialchars($row['meta_description']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label>Meta Keywords</label>
                                    <input type="text" name="meta_keywords" class="form-control" value="<?= htmlspecialchars($row['meta_keywords']); ?>">
                                </div>

                                <!-- Imagine -->
                                <div class="mb-3">
                                    <label>Imagine pentru articol</label>
                                    <input type="file" name="image_upload" class="form-control mb-2" accept="image/*" onchange="previewImage(event, 'preview_edit<?= $row['id']; ?>')">
                                    <label class="mt-2">Sau selectează imagine existentă:</label>
                                    <select name="image_select" class="form-select mb-2" onchange="updatePreviewFromSelect(this, 'preview_edit<?= $row['id']; ?>')">
                                        <option value="">-- Selectează --</option>
                                        <?php
                                        foreach (glob($img_dir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE) as $img) {
                                            $img_rel = str_replace('../', '', $img);
                                            $selected = ($row['image'] == $img_rel) ? 'selected' : '';
                                            echo '<option value="' . htmlspecialchars($img_rel) . '" ' . $selected . '>' . htmlspecialchars(basename($img)) . '</option>';
                                        }
                                        ?>
                                    </select>
                                    <div class="mt-3 text-center">
                                        <img id="preview_edit<?= $row['id']; ?>" 
                                             src="../<?= htmlspecialchars($row['image']); ?>" 
                                             alt="Previzualizare imagine" 
                                             style="max-height:150px; border-radius:10px; border:1px solid #ddd; padding:5px;">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label>Status</label>
                                    <select name="status" class="form-select">
                                        <option value="draft" <?= $row['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                        <option value="published" <?= $row['status'] === 'published' ? 'selected' : ''; ?>>Publicat</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Salvează</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulează</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </main>
    </div>
</div>

<script>
function previewImage(event, previewId) {
    const output = document.getElementById(previewId);
    const file = event.target.files[0];
    if (file) {
        output.src = URL.createObjectURL(file);
        output.style.display = 'block';
    } else {
        output.src = '';
        output.style.display = 'none';
    }
}
function updatePreviewFromSelect(select, previewId) {
    const value = select.value;
    const output = document.getElementById(previewId);
    if (value) {
        output.src = '../' + value;
        output.style.display = 'block';
    } else {
        output.src = '';
        output.style.display = 'none';
    }
}
</script>

<?php include 'includes/admin_footer.php'; ?>
