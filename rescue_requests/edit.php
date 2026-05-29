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

$pageTitle = 'Edit Rescue Request #' . $id;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_date      = escape($_POST['report_date'] ?? '');
    $report_time      = escape($_POST['report_time'] ?? '');
    $location_address = escape($_POST['location_address'] ?? '');
    $citizen_name     = escape($_POST['citizen_name'] ?? '');
    $citizen_phone    = escape($_POST['citizen_phone'] ?? '');
    $status           = escape($_POST['status'] ?? 'Open');

    $latitude   = $_POST['latitude']  !== '' ? escape($_POST['latitude'])  : null;
    $longitude  = $_POST['longitude'] !== '' ? escape($_POST['longitude']) : null;
    $rescuer_id = $_POST['rescuer_id'] !== '' ? escape($_POST['rescuer_id']) : null;
    $animal_id  = $_POST['animal_id']  !== '' ? escape($_POST['animal_id'])  : null;

    if ($report_date === '')      $errors[] = 'Report date is required.';
    if ($location_address === '') $errors[] = 'Location address is required.';
    if ($citizen_name === '')     $errors[] = 'Citizen name is required.';
    if ($citizen_phone === '')    $errors[] = 'Citizen phone is required.';

    if (empty($errors)) {
        $lat_sql    = $latitude   !== null ? "'$latitude'"   : 'NULL';
        $lng_sql    = $longitude  !== null ? "'$longitude'"  : 'NULL';
        $resc_sql   = $rescuer_id !== null ? "'$rescuer_id'" : 'NULL';
        $animal_sql = $animal_id  !== null ? "'$animal_id'"  : 'NULL';
        $time_sql   = $report_time !== '' ? "'$report_time'" : 'NULL';

        query("UPDATE RESCUE_REQUEST SET
               report_date='$report_date', report_time=$time_sql, location_address='$location_address',
               latitude=$lat_sql, longitude=$lng_sql, citizen_name='$citizen_name',
               citizen_phone='$citizen_phone', status='$status', rescuer_id=$resc_sql, animal_id=$animal_sql
               WHERE request_id=$id");

        flash('success', 'Rescue request updated successfully.');
        redirect('/sarpams/rescue_requests/index.php');
    }
}

$d = ($_SERVER['REQUEST_METHOD'] === 'POST') ? $_POST : $request;

$rescuers = fetchAll("SELECT rescuer_id, first_name, last_name, zone_area, is_available FROM RESCUER ORDER BY first_name");
$animals  = fetchAll("SELECT animal_id, name, species FROM ANIMAL ORDER BY name");

require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Edit Rescue Request #<?= $id ?></h1>
    <a href="/sarpams/rescue_requests/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Rescue Requests</a>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul style="margin:0;padding-left:1.2em;"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Request Details</div>
    <div class="card-body">
        <form method="post" action="">
            <div class="form-grid">
                <div class="form-group required">
                    <label>Report Date</label>
                    <input type="date" name="report_date" value="<?= htmlspecialchars($d['report_date'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Report Time</label>
                    <input type="time" name="report_time" value="<?= htmlspecialchars($d['report_time'] ?? '') ?>">
                </div>
                <div class="form-group required">
                    <label>Citizen Name</label>
                    <input type="text" name="citizen_name" value="<?= htmlspecialchars($d['citizen_name'] ?? '') ?>" required>
                </div>
                <div class="form-group required">
                    <label>Citizen Phone</label>
                    <input type="text" name="citizen_phone" value="<?= htmlspecialchars($d['citizen_phone'] ?? '') ?>" required>
                </div>
                <div class="form-group full required">
                    <label>Location Address</label>
                    <textarea name="location_address" rows="3" required><?= htmlspecialchars($d['location_address'] ?? '') ?></textarea>
                </div>
                <div class="form-group">
                    <label>Latitude (optional)</label>
                    <input type="number" name="latitude" step="0.000001" value="<?= htmlspecialchars($d['latitude'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Longitude (optional)</label>
                    <input type="number" name="longitude" step="0.000001" value="<?= htmlspecialchars($d['longitude'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <?php foreach (['Open','Assigned','Closed'] as $st): ?>
                            <option value="<?= $st ?>" <?= (($d['status'] ?? 'Open') === $st) ? 'selected' : '' ?>><?= $st ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Assign Rescuer</label>
                    <select name="rescuer_id">
                        <option value="">-- None --</option>
                        <?php foreach ($rescuers as $r): ?>
                            <option value="<?= $r['rescuer_id'] ?>" <?= (($d['rescuer_id'] ?? '') == $r['rescuer_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?>
                                <?= $r['is_available'] ? '' : ' (unavailable)' ?>
                                – <?= htmlspecialchars($r['zone_area']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Link Animal (optional)</label>
                    <select name="animal_id">
                        <option value="">-- None --</option>
                        <?php foreach ($animals as $a): ?>
                            <option value="<?= $a['animal_id'] ?>" <?= (($d['animal_id'] ?? '') == $a['animal_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($a['name']) ?> (<?= htmlspecialchars($a['species']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div style="margin-top:1.5rem;">
                <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Update Request</button>
                <a href="/sarpams/rescue_requests/index.php" class="btn btn-secondary"><i data-lucide="x"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

