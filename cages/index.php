<?php
require_once __DIR__ . '/../config/db.php';
$pageTitle = 'Cages';
require_once __DIR__ . '/../includes/header.php';

$filter_shelter = isset($_GET['shelter_id']) ? (int)$_GET['shelter_id'] : 0;
$where = $filter_shelter > 0 ? "WHERE c.shelter_id = $filter_shelter" : '';

$cages = fetchAll("
    SELECT c.*, sh.shelter_name
    FROM CAGE c
    JOIN SHELTER sh ON c.shelter_id = sh.shelter_id
    $where
    ORDER BY sh.shelter_name, c.cage_number
");

$shelters = fetchAll("SELECT shelter_id, shelter_name FROM SHELTER ORDER BY shelter_name");
?>
<div class="page-header">
    <h1 class="page-title"><i data-lucide="grid-3x3"></i> Cages</h1>
    <a href="/sarpams/cages/create.php" class="btn btn-primary"><i data-lucide="plus"></i> Add Cage</a>
</div>

<div class="search-bar">
    <form method="get" action="">
        <select name="shelter_id" onchange="this.form.submit()">
            <option value="">All Shelters</option>
            <?php foreach ($shelters as $sh): ?>
                <option value="<?= $sh['shelter_id'] ?>" <?= ($filter_shelter == $sh['shelter_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sh['shelter_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if ($filter_shelter > 0): ?>
            <a href="/sarpams/cages/index.php" class="btn btn-secondary">Clear Filter</a>
        <?php endif; ?>
    </form>
</div>

<?php if (empty($cages)): ?>
<div class="empty-state"><p>No cages found.</p></div>
<?php else: ?>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Shelter</th>
            <th>Cage Number</th>
            <th>Size</th>
            <th>Status</th>
            <th>Notes</th>
            <th class="table-actions">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($cages as $c): ?>
        <tr>
            <td><?= $c['cage_id'] ?></td>
            <td><?= htmlspecialchars($c['shelter_name']) ?></td>
            <td><?= htmlspecialchars($c['cage_number']) ?></td>
            <td><?= htmlspecialchars($c['size_category']) ?></td>
            <td>
                <span class="badge badge-<?= $c['is_occupied'] ? 'treatment' : 'healthy' ?>">
                    <?= $c['is_occupied'] ? 'Occupied' : 'Free' ?>
                </span>
            </td>
            <td><?= htmlspecialchars(mb_strimwidth($c['notes'] ?? '', 0, 60, '…')) ?></td>
            <td class="table-actions">
                <a href="/sarpams/cages/edit.php?id=<?= $c['cage_id'] ?>" class="btn btn-sm btn-warning"><i data-lucide="pencil"></i> Edit</a>
                <a href="/sarpams/cages/delete.php?id=<?= $c['cage_id'] ?>" class="btn btn-sm btn-danger"><i data-lucide="trash-2"></i> Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

