<?php
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    flash('danger', 'Invalid adoption ID.');
    redirect('/sarpams/adoptions/index.php');
}

$adoption = fetchOne("SELECT * FROM ADOPTION WHERE adoption_id=$id");
if (!$adoption) {
    flash('danger', 'Adoption not found.');
    redirect('/sarpams/adoptions/index.php');
}

$pageTitle = 'Edit Adoption #' . $id;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $animal_id        = escape($_POST['animal_id'] ?? '');
    $applicant_id     = escape($_POST['applicant_id'] ?? '');
    $officer_id       = escape($_POST['officer_id'] ?? '');
    $application_date = escape($_POST['application_date'] ?? '');
    $approval_date    = $_POST['approval_date'] !== '' ? escape($_POST['approval_date']) : null;
    $adoption_date    = $_POST['adoption_date'] !== '' ? escape($_POST['adoption_date']) : null;
    $status           = escape($_POST['status'] ?? 'Pending');
    $agreement_signed = isset($_POST['agreement_signed']) ? 1 : 0;
    $notes            = escape($_POST['notes'] ?? '');

    if ($animal_id === '')        $errors[] = 'Animal is required.';
    if ($applicant_id === '')     $errors[] = 'Applicant is required.';
    if ($officer_id === '')       $errors[] = 'Processing officer is required.';
    if ($application_date === '') $errors[] = 'Application date is required.';

    if (empty($errors)) {
        if ($status === 'Completed' && $adoption_date === null) {
            $adoption_date = date('Y-m-d');
        }

        $approval_sql = $approval_date !== null ? "'$approval_date'" : 'NULL';
        $adoption_sql = $adoption_date !== null ? "'$adoption_date'" : 'NULL';

        query("UPDATE ADOPTION SET
               animal_id='$animal_id', applicant_id='$applicant_id', officer_id='$officer_id',
               application_date='$application_date', approval_date=$approval_sql,
               adoption_date=$adoption_sql, status='$status',
               agreement_signed=$agreement_signed, notes='$notes'
               WHERE adoption_id=$id");

        flash('success', "Adoption #$id updated successfully.");
        redirect('/sarpams/adoptions/index.php');
    }
}

$d = ($_SERVER['REQUEST_METHOD'] === 'POST') ? $_POST : $adoption;

$animals    = fetchAll("SELECT animal_id, name, species FROM ANIMAL ORDER BY name");
$applicants = fetchAll("SELECT applicant_id, first_name, last_name, city FROM ADOPTION_APPLICANT ORDER BY first_name");
$officers   = fetchAll("SELECT rescuer_id, first_name, last_name FROM RESCUER ORDER BY first_name");

require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Edit Adoption #<?= $id ?></h1>
    <a href="/sarpams/adoptions/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Adoptions</a>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul style="margin:0;padding-left:1.2em;"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header"><i data-lucide="heart-handshake"></i> Adoption Details</div>
    <div class="card-body">
        <form method="post" action="">
            <div class="form-grid">
                <div class="form-group required">
                    <label>Animal</label>
                    <select name="animal_id" required>
                        <option value="">-- Select Animal --</option>
                        <?php foreach ($animals as $a): ?>
                            <option value="<?= $a['animal_id'] ?>" <?= (($d['animal_id'] ?? '') == $a['animal_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($a['name']) ?> (<?= htmlspecialchars($a['species']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group required">
                    <label>Applicant</label>
                    <select name="applicant_id" required>
                        <option value="">-- Select Applicant --</option>
                        <?php foreach ($applicants as $ap): ?>
                            <option value="<?= $ap['applicant_id'] ?>" <?= (($d['applicant_id'] ?? '') == $ap['applicant_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ap['first_name'] . ' ' . $ap['last_name']) ?> – <?= htmlspecialchars($ap['city']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Officer (Rescuer)</label>
                    <select name="officer_id">
                        <option value="">-- None --</option>
                        <?php foreach ($officers as $o): ?>
                            <option value="<?= $o['rescuer_id'] ?>" <?= (($d['officer_id'] ?? '') == $o['rescuer_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($o['first_name'] . ' ' . $o['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group required">
                    <label>Application Date</label>
                    <input type="date" name="application_date" value="<?= htmlspecialchars($d['application_date'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Approval Date</label>
                    <input type="date" name="approval_date" value="<?= htmlspecialchars($d['approval_date'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Adoption Date <small>(auto-filled if Completed)</small></label>
                    <input type="date" name="adoption_date" value="<?= htmlspecialchars($d['adoption_date'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <?php foreach (['Pending','Approved','Rejected','Completed'] as $st): ?>
                            <option value="<?= $st ?>" <?= (($d['status'] ?? 'Pending') === $st) ? 'selected' : '' ?>><?= $st ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:.5rem;">
                        <input type="checkbox" name="agreement_signed" <?= ($d['agreement_signed'] ?? 0) ? 'checked' : '' ?>>
                        Agreement Signed
                    </label>
                </div>
                <div class="form-group full">
                    <label>Notes</label>
                    <textarea name="notes" rows="3"><?= htmlspecialchars($d['notes'] ?? '') ?></textarea>
                </div>
            </div>
            <div style="margin-top:1.5rem;">
                <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Update Adoption</button>
                <a href="/sarpams/adoptions/index.php" class="btn btn-secondary"><i data-lucide="x"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

