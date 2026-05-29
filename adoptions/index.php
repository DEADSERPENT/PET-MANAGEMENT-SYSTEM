<?php
require_once __DIR__ . '/../config/db.php';
$pageTitle = 'Adoptions';
require_once __DIR__ . '/../includes/header.php';

$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$where = '';
if ($status !== '') {
    $s = escape($status);
    $where = "WHERE ad.status = '$s'";
}

$adoptions = fetchAll("
    SELECT ad.*,
           a.name AS animal_name,
           aa.first_name AS app_first, aa.last_name AS app_last,
           r.first_name AS off_first, r.last_name AS off_last
    FROM ADOPTION ad
    JOIN ANIMAL a ON ad.animal_id = a.animal_id
    JOIN ADOPTION_APPLICANT aa ON ad.applicant_id = aa.applicant_id
    LEFT JOIN RESCUER r ON ad.officer_id = r.rescuer_id
    $where
    ORDER BY ad.adoption_id DESC
");

$statuses = ['Pending','Approved','Rejected','Completed'];
?>
<div class="page-header">
    <h1 class="page-title"><i data-lucide="heart-handshake"></i> Adoptions</h1>
    <a href="/sarpams/adoptions/create.php" class="btn btn-primary"><i data-lucide="plus"></i> New Adoption</a>
</div>

<div class="search-bar">
    <form method="get" action="">
        <select name="status" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <?php foreach ($statuses as $st): ?>
                <option value="<?= $st ?>" <?= ($status === $st) ? 'selected' : '' ?>><?= $st ?></option>
            <?php endforeach; ?>
        </select>
        <?php if ($status !== ''): ?>
            <a href="/sarpams/adoptions/index.php" class="btn btn-secondary">Clear Filter</a>
        <?php endif; ?>
    </form>
</div>

<?php if (empty($adoptions)): ?>
<div class="empty-state"><p>No adoptions found<?= $status !== '' ? ' with status "' . htmlspecialchars($status) . '"' : '' ?>.</p></div>
<?php else: ?>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Animal</th>
            <th>Applicant</th>
            <th>Officer</th>
            <th>App. Date</th>
            <th>Approval Date</th>
            <th>Status</th>
            <th>Agreement</th>
            <th class="table-actions">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($adoptions as $ad): ?>
        <tr>
            <td><?= $ad['adoption_id'] ?></td>
            <td>
                <a href="/sarpams/animals/view.php?id=<?= $ad['animal_id'] ?>"><?= htmlspecialchars($ad['animal_name']) ?></a>
            </td>
            <td><?= htmlspecialchars($ad['app_first'] . ' ' . $ad['app_last']) ?></td>
            <td><?= $ad['off_first'] ? htmlspecialchars($ad['off_first'] . ' ' . $ad['off_last']) : '<span style="color:var(--muted)">—</span>' ?></td>
            <td><?= htmlspecialchars($ad['application_date'] ?? '—') ?></td>
            <td><?= htmlspecialchars($ad['approval_date'] ?? '—') ?></td>
            <td>
                <?php $stMap = ['pending'=>'pending','approved'=>'approved','rejected'=>'rejected','completed'=>'completed']; ?>
                <span class="badge badge-<?= $stMap[strtolower($ad['status'])] ?? 'pending' ?>"><?= htmlspecialchars($ad['status']) ?></span>
            </td>
            <td>
                <span class="badge badge-<?= $ad['agreement_signed'] ? 'yes' : 'no' ?>">
                    <?= $ad['agreement_signed'] ? 'Yes' : 'No' ?>
                </span>
            </td>
            <td class="table-actions">
                <a href="/sarpams/adoptions/edit.php?id=<?= $ad['adoption_id'] ?>" class="btn btn-sm btn-warning"><i data-lucide="pencil"></i> Edit</a>
                <a href="/sarpams/adoptions/delete.php?id=<?= $ad['adoption_id'] ?>" class="btn btn-sm btn-danger"><i data-lucide="trash-2"></i> Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
