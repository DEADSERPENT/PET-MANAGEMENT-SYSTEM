<?php
require_once __DIR__ . '/../config/db.php';
$pageTitle = 'Adoption Applicants';
require_once __DIR__ . '/../includes/header.php';

$applicants = fetchAll("SELECT * FROM ADOPTION_APPLICANT ORDER BY applicant_id DESC");
?>
<div class="page-header">
    <h1 class="page-title"><i data-lucide="users"></i> Adoption Applicants</h1>
    <a href="/sarpams/adoption_applicants/create.php" class="btn btn-primary"><i data-lucide="plus"></i> Add Applicant</a>
</div>

<?php if (empty($applicants)): ?>
<div class="empty-state"><p>No applicants found.</p></div>
<?php else: ?>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>City</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Living Situation</th>
            <th>Previous Pets</th>
            <th class="table-actions">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($applicants as $a): ?>
        <tr>
            <td><?= $a['applicant_id'] ?></td>
            <td><?= htmlspecialchars($a['first_name'] . ' ' . $a['last_name']) ?></td>
            <td><?= htmlspecialchars($a['city']) ?></td>
            <td><?= htmlspecialchars($a['phone']) ?></td>
            <td><?= htmlspecialchars($a['email']) ?></td>
            <td><?= htmlspecialchars($a['living_situation'] ?? '—') ?></td>
            <td>
                <span class="badge badge-<?= $a['has_previous_pets'] ? 'yes' : 'no' ?>">
                    <?= $a['has_previous_pets'] ? 'Yes' : 'No' ?>
                </span>
            </td>
            <td class="table-actions">
                <a href="/sarpams/adoption_applicants/edit.php?id=<?= $a['applicant_id'] ?>" class="btn btn-sm btn-warning"><i data-lucide="pencil"></i> Edit</a>
                <a href="/sarpams/adoption_applicants/delete.php?id=<?= $a['applicant_id'] ?>" class="btn btn-sm btn-danger"><i data-lucide="trash-2"></i> Delete</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

