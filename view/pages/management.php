<?php include __DIR__ . '/../../config.php'; ?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="hero-100vh">
    <div class="container">
        <div class="p-4 bg-white rounded-3 shadow-sm text-center mx-auto" style="max-width:900px;">
            <h3 class="mb-3">Management College</h3>
            <?php $page = $_GET['page'] ?? 'overview'; ?>
            <?php if ($page === 'departments'): ?>
                <p class="mb-3">Explore programs like BBA, MBA with specializations in HR, Finance, Marketing, and Analytics. Show program outcomes, internship partners, and placement records.</p>
                <ul class="text-start small mb-0">
                    <li>Layouts: tabs for specializations, partner logos grid, stats counters.</li>
                    <li>Options: curriculum PDFs, alumni stories, enquiry CTA.</li>
                </ul>
            <?php elseif ($page === 'admissions'): ?>
                <p class="mb-3">Admissions details: eligibility (entrance/cutoff), fee plans, scholarships, and important dates with reminders.</p>
                <ul class="text-start small mb-0">
                    <li>Layouts: stepper timeline, FAQ accordion, fee comparison tables.</li>
                    <li>Options: lead form, counseling slots, document checklist.</li>
                </ul>
            <?php else: ?>
                <p class="mb-3">Overview: institute accreditations, industry tie-ups, case study competitions, and experiential learning highlights.</p>
                <ul class="text-start small mb-0">
                    <li>Layouts: hero with metrics, testimonial carousel, event cards.</li>
                    <li>Options: newsletter signup, upcoming events, faculty highlights.</li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>


