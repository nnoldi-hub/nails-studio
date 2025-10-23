<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_admin_login();
$page_title = 'Rapoarte';
include 'includes/admin_header.php';
?>
<div class="container-fluid">
    <div class="row">
        <?php include 'includes/admin_sidebar.php'; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Rapoarte</h1>
            </div>
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <i class="fas fa-calendar-alt me-2"></i>Raport Programări Servicii
                        </div>
                        <div class="card-body">
                            <a href="report_appointments.php" class="btn btn-outline-primary">Vezi raport programări</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card shadow">
                        <div class="card-header bg-info text-white">
                            <i class="fas fa-graduation-cap me-2"></i>Raport Programări Cursuri
                        </div>
                        <div class="card-body">
                            <a href="report_coaching.php" class="btn btn-outline-info">Vezi raport cursuri</a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<?php include 'includes/admin_footer.php'; ?>
