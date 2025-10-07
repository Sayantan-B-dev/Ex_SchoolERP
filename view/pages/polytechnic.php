<?php include __DIR__ . '/../../config.php'; ?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="hero-100vh">
    <div class="container">
        <div class="p-4 bg-white rounded-3 shadow-sm text-center mx-auto" style="max-width:900px;">
            <h3 class="mb-3">Polytechnic College</h3>
            <?php $page = $_GET['page'] ?? 'overview'; ?>
            <?php if ($page === 'departments'): ?>
                <p class="mb-3">Explore polytechnic departments and programs such as Civil, Mechanical, Electrical, and Computer Science. Each department page can include curriculum outlines, faculty profiles, lab facilities, placement stats, and downloadable syllabi.</p>
                <ul class="text-start small mb-0">
                    <li>Layouts: grid of departments, tabbed curriculum/faculty, or accordion FAQs.</li>
                    <li>Options: filters by year/semester, downloadable brochures, contact forms.</li>
                </ul>
            <?php elseif ($page === 'admissions'): ?>
                <p class="mb-3">Admissions information for Polytechnic College: eligibility criteria, important dates, fee structure, and scholarship options. Add an Apply Now CTA and a timeline component.</p>
                <ul class="text-start small mb-0">
                    <li>Layouts: timeline steps, pricing tables, or Q&A accordion.</li>
                    <li>Options: seat matrix tables, downloadable forms, enquiry form.</li>
                </ul>
            <?php else: ?>
                <p class="mb-3">Overview of Polytechnic College: vision, mission, affiliations, NAAC/NBA status, infrastructure, and student life highlights.</p>
                <ul class="text-start small mb-0">
                    <li>Layouts: hero with stats, carousel gallery, or cards for highlights.</li>
                    <li>Options: campus map, virtual tour link, testimonials slider.</li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>


