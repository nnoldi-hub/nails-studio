<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

require_admin_login();

$page_title = 'Admin Dashboard';

// Get statistics
$total_appointments = count(get_all_appointments());
$pending_appointments = count(get_all_appointments('pending'));
$services_count = count(get_all_services(false));
$gallery_count = count(get_gallery_items());

include 'includes/admin_header.php';
?>

<div class="container-fluid">
    <div class="row">
        <?php include 'includes/admin_sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <a href="appointments.php" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-calendar-alt me-1"></i>Programări
                        </a>
                        <a href="services.php" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-concierge-bell me-1"></i>Servicii
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Programări
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $total_appointments; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Programări în Așteptare
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $pending_appointments; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Servicii Active
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $services_count; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-concierge-bell fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Imagini Galerie
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $gallery_count; ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-images fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Appointments -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Programări Recente</h6>
                        </div>
                        <div class="card-body">
                            <?php 
                            $recent_appointments = array_slice(get_all_appointments(), 0, 5);
                            if (!empty($recent_appointments)): 
                            ?>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Client</th>
                                            <th>Serviciu</th>
                                            <th>Data</th>
                                            <th>Ora</th>
                                            <th>Status</th>
                                            <th>Acțiuni</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_appointments as $appointment): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($appointment['client_name']); ?></td>
                                            <td><?php echo htmlspecialchars($appointment['service_name']); ?></td>
                                            <td><?php echo format_date($appointment['appointment_date']); ?></td>
                                            <td><?php echo format_time($appointment['appointment_time']); ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo $appointment['status']; ?>">
                                                    <?php echo ucfirst($appointment['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="appointments.php?id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center">
                                <a href="appointments.php" class="btn btn-primary">Vezi Toate Programările</a>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-calendar fa-3x text-muted mb-3"></i>
                                <h5>Nu există programări încă</h5>
                                <p class="text-muted">Programările vor apărea aici când clienții se vor înscrie.</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Acțiuni Rapide</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <a href="appointments.php" class="btn btn-outline-primary btn-block">
                                        <i class="fas fa-calendar-plus fa-2x mb-2"></i><br>
                                        Gestionează Programări
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="services.php" class="btn btn-outline-success btn-block">
                                        <i class="fas fa-plus-circle fa-2x mb-2"></i><br>
                                        Adaugă Serviciu
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="gallery.php" class="btn btn-outline-info btn-block">
                                        <i class="fas fa-image fa-2x mb-2"></i><br>
                                        Gestionează Galeria
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="messages.php" class="btn btn-outline-warning btn-block">
                                        <i class="fas fa-envelope fa-2x mb-2"></i><br>
                                        Mesaje Contact
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'includes/admin_footer.php'; ?>
