<?php
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    flash('danger', 'Invalid foster family ID.');
    redirect('/sarpams/foster_families/index.php');
}

$family = fetchOne("SELECT * FROM FOSTER_FAMILY WHERE foster_id=$id");
if (!$family) {
    flash('danger', 'Foster family not found.');
    redirect('/sarpams/foster_families/index.php');
}

$pageTitle = 'Edit Foster Family – ' . htmlspecialchars($family['family_name']);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $family_name    = escape($_POST['family_name'] ?? '');
    $address        = escape($_POST['address'] ?? '');
    $city           = escape($_POST['city'] ?? '');
    $phone          = escape($_POST['phone'] ?? '');
    $email          = escape($_POST['email'] ?? '');
    $house_type     = escape($_POST['house_type'] ?? '');
    $has_other_pets = isset($_POST['has_other_pets']) ? 1 : 0;
    $is_approved    = isset($_POST['is_approved']) ? 1 : 0;
    $approval_date  = $_POST['approval_date'] !== '' ? escape($_POST['approval_date']) : null;

    if ($family_name === '') $errors[] = 'Family name is required.';
    if ($city === '')        $errors[] = 'City is required.';
    if ($phone === '')       $errors[] = 'Phone is required.';

    if (empty($errors)) {
        $appr_sql = $approval_date !== null ? "'$approval_date'" : 'NULL';
        query("UPDATE FOSTER_FAMILY SET
               family_name='$family_name', address='$address', city='$city', phone='$phone',
               email='$email', house_type='$house_type', has_other_pets=$has_other_pets,
               is_approved=$is_approved, approval_date=$appr_sql
               WHERE foster_id=$id");
        flash('success', "Foster family '$family_name' updated.");
        redirect('/sarpams/foster_families/index.php');
    }
}

$d = ($_SERVER['REQUEST_METHOD'] === 'POST') ? $_POST : $family;

require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Edit Foster Family</h1>
    <a href="/sarpams/foster_families/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Foster Families</a>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul style="margin:0;padding-left:1.2em;"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Foster Family Information – #<?= $id ?></div>
    <div class="card-body">
        <form method="post" action="">
            <div class="form-grid">
                <div class="form-group required">
                    <label>Family Name</label>
                    <input type="text" name="family_name" value="<?= htmlspecialchars($d['family_name'] ?? '') ?>" required>
                </div>
                <div class="form-group required">
                    <label>City</label>
                    <input type="text" name="city" value="<?= htmlspecialchars($d['city'] ?? '') ?>" required>
                </div>
                <div class="form-group full">
                    <label>Address</label>
                    <textarea name="address" rows="2"><?= htmlspecialchars($d['address'] ?? '') ?></textarea>
                </div>
                <div class="form-group required">
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($d['phone'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($d['email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>House Type</label>
                    <input type="text" name="house_type" value="<?= htmlspecialchars($d['house_type'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Approval Date (optional)</label>
                    <input type="date" name="approval_date" value="<?= htmlspecialchars($d['approval_date'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:.5rem;">
                        <input type="checkbox" name="has_other_pets" <?= ($d['has_other_pets'] ?? 0) ? 'checked' : '' ?>>
                        Has Other Pets
                    </label>
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:.5rem;">
                        <input type="checkbox" name="is_approved" <?= ($d['is_approved'] ?? 0) ? 'checked' : '' ?>>
                        Approved
                    </label>
                </div>
            </div>
            <div style="margin-top:1.5rem;">
                <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Update Foster Family</button>
                <a href="/sarpams/foster_families/index.php" class="btn btn-secondary"><i data-lucide="x"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

