<?php
require_once 'includes/admin_header.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_admin_login();

$page_title = 'Administrare Produse Magazin';
$error_message = '';
$success_message = '';

$upload_dir = '../assets/images/gallery/';
if (!is_dir($upload_dir)) {
    @mkdir($upload_dir, 0755, true);
}

// Helper: sanitize (folosește funcția ta deja existentă dacă ai)
function esc($v) { return htmlspecialchars($v, ENT_QUOTES); }

// === Adaugă produs ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $slug = sanitize_input($_POST['slug']);
    $title = sanitize_input($_POST['title']);
    $description = $_POST['description'];
    $content_list = $_POST['content_list'];
    $meta_keywords = sanitize_input($_POST['meta_keywords']);

    // categorie custom
    $category = '';
    if (!empty($_POST['category_custom'])) {
        $category = sanitize_input($_POST['category_custom']);
    } else {
        $category = sanitize_input($_POST['category']);
    }

    $level = sanitize_input($_POST['level']);
    $brand = sanitize_input($_POST['brand']);
    $accent_color = sanitize_input($_POST['accent_color']);
    $badge_recommended = isset($_POST['badge_recommended']) ? 1 : 0;
    $tutorial_link = sanitize_input($_POST['tutorial_link']);
    $price = isset($_POST['price']) ? trim($_POST['price']) : '0';
    $status = sanitize_input($_POST['status']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Upload imagine
    $image_path = '';
    if (!empty($_FILES['image_upload']['name'])) {
        $filename = basename($_FILES['image_upload']['name']);
        $target_file = $upload_dir . time() . '_' . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $filename);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            if (move_uploaded_file($_FILES['image_upload']['tmp_name'], $target_file)) {
                // stocăm calea relativă fără ../ pentru a o folosi în frontend
                $image_path = ltrim(str_replace('../', '', $target_file), '/');
            } else {
                $error_message = 'Eroare la încărcarea imaginii!';
            }
        } else {
            $error_message = 'Format invalid de imagine! Doar JPG, PNG, GIF, WEBP.';
        }
    }

    if (empty($error_message)) {
    $stmt = $conn->prepare("INSERT INTO products (slug, title, description, content_list, meta_keywords, image, category, level, brand, accent_color, badge_recommended, tutorial_link, price, status, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $types = 'ssssssssssisssi';
    $stmt->bind_param($types, $slug, $title, $description, $content_list, $meta_keywords, $image_path, $category, $level, $brand, $accent_color, $badge_recommended, $tutorial_link, $price, $status, $is_active);
        if ($stmt->execute()) {
            $success_message = 'Produsul a fost adăugat!';
        } else {
            $error_message = 'Eroare la adăugarea produsului: ' . $stmt->error;
        }
        $stmt->close();
    }
}

// === Șterge produs ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $id = (int)$_POST['id'];
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        $success_message = 'Produsul a fost șters!';
    } else {
        $error_message = 'Eroare la ștergere!';
    }
    $stmt->close();
}

// === Actualizează produs ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_product'])) {
    $id = (int)$_POST['id'];
    $slug = sanitize_input($_POST['slug']);
    $title = sanitize_input($_POST['title']);
    $description = $_POST['description'];
    $content_list = $_POST['content_list'];
    $meta_keywords = sanitize_input($_POST['meta_keywords']);

    // categorie custom
    if (!empty($_POST['category_custom'])) {
        $category = sanitize_input($_POST['category_custom']);
    } else {
        $category = sanitize_input($_POST['category']);
    }

    $level = sanitize_input($_POST['level']);
    $brand = sanitize_input($_POST['brand']);
    $accent_color = sanitize_input($_POST['accent_color']);
    $badge_recommended = isset($_POST['badge_recommended']) ? 1 : 0;
    $tutorial_link = sanitize_input($_POST['tutorial_link']);
    $price = isset($_POST['price']) ? trim($_POST['price']) : '0';
    $status = sanitize_input($_POST['status']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // Imagine nouă (opțional)
    $image_path = isset($_POST['existing_image']) ? $_POST['existing_image'] : '';
    if (!empty($_FILES['image_upload']['name'])) {
        $filename = basename($_FILES['image_upload']['name']);
        $target_file = $upload_dir . time() . '_' . preg_replace('/[^A-Za-z0-9_\.-]/', '_', $filename);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        if (in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            if (move_uploaded_file($_FILES['image_upload']['tmp_name'], $target_file)) {
                $image_path = ltrim(str_replace('../', '', $target_file), '/');
            } else {
                $error_message = 'Eroare la încărcarea noii imagini!';
            }
        } else {
            $error_message = 'Format invalid de imagine!';
        }
    }

    if (empty($error_message)) {
    $stmt = $conn->prepare("UPDATE products SET slug=?, title=?, description=?, content_list=?, meta_keywords=?, image=?, category=?, level=?, brand=?, accent_color=?, badge_recommended=?, tutorial_link=?, price=?, status=?, is_active=? WHERE id=?");
    $types = 'ssssssssssisssii';
    $stmt->bind_param($types, $slug, $title, $description, $content_list, $meta_keywords, $image_path, $category, $level, $brand, $accent_color, $badge_recommended, $tutorial_link, $price, $status, $is_active, $id);
        if ($stmt->execute()) {
            $success_message = 'Produsul a fost actualizat!';
        } else {
            $error_message = 'Eroare la actualizare: ' . $stmt->error;
        }
        $stmt->close();
    }
}

// === Select produse existente ===
$products_q = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
$products_list = [];
while ($r = $products_q->fetch_assoc()) {
    $products_list[] = $r;
}
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/admin_sidebar.php'; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Administrare Produse Magazin</h1>
            </div>

            <?php if ($success_message): ?>
                <div class="alert alert-success"><?= esc($success_message); ?></div>
            <?php elseif ($error_message): ?>
                <div class="alert alert-danger"><?= esc($error_message); ?></div>
            <?php endif; ?>

            <div class="card mb-4 shadow">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Produse existente</span>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                        <i class="fas fa-plus"></i> Adaugă produs
                    </button>
                </div>
                <!-- Filtru/Căutare produse -->
                <form method="GET" class="row g-2 mb-3 align-items-end bg-light p-3 rounded shadow-sm">
                    <div class="col-md-3">
                        <label class="form-label">Titlu</label>
                        <input type="text" name="filter_title" class="form-control" value="<?= isset($_GET['filter_title']) ? htmlspecialchars($_GET['filter_title']) : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Categorie</label>
                        <input type="text" name="filter_category" class="form-control" value="<?= isset($_GET['filter_category']) ? htmlspecialchars($_GET['filter_category']) : ''; ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="filter_status" class="form-select">
                            <option value="">Toate</option>
                            <option value="disponibil" <?= (isset($_GET['filter_status']) && $_GET['filter_status']=='disponibil')?'selected':''; ?>>Disponibil</option>
                            <option value="indisponibil" <?= (isset($_GET['filter_status']) && $_GET['filter_status']=='indisponibil')?'selected':''; ?>>Indisponibil</option>
                            <option value="stoc_limitat" <?= (isset($_GET['filter_status']) && $_GET['filter_status']=='stoc_limitat')?'selected':''; ?>>Stoc limitat</option>
                            <option value="ascuns" <?= (isset($_GET['filter_status']) && $_GET['filter_status']=='ascuns')?'selected':''; ?>>Ascuns</option>
                            <option value="revizie" <?= (isset($_GET['filter_status']) && $_GET['filter_status']=='revizie')?'selected':''; ?>>În revizie</option>
                            <option value="arhivat" <?= (isset($_GET['filter_status']) && $_GET['filter_status']=='arhivat')?'selected':''; ?>>Arhivat</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Activ</label>
                        <select name="filter_active" class="form-select">
                            <option value="">Toate</option>
                            <option value="1" <?= (isset($_GET['filter_active']) && $_GET['filter_active']=='1')?'selected':''; ?>>Activ</option>
                            <option value="0" <?= (isset($_GET['filter_active']) && $_GET['filter_active']=='0')?'selected':''; ?>>Inactiv</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i> Filtrează</button>
                    </div>
                </form>
                <?php
                // Filtrare produse
                $where = [];
                if (!empty($_GET['filter_title'])) $where[] = "title LIKE '%".$conn->real_escape_string($_GET['filter_title'])."%'";
                if (!empty($_GET['filter_category'])) $where[] = "category LIKE '%".$conn->real_escape_string($_GET['filter_category'])."%'";
                if (isset($_GET['filter_status']) && $_GET['filter_status']!=='') $where[] = "status='".$conn->real_escape_string($_GET['filter_status'])."'";
                if (isset($_GET['filter_active']) && $_GET['filter_active']!=='') $where[] = "is_active='".intval($_GET['filter_active'])."'";
                $sql_products = "SELECT * FROM products";
                if ($where) $sql_products .= " WHERE ".implode(' AND ', $where);
                $sql_products .= " ORDER BY id DESC";
                $products = $conn->query($sql_products);
                $products_list = [];
                while ($row = $products->fetch_assoc()) {
                    $products_list[] = $row;
                }
                ?>
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Titlu</th>
                                <th>Categorie</th>
                                <th>Preț</th>
                                <th>Status</th>
                                <th>Activ</th>
                                <th>Acțiuni</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products_list as $row): ?>
                            <tr>
                                <td><?= $row['id']; ?></td>
                                <td>
                                    <?php if (!empty($row['image'])): ?>
                                        <img src="../<?= esc($row['image']); ?>" style="height:40px;width:40px;object-fit:cover;border-radius:6px;margin-right:6px;vertical-align:middle;"> 
                                    <?php endif; ?>
                                    <?= esc($row['title']); ?>
                                </td>
                                <td><?= esc($row['category']); ?></td>
                                <td><?= number_format((float)$row['price'], 2); ?> RON</td>
                                <td><?= esc($row['status']); ?></td>
                                <td><span class="badge bg-<?= $row['is_active'] ? 'success' : 'secondary'; ?>"><?= $row['is_active'] ? 'activ' : 'inactiv'; ?></span></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewModal<?= $row['id']; ?>">Vizualizare</button>

                                    <form method="POST" onsubmit="return confirm('Sigur ștergi acest produs?');" class="d-inline ms-1">
                                        <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                        <button type="submit" name="delete_product" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modale Vizualizare -->
            <?php foreach ($products_list as $row): ?>
            <div class="modal fade" id="viewModal<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title">Detalii produs: <?= esc($row['title']); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <strong>Categorie:</strong> <?= esc($row['category']); ?><br>
                                    <strong>Brand:</strong> <?= esc($row['brand']); ?><br>
                                    <strong>Level:</strong> <?= esc($row['level']); ?><br>
                                    <strong>Status:</strong> <?= esc($row['status']); ?><br>
                                    <strong>Preț:</strong> <?= number_format((float)$row['price'],2); ?> RON<br>
                                    <strong>Activ:</strong> <span class="badge bg-<?= $row['is_active'] ? 'success' : 'secondary'; ?>"><?= $row['is_active'] ? 'activ' : 'inactiv'; ?></span><br>
                                    <strong>Badge:</strong> <?= $row['badge_recommended'] ? '<span class="badge bg-warning">Recomandat</span>' : 'Nu'; ?><br>
                                    <strong>Tutorial:</strong> <?php if ($row['tutorial_link']) echo '<a href="'.esc($row['tutorial_link']).'" target="_blank">Tutorial</a>'; ?><br>
                                </div>
                                <div class="col-md-6">
                                    <strong>Descriere:</strong><br><?= nl2br(esc($row['description'])); ?><br>
                                    <strong>Conținut:</strong><br><?= $row['content_list']; ?><br>
                                    <strong>Accent color:</strong> <span style="background:<?= esc($row['accent_color']); ?>;display:inline-block;width:32px;height:32px;border-radius:50%;border:1px solid #ccc;"></span><br>
                                    <strong>Meta:</strong><br>
                                    <?php
                                    $meta = [];
                                    if (!empty($row['meta_title'])) $meta[] = '<strong>Titlu:</strong> '.esc($row['meta_title']);
                                    if (!empty($row['meta_description'])) $meta[] = '<strong>Descriere:</strong> '.esc($row['meta_description']);
                                    if (!empty($row['meta_keywords'])) $meta[] = '<strong>Cuvinte cheie:</strong> '.esc($row['meta_keywords']);
                                    echo implode('<br>', $meta);
                                    ?>
                                </div>
                                <div class="col-12 mt-3">
                                    <strong>Imagine:</strong><br>
                                    <?php if (!empty($row['image'])): ?>
                                        <img src="../<?= esc($row['image']); ?>" alt="" style="max-height:160px; border-radius:8px;">
                                    <?php else: ?>
                                        <small class="text-muted">Fără imagine</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Închide</button>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id']; ?>" data-bs-dismiss="modal">Modifică</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Modal: Adaugă Produs -->
            <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="add_product" value="1">
                            <div class="modal-header">
                                <h5 class="modal-title">Adaugă Produs Nou</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label>Slug</label>
                                        <input type="text" name="slug" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Titlu</label>
                                        <input type="text" name="title" class="form-control" required>
                                    </div>
                                    <div class="col-12">
                                        <label>Descriere</label>
                                        <textarea name="description" class="form-control" rows="3" required></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label>Conținut (scrie fiecare element pe o linie nouă, fără HTML)</label>
                                        <textarea name="content_list" class="form-control" rows="5" placeholder="Ex: Mini lampa UV\nGel starter kit\nPile si buffer\nUlei cuticule\nPensula universala\nSuport pentru maini"></textarea>
                                        <small class="text-muted">Fiecare element pe o linie separată. Nu folosi HTML!</small>
                                    </div>
                                    <div class="col-12">
                                        <label>Meta (SEO)</label>
                                        <input type="text" name="meta" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Categorie</label>
                                        <select name="category" class="form-select">
                                            <option value="Instrumente">Instrumente</option>
                                            <option value="Consumabile">Consumabile</option>
                                            <option value="Kituri">Kituri complete</option>
                                            <option value="Accesorii">Accesorii</option>
                                            <option value="">Altă categorie...</option>
                                        </select>
                                        <input type="text" name="category_custom" class="form-control mt-2" placeholder="Subcategorie/altă categorie (opțional)">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Level</label>
                                        <input type="text" name="level" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Brand</label>
                                        <input type="text" name="brand" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Accent color</label>
                                        <div class="d-flex align-items-center">
                                            <input type="color" name="accent_color" class="form-control form-control-color me-2" value="#e91e63" onchange="document.getElementById('accentPreview').style.background=this.value;">
                                            <span id="accentPreview" style="display:inline-block;width:32px;height:32px;border-radius:50%;background:#e91e63;border:1px solid #ccc;"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Tutorial link</label>
                                        <input type="url" name="tutorial_link" class="form-control" placeholder="https://...">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Preț</label>
                                        <input type="number" name="price" class="form-control mb-1" step="0.01" required>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="price_estimated" id="price_estimated">
                                            <label class="form-check-label" for="price_estimated">Preț orientativ / valoare estimată</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Status</label>
                                        <select name="status" class="form-select">
                                            <option value="disponibil">Disponibil</option>
                                            <option value="indisponibil">Indisponibil</option>
                                            <option value="stoc_limitat">Stoc limitat</option>
                                            <option value="ascuns">Ascuns</option>
                                            <option value="revizie">În revizie</option>
                                            <option value="arhivat">Arhivat</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 d-flex align-items-center">
                                        <input class="form-check-input" type="checkbox" name="badge_recommended" id="badge_recommended">
                                        <label class="form-check-label ms-2" for="badge_recommended">Badge Recomandat</label>
                                    </div>
                                    <div class="col-12">
                                        <label>Imagine produs</label>
                                        <input type="file" name="image_upload" class="form-control" accept="image/*" onchange="previewImageUpload(this, 'previewAdd')">
                                        <div id="previewAdd" class="mt-3 text-center"></div>
                                    </div>
                                    <div class="col-12">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
                                        <label class="form-check-label ms-2" for="is_active">Produs activ</label>
                                    </div>
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

            <!-- Modale Editare -->
            <?php foreach ($products_list as $row): ?>
            <div class="modal fade" id="editModal<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="edit_product" value="1">
                            <input type="hidden" name="id" value="<?= $row['id']; ?>">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title">Editează Produs</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label>Slug</label>
                                        <input type="text" name="slug" class="form-control" value="<?= esc($row['slug']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Titlu</label>
                                        <input type="text" name="title" class="form-control" value="<?= esc($row['title']); ?>" required>
                                    </div>
                                    <div class="col-12">
                                        <label>Descriere</label>
                                        <textarea name="description" class="form-control" rows="3" required><?= esc($row['description']); ?></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label>Conținut (listă, fiecare pe rând nou)</label>
                                        <textarea name="content_list" class="form-control" rows="2"><?= esc($row['content_list']); ?></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label>Meta keywords (SEO)</label>
                                        <input type="text" name="meta_keywords" class="form-control" value="<?= isset($row['meta_keywords']) ? esc($row['meta_keywords']) : ''; ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Categorie</label>
                                        <input type="text" name="category" class="form-control" value="<?= esc($row['category']); ?>">
                                        <input type="text" name="category_custom" class="form-control mt-2" placeholder="Subcategorie/altă categorie (opțional)">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Level</label>
                                        <input type="text" name="level" class="form-control" value="<?= esc($row['level']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Brand</label>
                                        <input type="text" name="brand" class="form-control" value="<?= esc($row['brand']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Accent color</label>
                                        <input type="color" name="accent_color" class="form-control" value="<?= esc($row['accent_color']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Tutorial link</label>
                                        <input type="url" name="tutorial_link" class="form-control" value="<?= esc($row['tutorial_link']); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label>Preț</label>
                                        <input type="number" name="price" class="form-control" step="0.01" value="<?= esc($row['price']); ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label>Status</label>
                                        <select name="status" class="form-select">
                                            <option value="disponibil" <?= $row['status']=='disponibil'?'selected':''; ?>>Disponibil</option>
                                            <option value="indisponibil" <?= $row['status']=='indisponibil'?'selected':''; ?>>Indisponibil</option>
                                            <option value="stoc_limitat" <?= $row['status']=='stoc_limitat'?'selected':''; ?>>Stoc limitat</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 d-flex align-items-center">
                                        <input class="form-check-input" type="checkbox" name="badge_recommended" id="badge_recommended<?= $row['id']; ?>" <?= $row['badge_recommended'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label ms-2" for="badge_recommended<?= $row['id']; ?>">Badge Recomandat</label>
                                    </div>
                                    <div class="col-12">
                                        <label>Imagine produs (opțional)</label>
                                        <input type="file" name="image_upload" class="form-control" accept="image/*" onchange="previewImageUpload(this, 'previewEdit<?= $row['id']; ?>')">
                                        <input type="hidden" name="existing_image" value="<?= esc($row['image']); ?>">
                                        <div id="previewEdit<?= $row['id']; ?>" class="mt-3 text-center">
                                            <?php if (!empty($row['image'])): ?>
                                                <img src="../<?= esc($row['image']); ?>" style="max-height:100px;border-radius:8px;">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <input class="form-check-input" type="checkbox" name="is_active" <?= $row['is_active'] ? 'checked' : ''; ?>>
                                        <label class="form-check-label ms-2">Produs activ</label>
                                    </div>
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
            <?php endforeach; ?>
        </main>
    </div>
</div>

<script>
function previewImageUpload(input, previewId) {
    const container = document.getElementById(previewId);
    container.innerHTML = '';
    const file = input.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = e => {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxHeight = '100px';
            img.style.borderRadius = '8px';
            img.style.boxShadow = '0 0 6px rgba(0,0,0,0.2)';
            container.appendChild(img);
        };
        reader.readAsDataURL(file);
    }
}

// hide alerts after 4s
setTimeout(() => {
    document.querySelectorAll('.alert').forEach(a => a.remove());
}, 4000);
</script>

<?php include 'includes/admin_footer.php'; ?>
