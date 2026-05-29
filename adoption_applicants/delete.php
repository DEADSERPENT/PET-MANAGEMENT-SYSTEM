<?php
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    flash('danger', 'Invalid applicant ID.');
    redirect('/sarpams/adoption_applicants/index.php');
}

$applicant = fetchOne("SELECT * FROM ADOPTION_APPLICANT WHERE applicant_id=$id");
if (!$applicant) {
    flash('danger', 'Applicant not found.');
    redirect('/sarpams/adoption_applicants/index.php');
}

$blockDelete = false;
$blockMsg    = '';

$adoptCount = fetchOne("SELECT COUNT(*) AS cnt FROM ADOPTION WHERE applicant_id=$id");
if ($adoptCount['cnt'] > 0) {
    $blockDelete = true;
    $blockMsg = "This applicant has {$adoptCount['cnt']} adoption record(s) and cannot be deleted. Please remove those adoption records first.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$blockDelete) {
    $name = $applicant['first_name'] . ' ' . $applicant['last_name'];
    query("DELETE FROM ADOPTION_APPLICANT WHERE applicant_id=$id");
    flash('success', "Applicant '$name' deleted.");
    redirect('/sarpams/adoption_applicants/index.php');
}

$pageTitle = 'Delete Applicant';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Delete Applicant</h1>
    <a href="/sarpams/adoption_applicants/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Applicants</a>
</div>

<?php if ($blockDelete): ?>
<div class="alert alert-danger"><?= htmlspecialchars($blockMsg) ?></div>
<a href="/sarpams/adoption_applicants/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Applicants</a>
<?php else: ?>
<div class="card">
    <div class="card-header"><i data-lucide="triangle-alert"></i> Confirm Deletion</div>
    <div class="card-body">
        <p>Are you sure you want to delete this applicant? This action <strong>cannot be undone</strong>.</p>
        <table class="table" style="max-width:500px;margin-bottom:1.5rem;">
            <tr><th>ID</th><td><?= $applicant['applicant_id'] ?></td></tr>
            <tr><th>Name</th><td><?= htmlspecialchars($applicant['first_name'] . ' ' . $applicant['last_name']) ?></td></tr>
            <tr><th>City</th><td><?= htmlspecialchars($applicant['city']) ?></td></tr>
            <tr><th>Phone</th><td><?= htmlspecialchars($applicant['phone']) ?></td></tr>
        </table>
        <form method="post" action="">
            <button type="submit" class="btn btn-danger">Yes, Delete Applicant</button>
            <a href="/sarpams/adoption_applicants/index.php" class="btn btn-secondary"><i data-lucide="x"></i> Cancel</a>
        </form>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
