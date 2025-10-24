<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';
$page_title = 'Ghid de alegere pachet';
$page_description = 'Alege pachetul potrivit pentru tine';
include 'includes/header.php';
?>
<div class="container py-5">
  <div class="mb-5 text-center">
    <h1 class="fw-bold">Alege pachetul potrivit pentru tine</h1>
    <p class="lead">Fie că ești la început de drum sau vrei să-ți perfecționezi tehnica, avem recomandări clare pentru fiecare nivel.</p>
  </div>
  <div class="row mb-4">
    <div class="col-12">
      <h3 class="mb-3">Profiluri de utilizator</h3>
      <div class="table-responsive">
        <table class="table table-bordered align-middle">
          <thead class="table-light">
            <tr>
              <th>Profil</th>
              <th>Descriere</th>
              <th>Pachet recomandat</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $profiles = [];
            if (file_exists('assets/data/ghid_pachete_profiles.json')) {
                $profiles = json_decode(file_get_contents('assets/data/ghid_pachete_profiles.json'), true);
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
            foreach ($profiles as $row):
                $badge_class = strtolower($row['profil']) === 'începător' ? 'beginner' : (strtolower($row['profil']) === 'cursant' ? 'student' : (strtolower($row['profil']) === 'avansat' ? 'advanced' : (strtolower($row['profil']) === 'creativ' ? 'creative' : (strtolower($row['profil']) === 'responsabil' ? 'hygiene' : 'bg-secondary'))));
            ?>
            <tr>
              <td><span class="badge <?= $badge_class ?>"><?= htmlspecialchars($row['profil']); ?></span></td>
              <td><?= htmlspecialchars($row['descriere']); ?></td>
              <td><strong><?= htmlspecialchars($row['pachet']); ?></strong></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="row mb-4">
    <div class="col-md-6 text-center mb-3 mb-md-0">
      <a href="products.php" class="btn btn-lg btn-primary"><i class="fas fa-box"></i> Vezi toate pachetele</a>
    </div>
    <div class="col-md-6 text-center">
      <a href="products.php" class="btn btn-lg btn-outline-secondary"><i class="fas fa-th"></i> Descoperă produsele individuale</a>
    </div>
  </div>
  <div class="row mb-5">
    <div class="col-12">
      <h3 class="mb-3">Testimoniale de la traineri</h3>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="testimonial card p-3 h-100">
            <img src="assets/images/trainer1.jpg" alt="Trainer" class="rounded-circle mb-3" style="width:64px;height:64px;object-fit:cover;">
            <blockquote class="mb-2">„Kitul de start este exact ce recomand cursanților mei. Simplu, complet și eficient.”</blockquote>
            <p class="mb-0"><strong>Andreea M., Trainer Nail Academy</strong></p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="testimonial card p-3 h-100">
            <img src="assets/images/trainer2.jpg" alt="Trainer" class="rounded-circle mb-3" style="width:64px;height:64px;object-fit:cover;">
            <blockquote class="mb-2">„Pachetele EduNails sunt gândite pentru fiecare nivel. Recomand cu încredere!”</blockquote>
            <p class="mb-0"><strong>Maria D., Formator EduNails</strong></p>
          </div>
        </div>
        <div class="col-md-4">
          <div class="testimonial card p-3 h-100">
            <img src="assets/images/trainer3.jpg" alt="Trainer" class="rounded-circle mb-3" style="width:64px;height:64px;object-fit:cover;">
            <blockquote class="mb-2">„Kit Nail Art e alegerea perfectă pentru cei creativi!”</blockquote>
            <p class="mb-0"><strong>Ioana P., Nail Art Specialist</strong></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<style>
.badge.beginner { background-color: #E91E63; color: #fff; }
.badge.student { background-color: #03A9F4; color: #fff; }
.badge.advanced { background-color: #9C27B0; color: #fff; }
.badge.creative { background-color: #FF9800; color: #fff; }
.badge.hygiene { background-color: #4CAF50; color: #fff; }
.testimonial img { border: 3px solid #eee; }
.testimonial blockquote { font-size: 1.1rem; font-style: italic; color: #555; }
</style>
<?php include 'includes/footer.php'; ?>