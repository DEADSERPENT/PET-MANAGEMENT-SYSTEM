<?php
require_once __DIR__ . '/../config/db.php';
$pageTitle = 'Add Rescuer';
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
        query("INSERT INTO RESCUER (first_name, last_name, phone, email, zone_area, certification_level, is_available, join_date)
               VALUES ('$first_name','$last_name','$phone','$email','$zone_area','$certification_level',$is_available,$join_sql)");
        flash('success', "Rescuer '$first_name $last_name' added successfully.");
        redirect('/sarpams/rescuers/index.php');
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Add Rescuer</h1>
    <a href="/sarpams/rescuers/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Rescuers</a>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul style="margin:0;padding-left:1.2em;"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Rescuer Information</div>
    <div class="card-body">
        <form method="post" action="">
            <div class="form-grid">
                <div class="form-group required">
                    <label>First Name</label>
                    <input type="text" name="first_name" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
                </div>
                <div class="form-group required">
                    <label>Last Name</label>
                    <input type="text" name="last_name" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
                </div>
                <div class="form-group required">
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="form-group required">
                    <label>Zone Area</label>
                    <input type="text" name="zone_area" value="<?= htmlspecialchars($_POST['zone_area'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Certification Level</label>
                    <select name="certification_level">
                        <?php foreach (['Basic','Intermediate','Advanced'] as $cl): ?>
                            <option value="<?= $cl ?>" <?= (($_POST['certification_level'] ?? 'Basic') === $cl) ? 'selected' : '' ?>><?= $cl ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Join Date</label>
                    <input type="date" name="join_date" value="<?= htmlspecialchars($_POST['join_date'] ?? date('Y-m-d')) ?>">
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:.5rem;">
                        <input type="checkbox" name="is_available" <?= isset($_POST['is_available']) ? 'checked' : 'checked' ?>>
                        Available
                    </label>
                </div>
            </div>
            <div style="margin-top:1.5rem;">
                <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Save Rescuer</button>
                <a href="/sarpams/rescuers/index.php" class="btn btn-secondary"><i data-lucide="x"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

