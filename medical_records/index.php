<?php
require_once __DIR__ . '/../config/db.php';

$animal_id = isset($_GET['animal_id']) ? (int)$_GET['animal_id'] : 0;
$animal = null;
$where = '';

if ($animal_id > 0) {
    $animal = fetchOne("SELECT animal_id, name FROM ANIMAL WHERE animal_id=$animal_id");
    if ($animal) {
        $where = "WHERE mr.animal_id = $animal_id";
    }
}

$pageTitle = $animal ? 'Medical Records – ' . htmlspecialchars($animal['name']) : 'Medical Records';
require_once __DIR__ . '/../includes/header.php';

$records = fetchAll("
    SELECT mr.*, a.name AS animal_name, v.first_name, v.last_name
    FROM MEDICAL_RECORD mr
    JOIN ANIMAL a ON mr.animal_id = a.animal_id
    JOIN VETERINARIAN v ON mr.vet_id = v.vet_id
    $where
    ORDER BY mr.exam_date DESC, mr.record_id DESC
");
?>
<div class="page-header">
    <h1 class="page-title"><?= $animal ? 'Medical Records – ' . htmlspecialchars($animal['name']) : 'Medical Records' ?></h1>
    <div style="display:flex;gap:.5rem;">
        <a href="/sarpams/medical_records/create.php<?= $animal_id > 0 ? '?animal_id=' . $animal_id : '' ?>" class="btn btn-primary"><i data-lucide="plus"></i> Add Record</a>
        <?php if ($animal_id > 0): ?>
            <a href="/sarpams/animals/view.php?id=<?= $animal_id ?>" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Animal</a>
            <a href="/sarpams/medical_records/index.php" class="btn btn-secondary">All Records</a>
        <?php endif; ?>
    </div>
</div>

<?php if (empty($records)): ?>
<div class="empty-state"><p>No medical records found.</p></div>
<?php else: ?>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Animal</th>
            <th>Vet</th>
            <th>Exam Date</th>
            <th>Diagnosis</th>
            <th>Medication</th>
            <th>Next Checkup</th>
            <th class="table-actions">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($records as $r): ?>
        <tr>
            <td><?= $r['record_id'] ?></td>
            <td>
                <a href="/sarpams/animals/view.php?id=<?= $r['animal_id'] ?>"><?= htmlspecialchars($r['animal_name']) ?></a>
            </td>
            <td><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></td>
            <td><?= htmlspecialchars($r['exam_date']) ?></td>
            <td title="<?= htmlspecialchars($r['diagnosis'] ?? '') ?>">
                <?= htmlspecialchars(mb_strimwidth($r['diagnosis'] ?? '', 0, 60, '…')) ?>
            </td>
            <td><?= htmlspecialchars($r['medication'] ?? '—') ?></td>
            <td><?= htmlspecialchars($r['next_checkup_date'] ?? '—') ?></td>
            <td class="table-actions">
                <a href="/sarpams/medical_records/edit.php?id=<?= $r['record_id'] ?>" class="btn btn-sm btn-warning"><i data-lucide="pencil"></i> Edit</a>
                <a href="/sarpams/medical_records/delete.php?id=<?= $r['record_id'] ?>" class="btn btn-sm btn-danger"><i data-lucide="trash-2"></i> Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

