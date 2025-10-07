</div>
 <footer class="p-5 bg-dark text-light mt-5 pt-5 ">
    <div class="container">
        <div class="row g-4">
            <div class="col-12 col-md-6 col-lg-4">
                <h5 class="mb-3">About Our Institute</h5>
                <p class="mb-3">A unified campus with Polytechnic, Management, Law, School, and Hospital—advancing education and healthcare with excellence.</p>
                <a href="https://example.com/apply" target="_blank" class="btn btn-warning btn-sm">Apply Now</a>
            </div>
            <div class="col-6 col-md-3 col-lg-2">
                <h6 class="mb-3">Quick Links</h6>
                <ul class="list-unstyled small mb-0">
                    <li><a class="link-light text-decoration-none" href="<?php echo BASE_URL; ?>/index.php">Home</a></li>
                    <li><a class="link-light text-decoration-none" href="<?php echo BASE_URL; ?>/view/pages/polytechnic.php">Polytechnic</a></li>
                    <li><a class="link-light text-decoration-none" href="<?php echo BASE_URL; ?>/view/pages/management.php">Management</a></li>
                    <li><a class="link-light text-decoration-none" href="<?php echo BASE_URL; ?>/view/pages/law.php">Law</a></li>
                    <li><a class="link-light text-decoration-none" href="<?php echo BASE_URL; ?>/view/pages/school.php">School</a></li>
                    <li><a class="link-light text-decoration-none" href="<?php echo BASE_URL; ?>/view/pages/hospital.php">Hospital</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3 col-lg-3">
                <h6 class="mb-3">Resources</h6>
                <ul class="list-unstyled small mb-0">
                    <li><a class="link-light text-decoration-none" href="<?php echo BASE_URL; ?>/view/pages/home.php">Dashboard</a></li>
                    <li><a class="link-light text-decoration-none" href="<?php echo BASE_URL; ?>/view/pages/profile.php">Edit Profile</a></li>
                </ul>
            </div>
            <div class="col-12 col-lg-3">
                <h6 class="mb-3">Contact</h6>
                <ul class="list-unstyled small mb-3">
                    <li><span class="opacity-75">Email:</span> contact@institute.edu</li>
                    <li><span class="opacity-75">Phone:</span> +91 00000 00000</li>
                    <li><span class="opacity-75">Address:</span> Campus Road, City, State</li>
                </ul>
                <div class="d-flex gap-2">
                    <a href="#" class="btn btn-outline-light btn-sm">Facebook</a>
                    <a href="#" class="btn btn-outline-light btn-sm">Twitter</a>
                    <a href="#" class="btn btn-outline-light btn-sm">LinkedIn</a>
                </div>
            </div>
        </div>
        <hr class="border-secondary mt-4">
        <div class="d-flex flex-column flex-md-row align-items-center justify-content-between py-3">
            <div class="small">© <span id="yearCopy"></span> Student ERP. All rights reserved.</div>
            <div class="small">
                <a href="#" class="link-light text-decoration-none me-3">Privacy</a>
                <a href="#" class="link-light text-decoration-none">Terms</a>
            </div>
        </div>
    </div>
    <script>document.getElementById('yearCopy').textContent = new Date().getFullYear();</script>
 </footer>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
<script src="<?php echo BASE_URL; ?>/view/assets/js/app.js"></script>
</body>
</html>
