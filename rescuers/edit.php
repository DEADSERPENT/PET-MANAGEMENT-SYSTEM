<?php
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    flash('danger', 'Invalid rescuer ID.');
    redirect('/sarpams/rescuers/index.php');
}

$rescuer = fetchOne("SELECT * FROM RESCUER WHERE rescuer_id=$id");
if (!$rescuer) {
    flash('danger', 'Rescuer not found.');
    redirect('/sarpams/rescuers/index.php');
}

$pageTitle = 'Edit Rescuer – ' . htmlspecialchars($rescuer['first_name'] . ' ' . $rescuer['last_name']);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name          = escape($_POST['first_name'] ?? '');
    $last_name           = escape($_POST['last_name'] ?? '');
    $phone               = escape($_POST['phone'] ?? '');
    $email               = escape($_POST['email'] ?? '');
    $zone_area           = escape($_POST['zone_area'] ?? '');
    $certification_level = escape($_POST['certification_level'] ?? 'Basic');
    $join_date           = escape($_POST['join_date'] ?? '');
    $is_available        = isset($_POST['is_available']) ? 1 : 0;

    if ($first_name === '') $errors[] = 'First name is required.';
    if ($last_name === '')  $errors[] = 'Last name is required.';
    if ($phone === '')      $errors[] = 'Phone is required.';
    if ($zone_area === '')  $errors[] = 'Zone area is required.';

    if (empty($errors)) {
        $join_sql = $join_date !== '' ? "'$join_date'" : 'NULL';
        query("UPDATE RESCUER SET
               first_name='$first_name', last_name='$last_name', phone='$phone', email='$email',
               zone_area='$zone_area', certification_level='$certification_level',
               is_available=$is_available, join_date=$join_sql
               WHERE rescuer_id=$id");
        flash('success', "Rescuer '$first_name $last_name' updated.");
        redirect('/sarpams/rescuers/index.php');
    }
}

$d = ($_SERVER['REQUEST_METHOD'] === 'POST') ? $_POST : $rescuer;

require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Edit Rescuer</h1>
    <a href="/sarpams/rescuers/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Rescuers</a>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul style="margin:0;padding-left:1.2em;"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Rescuer Information – #<?= $id ?></div>
    <div class="card-body">
        <form method="post" action="">
            <div class="form-grid">
                <div class="form-group required">
                    <label>First Name</label>
                    <input type="text" name="first_name" value="<?= htmlspecialchars($d['first_name'] ?? '') ?>" required>
                </div>
                <div class="form-group required">
                    <label>Last Name</label>
                    <input type="text" name="last_name" value="<?= htmlspecialchars($d['last_name'] ?? '') ?>" required>
                </div>
                <div class="form-group required">
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($d['phone'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($d['email'] ?? '') ?>">
                </div>
                <div class="form-group required">
                    <label>Zone Area</label>
                    <input type="text" name="zone_area" value="<?= htmlspecialchars($d['zone_area'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Certification Level</label>
                    <select name="certification_level">
                        <?php foreach (['Basic','Intermediate','Advanced'] as $cl): ?>
                            <option value="<?= $cl ?>" <?= (($d['certification_level'] ?? 'Basic') === $cl) ? 'selected' : '' ?>><?= $cl ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Join Date</label>
                    <input type="date" name="join_date" value="<?= htmlspecialchars($d['join_date'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:.5rem;">
                        <input type="checkbox" name="is_available" <?= ($d['is_available'] ?? 0) ? 'checked' : '' ?>>
                        Available
                    </label>
                </div>
            </div>
            <div style="margin-top:1.5rem;">
                <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Update Rescuer</button>
                <a href="/sarpams/rescuers/index.php" class="btn btn-secondary"><i data-lucide="x"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
