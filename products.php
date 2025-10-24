<?php
ini_set('session.cookie_path', '/');
session_name('shop_session');
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Verifică dacă magazinul este activ
define('SHOP_ENABLED', get_shop_enabled());
$page_title = 'Magazin';
$page_description = 'Descoperă produsele noastre pentru îngrijirea unghiilor.';

if (!SHOP_ENABLED) {
    include 'includes/header.php';
    echo '<div class="container py-5"><div class="alert alert-warning">Magazinul nu este activ momentan.</div></div>';
    include 'includes/footer.php';
    exit;
}

// Adaugă produs în coș
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }
    // Redirect pentru a evita re-trimiterea POST
    header('Location: products.php?added=1');
    exit;
}

// Preia filtre
$categories = $levels = $brands = [];
$min_price = $max_price = 0;

$res = $conn->query("SELECT DISTINCT category, level, brand, price FROM products WHERE is_active = 1");
while ($r = $res->fetch_assoc()) {
    if ($r['category']) $categories[] = $r['category'];
    if ($r['level']) $levels[] = $r['level'];
    if ($r['brand']) $brands[] = $r['brand'];
    if ($min_price == 0 || $r['price'] < $min_price) $min_price = $r['price'];
    if ($r['price'] > $max_price) $max_price = $r['price'];
}
$categories = array_unique($categories);
$levels = array_unique($levels);
$brands = array_unique($brands);

// Filtre din GET
$filter_category = $_GET['category'] ?? '';
$filter_level = $_GET['level'] ?? '';
$filter_brand = $_GET['brand'] ?? '';
$filter_badge = $_GET['badge'] ?? '';
$filter_min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : $min_price;
$filter_max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : $max_price;

// Preluare produse
$sql = "SELECT * FROM products WHERE is_active = 1";
if ($filter_category) $sql .= " AND category='".$conn->real_escape_string($filter_category)."'";
if ($filter_level) $sql .= " AND level='".$conn->real_escape_string($filter_level)."'";
if ($filter_brand) $sql .= " AND brand='".$conn->real_escape_string($filter_brand)."'";
if ($filter_badge === '1') $sql .= " AND badge_recommended=1";
if ($filter_min_price > 0) $sql .= " AND price>=".$filter_min_price;
if ($filter_max_price > 0) $sql .= " AND price<=".$filter_max_price;
$sql .= " ORDER BY created_at DESC";
$result = $conn->query($sql);

include 'includes/header.php';
?>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">Magazin</h1>
        <p class="text-muted">Produse profesionale pentru unghii</p>
    </div>

    <!-- Mesaj succes adăugare -->
    <?php if (isset($_GET['added'])): ?>
        <div class="alert alert-success text-center">Produsul a fost adăugat în coș!</div>
    <?php endif; ?>

    <!-- Listă de selecție produse pentru estimare cost -->
    <form id="estimateForm" class="mb-4">
        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 shadow-sm border-0" style="border-top: 6px solid <?= htmlspecialchars($row['accent_color'] ?? '#e91e63'); ?>;">
                            <?php if (!empty($row['image'])): ?>
                                <img src="<?= htmlspecialchars($row['image']); ?>" class="card-img-top" alt="<?= htmlspecialchars($row['title']); ?>" style="height:200px;object-fit:cover;">
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge <?= strtolower($row['level']) === 'începător' ? 'beginner' : (strtolower($row['level']) === 'cursant' ? 'student' : (strtolower($row['level']) === 'avansat' ? 'advanced' : (strtolower($row['level']) === 'creativ' ? 'creative' : (strtolower($row['level']) === 'igienă' ? 'hygiene' : 'bg-secondary')))); ?> text-uppercase">
                                        <?= htmlspecialchars($row['level']); ?>
                                    </span>
                                    <?php if ($row['badge_recommended']): ?>
                                        <span class="badge bg-warning text-dark"><i class="fas fa-star"></i> Recomandat</span>
                                    <?php endif; ?>
                                </div>
                                <h5 class="card-title mb-1 fw-bold" style="color:<?= htmlspecialchars($row['accent_color'] ?? '#e91e63'); ?>;">
                                    <?= htmlspecialchars($row['title']); ?>
                                </h5>
                                <p class="card-text flex-grow-1 mb-2"><?= htmlspecialchars(mb_substr(strip_tags($row['description']),0,120)) . '...'; ?></p>
                                <div class="fw-bold mb-2" style="color:<?= htmlspecialchars($row['accent_color'] ?? '#e91e63'); ?>;font-size:1.2rem;">
                                    <?= number_format($row['price'],2); ?> RON
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input product-check" type="checkbox" value="<?= $row['id']; ?>" data-title="<?= htmlspecialchars($row['title']); ?>" data-price="<?= $row['price']; ?>" id="prod<?= $row['id']; ?>">
                                    <label class="form-check-label" for="prod<?= $row['id']; ?>">Selectează pentru estimare</label>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h4>Niciun produs disponibil momentan</h4>
                    <p class="text-muted">Revin-o curând pentru noutăți!</p>
                </div>
            <?php endif; ?>
        </div>
        <div class="card mt-4 p-4 shadow" id="estimateSummary" style="max-width:700px;margin:auto;display:none;">
            <h4 class="mb-3">Lista ta de start &mdash; Estimare cost</h4>
            <div id="estimateList"></div>
            <hr>
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold">Total estimat:</span>
                <span class="fw-bold" id="estimateTotal" style="font-size:1.3rem;color:#e91e63;">0.00 RON</span>
            </div>
            <button type="button" class="btn btn-outline-secondary mt-3" onclick="window.print();"><i class="fas fa-print me-1"></i> Printează pe A4</button>
        </div>
    </form>
    <script>
    // JS pentru estimare cost
    const checks = document.querySelectorAll('.product-check');
    const summary = document.getElementById('estimateSummary');
    const list = document.getElementById('estimateList');
    const total = document.getElementById('estimateTotal');
    function updateEstimate() {
        let items = [];
        let sum = 0;
        document.querySelectorAll('.product-check:checked').forEach(chk => {
            items.push({title: chk.getAttribute('data-title'), price: parseFloat(chk.getAttribute('data-price'))});
            sum += parseFloat(chk.getAttribute('data-price'));
        });
        if (items.length) {
            summary.style.display = 'block';
            list.innerHTML = '<ol>' + items.map(i => `<li><span class="fw-bold">${i.title}</span> <span class="float-end">${i.price.toFixed(2)} RON</span></li>`).join('') + '</ol>';
            total.textContent = sum.toFixed(2) + ' RON';
        } else {
            summary.style.display = 'none';
        }
    }
    checks.forEach(chk => chk.addEventListener('change', updateEstimate));
    </script>

    <!-- Filtre produse -->
    <form method="GET" class="row g-2 mb-4 align-items-end bg-light p-3 rounded shadow-sm">
        <div class="col-md-2">
            <label class="form-label">Categorie</label>
            <select name="category" class="form-select">
                <option value="">Toate</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= htmlspecialchars($cat) ?>" <?= $filter_category==$cat?'selected':''; ?>><?= htmlspecialchars($cat) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Nivel</label>
            <select name="level" class="form-select">
                <option value="">Toate</option>
                <?php foreach ($levels as $lvl): ?>
                    <option value="<?= htmlspecialchars($lvl) ?>" <?= $filter_level==$lvl?'selected':''; ?>><?= htmlspecialchars($lvl) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Brand</label>
            <select name="brand" class="form-select">
                <option value="">Toate</option>
                <?php foreach ($brands as $br): ?>
                    <option value="<?= htmlspecialchars($br) ?>" <?= $filter_brand==$br?'selected':''; ?>><?= htmlspecialchars($br) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Badge</label>
            <select name="badge" class="form-select">
                <option value="">Toate</option>
                <option value="1" <?= $filter_badge==='1'?'selected':''; ?>>Recomandat</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Preț</label>
            <div class="d-flex align-items-center">
                <input type="number" name="min_price" class="form-control me-1" value="<?= htmlspecialchars($filter_min_price) ?>" min="<?= $min_price ?>" max="<?= $max_price ?>" style="width:70px;">
                <span class="mx-1">-</span>
                <input type="number" name="max_price" class="form-control" value="<?= htmlspecialchars($filter_max_price) ?>" min="<?= $min_price ?>" max="<?= $max_price ?>" style="width:70px;">
            </div>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-1"></i> Filtrează</button>
        </div>
    </form>

    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm border-0" style="border-top: 6px solid <?= htmlspecialchars($row['accent_color'] ?? '#e91e63'); ?>;">
                        <?php if (!empty($row['image'])): ?>
                            <img src="<?= htmlspecialchars($row['image']); ?>" class="card-img-top" alt="<?= htmlspecialchars($row['title']); ?>" style="height:200px;object-fit:cover;">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge <?= strtolower($row['level']) === 'începător' ? 'beginner' : (strtolower($row['level']) === 'cursant' ? 'student' : (strtolower($row['level']) === 'avansat' ? 'advanced' : (strtolower($row['level']) === 'creativ' ? 'creative' : (strtolower($row['level']) === 'igienă' ? 'hygiene' : 'bg-secondary')))); ?> text-uppercase">
                                    <?= htmlspecialchars($row['level']); ?>
                                </span>
                                <?php if ($row['badge_recommended']): ?>
                                    <span class="badge bg-warning text-dark"><i class="fas fa-star"></i> Recomandat</span>
                                <?php endif; ?>
                            </div>
                            <h5 class="card-title mb-1 fw-bold" style="color:<?= htmlspecialchars($row['accent_color'] ?? '#e91e63'); ?>;">
                                <?= htmlspecialchars($row['title']); ?>
                            </h5>
                            <p class="card-text flex-grow-1 mb-2"><?= htmlspecialchars(mb_substr(strip_tags($row['description']),0,120)) . '...'; ?></p>
                            <div class="fw-bold mb-2" style="color:<?= htmlspecialchars($row['accent_color'] ?? '#e91e63'); ?>;font-size:1.2rem;">
                                <?= number_format($row['price'],2); ?> RON
                            </div>
                            <form method="POST" class="mt-auto d-flex align-items-center">
                                <input type="hidden" name="product_id" value="<?= $row['id']; ?>">
                                <input type="number" name="quantity" value="1" min="1" class="form-control me-2" style="width:80px;">
                                <button type="submit" name="add_to_cart" class="btn btn-success"><i class="fas fa-cart-plus me-1"></i> Adaugă în coș</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h4>Niciun produs disponibil momentan</h4>
                <p class="text-muted">Revin-o curând pentru noutăți!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
