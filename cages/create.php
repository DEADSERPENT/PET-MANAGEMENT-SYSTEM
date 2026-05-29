<?php
require_once __DIR__ . '/../config/db.php';
$pageTitle = 'Add Cage';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shelter_id    = escape($_POST['shelter_id'] ?? '');
    $cage_number   = escape($_POST['cage_number'] ?? '');
    $size_category = escape($_POST['size_category'] ?? 'Medium');
    $notes         = escape($_POST['notes'] ?? '');

    if ($shelter_id === '')  $errors[] = 'Shelter is required.';
    if ($cage_number === '') $errors[] = 'Cage number is required.';

    if (empty($errors)) {
        query("INSERT INTO CAGE (shelter_id, cage_number, size_category, is_occupied, notes)
               VALUES ('$shelter_id','$cage_number','$size_category',0,'$notes')");
        flash('success', "Cage '$cage_number' added successfully.");
        redirect('/sarpams/cages/index.php');
    }
}

$shelters = fetchAll("SELECT shelter_id, shelter_name FROM SHELTER ORDER BY shelter_name");

require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Add Cage</h1>
    <a href="/sarpams/cages/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Cages</a>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul style="margin:0;padding-left:1.2em;"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Cage Information</div>
    <div class="card-body">
        <form method="post" action="">
            <div class="form-grid">
                <div class="form-group required">
                    <label>Shelter</label>
                    <select name="shelter_id" required>
                        <option value="">-- Select Shelter --</option>
                        <?php foreach ($shelters as $sh): ?>
                            <option value="<?= $sh['shelter_id'] ?>" <?= (($_POST['shelter_id'] ?? '') == $sh['shelter_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sh['shelter_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group required">
                    <label>Cage Number</label>
                    <input type="text" name="cage_number" value="<?= htmlspecialchars($_POST['cage_number'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Size Category</label>
                    <select name="size_category">
                        <?php foreach (['Small','Medium','Large'] as $sz): ?>
                            <option value="<?= $sz ?>" <?= (($_POST['size_category'] ?? 'Medium') === $sz) ? 'selected' : '' ?>><?= $sz ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group full">
                    <label>Notes</label>
                    <textarea name="notes" rows="2"><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
                </div>
            </div>
            <div style="margin-top:1.5rem;">
                <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Save Cage</button>
                <a href="/sarpams/cages/index.php" class="btn btn-secondary"><i data-lucide="x"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
