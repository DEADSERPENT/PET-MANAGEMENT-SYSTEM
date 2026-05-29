<?php
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    flash('danger', 'Invalid rescuer ID.');
    redirect('/sarpams/rescuers/index.php');
}

$rescuer = fetchOne("SELECT * FROM RESCUER WHERE rescuer_id=$id");
if (!$rescuer) {
    flash('danger', 'Rescuer not found.');
    redirect('/sarpams/rescuers/index.php');
}

$blockDelete = false;
$blockMsg    = '';

$adoptionCount = fetchOne("SELECT COUNT(*) AS cnt FROM ADOPTION WHERE officer_id=$id");
if ($adoptionCount['cnt'] > 0) {
    $blockDelete = true;
    $blockMsg = "This rescuer is linked to {$adoptionCount['cnt']} adoption record(s) as an officer and cannot be deleted.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$blockDelete) {
    $name = $rescuer['first_name'] . ' ' . $rescuer['last_name'];
    query("DELETE FROM RESCUER WHERE rescuer_id=$id");
    flash('success', "Rescuer '$name' deleted.");
    redirect('/sarpams/rescuers/index.php');
}

$pageTitle = 'Delete Rescuer';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Delete Rescuer</h1>
    <a href="/sarpams/rescuers/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Rescuers</a>
</div>

<?php if ($blockDelete): ?>
<div class="alert alert-danger"><?= htmlspecialchars($blockMsg) ?></div>
<a href="/sarpams/rescuers/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Rescuers</a>
<?php else: ?>
<div class="card">
    <div class="card-header"><i data-lucide="triangle-alert"></i> Confirm Deletion</div>
    <div class="card-body">
        <p>Are you sure you want to delete this rescuer? This action <strong>cannot be undone</strong>.</p>
        <table class="table" style="max-width:500px;margin-bottom:1.5rem;">
            <tr><th>ID</th><td><?= $rescuer['rescuer_id'] ?></td></tr>
            <tr><th>Name</th><td><?= htmlspecialchars($rescuer['first_name'] . ' ' . $rescuer['last_name']) ?></td></tr>
            <tr><th>Phone</th><td><?= htmlspecialchars($rescuer['phone']) ?></td></tr>
            <tr><th>Zone</th><td><?= htmlspecialchars($rescuer['zone_area']) ?></td></tr>
            <tr><th>Certification</th><td><?= htmlspecialchars($rescuer['certification_level']) ?></td></tr>
        </table>
        <form method="post" action="">
            <button type="submit" class="btn btn-danger">Yes, Delete Rescuer</button>
            <a href="/sarpams/rescuers/index.php" class="btn btn-secondary"><i data-lucide="x"></i> Cancel</a>
        </form>
    </div>
</div>
<?php endif; ?>

