<?php
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    flash('danger', 'Invalid shelter ID.');
    redirect('/sarpams/shelters/index.php');
}

$shelter = fetchOne("SELECT * FROM SHELTER WHERE shelter_id=$id");
if (!$shelter) {
    flash('danger', 'Shelter not found.');
    redirect('/sarpams/shelters/index.php');
}

$pageTitle = 'Edit Shelter – ' . htmlspecialchars($shelter['shelter_name']);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shelter_name  = escape($_POST['shelter_name'] ?? '');
    $address       = escape($_POST['address'] ?? '');
    $city          = escape($_POST['city'] ?? '');
    $capacity      = escape($_POST['capacity'] ?? '');
    $contact_phone = escape($_POST['contact_phone'] ?? '');
    $manager_name  = escape($_POST['manager_name'] ?? '');

    if ($shelter_name === '') $errors[] = 'Shelter name is required.';
    if ($city === '')         $errors[] = 'City is required.';
    if ($capacity === '')     $errors[] = 'Capacity is required.';

    if (empty($errors)) {
        query("UPDATE SHELTER SET
               shelter_name='$shelter_name', address='$address', city='$city',
               capacity='$capacity', contact_phone='$contact_phone', manager_name='$manager_name'
               WHERE shelter_id=$id");
        flash('success', "Shelter '$shelter_name' updated.");
        redirect('/sarpams/shelters/index.php');
    }
}

$d = ($_SERVER['REQUEST_METHOD'] === 'POST') ? $_POST : $shelter;

require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Edit Shelter</h1>
    <a href="/sarpams/shelters/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Shelters</a>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul style="margin:0;padding-left:1.2em;"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Shelter Information – #<?= $id ?></div>
    <div class="card-body">
        <form method="post" action="">
            <div class="form-grid">
                <div class="form-group required">
                    <label>Shelter Name</label>
                    <input type="text" name="shelter_name" value="<?= htmlspecialchars($d['shelter_name'] ?? '') ?>" required>
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
                    <label>Capacity</label>
                    <input type="number" name="capacity" min="1" value="<?= htmlspecialchars($d['capacity'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Contact Phone</label>
                    <input type="text" name="contact_phone" value="<?= htmlspecialchars($d['contact_phone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Manager Name</label>
                    <input type="text" name="manager_name" value="<?= htmlspecialchars($d['manager_name'] ?? '') ?>">
                </div>
            </div>
            <div style="margin-top:1.5rem;">
                <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Update Shelter</button>
                <a href="/sarpams/shelters/index.php" class="btn btn-secondary"><i data-lucide="x"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
