<?php
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    flash('danger', 'Invalid cage ID.');
    redirect('/sarpams/cages/index.php');
}

$cage = fetchOne("SELECT * FROM CAGE WHERE cage_id=$id");
if (!$cage) {
    flash('danger', 'Cage not found.');
    redirect('/sarpams/cages/index.php');
}

$pageTitle = 'Edit Cage – ' . htmlspecialchars($cage['cage_number']);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shelter_id    = escape($_POST['shelter_id'] ?? '');
    $cage_number   = escape($_POST['cage_number'] ?? '');
    $size_category = escape($_POST['size_category'] ?? 'Medium');
    $notes         = escape($_POST['notes'] ?? '');

    if ($shelter_id === '')  $errors[] = 'Shelter is required.';
    if ($cage_number === '') $errors[] = 'Cage number is required.';

    if (empty($errors)) {
        query("UPDATE CAGE SET
               shelter_id='$shelter_id', cage_number='$cage_number',
               size_category='$size_category', notes='$notes'
               WHERE cage_id=$id");
        flash('success', "Cage '$cage_number' updated.");
        redirect('/sarpams/cages/index.php');
    }
}

$d = ($_SERVER['REQUEST_METHOD'] === 'POST') ? $_POST : $cage;
$shelters = fetchAll("SELECT shelter_id, shelter_name FROM SHELTER ORDER BY shelter_name");

require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Edit Cage</h1>
    <a href="/sarpams/cages/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Cages</a>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul style="margin:0;padding-left:1.2em;"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Cage Information – #<?= $id ?></div>
    <div class="card-body">
        <form method="post" action="">
            <div class="form-grid">
                <div class="form-group required">
                    <label>Shelter</label>
                    <select name="shelter_id" required>
                        <option value="">-- Select Shelter --</option>
                        <?php foreach ($shelters as $sh): ?>
                            <option value="<?= $sh['shelter_id'] ?>" <?= (($d['shelter_id'] ?? '') == $sh['shelter_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sh['shelter_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group required">
                    <label>Cage Number</label>
                    <input type="text" name="cage_number" value="<?= htmlspecialchars($d['cage_number'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Size Category</label>
                    <select name="size_category">
                        <?php foreach (['Small','Medium','Large'] as $sz): ?>
                            <option value="<?= $sz ?>" <?= (($d['size_category'] ?? 'Medium') === $sz) ? 'selected' : '' ?>><?= $sz ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group full">
                    <label>Notes</label>
                    <textarea name="notes" rows="2"><?= htmlspecialchars($d['notes'] ?? '') ?></textarea>
                </div>
            </div>
            <div style="margin-top:1.5rem;">
                <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Update Cage</button>
                <a href="/sarpams/cages/index.php" class="btn btn-secondary"><i data-lucide="x"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
