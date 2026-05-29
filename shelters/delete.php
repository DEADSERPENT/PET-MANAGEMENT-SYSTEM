<?php
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    flash('danger', 'Invalid shelter ID.');
    redirect('/sarpams/shelters/index.php');
}

$shelter = fetchOne("SELECT * FROM SHELTER WHERE shelter_id=$id");
if (!$shelter) {
    flash('danger', 'Shelter not found.');
    redirect('/sarpams/shelters/index.php');
}

$blockDelete = false;
$blockMsg    = '';

$cageCount = fetchOne("SELECT COUNT(*) AS cnt FROM CAGE WHERE shelter_id=$id");
if ($cageCount['cnt'] > 0) {
    $blockDelete = true;
    $blockMsg = "This shelter has {$cageCount['cnt']} cage(s) assigned to it. Please remove all cages before deleting the shelter.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$blockDelete) {
    $name = $shelter['shelter_name'];
    query("DELETE FROM SHELTER WHERE shelter_id=$id");
    flash('success', "Shelter '$name' deleted.");
    redirect('/sarpams/shelters/index.php');
}

$pageTitle = 'Delete Shelter';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Delete Shelter</h1>
    <a href="/sarpams/shelters/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Shelters</a>
</div>

<?php if ($blockDelete): ?>
<div class="alert alert-danger"><?= htmlspecialchars($blockMsg) ?></div>
<a href="/sarpams/shelters/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Shelters</a>
<?php else: ?>
<div class="card">
    <div class="card-header"><i data-lucide="triangle-alert"></i> Confirm Deletion</div>
    <div class="card-body">
        <p>Are you sure you want to delete this shelter? This action <strong>cannot be undone</strong>.</p>
        <table class="table" style="max-width:500px;margin-bottom:1.5rem;">
            <tr><th>ID</th><td><?= $shelter['shelter_id'] ?></td></tr>
            <tr><th>Name</th><td><?= htmlspecialchars($shelter['shelter_name']) ?></td></tr>
            <tr><th>City</th><td><?= htmlspecialchars($shelter['city']) ?></td></tr>
            <tr><th>Manager</th><td><?= htmlspecialchars($shelter['manager_name']) ?></td></tr>
        </table>
        <form method="post" action="">
            <button type="submit" class="btn btn-danger">Yes, Delete Shelter</button>
            <a href="/sarpams/shelters/index.php" class="btn btn-secondary"><i data-lucide="x"></i> Cancel</a>
        </form>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
