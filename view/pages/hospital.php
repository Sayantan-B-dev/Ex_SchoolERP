<?php include __DIR__ . '/../../config.php'; ?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="hero-100vh">
    <div class="container">
        <div class="p-4 bg-white rounded-3 shadow-sm text-center mx-auto" style="max-width:900px;">
            <h3 class="mb-3">Hospital</h3>
            <?php $page = $_GET['page'] ?? 'overview'; ?>
            <?php if ($page === 'departments'): ?>
                <p class="mb-3">Departments like General Medicine, Surgery, Orthopedics, Pediatrics, and Diagnostics with doctor rosters and OPD timings.</p>
                <ul class="text-start small mb-0">
                    <li>Layouts: department cards, doctor profiles, schedule tables.</li>
                    <li>Options: appointment booking, emergency numbers, insurance partners.</li>
                </ul>
            <?php elseif ($page === 'services'): ?>
                <p class="mb-3">Services: 24x7 pharmacy, ambulance, ICU, labs, imaging, and telemedicine.</p>
                <ul class="text-start small mb-0">
                    <li>Layouts: icon features grid, pricing table, FAQs.</li>
                    <li>Options: online reports, feedback form, service SLAs.</li>
                </ul>
            <?php else: ?>
                <p class="mb-3">Overview: care philosophy, accreditations, facilities, and patient testimonials.</p>
                <ul class="text-start small mb-0">
                    <li>Layouts: stats with icons, photo gallery, success stories.</li>
                    <li>Options: contact directory, location maps, visiting hours.</li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>


