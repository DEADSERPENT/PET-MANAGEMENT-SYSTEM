<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/config/db.php';

$stats = [
    'animals'       => fetchOne("SELECT COUNT(*) AS n FROM ANIMAL")['n'],
    'healthy'       => fetchOne("SELECT COUNT(*) AS n FROM ANIMAL WHERE health_status='Healthy'")['n'],
    'rescues_open'  => fetchOne("SELECT COUNT(*) AS n FROM RESCUE_REQUEST WHERE status='Open'")['n'],
    'adoptions'     => fetchOne("SELECT COUNT(*) AS n FROM ADOPTION WHERE status='Completed'")['n'],
    'pending_adopt' => fetchOne("SELECT COUNT(*) AS n FROM ADOPTION WHERE status='Pending'")['n'],
    'shelters'      => fetchOne("SELECT COUNT(*) AS n FROM SHELTER")['n'],
    'rescuers'      => fetchOne("SELECT COUNT(*) AS n FROM RESCUER WHERE is_available=1")['n'],
    'foster'        => fetchOne("SELECT COUNT(*) AS n FROM FOSTER_PLACEMENT WHERE actual_end IS NULL")['n'],
];

$recentAnimals    = fetchAll("SELECT animal_id,name,species,health_status,intake_date FROM ANIMAL ORDER BY intake_date DESC LIMIT 5");
$recentRescues    = fetchAll("SELECT request_id,citizen_name,location_address,status,report_date FROM RESCUE_REQUEST ORDER BY report_date DESC LIMIT 5");
$shelterOccupancy = fetchAll("SELECT s.shelter_name, s.capacity, COUNT(c.cage_id) AS total_cages, SUM(c.is_occupied) AS occupied FROM SHELTER s LEFT JOIN CAGE c ON s.shelter_id=c.shelter_id GROUP BY s.shelter_id LIMIT 4");

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div>
    <div class="page-title"><i data-lucide="layout-dashboard"></i> Dashboard</div>
    <div class="page-subtitle">Welcome to SARPAMS — Stray Animals Rescue &amp; Pet Adoption Management System</div>
  </div>
  <a href="/sarpams/rescue_requests/create.php" class="btn btn-primary">
    <i data-lucide="plus"></i> New Rescue Request
  </a>
</div>

<!-- STAT CARDS -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon navy"><i data-lucide="dog"></i></div>
    <div class="stat-info"><h3><?= $stats['animals'] ?></h3><p>Total Animals</p></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon green"><i data-lucide="heart-pulse"></i></div>
    <div class="stat-info"><h3><?= $stats['healthy'] ?></h3><p>Healthy Animals</p></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon orange"><i data-lucide="siren"></i></div>
    <div class="stat-info"><h3><?= $stats['rescues_open'] ?></h3><p>Open Rescues</p></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon sky"><i data-lucide="heart-handshake"></i></div>
    <div class="stat-info"><h3><?= $stats['adoptions'] ?></h3><p>Completed Adoptions</p></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon purple"><i data-lucide="clock"></i></div>
    <div class="stat-info"><h3><?= $stats['pending_adopt'] ?></h3><p>Pending Adoptions</p></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon navy"><i data-lucide="building-2"></i></div>
    <div class="stat-info"><h3><?= $stats['shelters'] ?></h3><p>Shelters</p></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon green"><i data-lucide="shield-check"></i></div>
    <div class="stat-info"><h3><?= $stats['rescuers'] ?></h3><p>Available Rescuers</p></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon orange"><i data-lucide="house-heart"></i></div>
    <div class="stat-info"><h3><?= $stats['foster'] ?></h3><p>Active Fosters</p></div>
  </div>
</div>

<!-- TWO-COLUMN GRID -->
<div class="two-col-grid">

  <!-- Recent Animals -->
  <div class="card">
    <div class="card-header"><i data-lucide="paw-print"></i> Recently Rescued Animals</div>
    <div class="card-body p-0">
      <table>
        <thead>
          <tr><th>Name</th><th>Species</th><th>Status</th><th>Intake</th></tr>
        </thead>
        <tbody>
          <?php foreach ($recentAnimals as $a):
            $s = strtolower(str_replace(' ', '', $a['health_status']));
            $cls = ['healthy'=>'healthy','undertreatment'=>'treatment','adopted'=>'adopted','critical'=>'critical'][$s] ?? 'unknown';
          ?>
          <tr>
            <td><a href="/sarpams/animals/view.php?id=<?= $a['animal_id'] ?>" class="animal-link"><?= htmlspecialchars($a['name']) ?></a></td>
            <td><?= htmlspecialchars($a['species']) ?></td>
            <td><span class="badge badge-<?= $cls ?>"><?= htmlspecialchars($a['health_status']) ?></span></td>
            <td class="text-muted font-mono"><?= $a['intake_date'] ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Recent Rescue Requests -->
  <div class="card">
    <div class="card-header"><i data-lucide="siren"></i> Recent Rescue Requests</div>
    <div class="card-body p-0">
      <table>
        <thead>
          <tr><th>Citizen</th><th>Location</th><th>Status</th><th>Date</th></tr>
        </thead>
        <tbody>
          <?php foreach ($recentRescues as $r): ?>
          <tr>
            <td style="font-weight:600"><?= htmlspecialchars($r['citizen_name']) ?></td>
            <td class="text-muted" style="max-width:130px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:.8rem">
              <?= htmlspecialchars($r['location_address']) ?>
            </td>
            <td><span class="badge badge-<?= strtolower($r['status']) ?>"><?= $r['status'] ?></span></td>
            <td class="text-muted font-mono"><?= $r['report_date'] ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<!-- Shelter Occupancy -->
<div class="card mt-2">
  <div class="card-header"><i data-lucide="building-2"></i> Shelter Occupancy Overview</div>
  <div class="card-body">
    <div class="shelter-grid">
      <?php foreach ($shelterOccupancy as $sh):
        $pct   = $sh['total_cages'] > 0 ? round(($sh['occupied'] / $sh['total_cages']) * 100) : 0;
        $color = $pct > 80 ? 'var(--danger)' : ($pct > 60 ? 'var(--warning)' : 'var(--success)');
      ?>
      <div>
        <div class="shelter-stat-name"><?= htmlspecialchars($sh['shelter_name']) ?></div>
        <div class="shelter-stat-sub"><?= (int)$sh['occupied'] ?> of <?= (int)$sh['total_cages'] ?> cages occupied</div>
        <div class="progress-bar">
          <div class="progress-fill" style="width:<?= $pct ?>%;background:linear-gradient(90deg,<?= $color ?>,<?= $color ?>dd)"></div>
        </div>
        <div class="shelter-stat-pct"><?= $pct ?>% capacity</div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

