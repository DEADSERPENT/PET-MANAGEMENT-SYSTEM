<?php
require_once __DIR__ . '/../config/db.php';
$pageTitle = 'Animals';
require_once __DIR__ . '/../includes/header.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';
if ($search !== '') {
    $s = escape($search);
    $where = "WHERE a.name LIKE '%$s%' OR a.species LIKE '%$s%'";
}

$animals = fetchAll("
    SELECT a.*, c.cage_number, c.size_category, sh.shelter_name
    FROM ANIMAL a
    LEFT JOIN CAGE c ON a.cage_id = c.cage_id
    LEFT JOIN SHELTER sh ON c.shelter_id = sh.shelter_id
    $where
    ORDER BY a.animal_id DESC
");
?>
<div class="page-header">
    <h1 class="page-title"><i data-lucide="dog"></i> Animals</h1>
    <a href="/sarpams/animals/create.php" class="btn btn-primary"><i data-lucide="plus"></i> Add Animal</a>
</div>

<div class="search-bar">
    <form method="get" action="">
        <input type="text" name="search" placeholder="Search by name or species…" value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-secondary"><i data-lucide="search"></i> Search</button>
        <?php if ($search !== ''): ?>
            <a href="/sarpams/animals/index.php" class="btn btn-secondary"><i data-lucide="x"></i> Clear</a>
        <?php endif; ?>
    </form>
</div>

<?php if (empty($animals)): ?>
<div class="empty-state">
    <p>No animals found<?= $search !== '' ? ' matching "' . htmlspecialchars($search) . '"' : '' ?>.</p>
</div>
<?php else: ?>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Species</th>
            <th>Breed</th>
            <th>Age (yrs)</th>
            <th>Sex</th>
            <th>Health Status</th>
            <th>Vaccinated</th>
            <th>Shelter / Cage</th>
            <th>Intake Date</th>
            <th class="table-actions">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($animals as $a): ?>
        <tr>
            <td><?= $a['animal_id'] ?></td>
            <td><?= htmlspecialchars($a['name']) ?></td>
            <td><?= htmlspecialchars($a['species']) ?></td>
            <td><?= htmlspecialchars($a['breed'] ?? '—') ?></td>
            <td><?= $a['age_years'] !== null ? $a['age_years'] : '—' ?></td>
            <td><?= htmlspecialchars($a['sex']) ?></td>
            <td>
                <?php
                $hs = strtolower($a['health_status'] ?? '');
                $hsMap = ['healthy' => 'healthy', 'under treatment' => 'treatment', 'adopted' => 'adopted', 'unknown' => 'unknown'];
                $hsBadge = $hsMap[$hs] ?? 'unknown';
                ?>
                <span class="badge badge-<?= $hsBadge ?>"><?= htmlspecialchars($a['health_status'] ?? 'Unknown') ?></span>
            </td>
            <td>
                <span class="badge badge-<?= $a['is_vaccinated'] ? 'yes' : 'no' ?>">
                    <?= $a['is_vaccinated'] ? 'Yes' : 'No' ?>
                </span>
            </td>
            <td>
                <?php if ($a['shelter_name']): ?>
                    <?= htmlspecialchars($a['shelter_name']) ?><br>
                    <small>Cage: <?= htmlspecialchars($a['cage_number']) ?></small>
                <?php else: ?>
                    <span style="color:var(--muted)">—</span>
                <?php endif; ?>
            </td>
            <td><?= htmlspecialchars($a['intake_date'] ?? '—') ?></td>
            <td class="table-actions">
                <a href="/sarpams/animals/view.php?id=<?= $a['animal_id'] ?>" class="btn btn-sm btn-secondary"><i data-lucide="eye"></i> View</a>
                <a href="/sarpams/animals/edit.php?id=<?= $a['animal_id'] ?>" class="btn btn-sm btn-warning"><i data-lucide="pencil"></i> Edit</a>
                <a href="/sarpams/animals/delete.php?id=<?= $a['animal_id'] ?>" class="btn btn-sm btn-danger"><i data-lucide="trash-2"></i> Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

