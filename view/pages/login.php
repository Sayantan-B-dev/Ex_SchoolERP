<?php include __DIR__ . '/../../config.php'; ?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="d-flex align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="col-12 col-md-6 col-lg-5">
        <div class="card shadow-sm" style=" display: flex; flex-direction: column; justify-content: center;">
            <div class="card-body">
                <h5 class="card-title mb-3">Login</h5>
                <?php if (!empty($_SESSION['flash'])): ?>
                    <script>window.addEventListener('DOMContentLoaded',()=>{Swal.fire({icon:<?php echo json_encode($_SESSION['flash_type'] ?? 'info'); ?>,title:<?php echo json_encode(($_SESSION['flash_type'] ?? 'info')==='success'?'Success':(($_SESSION['flash_type'] ?? 'info')==='error'?'Error':'Notice')); ?>,text:<?php echo json_encode($_SESSION['flash']); ?>});});</script>
                    <?php unset($_SESSION['flash'], $_SESSION['flash_type']); ?>
                <?php endif; ?>
                <form action="<?php echo BASE_URL; ?>/controller/auth/login_action.php" method="post" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <button type="button" class="btn btn-outline-secondary password-toggle" data-target="#password">Show</button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>
                <p class="mt-3 mb-0">No account? <a href="register.php">Register</a></p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

