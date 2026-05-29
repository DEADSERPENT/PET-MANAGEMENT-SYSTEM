<?php
require_once __DIR__ . '/../config/db.php';
$pageTitle = 'Rescue Requests';
require_once __DIR__ . '/../includes/header.php';

$status = isset($_GET['status']) ? trim($_GET['status']) : '';
$where = '';
if ($status !== '') {
    $s = escape($status);
    $where = "WHERE rr.status = '$s'";
}

$requests = fetchAll("
    SELECT rr.*,
           r.first_name AS rescuer_first, r.last_name AS rescuer_last,
           a.name AS animal_name
    FROM RESCUE_REQUEST rr
    LEFT JOIN RESCUER r ON rr.rescuer_id = r.rescuer_id
    LEFT JOIN ANIMAL a ON rr.animal_id = a.animal_id
    $where
    ORDER BY rr.request_id DESC
");

$statuses = ['Open', 'Assigned', 'Closed'];
?>
<div class="page-header">
    <h1 class="page-title"><i data-lucide="siren"></i> Rescue Requests</h1>
    <a href="/sarpams/rescue_requests/create.php" class="btn btn-primary"><i data-lucide="plus"></i> New Rescue Request</a>
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
            <a href="/sarpams/rescue_requests/index.php" class="btn btn-secondary">Clear Filter</a>
        <?php endif; ?>
    </form>
</div>

<?php if (empty($requests)): ?>
<div class="empty-state"><p>No rescue requests found<?= $status !== '' ? ' with status "' . htmlspecialchars($status) . '"' : '' ?>.</p></div>
<?php else: ?>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Citizen</th>
            <th>Phone</th>
            <th>Location</th>
            <th>Status</th>
            <th>Rescuer</th>
            <th>Animal</th>
            <th>Date</th>
            <th class="table-actions">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($requests as $rr): ?>
        <tr>
            <td><?= $rr['request_id'] ?></td>
            <td><?= htmlspecialchars($rr['citizen_name']) ?></td>
            <td><?= htmlspecialchars($rr['citizen_phone']) ?></td>
            <td title="<?= htmlspecialchars($rr['location_address']) ?>">
                <?= htmlspecialchars(mb_strimwidth($rr['location_address'], 0, 50, '…')) ?>
            </td>
            <td>
                <?php $stMap = ['open'=>'open','assigned'=>'assigned','closed'=>'closed']; ?>
                <span class="badge badge-<?= $stMap[strtolower($rr['status'])] ?? 'open' ?>"><?= htmlspecialchars($rr['status']) ?></span>
            </td>
            <td><?= $rr['rescuer_first'] ? htmlspecialchars($rr['rescuer_first'] . ' ' . $rr['rescuer_last']) : '<span style="color:var(--muted)">—</span>' ?></td>
            <td><?= $rr['animal_name'] ? htmlspecialchars($rr['animal_name']) : '<span style="color:var(--muted)">—</span>' ?></td>
            <td><?= htmlspecialchars($rr['report_date'] ?? '—') ?></td>
            <td class="table-actions">
                <a href="/sarpams/rescue_requests/edit.php?id=<?= $rr['request_id'] ?>" class="btn btn-sm btn-warning"><i data-lucide="pencil"></i> Edit</a>
                <a href="/sarpams/rescue_requests/delete.php?id=<?= $rr['request_id'] ?>" class="btn btn-sm btn-danger"><i data-lucide="trash-2"></i> Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
