<?php
if (!function_exists('fetchAll')) {
    require_once $_SERVER['DOCUMENT_ROOT'] . '/sarpams/config/db.php';
}

$currentPage = basename(dirname($_SERVER['PHP_SELF']));
$currentFile = basename($_SERVER['PHP_SELF']);

function isActive($section) {
    global $currentPage;
    return ($currentPage === $section) ? 'active' : '';
}
function isFilePage($file) {
    global $currentFile;
    return ($currentFile === $file) ? 'active' : '';
}

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $pageTitle ?? 'SARPAMS' ?> | Stray Animals Rescue & Adoption</title>
<link rel="stylesheet" href="/sarpams/assets/css/style.css">
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script>document.addEventListener('DOMContentLoaded', () => lucide.createIcons());</script>
</head>
<body>

<nav class="navbar">
  <a class="navbar-brand" href="/sarpams/index.php">
    <i data-lucide="paw-print"></i> SARPAMS
  </a>
  <ul class="navbar-nav">
    <li><a href="/sarpams/index.php" class="<?= isFilePage('index.php') && $currentPage === 'sarpams' ? 'active' : '' ?>"><i data-lucide="layout-dashboard"></i> Dashboard</a></li>
    <li><a href="/sarpams/animals/index.php" class="<?= isActive('animals') ?>"><i data-lucide="dog"></i> Animals</a></li>
    <li><a href="/sarpams/rescue_requests/index.php" class="<?= isActive('rescue_requests') ?>"><i data-lucide="siren"></i> Rescues</a></li>
    <li><a href="/sarpams/adoptions/index.php" class="<?= isActive('adoptions') ?>"><i data-lucide="heart-handshake"></i> Adoptions</a></li>
    <li><a href="/sarpams/shelters/index.php" class="<?= isActive('shelters') ?>"><i data-lucide="building-2"></i> Shelters</a></li>
    <li><a href="/sarpams/medical_records/index.php" class="<?= isActive('medical_records') ?>"><i data-lucide="activity"></i> Medical</a></li>
  </ul>
</nav>

<div class="layout">
<aside class="sidebar">
  <div class="sidebar-section">Animal Care</div>
  <a href="/sarpams/animals/index.php" class="<?= isActive('animals') ?>">
    <i data-lucide="dog"></i><span class="nav-label"> Animals</span>
  </a>
  <a href="/sarpams/medical_records/index.php" class="<?= isActive('medical_records') ?>">
    <i data-lucide="clipboard-plus"></i><span class="nav-label"> Medical Records</span>
  </a>
  <a href="/sarpams/veterinarians/index.php" class="<?= isActive('veterinarians') ?>">
    <i data-lucide="stethoscope"></i><span class="nav-label"> Veterinarians</span>
  </a>

  <div class="sidebar-section">Operations</div>
  <a href="/sarpams/rescue_requests/index.php" class="<?= isActive('rescue_requests') ?>">
    <i data-lucide="siren"></i><span class="nav-label"> Rescue Requests</span>
  </a>
  <a href="/sarpams/rescuers/index.php" class="<?= isActive('rescuers') ?>">
    <i data-lucide="shield-check"></i><span class="nav-label"> Rescuers</span>
  </a>
  <a href="/sarpams/shelters/index.php" class="<?= isActive('shelters') ?>">
    <i data-lucide="building-2"></i><span class="nav-label"> Shelters</span>
  </a>
  <a href="/sarpams/cages/index.php" class="<?= isActive('cages') ?>">
    <i data-lucide="grid-3x3"></i><span class="nav-label"> Cages</span>
  </a>

  <div class="sidebar-section">Adoption</div>
  <a href="/sarpams/adoption_applicants/index.php" class="<?= isActive('adoption_applicants') ?>">
    <i data-lucide="users"></i><span class="nav-label"> Applicants</span>
  </a>
  <a href="/sarpams/adoptions/index.php" class="<?= isActive('adoptions') ?>">
    <i data-lucide="heart-handshake"></i><span class="nav-label"> Adoptions</span>
  </a>
  <a href="/sarpams/foster_families/index.php" class="<?= isActive('foster_families') ?>">
    <i data-lucide="house-heart"></i><span class="nav-label"> Foster Families</span>
  </a>
</aside>

<main class="main">
<?php if ($flash): ?>
  <div class="alert alert-<?= $flash['type'] ?>">
    <?php if ($flash['type'] === 'success'): ?>
      <i data-lucide="check-circle-2"></i>
    <?php elseif ($flash['type'] === 'danger'): ?>
      <i data-lucide="x-circle"></i>
    <?php else: ?>
      <i data-lucide="info"></i>
    <?php endif; ?>
    <?= htmlspecialchars($flash['msg']) ?>
  </div>
<?php endif; ?>
