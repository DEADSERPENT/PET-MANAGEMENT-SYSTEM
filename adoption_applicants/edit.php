<?php
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    flash('danger', 'Invalid applicant ID.');
    redirect('/sarpams/adoption_applicants/index.php');
}

$applicant = fetchOne("SELECT * FROM ADOPTION_APPLICANT WHERE applicant_id=$id");
if (!$applicant) {
    flash('danger', 'Applicant not found.');
    redirect('/sarpams/adoption_applicants/index.php');
}

$pageTitle = 'Edit Applicant – ' . htmlspecialchars($applicant['first_name'] . ' ' . $applicant['last_name']);
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
        query("UPDATE ADOPTION_APPLICANT SET
               first_name='$first_name', last_name='$last_name', dob=$dob_sql,
               address='$address', city='$city', phone='$phone', email='$email',
               occupation='$occupation', has_previous_pets=$has_previous_pets, living_situation='$living_situation'
               WHERE applicant_id=$id");
        flash('success', "Applicant '$first_name $last_name' updated.");
        redirect('/sarpams/adoption_applicants/index.php');
    }
}

$d = ($_SERVER['REQUEST_METHOD'] === 'POST') ? $_POST : $applicant;

require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Edit Applicant</h1>
    <a href="/sarpams/adoption_applicants/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Applicants</a>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul style="margin:0;padding-left:1.2em;"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Applicant Information – #<?= $id ?></div>
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
                <div class="form-group">
                    <label>Date of Birth</label>
                    <input type="date" name="dob" value="<?= htmlspecialchars($d['dob'] ?? '') ?>">
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
                    <label>Occupation</label>
                    <input type="text" name="occupation" value="<?= htmlspecialchars($d['occupation'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Living Situation</label>
                    <input type="text" name="living_situation" value="<?= htmlspecialchars($d['living_situation'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:.5rem;">
                        <input type="checkbox" name="has_previous_pets" <?= ($d['has_previous_pets'] ?? 0) ? 'checked' : '' ?>>
                        Has Previous Pets
                    </label>
                </div>
            </div>
            <div style="margin-top:1.5rem;">
                <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Update Applicant</button>
                <a href="/sarpams/adoption_applicants/index.php" class="btn btn-secondary"><i data-lucide="x"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
