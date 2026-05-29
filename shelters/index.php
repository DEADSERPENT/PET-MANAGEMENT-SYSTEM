<?php
require_once __DIR__ . '/../config/db.php';
$pageTitle = 'Shelters';
require_once __DIR__ . '/../includes/header.php';

$shelters = fetchAll("
    SELECT s.*,
           COUNT(c.cage_id) AS total_cages,
           SUM(c.is_occupied) AS occupied_cages
    FROM SHELTER s
    LEFT JOIN CAGE c ON s.shelter_id = c.shelter_id
    GROUP BY s.shelter_id
    ORDER BY s.shelter_id DESC
");
?>
<div class="page-header">
    <h1 class="page-title"><i data-lucide="building-2"></i> Shelters</h1>
    <a href="/sarpams/shelters/create.php" class="btn btn-primary"><i data-lucide="plus"></i> Add Shelter</a>
</div>

<?php if (empty($shelters)): ?>
<div class="empty-state"><p>No shelters found.</p></div>
<?php else: ?>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>City</th>
            <th>Capacity</th>
            <th>Contact</th>
            <th>Manager</th>
            <th>Total Cages</th>
            <th>Occupied</th>
            <th class="table-actions">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($shelters as $s): ?>
        <tr>
            <td><?= $s['shelter_id'] ?></td>
            <td><?= htmlspecialchars($s['shelter_name']) ?></td>
            <td><?= htmlspecialchars($s['city']) ?></td>
            <td><?= htmlspecialchars($s['capacity']) ?></td>
            <td><?= htmlspecialchars($s['contact_phone']) ?></td>
            <td><?= htmlspecialchars($s['manager_name']) ?></td>
            <td><?= (int)$s['total_cages'] ?></td>
            <td><?= (int)$s['occupied_cages'] ?></td>
            <td class="table-actions">
                <a href="/sarpams/shelters/edit.php?id=<?= $s['shelter_id'] ?>" class="btn btn-sm btn-warning"><i data-lucide="pencil"></i> Edit</a>
                <a href="/sarpams/shelters/delete.php?id=<?= $s['shelter_id'] ?>" class="btn btn-sm btn-danger"><i data-lucide="trash-2"></i> Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
