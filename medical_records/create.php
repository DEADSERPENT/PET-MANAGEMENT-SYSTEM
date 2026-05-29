<?php
require_once __DIR__ . '/../config/db.php';
$pageTitle = 'Add Medical Record';

$preselect_animal = isset($_GET['animal_id']) ? (int)$_GET['animal_id'] : 0;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $animal_id        = escape($_POST['animal_id'] ?? '');
    $vet_id           = escape($_POST['vet_id'] ?? '');
    $exam_date        = escape($_POST['exam_date'] ?? '');
    $diagnosis        = escape($_POST['diagnosis'] ?? '');
    $treatment        = escape($_POST['treatment'] ?? '');
    $medication       = escape($_POST['medication'] ?? '');
    $notes            = escape($_POST['notes'] ?? '');
    $next_checkup_date = $_POST['next_checkup_date'] !== '' ? escape($_POST['next_checkup_date']) : null;

    if ($animal_id === '') $errors[] = 'Animal is required.';
    if ($vet_id === '')    $errors[] = 'Veterinarian is required.';
    if ($exam_date === '') $errors[] = 'Exam date is required.';
    if ($diagnosis === '') $errors[] = 'Diagnosis is required.';

    if (empty($errors)) {
        $next_sql = $next_checkup_date !== null ? "'$next_checkup_date'" : 'NULL';
        query("INSERT INTO MEDICAL_RECORD (animal_id, vet_id, exam_date, diagnosis, treatment, medication, next_checkup_date, notes)
               VALUES ('$animal_id','$vet_id','$exam_date','$diagnosis','$treatment','$medication',$next_sql,'$notes')");
        flash('success', 'Medical record added successfully.');

        $back_animal = (int)$animal_id;
        redirect("/sarpams/animals/view.php?id=$back_animal");
    }
}

$animals = fetchAll("SELECT animal_id, name, species FROM ANIMAL ORDER BY name");
$vets    = fetchAll("SELECT vet_id, first_name, last_name, specialisation FROM VETERINARIAN ORDER BY first_name");

require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Add Medical Record</h1>
    <a href="/sarpams/medical_records/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Records</a>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul style="margin:0;padding-left:1.2em;"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Record Information</div>
    <div class="card-body">
        <form method="post" action="">
            <div class="form-grid">
                <div class="form-group required">
                    <label>Animal</label>
                    <select name="animal_id" required>
                        <option value="">-- Select Animal --</option>
                        <?php
                        $sel_animal = $_POST['animal_id'] ?? ($preselect_animal ?: '');
                        foreach ($animals as $a):
                        ?>
                            <option value="<?= $a['animal_id'] ?>" <?= ($sel_animal == $a['animal_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($a['name']) ?> (<?= htmlspecialchars($a['species']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group required">
                    <label>Veterinarian</label>
                    <select name="vet_id" required>
                        <option value="">-- Select Vet --</option>
                        <?php foreach ($vets as $v): ?>
                            <option value="<?= $v['vet_id'] ?>" <?= (($_POST['vet_id'] ?? '') == $v['vet_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($v['first_name'] . ' ' . $v['last_name']) ?>
                                <?= $v['specialisation'] ? ' – ' . htmlspecialchars($v['specialisation']) : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group required">
                    <label>Exam Date</label>
                    <input type="date" name="exam_date" value="<?= htmlspecialchars($_POST['exam_date'] ?? date('Y-m-d')) ?>" required>
                </div>
                <div class="form-group">
                    <label>Medication</label>
                    <input type="text" name="medication" value="<?= htmlspecialchars($_POST['medication'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Next Checkup Date</label>
                    <input type="date" name="next_checkup_date" value="<?= htmlspecialchars($_POST['next_checkup_date'] ?? '') ?>">
                </div>
                <div class="form-group full required">
                    <label>Diagnosis</label>
                    <textarea name="diagnosis" rows="3" required><?= htmlspecialchars($_POST['diagnosis'] ?? '') ?></textarea>
                </div>
                <div class="form-group full">
                    <label>Treatment</label>
                    <textarea name="treatment" rows="3"><?= htmlspecialchars($_POST['treatment'] ?? '') ?></textarea>
                </div>
                <div class="form-group full">
                    <label>Notes</label>
                    <textarea name="notes" rows="2"><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
                </div>
            </div>
            <div style="margin-top:1.5rem;">
                <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Save Record</button>
                <a href="/sarpams/medical_records/index.php" class="btn btn-secondary"><i data-lucide="x"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
