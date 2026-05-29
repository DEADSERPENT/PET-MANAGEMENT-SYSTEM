<?php
require_once __DIR__ . '/../config/db.php';
$pageTitle = 'Add Adoption Applicant';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name        = escape($_POST['first_name'] ?? '');
    $last_name         = escape($_POST['last_name'] ?? '');
    $dob               = $_POST['dob'] !== '' ? escape($_POST['dob']) : null;
    $address           = escape($_POST['address'] ?? '');
    $city              = escape($_POST['city'] ?? '');
    $phone             = escape($_POST['phone'] ?? '');
    $email             = escape($_POST['email'] ?? '');
    $occupation        = escape($_POST['occupation'] ?? '');
    $living_situation  = escape($_POST['living_situation'] ?? '');
    $has_previous_pets = isset($_POST['has_previous_pets']) ? 1 : 0;

    if ($first_name === '') $errors[] = 'First name is required.';
    if ($last_name === '')  $errors[] = 'Last name is required.';
    if ($phone === '')      $errors[] = 'Phone is required.';
    if ($city === '')       $errors[] = 'City is required.';

    if (empty($errors)) {
        $dob_sql = $dob !== null ? "'$dob'" : 'NULL';
        query("INSERT INTO ADOPTION_APPLICANT (first_name, last_name, dob, address, city, phone, email, occupation, has_previous_pets, living_situation)
               VALUES ('$first_name','$last_name',$dob_sql,'$address','$city','$phone','$email','$occupation',$has_previous_pets,'$living_situation')");
        flash('success', "Applicant '$first_name $last_name' added successfully.");
        redirect('/sarpams/adoption_applicants/index.php');
    }
}

require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Add Adoption Applicant</h1>
    <a href="/sarpams/adoption_applicants/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Applicants</a>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul style="margin:0;padding-left:1.2em;"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Applicant Information</div>
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
                    <label>Date of Birth</label>
                    <input type="date" name="dob" value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>">
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
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Occupation</label>
                    <input type="text" name="occupation" value="<?= htmlspecialchars($_POST['occupation'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Living Situation</label>
                    <input type="text" name="living_situation" placeholder="e.g. Own House, Renting, With Family" value="<?= htmlspecialchars($_POST['living_situation'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:.5rem;">
                        <input type="checkbox" name="has_previous_pets" <?= isset($_POST['has_previous_pets']) ? 'checked' : '' ?>>
                        Has Previous Pets
                    </label>
                </div>
            </div>
            <div style="margin-top:1.5rem;">
                <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Save Applicant</button>
                <a href="/sarpams/adoption_applicants/index.php" class="btn btn-secondary"><i data-lucide="x"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
