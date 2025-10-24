<?php
require_once 'includes/admin_header.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_admin_login();

$page_title = 'Administrare Pagini Publice';
$error_message = '';
$success_message = '';

// Handle add new page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_page'])) {
    $slug = sanitize_input($_POST['slug']);
    $title = sanitize_input($_POST['title']);
    $content = $_POST['content'];
    $meta_title = sanitize_input($_POST['meta_title']);
    $meta_description = sanitize_input($_POST['meta_description']);
    $meta_keywords = sanitize_input($_POST['meta_keywords']);
    $color = sanitize_input($_POST['color']);
    $image = sanitize_input($_POST['image']);
    // Check for duplicate slug
    $check = $conn->prepare("SELECT id FROM pages WHERE slug = ? LIMIT 1");
    $check->bind_param('s', $slug);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        $error_message = 'Slug-ul există deja!';
    } else {
        $stmt = $conn->prepare("INSERT INTO pages (slug, title, content, meta_title, meta_description, meta_keywords, color, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssssss', $slug, $title, $content, $meta_title, $meta_description, $meta_keywords, $color, $image);
        if ($stmt->execute()) {
            $success_message = 'Pagina nouă a fost adăugată!';
        } else {
            $error_message = 'Eroare la adăugare pagină!';
        }
        $stmt->close();
    }
    $check->close();
}

// Handle delete page
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_page']) && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $stmt = $conn->prepare("DELETE FROM pages WHERE id = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $success_message = 'Pagina a fost ștearsă!';
    } else {
        $error_message = 'Eroare la ștergerea paginii!';
    }
    $stmt->close();
}

// Handle edit/save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $title = sanitize_input($_POST['title']);
    $content = $_POST['content'];
    $meta_title = sanitize_input($_POST['meta_title']);
    $meta_description = sanitize_input($_POST['meta_description']);
    $meta_keywords = sanitize_input($_POST['meta_keywords']);
    $color = sanitize_input($_POST['color']);
    $image = sanitize_input($_POST['image']); // For simplicity, just path
    $stmt = $conn->prepare("UPDATE pages SET title=?, content=?, meta_title=?, meta_description=?, meta_keywords=?, color=?, image=? WHERE id=?");
    $stmt->bind_param('sssssssi', $title, $content, $meta_title, $meta_description, $meta_keywords, $color, $image, $id);
    $stmt->execute();
    $stmt->close();
    $success_message = 'Pagina a fost actualizată!';
}

// Get all pages
$pages = $conn->query("SELECT * FROM pages ORDER BY id ASC");

// Footer settings (temporar, se pot muta în DB)
$footer_settings = [
    'brand_title' => 'Nail Studio Andreea',
    'brand_text' => 'Salonul tău de încredere pentru servicii profesionale de manichiură, pedichiură și cursuri de specializare.',
    'facebook' => '#',
    'instagram' => '#',
    'tiktok' => '#',
    'services' => 'Manichiură,Pedichiură,Extensii Unghii,Cursuri Coaching',
    'contact_address' => 'Str. Exemplu, Nr. 123, București',
    'contact_phone' => '+40 123 456 789',
    'contact_email' => 'contact@nailstudioandreea.ro',
    'contact_hours' => 'Lun-Vin: 9:00-19:00, Sâm: 9:00-17:00',
    'copyright' => '© ' . date('Y') . ' Nail Studio Andreea. Toate drepturile rezervate.',
    'admin_link' => SITE_URL . '/admin',
];

// Handle footer update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_footer'])) {
    foreach ($footer_settings as $key => $val) {
        if (isset($_POST[$key])) {
            $footer_settings[$key] = sanitize_input($_POST[$key]);
        }
    }
    $success_message = 'Footer actualizat! (temporar, nu persistă după refresh)';
}
?>
<div class="container-fluid">
    <div class="row">
        <?php include 'includes/admin_sidebar.php'; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Administrare Pagini Publice</h1>
            </div>
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success"> <?php echo $success_message; ?> </div>
            <?php endif; ?>
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"> <?php echo $error_message; ?> </div>
            <?php endif; ?>
            <div class="card mb-4 shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Pagini disponibile</span>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPageModal">
                        <i class="fas fa-plus"></i> Adaugă pagină
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Slug</th>
                                <th>Titlu</th>
                                <th>Meta Title</th>
                                <th>Imagine</th>
                                <th>Culoare</th>
                                <th>Acțiuni</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = $pages->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['slug']); ?></td>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['meta_title']); ?></td>
                                <td><?php echo htmlspecialchars($row['image']); ?></td>
                                <td><span style="background:<?php echo htmlspecialchars($row['color']); ?>;padding:2px 10px;border-radius:5px;color:#fff;">&nbsp;</span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>">Editează</button>
                                    <form method="POST" style="display:inline-block;" onsubmit="return confirm('Sigur vrei să ștergi această pagină?');">
                                        <input type="hidden" name="delete_page" value="1">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger ms-1">Șterge</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Modal Adaugă Pagina Nouă -->
            <div class="modal fade" id="addPageModal" tabindex="-1" aria-labelledby="addPageModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="POST">
                            <input type="hidden" name="add_page" value="1">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addPageModalLabel">Adaugă Pagina Nouă</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                <div class="mb-3">
                                    <label>Culoare</label>
                                    <input type="color" name="color" class="form-control form-control-color" value="#e91e63">
                                </div>
                                <div class="mb-3">
                                    <label>Imagine (cale sau URL)</label>
                                    <input type="text" name="image" class="form-control">
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

            <!-- Modals for edit -->
            <?php $pages->data_seek(0); while ($row = $pages->fetch_assoc()): ?>
            <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editModalLabel<?php echo $row['id']; ?>">Editează Pagina: <?php echo htmlspecialchars($row['title']); ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <div class="mb-3">
                                    <label>Titlu</label>
                                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($row['title']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label>Conținut</label>
                                    <textarea name="content" class="form-control" rows="8" required><?php echo htmlspecialchars($row['content']); ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label>Meta Title</label>
                                    <input type="text" name="meta_title" class="form-control" value="<?php echo htmlspecialchars($row['meta_title']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label>Meta Description</label>
                                    <input type="text" name="meta_description" class="form-control" value="<?php echo htmlspecialchars($row['meta_description']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label>Meta Keywords</label>
                                    <input type="text" name="meta_keywords" class="form-control" value="<?php echo htmlspecialchars($row['meta_keywords']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label>Culoare</label>
                                    <input type="color" name="color" class="form-control form-control-color" value="<?php echo htmlspecialchars($row['color']); ?>">
                                </div>
                                <div class="mb-3">
                                    <label>Imagine (cale sau URL)</label>
                                    <input type="text" name="image" class="form-control" value="<?php echo htmlspecialchars($row['image']); ?>">
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

            <!-- Footer settings card -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">Editare Footer Site</div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="update_footer" value="1">
                        <div class="row mb-3">
                            <div class="col-md-6 mb-2">
                                <label>Titlu Brand</label>
                                <input type="text" name="brand_title" class="form-control" value="<?php echo htmlspecialchars($footer_settings['brand_title']); ?>">
                            </div>
                            <div class="col-md-6 mb-2">
                                <label>Text Brand</label>
                                <input type="text" name="brand_text" class="form-control" value="<?php echo htmlspecialchars($footer_settings['brand_text']); ?>">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 mb-2">
                                <label>Facebook</label>
                                <input type="text" name="facebook" class="form-control" value="<?php echo htmlspecialchars($footer_settings['facebook']); ?>">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label>Instagram</label>
                                <input type="text" name="instagram" class="form-control" value="<?php echo htmlspecialchars($footer_settings['instagram']); ?>">
                            </div>
                            <div class="col-md-4 mb-2">
                                <label>TikTok</label>
                                <input type="text" name="tiktok" class="form-control" value="<?php echo htmlspecialchars($footer_settings['tiktok']); ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Servicii (separate prin virgulă)</label>
                            <input type="text" name="services" class="form-control" value="<?php echo htmlspecialchars($footer_settings['services']); ?>">
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6 mb-2">
                                <label>Adresă Contact</label>
                                <input type="text" name="contact_address" class="form-control" value="<?php echo htmlspecialchars($footer_settings['contact_address']); ?>">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label>Telefon</label>
                                <input type="text" name="contact_phone" class="form-control" value="<?php echo htmlspecialchars($footer_settings['contact_phone']); ?>">
                            </div>
                            <div class="col-md-3 mb-2">
                                <label>Email</label>
                                <input type="text" name="contact_email" class="form-control" value="<?php echo htmlspecialchars($footer_settings['contact_email']); ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Program</label>
                            <input type="text" name="contact_hours" class="form-control" value="<?php echo htmlspecialchars($footer_settings['contact_hours']); ?>">
                        </div>
                        <div class="mb-3">
                            <label>Copyright</label>
                            <input type="text" name="copyright" class="form-control" value="<?php echo htmlspecialchars($footer_settings['copyright']); ?>">
                        </div>
                        <div class="mb-3">
                            <label>Link Admin</label>
                            <input type="text" name="admin_link" class="form-control" value="<?php echo htmlspecialchars($footer_settings['admin_link']); ?>">
                        </div>
                        <button type="submit" class="btn btn-dark">Salvează Footer</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
<?php include 'includes/admin_footer.php'; ?>
