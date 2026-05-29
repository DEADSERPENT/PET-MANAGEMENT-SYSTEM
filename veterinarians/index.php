<?php
require_once __DIR__ . '/../config/db.php';
$pageTitle = 'Veterinarians';
require_once __DIR__ . '/../includes/header.php';

$vets = fetchAll("SELECT * FROM VETERINARIAN ORDER BY vet_id DESC");
?>
<div class="page-header">
    <h1 class="page-title"><i data-lucide="stethoscope"></i> Veterinarians</h1>
    <a href="/sarpams/veterinarians/create.php" class="btn btn-primary"><i data-lucide="plus"></i> Add Veterinarian</a>
</div>

<?php if (empty($vets)): ?>
<div class="empty-state"><p>No veterinarians found.</p></div>
<?php else: ?>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Specialisation</th>
            <th>Phone</th>
            <th>Email</th>
            <th>License No.</th>
            <th class="table-actions">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($vets as $v): ?>
        <tr>
            <td><?= $v['vet_id'] ?></td>
            <td><?= htmlspecialchars($v['first_name'] . ' ' . $v['last_name']) ?></td>
            <td><?= htmlspecialchars($v['specialisation'] ?? '—') ?></td>
            <td><?= htmlspecialchars($v['phone']) ?></td>
            <td><?= htmlspecialchars($v['email']) ?></td>
            <td><?= htmlspecialchars($v['license_no'] ?? '—') ?></td>
            <td class="table-actions">
                <a href="/sarpams/veterinarians/edit.php?id=<?= $v['vet_id'] ?>" class="btn btn-sm btn-warning"><i data-lucide="pencil"></i> Edit</a>
                <a href="/sarpams/veterinarians/delete.php?id=<?= $v['vet_id'] ?>" class="btn btn-sm btn-danger"><i data-lucide="trash-2"></i> Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

