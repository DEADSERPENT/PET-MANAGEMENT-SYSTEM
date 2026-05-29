<?php
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    flash('danger', 'Invalid animal ID.');
    redirect('/sarpams/animals/index.php');
}

$animal = fetchOne("SELECT * FROM ANIMAL WHERE animal_id=$id");
if (!$animal) {
    flash('danger', 'Animal not found.');
    redirect('/sarpams/animals/index.php');
}

$blockDelete = false;
$blockMsg    = '';

if (strtolower($animal['health_status']) === 'adopted') {
    $blockDelete = true;
    $blockMsg    = 'This animal has status "Adopted" and cannot be deleted. Update the health status before deleting.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$blockDelete) {
    $name = $animal['name'];
    $cage_id = $animal['cage_id'];

    query("DELETE FROM ANIMAL WHERE animal_id=$id");

    if ($cage_id) {
        query("UPDATE CAGE SET is_occupied=0 WHERE cage_id='$cage_id'");
    }

    flash('success', "Animal '$name' deleted.");
    redirect('/sarpams/animals/index.php');
}

$pageTitle = 'Delete Animal';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Delete Animal</h1>
    <a href="/sarpams/animals/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Animals</a>
</div>

<?php if ($blockDelete): ?>
<div class="alert alert-danger"><?= htmlspecialchars($blockMsg) ?></div>
<a href="/sarpams/animals/view.php?id=<?= $id ?>" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Animal</a>
<?php else: ?>
<div class="card">
    <div class="card-header"><i data-lucide="triangle-alert"></i> Confirm Deletion</div>
    <div class="card-body">
        <p>Are you sure you want to delete the following animal? This action <strong>cannot be undone</strong>.</p>
        <table class="table" style="max-width:500px;margin-bottom:1.5rem;">
            <tr><th>ID</th><td><?= $animal['animal_id'] ?></td></tr>
            <tr><th>Name</th><td><?= htmlspecialchars($animal['name']) ?></td></tr>
            <tr><th>Species</th><td><?= htmlspecialchars($animal['species']) ?></td></tr>
            <tr><th>Health Status</th><td><?= htmlspecialchars($animal['health_status'] ?? 'Unknown') ?></td></tr>
        </table>
        <form method="post" action="">
            <button type="submit" class="btn btn-danger">Yes, Delete Animal</button>
            <a href="/sarpams/animals/view.php?id=<?= $id ?>" class="btn btn-secondary"><i data-lucide="x"></i> Cancel</a>
        </form>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
