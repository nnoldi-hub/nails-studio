<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_admin_login();
$page_title = 'Raport Programări Servicii';

// Filtrare
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Query programări filtrate
$where = [];
$params = [];
$types = '';
if ($start_date) {
    $where[] = 'a.appointment_date >= ?';
    $params[] = $start_date;
    $types .= 's';
}
if ($end_date) {
    $where[] = 'a.appointment_date <= ?';
    $params[] = $end_date;
    $types .= 's';
}
if ($status) {
    $where[] = 'a.status = ?';
    $params[] = $status;
    $types .= 's';
}
$sql = 'SELECT a.*, s.name AS service_name, s.price AS service_price FROM appointments a LEFT JOIN services s ON a.service_id = s.id';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY a.appointment_date DESC, a.appointment_time DESC';
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
$appointments = $result->fetch_all(MYSQLI_ASSOC);

// Export CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=raport_programari.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Client', 'Email', 'Telefon', 'Serviciu', 'Data', 'Ora', 'Status']);
    foreach ($appointments as $appointment) {
        fputcsv($output, [
            $appointment['id'],
            $appointment['client_name'],
            $appointment['client_email'],
            $appointment['client_phone'],
            isset($appointment['service_name']) ? $appointment['service_name'] : 'N/A',
            $appointment['appointment_date'],
            $appointment['appointment_time'],
            $appointment['status']
        ]);
    }
    fclose($output);
    exit;
}

include 'includes/admin_header.php';
?>
<div class="container-fluid">
    <div class="row">
        <?php include 'includes/admin_sidebar.php'; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Raport Programări Servicii</h1>
                <div>
                    <button class="btn btn-outline-secondary" onclick="window.print()"><i class="fas fa-print me-1"></i>Tipărește</button>
                    <a href="?export=csv&amp;start_date=<?php echo $start_date; ?>&amp;end_date=<?php echo $end_date; ?>&amp;status=<?php echo $status; ?>" class="btn btn-outline-success"><i class="fas fa-file-csv me-1"></i>Export CSV</a>
                </div>
            </div>
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <label for="start_date" class="form-label">De la data</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">Până la data</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Toate</option>
                        <option value="pending" <?php if($status=='pending') echo 'selected'; ?>>În așteptare</option>
                        <option value="confirmed" <?php if($status=='confirmed') echo 'selected'; ?>>Confirmată</option>
                        <option value="completed" <?php if($status=='completed') echo 'selected'; ?>>Finalizată</option>
                        <option value="cancelled" <?php if($status=='cancelled') echo 'selected'; ?>>Anulată</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filtrează</button>
                </div>
            </form>
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Programări Filtrate</h6>
                </div>
                <div class="card-body">
                    <div id="printArea">
                        <div id="printTitle" style="display:none;text-align:center;font-size:1.5rem;font-weight:bold;margin-bottom:20px;"></div>
                        <?php if (!empty($appointments)): ?>
                        <div class="table-responsive" style="justify-content:center;display:flex;">
                            <table class="table table-bordered table-hover" style="margin:0 auto;">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Client</th>
                                        <th>Email</th>
                                        <th>Telefon</th>
                                        <th>Serviciu</th>
                                        <th>Preț</th>
                                        <th>Data</th>
                                        <th>Ora</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $total_pret = 0;
                                    foreach ($appointments as $appointment): ?>
                                    <tr>
                                        <td><?php echo $appointment['id']; ?></td>
                                        <td><?php echo htmlspecialchars($appointment['client_name']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['client_email']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['client_phone']); ?></td>
                                        <td><?php echo  htmlspecialchars($appointment['service_name']) && $appointment['service_name'] ? htmlspecialchars($appointment['service_name']) : 'N/A'; ?></td>
                                        <td>
                                            <?php 
                                            if (isset($appointment['service_price']) && $appointment['service_price'] !== null) {
                                                $total_pret += (float)$appointment['service_price'];
                                                echo number_format((float)$appointment['service_price'], 0) . ' RON';
                                            } else {
                                                echo 'N/A';
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
                                        <td><span class="badge badge-<?php echo $appointment['status']; ?>"><?php echo $appointment['status']; ?></span></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-3">
                            <strong>Total preț programări filtrate: <?php echo number_format($total_pret, 0); ?> RON</strong>
                        </div>
                            </table>
                        </div>
                        <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                            <h5>Nu există programări pentru filtrul selectat</h5>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
<style>
@media print {
    @page { size: A4 portrait; margin: 20mm; }
    body * { visibility: hidden !important; }
    #printArea, #printArea * { visibility: visible !important; }
    #printArea {
        position: fixed;
        left: 0; top: 0; width: 100vw; height: 100vh;
        background: #fff;
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .card-header, .card-body { box-shadow: none !important; }
    #printTitle { display: block !important; margin-bottom: 20px; }
    .table-responsive { display: flex !important; justify-content: center !important; }
    table { margin: 0 auto !important; }
}
</style>
<script>
document.querySelector('button[onclick*="print"]').addEventListener('click', function(e) {
    var start = document.getElementById('start_date').value;
    var end = document.getElementById('end_date').value;
    var titlu = 'Raport perioada ' + (start ? start : '-') + ' - ' + (end ? end : '-');
    var printTitle = document.getElementById('printTitle');
    printTitle.textContent = titlu;
    printTitle.style.display = 'block';
    window.print();
});
window.onafterprint = function() {
    var printTitle = document.getElementById('printTitle');
    printTitle.style.display = 'none';
};
if (window.matchMedia) {
    var mediaQueryList = window.matchMedia('print');
    mediaQueryList.addListener(function(mql) {
        if (!mql.matches) {
            var printTitle = document.getElementById('printTitle');
            printTitle.style.display = 'none';
        }
    });
}
</script>
        </main>
    </div>
</div>
<?php include 'includes/admin_footer.php'; ?>
