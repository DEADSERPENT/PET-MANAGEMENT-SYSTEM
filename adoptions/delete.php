<?php
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    flash('danger', 'Invalid adoption ID.');
    redirect('/sarpams/adoptions/index.php');
}

$adoption = fetchOne("
    SELECT ad.*, a.name AS animal_name,
           aa.first_name, aa.last_name
    FROM ADOPTION ad
    JOIN ANIMAL a ON ad.animal_id = a.animal_id
    JOIN ADOPTION_APPLICANT aa ON ad.applicant_id = aa.applicant_id
    WHERE ad.adoption_id = $id
");
if (!$adoption) {
    flash('danger', 'Adoption not found.');
    redirect('/sarpams/adoptions/index.php');
}

$blockDelete = false;
$blockMsg    = '';

$allowedStatuses = ['Pending', 'Rejected'];
if (!in_array($adoption['status'], $allowedStatuses)) {
    $blockDelete = true;
    $blockMsg = "Adoption #$id has status \"{$adoption['status']}\" and cannot be deleted. Only Pending or Rejected adoptions may be deleted.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$blockDelete) {
    query("DELETE FROM ADOPTION WHERE adoption_id=$id");
    flash('success', "Adoption #$id deleted.");
    redirect('/sarpams/adoptions/index.php');
}

$pageTitle = 'Delete Adoption';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Delete Adoption</h1>
    <a href="/sarpams/adoptions/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Adoptions</a>
</div>

<?php if ($blockDelete): ?>
<div class="alert alert-danger"><?= htmlspecialchars($blockMsg) ?></div>
<a href="/sarpams/adoptions/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Adoptions</a>
<?php else: ?>
<div class="card">
    <div class="card-header"><i data-lucide="triangle-alert"></i> Confirm Deletion</div>
    <div class="card-body">
        <p>Are you sure you want to delete this adoption record? This action <strong>cannot be undone</strong>.</p>
        <table class="table" style="max-width:500px;margin-bottom:1.5rem;">
            <tr><th>Adoption ID</th><td><?= $adoption['adoption_id'] ?></td></tr>
            <tr><th>Animal</th><td><?= htmlspecialchars($adoption['animal_name']) ?></td></tr>
            <tr><th>Applicant</th><td><?= htmlspecialchars($adoption['first_name'] . ' ' . $adoption['last_name']) ?></td></tr>
            <tr><th>Application Date</th><td><?= htmlspecialchars($adoption['application_date'] ?? '—') ?></td></tr>
            <tr><th>Status</th><td><?= htmlspecialchars($adoption['status']) ?></td></tr>
        </table>
        <form method="post" action="">
            <button type="submit" class="btn btn-danger">Yes, Delete Adoption</button>
            <a href="/sarpams/adoptions/index.php" class="btn btn-secondary"><i data-lucide="x"></i> Cancel</a>
        </form>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
