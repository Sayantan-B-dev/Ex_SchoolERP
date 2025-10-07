<?php include __DIR__ . '/../../config.php'; ?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="hero-100vh">
    <div class="container">
        <div class="p-4 bg-white rounded-3 shadow-sm text-center mx-auto" style="max-width:900px;">
            <h3 class="mb-3">Law College</h3>
            <?php $page = $_GET['page'] ?? 'overview'; ?>
            <?php if ($page === 'departments'): ?>
                <p class="mb-3">Departments like Corporate Law, Criminal Law, Constitutional Studies, and Moot Court Society details.</p>
                <ul class="text-start small mb-0">
                    <li>Layouts: department cards with briefs, faculty list, and publications.</li>
                    <li>Options: moot court schedule, legal aid clinic info, journals.</li>
                </ul>
            <?php elseif ($page === 'admissions'): ?>
                <p class="mb-3">Admissions: entrance tests, eligibility, fee structure, scholarships, and important deadlines.</p>
                <ul class="text-start small mb-0">
                    <li>Layouts: step-by-step guide, FAQs, and document checklist.</li>
                    <li>Options: application portal link, counseling dates, contact.</li>
                </ul>
            <?php else: ?>
                <p class="mb-3">Overview: bar council approvals, courtroom training, internships, and alumni in practice.</p>
                <ul class="text-start small mb-0">
                    <li>Layouts: stats with icons, gallery, achievements timeline.</li>
                    <li>Options: internship partners, alumni spotlight, newsletter.</li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>


