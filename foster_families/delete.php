<?php
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    flash('danger', 'Invalid foster family ID.');
    redirect('/sarpams/foster_families/index.php');
}

$family = fetchOne("SELECT * FROM FOSTER_FAMILY WHERE foster_id=$id");
if (!$family) {
    flash('danger', 'Foster family not found.');
    redirect('/sarpams/foster_families/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $family['family_name'];
    query("DELETE FROM FOSTER_FAMILY WHERE foster_id=$id");
    flash('success', "Foster family '$name' deleted.");
    redirect('/sarpams/foster_families/index.php');
}

$pageTitle = 'Delete Foster Family';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Delete Foster Family</h1>
    <a href="/sarpams/foster_families/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Foster Families</a>
</div>

<div class="card">
    <div class="card-header"><i data-lucide="triangle-alert"></i> Confirm Deletion</div>
    <div class="card-body">
        <p>Are you sure you want to delete this foster family? This action <strong>cannot be undone</strong>.</p>
        <table class="table" style="max-width:500px;margin-bottom:1.5rem;">
            <tr><th>ID</th><td><?= $family['foster_id'] ?></td></tr>
            <tr><th>Family Name</th><td><?= htmlspecialchars($family['family_name']) ?></td></tr>
            <tr><th>City</th><td><?= htmlspecialchars($family['city']) ?></td></tr>
            <tr><th>Phone</th><td><?= htmlspecialchars($family['phone']) ?></td></tr>
            <tr><th>Approved</th><td><?= $family['is_approved'] ? 'Yes' : 'No' ?></td></tr>
        </table>
        <form method="post" action="">
            <button type="submit" class="btn btn-danger">Yes, Delete Foster Family</button>
            <a href="/sarpams/foster_families/index.php" class="btn btn-secondary"><i data-lucide="x"></i> Cancel</a>
        </form>
    </div>
</div>

