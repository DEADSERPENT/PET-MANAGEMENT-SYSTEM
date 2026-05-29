<?php
require_once __DIR__ . '/../config/db.php';
$pageTitle = 'Rescuers';
require_once __DIR__ . '/../includes/header.php';

$rescuers = fetchAll("SELECT * FROM RESCUER ORDER BY rescuer_id DESC");
?>
<div class="page-header">
    <h1 class="page-title"><i data-lucide="shield-check"></i> Rescuers</h1>
    <a href="/sarpams/rescuers/create.php" class="btn btn-primary"><i data-lucide="plus"></i> Add Rescuer</a>
</div>

<?php if (empty($rescuers)): ?>
<div class="empty-state"><p>No rescuers found.</p></div>
<?php else: ?>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Zone Area</th>
            <th>Certification</th>
            <th>Available</th>
            <th>Join Date</th>
            <th class="table-actions">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($rescuers as $r): ?>
        <tr>
            <td><?= $r['rescuer_id'] ?></td>
            <td><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></td>
            <td><?= htmlspecialchars($r['phone']) ?></td>
            <td><?= htmlspecialchars($r['email']) ?></td>
            <td><?= htmlspecialchars($r['zone_area']) ?></td>
            <td><?= htmlspecialchars($r['certification_level']) ?></td>
            <td>
                <span class="badge badge-<?= $r['is_available'] ? 'yes' : 'no' ?>">
                    <?= $r['is_available'] ? 'Yes' : 'No' ?>
                </span>
            </td>
            <td><?= htmlspecialchars($r['join_date'] ?? '—') ?></td>
            <td class="table-actions">
                <a href="/sarpams/rescuers/edit.php?id=<?= $r['rescuer_id'] ?>" class="btn btn-sm btn-warning"><i data-lucide="pencil"></i> Edit</a>
                <a href="/sarpams/rescuers/delete.php?id=<?= $r['rescuer_id'] ?>" class="btn btn-sm btn-danger"><i data-lucide="trash-2"></i> Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
