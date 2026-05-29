<?php
require_once __DIR__ . '/../config/db.php';
$pageTitle = 'Add Animal';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = escape($_POST['name'] ?? '');
    $species     = escape($_POST['species'] ?? '');
    $breed       = escape($_POST['breed'] ?? '');
    $colour      = escape($_POST['colour'] ?? '');
    $intake_date = escape($_POST['intake_date'] ?? '');
    $health_status = escape($_POST['health_status'] ?? 'Unknown');
    $microchip_no  = escape($_POST['microchip_no'] ?? '');
    $sex           = escape($_POST['sex'] ?? 'U');
    $is_vaccinated = isset($_POST['is_vaccinated']) ? 1 : 0;
    $is_neutered   = isset($_POST['is_neutered']) ? 1 : 0;

    $age_years  = $_POST['age_years'] !== '' ? escape($_POST['age_years']) : null;
    $weight_kg  = $_POST['weight_kg'] !== '' ? escape($_POST['weight_kg']) : null;
    $cage_id    = $_POST['cage_id'] !== '' ? escape($_POST['cage_id']) : null;

    if ($name === '')        $errors[] = 'Name is required.';
    if ($species === '')     $errors[] = 'Species is required.';
    if ($colour === '')      $errors[] = 'Colour is required.';
    if ($intake_date === '') $errors[] = 'Intake date is required.';

    if (empty($errors)) {
        $age_sql    = $age_years !== null  ? "'$age_years'"  : 'NULL';
        $weight_sql = $weight_kg !== null  ? "'$weight_kg'"  : 'NULL';
        $cage_sql   = $cage_id   !== null  ? "'$cage_id'"    : 'NULL';

        query("INSERT INTO ANIMAL (name, species, breed, age_years, sex, colour, weight_kg, microchip_no, intake_date, health_status, is_vaccinated, is_neutered, cage_id)
               VALUES ('$name','$species','$breed',$age_sql,'$sex','$colour',$weight_sql,'$microchip_no','$intake_date','$health_status',$is_vaccinated,$is_neutered,$cage_sql)");

        if ($cage_id !== null) {
            query("UPDATE CAGE SET is_occupied=1 WHERE cage_id='$cage_id'");
        }

        flash('success', "Animal '$name' added successfully.");
        redirect('/sarpams/animals/index.php');
    }
}

$cages = fetchAll("
    SELECT c.cage_id, c.cage_number, c.size_category, sh.shelter_name
    FROM CAGE c
    JOIN SHELTER sh ON c.shelter_id = sh.shelter_id
    WHERE c.is_occupied = 0
    ORDER BY sh.shelter_name, c.cage_number
");

require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <h1 class="page-title">Add Animal</h1>
    <a href="/sarpams/animals/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back to Animals</a>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
    <ul style="margin:0;padding-left:1.2em;"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Animal Information</div>
    <div class="card-body">
        <form method="post" action="">
            <div class="form-grid">
                <div class="form-group required">
                    <label>Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
                </div>
                <div class="form-group required">
                    <label>Species</label>
                    <input type="text" name="species" value="<?= htmlspecialchars($_POST['species'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Breed</label>
                    <input type="text" name="breed" value="<?= htmlspecialchars($_POST['breed'] ?? '') ?>">
                </div>
                <div class="form-group required">
                    <label>Colour</label>
                    <input type="text" name="colour" value="<?= htmlspecialchars($_POST['colour'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label>Age (years)</label>
                    <input type="number" name="age_years" step="0.1" min="0" value="<?= htmlspecialchars($_POST['age_years'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Sex</label>
                    <select name="sex">
                        <option value="M" <?= (($_POST['sex'] ?? '') === 'M') ? 'selected' : '' ?>>Male</option>
                        <option value="F" <?= (($_POST['sex'] ?? '') === 'F') ? 'selected' : '' ?>>Female</option>
                        <option value="U" <?= (($_POST['sex'] ?? 'U') === 'U') ? 'selected' : '' ?>>Unknown</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Weight (kg)</label>
                    <input type="number" name="weight_kg" step="0.1" min="0" value="<?= htmlspecialchars($_POST['weight_kg'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label>Microchip No.</label>
                    <input type="text" name="microchip_no" value="<?= htmlspecialchars($_POST['microchip_no'] ?? '') ?>">
                </div>
                <div class="form-group required">
                    <label>Intake Date</label>
                    <input type="date" name="intake_date" value="<?= htmlspecialchars($_POST['intake_date'] ?? date('Y-m-d')) ?>" required>
                </div>
                <div class="form-group">
                    <label>Health Status</label>
                    <select name="health_status">
                        <?php foreach (['Healthy','Under Treatment','Adopted','Unknown'] as $hs): ?>
                            <option value="<?= $hs ?>" <?= (($_POST['health_status'] ?? 'Unknown') === $hs) ? 'selected' : '' ?>><?= $hs ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Cage Assignment</label>
                    <select name="cage_id">
                        <option value="">-- No Cage --</option>
                        <?php foreach ($cages as $c): ?>
                            <option value="<?= $c['cage_id'] ?>" <?= (($_POST['cage_id'] ?? '') == $c['cage_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($c['cage_number']) ?> (<?= htmlspecialchars($c['size_category']) ?>) – <?= htmlspecialchars($c['shelter_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:.5rem;">
                        <input type="checkbox" name="is_vaccinated" <?= isset($_POST['is_vaccinated']) ? 'checked' : '' ?>>
                        Vaccinated
                    </label>
                </div>
                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:.5rem;">
                        <input type="checkbox" name="is_neutered" <?= isset($_POST['is_neutered']) ? 'checked' : '' ?>>
                        Neutered / Spayed
                    </label>
                </div>
            </div>
            <div style="margin-top:1.5rem;">
                <button type="submit" class="btn btn-primary"><i data-lucide="save"></i> Save Animal</button>
                <a href="/sarpams/animals/index.php" class="btn btn-secondary"><i data-lucide="x"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
