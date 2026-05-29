<?php
require_once __DIR__ . '/../config/db.php';
$pageTitle = 'New Adoption';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $animal_id       = escape($_POST['animal_id'] ?? '');
    $applicant_id    = escape($_POST['applicant_id'] ?? '');
    $officer_id      = escape($_POST['officer_id'] ?? '');
    $application_date = escape($_POST['application_date'] ?? '');
    $status          = escape($_POST['status'] ?? 'Pending');
    $agreement_signed = isset($_POST['agreement_signed']) ? 1 : 0;
    $notes           = escape($_POST['notes'] ?? '');

    if ($animal_id === '')    $errors[] = 'Animal is required.';
    if ($applicant_id === '') $errors[] = 'Applicant is required.';
    if ($officer_id === '')   $errors[] = 'Processing officer is required.';
    if ($application_date === '') $errors[] = 'Application date is required.';

    if (empty($errors)) {
        query("INSERT INTO ADOPTION (animal_id, applicant_id, officer_id, application_date, status, agreement_signed, notes)
               VALUES ('$animal_id','$applicant_id','$officer_id','$application_date','$status',$agreement_signed,'$notes')");
        flash('success', 'Adoption application created successfully.');
        redirect('/sarpams/adoptions/index.php');
    }
}

// Only Healthy animals not already in Approved/Completed adoption
$animals = fetchAll("
    SELECT a.animal_id, a.name, a.species
    FROM ANIMAL a
    WHERE a.health_status = 'Healthy'
    AND a.animal_id NOT IN (
        SELECT animal_id FROM ADOPTION WHERE status IN ('Approved','Completed')
    )
    ORDER BY a.name
");

$applicants = fetchAll("SELECT applicant_id, first_name, last_name, city FROM ADOPTION_APPLICANT ORDER BY first_name");
$officers   = fetchAll("SELECT rescuer_id, first_name, last_name FROM RESCUER ORDER BY first_name");

require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title"><i data-lucide="heart-handshake"></i> New Adoption Application</h1>
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
                    <label>Animal <small>(Healthy, not yet approved/completed)</small></label>
                    <select name="animal_id" required>
                        <option value="">-- Select Animal --</option>
                        <?php foreach ($animals as $a): ?>
                            <option value="<?= $a['animal_id'] ?>" <?= (($_POST['animal_id'] ?? '') == $a['animal_id']) ? 'selected' : '' ?>>
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
                            <option value="<?= $ap['applicant_id'] ?>" <?= (($_POST['applicant_id'] ?? '') == $ap['applicant_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ap['first_name'] . ' ' . $ap['last_name']) ?> – <?= htmlspecialchars($ap['city']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group required">
                    <label>Processing Officer (Rescuer)</label>
                    <select name="officer_id" required>
                        <option value="">-- Select Officer --</option>
                        <?php foreach ($officers as $o): ?>
                            <option value="<?= $o['rescuer_id'] ?>" <?= (($_POST['officer_id'] ?? '') == $o['rescuer_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($o['first_name'] . ' ' . $o['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group required">
                    <label>Application Date</label>
                    <input type="date" name="application_date" value="<?= htmlspecialchars($_POST['application_date'] ?? date('Y-m-d')) ?>" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <?php foreach (['Pending','Approved','Rejected','Completed'] as $st): ?>
                            <option value="<?= $st ?>" <?= (($_POST['status'] ?? 'Pending') === $st) ? 'selected' : '' ?>><?= $st ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:.5rem;">
                        <input type="checkbox" name="agreement_signed" <?= isset($_POST['agreement_signed']) ? 'checked' : '' ?>>
                        Agreement Signed
                    </label>
                </div>
                <div class="form-group full">
                    <label>Notes</label>
                    <textarea name="notes" rows="3"><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
                </div>
            </div>
            <div style="margin-top:1.5rem;">
                <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Save Adoption</button>
                <a href="/sarpams/adoptions/index.php" class="btn btn-secondary"><i data-lucide="x"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
