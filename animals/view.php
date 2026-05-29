<?php
require_once __DIR__ . '/../config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    flash('danger', 'Invalid animal ID.');
    redirect('/sarpams/animals/index.php');
}

$animal = fetchOne("
    SELECT a.*, c.cage_number, c.size_category, sh.shelter_name
    FROM ANIMAL a
    LEFT JOIN CAGE c ON a.cage_id = c.cage_id
    LEFT JOIN SHELTER sh ON c.shelter_id = sh.shelter_id
    WHERE a.animal_id = $id
");
if (!$animal) {
    flash('danger', 'Animal not found.');
    redirect('/sarpams/animals/index.php');
}

$pageTitle = 'Animal – ' . htmlspecialchars($animal['name']);

$records = fetchAll("
    SELECT mr.*, v.first_name, v.last_name
    FROM MEDICAL_RECORD mr
    JOIN VETERINARIAN v ON mr.vet_id = v.vet_id
    WHERE mr.animal_id = $id
    ORDER BY mr.exam_date DESC
");

$adoption = fetchOne("
    SELECT ad.*, aa.first_name, aa.last_name, aa.phone, aa.email
    FROM ADOPTION ad
    JOIN ADOPTION_APPLICANT aa ON ad.applicant_id = aa.applicant_id
    WHERE ad.animal_id = $id
    ORDER BY ad.adoption_id DESC
    LIMIT 1
");

require_once __DIR__ . '/../includes/header.php';

$sexLabel = ['M' => 'Male', 'F' => 'Female', 'U' => 'Unknown'];
?>
<div class="page-header">
    <h1 class="page-title"><?= htmlspecialchars($animal['name']) ?></h1>
    <div style="display:flex;gap:.5rem;">
        <a href="/sarpams/animals/edit.php?id=<?= $id ?>" class="btn btn-warning"><i data-lucide="pencil"></i> Edit</a>
        <a href="/sarpams/animals/delete.php?id=<?= $id ?>" class="btn btn-danger">Delete</a>
        <a href="/sarpams/medical_records/create.php?animal_id=<?= $id ?>" class="btn btn-success">+ Medical Record</a>
        <a href="/sarpams/animals/index.php" class="btn btn-secondary"><i data-lucide="arrow-left"></i> Back</a>
    </div>
</div>

<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-header"><i data-lucide="dog"></i> Animal Details</div>
    <div class="card-body">
        <div class="detail-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:1rem 2rem;">
            <div><strong>ID</strong><p><?= $animal['animal_id'] ?></p></div>
            <div><strong>Name</strong><p><?= htmlspecialchars($animal['name']) ?></p></div>
            <div><strong>Species</strong><p><?= htmlspecialchars($animal['species']) ?></p></div>
            <div><strong>Breed</strong><p><?= htmlspecialchars($animal['breed'] ?: '—') ?></p></div>
            <div><strong>Age (years)</strong><p><?= $animal['age_years'] !== null ? $animal['age_years'] : '—' ?></p></div>
            <div><strong>Sex</strong><p><?= $sexLabel[$animal['sex']] ?? htmlspecialchars($animal['sex']) ?></p></div>
            <div><strong>Colour</strong><p><?= htmlspecialchars($animal['colour']) ?></p></div>
            <div><strong>Weight (kg)</strong><p><?= $animal['weight_kg'] !== null ? $animal['weight_kg'] : '—' ?></p></div>
            <div><strong>Microchip No.</strong><p><?= htmlspecialchars($animal['microchip_no'] ?: '—') ?></p></div>
            <div><strong>Intake Date</strong><p><?= htmlspecialchars($animal['intake_date'] ?? '—') ?></p></div>
            <div>
                <strong>Health Status</strong>
                <p>
                    <?php
                    $hs = strtolower($animal['health_status'] ?? '');
                    $hsMap = ['healthy'=>'healthy','under treatment'=>'treatment','adopted'=>'adopted','unknown'=>'unknown'];
                    $hsBadge = $hsMap[$hs] ?? 'unknown';
                    ?>
                    <span class="badge badge-<?= $hsBadge ?>"><?= htmlspecialchars($animal['health_status'] ?? 'Unknown') ?></span>
                </p>
            </div>
            <div>
                <strong>Vaccinated</strong>
                <p><span class="badge badge-<?= $animal['is_vaccinated'] ? 'yes' : 'no' ?>"><?= $animal['is_vaccinated'] ? 'Yes' : 'No' ?></span></p>
            </div>
            <div>
                <strong>Neutered / Spayed</strong>
                <p><span class="badge badge-<?= $animal['is_neutered'] ? 'yes' : 'no' ?>"><?= $animal['is_neutered'] ? 'Yes' : 'No' ?></span></p>
            </div>
            <div>
                <strong>Shelter</strong>
                <p><?= $animal['shelter_name'] ? htmlspecialchars($animal['shelter_name']) : '—' ?></p>
            </div>
            <div>
                <strong>Cage</strong>
                <p><?= $animal['cage_number'] ? htmlspecialchars($animal['cage_number'] . ' (' . $animal['size_category'] . ')') : '—' ?></p>
            </div>
        </div>
    </div>
</div>

<?php if ($adoption): ?>
<div class="card" style="margin-bottom:1.5rem;">
    <div class="card-header">Adoption Status</div>
    <div class="card-body">
        <div class="detail-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1rem 2rem;">
            <div><strong>Adoption ID</strong><p><?= $adoption['adoption_id'] ?></p></div>
            <div><strong>Applicant</strong><p><?= htmlspecialchars($adoption['first_name'] . ' ' . $adoption['last_name']) ?></p></div>
            <div><strong>Phone</strong><p><?= htmlspecialchars($adoption['phone']) ?></p></div>
            <div><strong>Application Date</strong><p><?= htmlspecialchars($adoption['application_date'] ?? '—') ?></p></div>
            <div><strong>Approval Date</strong><p><?= htmlspecialchars($adoption['approval_date'] ?? '—') ?></p></div>
            <div><strong>Adoption Date</strong><p><?= htmlspecialchars($adoption['adoption_date'] ?? '—') ?></p></div>
            <div>
                <strong>Status</strong>
                <p>
                    <?php $stMap = ['pending'=>'pending','approved'=>'approved','rejected'=>'rejected','completed'=>'completed']; ?>
                    <span class="badge badge-<?= $stMap[strtolower($adoption['status'])] ?? 'pending' ?>"><?= htmlspecialchars($adoption['status']) ?></span>
                </p>
            </div>
            <div>
                <strong>Agreement Signed</strong>
                <p><span class="badge badge-<?= $adoption['agreement_signed'] ? 'yes' : 'no' ?>"><?= $adoption['agreement_signed'] ? 'Yes' : 'No' ?></span></p>
            </div>
        </div>
        <?php if ($adoption['notes']): ?>
        <p style="margin-top:1rem;"><strong>Notes:</strong> <?= nl2br(htmlspecialchars($adoption['notes'])) ?></p>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        Medical Records
        <a href="/sarpams/medical_records/create.php?animal_id=<?= $id ?>" class="btn btn-sm btn-success" style="float:right;"><i data-lucide="plus"></i> Add Record</a>
    </div>
    <div class="card-body">
        <?php if (empty($records)): ?>
        <div class="empty-state"><p>No medical records for this animal.</p></div>
        <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Vet</th>
                    <th>Exam Date</th>
                    <th>Diagnosis</th>
                    <th>Medication</th>
                    <th>Next Checkup</th>
                    <th class="table-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($records as $r): ?>
                <tr>
                    <td><?= $r['record_id'] ?></td>
                    <td><?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?></td>
                    <td><?= htmlspecialchars($r['exam_date']) ?></td>
                    <td><?= htmlspecialchars(mb_strimwidth($r['diagnosis'] ?? '', 0, 60, '…')) ?></td>
                    <td><?= htmlspecialchars($r['medication'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($r['next_checkup_date'] ?? '—') ?></td>
                    <td class="table-actions">
                        <a href="/sarpams/medical_records/edit.php?id=<?= $r['record_id'] ?>" class="btn btn-sm btn-warning"><i data-lucide="pencil"></i> Edit</a>
                        <a href="/sarpams/medical_records/delete.php?id=<?= $r['record_id'] ?>" class="btn btn-sm btn-danger"><i data-lucide="trash-2"></i> Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

