<?php
require_once __DIR__ . '/../config/db.php';
$pageTitle = 'Add Veterinarian';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name     = escape($_POST['first_name'] ?? '');
    $last_name      = escape($_POST['last_name'] ?? '');
    $specialisation = escape($_POST['specialisation'] ?? '');
    $phone          = escape($_POST['phone'] ?? '');
    $email          = escape($_POST['email'] ?? '');
    $license_no     = escape($_POST['license_no'] ?? '');

    if ($first_name === '') $errors[] = 'First name is required.';
    if ($last_name === '')  $errors[] = 'Last name is required.';
    if ($phone === '')      $errors[] = 'Phone is required.';

    if (empty($errors)) {
        query("INSERT INTO VETERINARIAN (first_name, last_name, specialisation, phone, email, license_no)
               VALUES ('$first_name','$last_name','$specialisation','$phone','$email','$license_no')");
        flash('success', "Veterinarian '$first_name $last_name' added successfully.");
        redirect('/sarpams/veterinarians/index.php');
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Add Veterinarian</h1>
    <a href="/sarpams/veterinarians/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Veterinarians</a>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul style="margin:0;padding-left:1.2em;"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Veterinarian Information</div>
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
                <div class="form-group">
                    <label>Specialisation</label>
                    <input type="text" name="specialisation" value="<?= htmlspecialchars($_POST['specialisation'] ?? '') ?>">
                </div>
                <div class="form-group required">
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>License No.</label>
                    <input type="text" name="license_no" value="<?= htmlspecialchars($_POST['license_no'] ?? '') ?>">
                </div>
            </div>
            <div style="margin-top:1.5rem;">
                <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Save Veterinarian</button>
                <a href="/sarpams/veterinarians/index.php" class="btn btn-secondary"><i data-lucide="x"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

