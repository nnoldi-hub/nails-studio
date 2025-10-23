<?php
// Pornire sesiune și conexiune la baza de date
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Verificare login admin
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}


// Adăugare serviciu nou
if (isset($_POST['add_service'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $image_name = '';

    // Directorul unde se salvează pozele
    $upload_dir = __DIR__ . '/../assets/images/services/';

    if (!empty($_FILES['image']['name'])) {
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['image']['name']);
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file_tmp, $file_path)) {
            $image_name = $file_name;
        }
    }

    $stmt = $conn->prepare("INSERT INTO services (name, description, price, image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $name, $description, $price, $image_name);
    $stmt->execute();
    $stmt->close();

    header("Location: services.php?success=1");
    exit;
}

// Actualizare serviciu
if (isset($_POST['edit_service'])) {
    $id = (int)$_POST['service_id'];
    $name = trim($_POST['edit_name']);
    $description = trim($_POST['edit_description']);
    $price = trim($_POST['edit_price']);
    $image_name = $_POST['current_image'];

    $upload_dir = __DIR__ . '/../assets/images/services/';
    if (!empty($_FILES['edit_image']['name'])) {
        $file_tmp = $_FILES['edit_image']['tmp_name'];
        $file_name = time() . '_' . basename($_FILES['edit_image']['name']);
        $file_path = $upload_dir . $file_name;
        if (move_uploaded_file($file_tmp, $file_path)) {
            // Șterge imaginea veche dacă există
            if (!empty($image_name) && file_exists($upload_dir . $image_name)) {
                unlink($upload_dir . $image_name);
            }
            $image_name = $file_name;
        }
    }

    $stmt = $conn->prepare("UPDATE services SET name=?, description=?, price=?, image=? WHERE id=?");
    $stmt->bind_param("ssdsi", $name, $description, $price, $image_name, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: services.php?updated=1");
    exit;
}

// Ștergere serviciu
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // Ștergere imagine din folder
    $stmt = $conn->prepare("SELECT image FROM services WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($image);
    $stmt->fetch();
    $stmt->close();

    if (!empty($image) && file_exists(__DIR__ . '/../assets/images/services/' . $image)) {
        unlink(__DIR__ . '/../assets/images/services/' . $image);
    }

    // Ștergere înregistrare din baza de date
    $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: services.php?deleted=1");
    exit;
}

// Obținerea tuturor serviciilor
// Obținerea tuturor serviciilor
$services = $conn->query("SELECT * FROM services ORDER BY id DESC");

include 'includes/admin_header.php';
?>


<div class="container-fluid">
    <div class="row">
        <?php include 'includes/admin_sidebar.php'; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gestionare Servicii</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                            <i class="fas fa-sync-alt me-1"></i>Actualizează
                        </button>
                    </div>
                </div>
            </div>

            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>Serviciu adăugat cu succes!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif (isset($_GET['deleted'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-trash-alt me-2"></i>Serviciu șters cu succes!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif (isset($_GET['updated'])): ?>
                <div class="alert alert-info alert-dismissible fade show">
                    <i class="fas fa-edit me-2"></i>Serviciu actualizat cu succes!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Formular adăugare serviciu -->
            <div class="card mb-4 shadow">
                <div class="card-header bg-primary text-white">Adaugă Serviciu Nou</div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label>Nume</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label>Preț (RON)</label>
                                <input type="number" name="price" step="0.01" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label>Imagine</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Descriere</label>
                            <textarea name="description" class="form-control" rows="3" required></textarea>
                        </div>
                        <button type="submit" name="add_service" class="btn btn-success">Adaugă Serviciu</button>
                    </form>
                </div>
            </div>

            <!-- Card cu tabel servicii -->
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Lista Serviciilor</h6>
                </div>
                <div class="card-body">
                    <?php if ($services->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Imagine</th>
                                    <th>Nume</th>
                                    <th>Descriere</th>
                                    <th>Preț</th>
                                    <th>Acțiuni</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($service = $services->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $service['id']; ?></td>
                                    <td>
                                        <?php 
                                        $imgPath = '../assets/images/services/' . $service['image'];
                                        if (!empty($service['image']) && file_exists(__DIR__ . '/../assets/images/services/' . $service['image'])) {
                                            $thumb = $imgPath;
                                        } else {
                                            $thumb = '../assets/images/default-service.jpg';
                                        }
                                        ?>
                                        <img src="<?php echo $thumb; ?>" style="width:40px;height:40px;object-fit:cover;border-radius:4px;">
                                    </td>
                                    <td><?php echo htmlspecialchars($service['name']); ?></td>
                                    <td><?php echo htmlspecialchars($service['description']); ?></td>
                                    <td><span class="badge bg-success"><?php echo number_format($service['price'], 2); ?> RON</span></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-info" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#detailsModal"
                                                    data-service='<?php echo json_encode($service); ?>'>
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="services.php?delete=<?php echo $service['id']; ?>" 
                                               onclick="return confirm('Sigur doriți să ștergeți acest serviciu?')" 
                                               class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash-alt"></i>
                                            </a>
                                        </div>
                                    </td>
<!-- ...existing code... -->
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-spa fa-3x text-muted mb-3"></i>
                        <h5>Nu există servicii</h5>
                        <p class="text-muted">Serviciile vor apărea aici după adăugare.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </main>
    </div>
</div>

<!-- Modal Detalii/Modificare Serviciu -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalTitle">Detalii Serviciu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="serviceDetails">
                <!-- Detalii sau formular populate din JS -->
            </div>
            <div class="modal-footer" id="detailsModalFooter">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Închide</button>
                <button type="button" class="btn btn-primary" id="editServiceBtn">Modifică</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Modal detalii/modificare serviciu
    let currentService = null;
    const detailsModal = document.getElementById('detailsModal');
    const serviceDetails = document.getElementById('serviceDetails');
    const detailsModalTitle = document.getElementById('detailsModalTitle');
    const detailsModalFooter = document.getElementById('detailsModalFooter');
    const editServiceBtn = document.getElementById('editServiceBtn');

    detailsModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        currentService = JSON.parse(button.getAttribute('data-service'));
        showServiceDetails(currentService);
    });

    function showServiceDetails(service) {
        detailsModalTitle.textContent = 'Detalii Serviciu';
        editServiceBtn.style.display = 'inline-block';
        let imgHtml = '';
        if (service.image) {
            imgHtml = `<img src='../assets/images/services/${service.image}' style='width:80px;height:80px;object-fit:cover;border-radius:8px;' class='mb-3'>`;
        } else {
            imgHtml = `<img src='../assets/images/default-service.jpg' style='width:80px;height:80px;object-fit:cover;border-radius:8px;' class='mb-3'>`;
        }
        const detailsContent = `
            <div class='row'>
                <div class='col-md-4 text-center'>
                    ${imgHtml}
                </div>
                <div class='col-md-8'>
                    <h6>Nume: <span class='fw-bold'>${service.name}</span></h6>
                    <p><strong>Descriere:</strong> ${service.description}</p>
                    <p><strong>Preț:</strong> <span class='badge bg-success'>${parseFloat(service.price).toFixed(2)} RON</span></p>
                </div>
            </div>
        `;
        serviceDetails.innerHTML = detailsContent;
    }

    editServiceBtn.addEventListener('click', function() {
        showEditForm(currentService);
    });

    function showEditForm(service) {
        detailsModalTitle.textContent = 'Modifică Serviciu';
        editServiceBtn.style.display = 'none';
        const formContent = `
            <form method='POST' enctype='multipart/form-data' id='modalEditServiceForm'>
                <input type='hidden' name='service_id' value='${service.id}'>
                <input type='hidden' name='current_image' value='${service.image || ''}'>
                <div class='row mb-3'>
                    <div class='col-12 text-center'>
                        <img src='../assets/images/services/${service.image || 'default-service.jpg'}' style='width:80px;height:80px;object-fit:cover;border-radius:8px;' class='mb-2'>
                    </div>
                </div>
                <div class='mb-3'>
                    <label class='form-label'>Nume</label>
                    <input type='text' name='edit_name' class='form-control' value='${service.name.replace(/'/g, "&#39;")}' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label'>Preț (RON)</label>
                    <input type='number' name='edit_price' step='0.01' class='form-control' value='${parseFloat(service.price).toFixed(2)}' required>
                </div>
                <div class='mb-3'>
                    <label class='form-label'>Imagine (dacă vrei să schimbi)</label>
                    <input type='file' name='edit_image' class='form-control' accept='image/*'>
                </div>
                <div class='mb-3'>
                    <label class='form-label'>Descriere</label>
                    <textarea name='edit_description' class='form-control' rows='3' required>${service.description.replace(/'/g, "&#39;")}</textarea>
                </div>
                <div class='d-flex justify-content-end gap-2'>
                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Anulează</button>
                    <button type='submit' name='edit_service' class='btn btn-success'>Salvează modificările</button>
                </div>
            </form>
        `;
        serviceDetails.innerHTML = formContent;
    }

    // Modal editare serviciu
    const editModal = document.getElementById('editModal');
    let lastServiceId = null;
    editModal.addEventListener('show.bs.modal', function (event) {
        // Fix: populate modal only once per open
        const button = event.relatedTarget || event.target;
        if (!button || !button.hasAttribute('data-service')) return;
        const service = JSON.parse(button.getAttribute('data-service'));
        if (lastServiceId === service.id) return; // already populated
        lastServiceId = service.id;
        let imgHtml = '';
        if (service.image) {
            imgHtml = `<img src='../assets/images/services/${service.image}' style='width:80px;height:80px;object-fit:cover;border-radius:8px;display:block;margin:auto;' class='mb-2'>`;
        } else {
            imgHtml = `<img src='../assets/images/default-service.jpg' style='width:80px;height:80px;object-fit:cover;border-radius:8px;display:block;margin:auto;' class='mb-2'>`;
        }
        const formContent = `
            <input type='hidden' name='service_id' value='${service.id}'>
            <input type='hidden' name='current_image' value='${service.image || ''}'>
            <div class='row mb-3'>
                <div class='col-12 text-center' style='min-height:90px;'>${imgHtml}</div>
            </div>
            <div class='mb-3'>
                <label class='form-label'>Nume</label>
                <input type='text' name='edit_name' class='form-control' value='${service.name.replace(/'/g, "&#39;")}' required>
            </div>
            <div class='mb-3'>
                <label class='form-label'>Preț (RON)</label>
                <input type='number' name='edit_price' step='0.01' class='form-control' value='${parseFloat(service.price).toFixed(2)}' required>
            </div>
            <div class='mb-3'>
                <label class='form-label'>Imagine (dacă vrei să schimbi)</label>
                <input type='file' name='edit_image' class='form-control' accept='image/*'>
            </div>
            <div class='mb-3'>
                <label class='form-label'>Descriere</label>
                <textarea name='edit_description' class='form-control' rows='3' required>${service.description.replace(/'/g, "&#39;")}</textarea>
            </div>
        `;
        document.getElementById('editServiceBody').innerHTML = formContent;
    });
    editModal.addEventListener('hidden.bs.modal', function () {
        lastServiceId = null;
        document.getElementById('editServiceBody').innerHTML = '';
    });
});
</script>

<?php include 'includes/admin_footer.php'; ?>
