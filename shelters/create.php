<?php
require_once __DIR__ . '/../config/db.php';
$pageTitle = 'Add Shelter';
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
        query("INSERT INTO SHELTER (shelter_name, address, city, capacity, contact_phone, manager_name)
               VALUES ('$shelter_name','$address','$city','$capacity','$contact_phone','$manager_name')");
        flash('success', "Shelter '$shelter_name' added successfully.");
        redirect('/sarpams/shelters/index.php');
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Add Shelter</h1>
    <a href="/sarpams/shelters/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Shelters</a>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul style="margin:0;padding-left:1.2em;"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Shelter Information</div>
    <div class="card-body">
        <form method="post" action="">
            <div class="form-grid">
                <div class="form-group required">
                    <label>Shelter Name</label>
                    <input type="text" name="shelter_name" value="<?= htmlspecialchars($_POST['shelter_name'] ?? '') ?>" required>
                </div>
                <div class="form-group required">
                    <label>City</label>
                    <input type="text" name="city" value="<?= htmlspecialchars($_POST['city'] ?? '') ?>" required>
                </div>
                <div class="form-group full">
                    <label>Address</label>
                    <textarea name="address" rows="2"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                </div>
                <div class="form-group required">
                    <label>Capacity</label>
                    <input type="number" name="capacity" min="1" value="<?= htmlspecialchars($_POST['capacity'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Contact Phone</label>
                    <input type="text" name="contact_phone" value="<?= htmlspecialchars($_POST['contact_phone'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Manager Name</label>
                    <input type="text" name="manager_name" value="<?= htmlspecialchars($_POST['manager_name'] ?? '') ?>">
                </div>
            </div>
            <div style="margin-top:1.5rem;">
                <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Save Shelter</button>
                <a href="/sarpams/shelters/index.php" class="btn btn-secondary"><i data-lucide="x"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
