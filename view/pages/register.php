<?php include __DIR__ . '/../../config.php'; ?>
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="row justify-content-center">
	<div class="col-12 col-md-6 col-lg-5">
		<div class="card shadow-sm">
			<div class="card-body">
				<h5 class="card-title mb-3">Register</h5>
				<?php if (!empty($_SESSION['flash'])): ?>
					<script>window.addEventListener('DOMContentLoaded', () => { Swal.fire({ icon: <?php echo json_encode($_SESSION['flash_type'] ?? 'info'); ?>, title: <?php echo json_encode(($_SESSION['flash_type'] ?? 'info') === 'success' ? 'Success' : (($_SESSION['flash_type'] ?? 'info') === 'error' ? 'Error' : 'Notice')); ?>, text: <?php echo json_encode($_SESSION['flash']); ?> }); });</script>
					<?php unset($_SESSION['flash'], $_SESSION['flash_type']); ?>
				<?php endif; ?>
				<form action="<?php echo BASE_URL; ?>/controller/auth/register_action.php" method="post" novalidate>
					<div class="mb-3">
						<label for="name" class="form-label">Full Name</label>
						<input type="text" class="form-control" id="name" name="name" required>
					</div>
					<div class="mb-3">
						<label for="email" class="form-label">Email</label>
						<input type="email" class="form-control" id="email" name="email" required>
					</div>
					<div class="mb-3">
						<label for="gender" class="form-label">Gender</label>
						<select class="form-select" id="gender" name="gender" required>
							<option value="" disabled selected>Choose...</option>
							<option value="Male">Male</option>
							<option value="Female">Female</option>
						</select>
					</div>
					<div class="mb-3">
						<label for="course" class="form-label">Course</label>
						<select class="form-select" id="course" name="course" required data-selected="">
							<option value="" disabled selected>Choose course...</option>
						</select>
					</div>
					<div class="mb-3">
						<label for="college_id" class="form-label">College ID</label>
						<input type="text" class="form-control" id="college_id" name="college_id" placeholder="e.g., C-2025-0012" required>
					</div>
					<div class="mb-3">
						<label for="password" class="form-label">Password</label>
						<div class="input-group">
							<input type="password" class="form-control" id="password" name="password" required>
							<button type="button" class="btn btn-outline-secondary password-toggle" data-target="#password">Show</button>
						</div>
					</div>
					<div class="mb-3">
						<label for="confirm_password" class="form-label">Confirm Password</label>
						<div class="input-group">
							<input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
							<button type="button" class="btn btn-outline-secondary password-toggle" data-target="#confirm_password">Show</button>
						</div>
					</div>
					<button type="submit" class="btn btn-success w-100">Create Account</button>
				</form>
				<p class="mt-3 mb-0">Already have an account? <a href="<?php echo BASE_URL; ?>/view/pages/login.php">Login</a></p>
			</div>
		</div>
	</div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function(){
    var courseEl = document.getElementById('course');
    var deptEl = null;
    function fill(el, options){
        if (!el) return;
        var selected = el.getAttribute('data-selected') || '';
        if (el.options.length <= 1) {
            var ph = el.querySelector('option[value=""]');
            el.innerHTML = '';
            if (ph) el.appendChild(ph);
            options.forEach(function(opt){
                var o = document.createElement('option');
                o.value = opt; o.textContent = opt;
                if (selected && selected === opt) o.selected = true;
                el.appendChild(o);
            });
        }
    }
    fill(courseEl, ['BCA','BSc CS','Diploma CS','MBA','BBA','HR','Finance','Marketing','Civil','Mechanical','Electrical','LLB','BA LLB']);
    // no departments anymore
});
</script>


