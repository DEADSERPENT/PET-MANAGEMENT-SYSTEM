<?php
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    flash('danger', 'Invalid veterinarian ID.');
    redirect('/sarpams/veterinarians/index.php');
}

$vet = fetchOne("SELECT * FROM VETERINARIAN WHERE vet_id=$id");
if (!$vet) {
    flash('danger', 'Veterinarian not found.');
    redirect('/sarpams/veterinarians/index.php');
}

$blockDelete = false;
$blockMsg    = '';

$recCount = fetchOne("SELECT COUNT(*) AS cnt FROM MEDICAL_RECORD WHERE vet_id=$id");
if ($recCount['cnt'] > 0) {
    $blockDelete = true;
    $blockMsg = "This veterinarian has {$recCount['cnt']} medical record(s) and cannot be deleted. Please reassign or remove those records first.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$blockDelete) {
    $name = $vet['first_name'] . ' ' . $vet['last_name'];
    query("DELETE FROM VETERINARIAN WHERE vet_id=$id");
    flash('success', "Veterinarian '$name' deleted.");
    redirect('/sarpams/veterinarians/index.php');
}

$pageTitle = 'Delete Veterinarian';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Delete Veterinarian</h1>
    <a href="/sarpams/veterinarians/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Veterinarians</a>
</div>

<?php if ($blockDelete): ?>
<div class="alert alert-danger"><?= htmlspecialchars($blockMsg) ?></div>
<a href="/sarpams/veterinarians/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Veterinarians</a>
<?php else: ?>
<div class="card">
    <div class="card-header"><i data-lucide="triangle-alert"></i> Confirm Deletion</div>
    <div class="card-body">
        <p>Are you sure you want to delete this veterinarian? This action <strong>cannot be undone</strong>.</p>
        <table class="table" style="max-width:500px;margin-bottom:1.5rem;">
            <tr><th>ID</th><td><?= $vet['vet_id'] ?></td></tr>
            <tr><th>Name</th><td><?= htmlspecialchars($vet['first_name'] . ' ' . $vet['last_name']) ?></td></tr>
            <tr><th>Specialisation</th><td><?= htmlspecialchars($vet['specialisation'] ?? '—') ?></td></tr>
            <tr><th>License No.</th><td><?= htmlspecialchars($vet['license_no'] ?? '—') ?></td></tr>
        </table>
        <form method="post" action="">
            <button type="submit" class="btn btn-danger">Yes, Delete Veterinarian</button>
            <a href="/sarpams/veterinarians/index.php" class="btn btn-secondary"><i data-lucide="x"></i> Cancel</a>
        </form>
    </div>
</div>
<?php endif; ?>

