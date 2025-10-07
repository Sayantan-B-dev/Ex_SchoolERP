<?php
include __DIR__ . '/../../config.php';

if (!isset($_SESSION['userId'])) {
    $_SESSION['flash'] = 'Please login to continue';
    header('Location: login.php');
    exit;
}

// Fetch user (no role or department)
$stmt = $conn->prepare('SELECT name, email, reg_no, college_id, gender, course FROM users WHERE id = ? LIMIT 1');
$stmt->bind_param('i', $_SESSION['userId']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

include __DIR__ . '/../includes/header.php';
?>

<?php if (!empty($_SESSION['flash'])): ?>
    <script>window.addEventListener('DOMContentLoaded',()=>{Swal.fire({icon:'info',title:'Notice',text:<?php echo json_encode($_SESSION['flash']); ?>});});</script>
    <?php unset($_SESSION['flash']); ?>
<?php endif; ?>

<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <img src="<?php echo BASE_URL; ?>/view/assets/img/profile.svg" alt="avatar" width="56" height="56" class="rounded-circle me-3 border">
                    <h5 class="card-title mb-0">Edit Profile</h5>
                </div>
                <form action="<?php echo BASE_URL; ?>/controller/auth/profile_update.php" method="post" novalidate>
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="reg_no" class="form-label">Registration Number (optional)</label>
                        <input type="text" class="form-control" id="reg_no" name="reg_no" value="<?php echo htmlspecialchars($user['reg_no'] ?? ''); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="" disabled>Choose...</option>
                            <option value="Male" <?php echo (($user['gender'] ?? '')==='Male'?'selected':''); ?>>Male</option>
                            <option value="Female" <?php echo (($user['gender'] ?? '')==='Female'?'selected':''); ?>>Female</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="course" class="form-label">Course</label>
                        <select class="form-select" id="course" name="course" required data-selected="<?php echo htmlspecialchars($user['course'] ?? ''); ?>">
                            <option value="" disabled>Choose course...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="college_id" class="form-label">College ID</label>
                        <input type="text" class="form-control" id="college_id" name="college_id" value="<?php echo htmlspecialchars($user['college_id'] ?? ''); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password (optional)</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Leave blank to keep current">
                            <button type="button" class="btn btn-outline-secondary password-toggle" data-target="#new_password">Show</button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
