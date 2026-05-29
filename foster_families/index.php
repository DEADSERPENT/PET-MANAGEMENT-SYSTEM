<?php
require_once __DIR__ . '/../config/db.php';
$pageTitle = 'Foster Families';
require_once __DIR__ . '/../includes/header.php';

$families = fetchAll("SELECT * FROM FOSTER_FAMILY ORDER BY foster_id DESC");
?>
<div class="page-header">
    <h1 class="page-title"><i data-lucide="house-heart"></i> Foster Families</h1>
    <a href="/sarpams/foster_families/create.php" class="btn btn-primary"><i data-lucide="plus"></i> Add Foster Family</a>
</div>

<?php if (empty($families)): ?>
<div class="empty-state"><p>No foster families found.</p></div>
<?php else: ?>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Family Name</th>
            <th>City</th>
            <th>Phone</th>
            <th>House Type</th>
            <th>Has Other Pets</th>
            <th>Approved</th>
            <th class="table-actions">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($families as $f): ?>
        <tr>
            <td><?= $f['foster_id'] ?></td>
            <td><?= htmlspecialchars($f['family_name']) ?></td>
            <td><?= htmlspecialchars($f['city']) ?></td>
            <td><?= htmlspecialchars($f['phone']) ?></td>
            <td><?= htmlspecialchars($f['house_type'] ?? '—') ?></td>
            <td>
                <span class="badge badge-<?= $f['has_other_pets'] ? 'yes' : 'no' ?>">
                    <?= $f['has_other_pets'] ? 'Yes' : 'No' ?>
                </span>
            </td>
            <td>
                <span class="badge badge-<?= $f['is_approved'] ? 'yes' : 'no' ?>">
                    <?= $f['is_approved'] ? 'Approved' : 'Pending' ?>
                </span>
            </td>
            <td class="table-actions">
                <a href="/sarpams/foster_families/edit.php?id=<?= $f['foster_id'] ?>" class="btn btn-sm btn-warning"><i data-lucide="pencil"></i> Edit</a>
                <a href="/sarpams/foster_families/delete.php?id=<?= $f['foster_id'] ?>" class="btn btn-sm btn-danger"><i data-lucide="trash-2"></i> Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

