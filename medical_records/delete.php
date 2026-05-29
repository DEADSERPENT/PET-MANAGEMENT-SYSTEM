<?php
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    flash('danger', 'Invalid record ID.');
    redirect('/sarpams/medical_records/index.php');
}

$record = fetchOne("
    SELECT mr.*, a.name AS animal_name, v.first_name, v.last_name
    FROM MEDICAL_RECORD mr
    JOIN ANIMAL a ON mr.animal_id = a.animal_id
    JOIN VETERINARIAN v ON mr.vet_id = v.vet_id
    WHERE mr.record_id = $id
");
if (!$record) {
    flash('danger', 'Medical record not found.');
    redirect('/sarpams/medical_records/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $animal_id = (int)$record['animal_id'];
    query("DELETE FROM MEDICAL_RECORD WHERE record_id=$id");
    flash('success', 'Medical record deleted.');
    redirect("/sarpams/animals/view.php?id=$animal_id");
}

$pageTitle = 'Delete Medical Record';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Delete Medical Record</h1>
    <a href="/sarpams/medical_records/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Records</a>
</div>

<div class="card">
    <div class="card-header"><i data-lucide="triangle-alert"></i> Confirm Deletion</div>
    <div class="card-body">
        <p>Are you sure you want to delete this medical record? This action <strong>cannot be undone</strong>.</p>
        <table class="table" style="max-width:500px;margin-bottom:1.5rem;">
            <tr><th>Record ID</th><td><?= $record['record_id'] ?></td></tr>
            <tr><th>Animal</th><td><?= htmlspecialchars($record['animal_name']) ?></td></tr>
            <tr><th>Veterinarian</th><td><?= htmlspecialchars($record['first_name'] . ' ' . $record['last_name']) ?></td></tr>
            <tr><th>Exam Date</th><td><?= htmlspecialchars($record['exam_date']) ?></td></tr>
            <tr><th>Diagnosis</th><td><?= htmlspecialchars(mb_strimwidth($record['diagnosis'] ?? '', 0, 80, '…')) ?></td></tr>
        </table>
        <form method="post" action="">
            <button type="submit" class="btn btn-danger">Yes, Delete Record</button>
            <a href="/sarpams/animals/view.php?id=<?= $record['animal_id'] ?>" class="btn btn-secondary"><i data-lucide="x"></i> Cancel</a>
        </form>
    </div>
</div>

