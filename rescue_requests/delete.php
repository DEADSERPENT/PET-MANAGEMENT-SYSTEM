<?php
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    flash('danger', 'Invalid request ID.');
    redirect('/sarpams/rescue_requests/index.php');
}

$request = fetchOne("SELECT * FROM RESCUE_REQUEST WHERE request_id=$id");
if (!$request) {
    flash('danger', 'Rescue request not found.');
    redirect('/sarpams/rescue_requests/index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    query("DELETE FROM RESCUE_REQUEST WHERE request_id=$id");
    flash('success', 'Rescue request #' . $id . ' deleted.');
    redirect('/sarpams/rescue_requests/index.php');
}

$pageTitle = 'Delete Rescue Request';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Delete Rescue Request</h1>
    <a href="/sarpams/rescue_requests/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Rescue Requests</a>
</div>

<div class="card">
    <div class="card-header"><i data-lucide="triangle-alert"></i> Confirm Deletion</div>
    <div class="card-body">
        <p>Are you sure you want to delete this rescue request? This action <strong>cannot be undone</strong>.</p>
        <table class="table" style="max-width:500px;margin-bottom:1.5rem;">
            <tr><th>ID</th><td><?= $request['request_id'] ?></td></tr>
            <tr><th>Citizen</th><td><?= htmlspecialchars($request['citizen_name']) ?></td></tr>
            <tr><th>Phone</th><td><?= htmlspecialchars($request['citizen_phone']) ?></td></tr>
            <tr><th>Location</th><td><?= htmlspecialchars($request['location_address']) ?></td></tr>
            <tr><th>Status</th><td><?= htmlspecialchars($request['status']) ?></td></tr>
            <tr><th>Date</th><td><?= htmlspecialchars($request['report_date'] ?? '—') ?></td></tr>
        </table>
        <form method="post" action="">
            <button type="submit" class="btn btn-danger">Yes, Delete Request</button>
            <a href="/sarpams/rescue_requests/index.php" class="btn btn-secondary"><i data-lucide="x"></i> Cancel</a>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
