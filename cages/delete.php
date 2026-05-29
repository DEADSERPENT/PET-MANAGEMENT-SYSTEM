<?php
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    flash('danger', 'Invalid cage ID.');
    redirect('/sarpams/cages/index.php');
}

$cage = fetchOne("
    SELECT c.*, sh.shelter_name
    FROM CAGE c
    JOIN SHELTER sh ON c.shelter_id = sh.shelter_id
    WHERE c.cage_id = $id
");
if (!$cage) {
    flash('danger', 'Cage not found.');
    redirect('/sarpams/cages/index.php');
}

$blockDelete = false;
$blockMsg    = '';

if ($cage['is_occupied']) {
    $blockDelete = true;
    $blockMsg = 'This cage is currently occupied by an animal. Please reassign or remove the animal before deleting this cage.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$blockDelete) {
    $num = $cage['cage_number'];
    query("DELETE FROM CAGE WHERE cage_id=$id");
    flash('success', "Cage '$num' deleted.");
    redirect('/sarpams/cages/index.php');
}

$pageTitle = 'Delete Cage';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Delete Cage</h1>
    <a href="/sarpams/cages/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Cages</a>
</div>

<?php if ($blockDelete): ?>
<div class="alert alert-danger"><?= htmlspecialchars($blockMsg) ?></div>
<a href="/sarpams/cages/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Cages</a>
<?php else: ?>
<div class="card">
    <div class="card-header"><i data-lucide="triangle-alert"></i> Confirm Deletion</div>
    <div class="card-body">
        <p>Are you sure you want to delete this cage? This action <strong>cannot be undone</strong>.</p>
        <table class="table" style="max-width:500px;margin-bottom:1.5rem;">
            <tr><th>ID</th><td><?= $cage['cage_id'] ?></td></tr>
            <tr><th>Cage Number</th><td><?= htmlspecialchars($cage['cage_number']) ?></td></tr>
            <tr><th>Shelter</th><td><?= htmlspecialchars($cage['shelter_name']) ?></td></tr>
            <tr><th>Size</th><td><?= htmlspecialchars($cage['size_category']) ?></td></tr>
        </table>
        <form method="post" action="">
            <button type="submit" class="btn btn-danger">Yes, Delete Cage</button>
            <a href="/sarpams/cages/index.php" class="btn btn-secondary"><i data-lucide="x"></i> Cancel</a>
        </form>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
