<?php
require_once 'includes/admin_header.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_admin_login();
$page_title = 'Administrare Ghid Pachete';
$error_message = '';
$success_message = '';
?>
<div class="container-fluid">
    <div class="row">
        <?php include 'includes/admin_sidebar.php'; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">

// === Actualizare profiluri pachete ===
<?php
require_once 'includes/admin_header.php';
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_admin_login();
$page_title = 'Administrare Ghid Pachete';
$error_message = '';
$success_message = '';
// === Actualizare profiluri pachete ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profiles'])) {
    $profiles = $_POST['profiles'];
    // Salvează în fișier JSON sau în DB (aici: fișier local pentru simplitate)
    $json = json_encode($profiles, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if (file_put_contents('../assets/data/ghid_pachete_profiles.json', $json)) {
        $success_message = 'Profilurile au fost actualizate!';
    } else {
        $error_message = 'Eroare la salvare!';
    }
}

// === Preia profiluri existente ===
$profiles = [];
if (file_exists('../assets/data/ghid_pachete_profiles.json')) {
    $profiles = json_decode(file_get_contents('../assets/data/ghid_pachete_profiles.json'), true);
}
if (!$profiles) {
    $profiles = [
        ['profil'=>'Începător','descriere'=>'Nu ai experiență, vrei să înveți acasă sau să participi la cursuri','pachet'=>'Kit Start Manichiură'],
        ['profil'=>'Cursant','descriere'=>'Participi la workshopuri sau formare profesională','pachet'=>'Kit Cursant'],
        ['profil'=>'Avansat','descriere'=>'Ai deja experiență, vrei produse specializate','pachet'=>'Pachet Avansat – Tehnici cu Gel'],
        ['profil'=>'Creativ','descriere'=>'Vrei să explorezi designul artistic','pachet'=>'Kit Nail Art'],
        ['profil'=>'Responsabil','descriere'=>'Pui accent pe igienă și siguranță','pachet'=>'Pachet Igienă și Siguranță'],
    ];
}
?>
<div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card shadow mb-4">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h3 class="mb-0">Administrare Ghid Pachete</h3>
                        </div>
                        <div class="card-body">
                            <?php if ($success_message): ?>
                                    <div class="alert alert-success mb-3"><?= htmlspecialchars($success_message); ?></div>
                            <?php elseif ($error_message): ?>
                                    <div class="alert alert-danger mb-3"><?= htmlspecialchars($error_message); ?></div>
                            <?php endif; ?>
                            <form method="POST">
                                    <input type="hidden" name="save_profiles" value="1">
                                    <table class="table table-bordered align-middle">
                                            <thead class="table-light">
                                                    <tr>
                                                            <th>Profil</th>
                                                            <th>Descriere</th>
                                                            <th>Pachet recomandat</th>
                                                            <th></th>
                                                    </tr>
                                            </thead>
                                            <tbody>
                                                    <?php foreach ($profiles as $i => $row): ?>
                                                    <tr>
                                                            <td><input type="text" name="profiles[<?= $i ?>][profil]" class="form-control" value="<?= htmlspecialchars($row['profil']); ?>" required></td>
                                                            <td><input type="text" name="profiles[<?= $i ?>][descriere]" class="form-control" value="<?= htmlspecialchars($row['descriere']); ?>" required></td>
                                                            <td><input type="text" name="profiles[<?= $i ?>][pachet]" class="form-control" value="<?= htmlspecialchars($row['pachet']); ?>" required></td>
                                                            <td>
                                                                    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove();">Șterge</button>
                                                            </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                            </tbody>
                                    </table>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <button type="button" class="btn btn-secondary" onclick="addProfileRow();"><i class="fas fa-plus me-1"></i> Adaugă profil nou</button>
                                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Salvează modificările</button>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<?php require_once 'includes/admin_footer.php'; ?>
<script>
function addProfileRow() {
    var tbody = document.querySelector('table tbody');
    var idx = tbody.rows.length;
    var tr = document.createElement('tr');
    tr.innerHTML = `<td><input type='text' name='profiles[${idx}][profil]' class='form-control' required></td>
        <td><input type='text' name='profiles[${idx}][descriere]' class='form-control' required></td>
        <td><input type='text' name='profiles[${idx}][pachet]' class='form-control' required></td>
        <td><button type='button' class='btn btn-danger btn-sm' onclick='this.closest("tr").remove();'>Șterge</button></td>`;
    tbody.appendChild(tr);
}
</script>
<?php require_once 'includes/admin_footer.php'; ?>
