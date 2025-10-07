<?php include __DIR__ . '/../../config.php'; ?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="hero-100vh">
    <div class="container">
        <div class="p-4 bg-white rounded-3 shadow-sm text-center mx-auto" style="max-width:900px;">
            <h3 class="mb-3">School</h3>
            <?php $page = $_GET['page'] ?? 'overview'; ?>
            <?php if ($page === 'departments'): ?>
                <p class="mb-3">Primary, Secondary, and Higher Secondary wings with focus on holistic development, labs, and co-curriculars.</p>
                <ul class="text-start small mb-0">
                    <li>Layouts: class-wise sections, timetable downloads, and activity galleries.</li>
                    <li>Options: staff directory, parent portal, announcements feed.</li>
                </ul>
            <?php elseif ($page === 'admissions'): ?>
                <p class="mb-3">Admissions: age criteria, required documents, fee details, and scholarship information.</p>
                <ul class="text-start small mb-0">
                    <li>Layouts: stepper with requirements, fees table, FAQ.</li>
                    <li>Options: enquiry form, campus tour booking, calendar.</li>
                </ul>
            <?php else: ?>
                <p class="mb-3">Overview: school ethos, infrastructure, sports facilities, and achievements.</p>
                <ul class="text-start small mb-0">
                    <li>Layouts: hero with quick stats, image carousel, news cards.</li>
                    <li>Options: newsletters, event calendar, student spotlight.</li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>


