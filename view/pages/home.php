<?php
include __DIR__ . '/../../config.php';

if (!isset($_SESSION['userId'])) {
    $_SESSION['flash'] = 'Please login to continue';
    $_SESSION['flash_type'] = 'info';
    header('Location: login.php');
    exit;
}

include __DIR__ . '/../includes/header.php';
?>

<?php if (!empty($_SESSION['flash'])): ?>
    <script>window.addEventListener('DOMContentLoaded',()=>{Swal.fire({icon:<?php echo json_encode($_SESSION['flash_type'] ?? 'success'); ?>,title:<?php echo json_encode(($_SESSION['flash_type'] ?? 'success')==='success'?'Success':(($_SESSION['flash_type'] ?? 'info')==='info'?'Notice':'Message')); ?>,text:<?php echo json_encode($_SESSION['flash']); ?>});});</script>
    <?php unset($_SESSION['flash'], $_SESSION['flash_type']); ?>
<?php endif; ?>

<div class="hero-100vh d-flex align-items-center justify-content-center bg-gradient position-relative overflow-hidden">
  <div class="container text-center text-dark">
    <div class="p-5 bg-light bg-opacity-75 rounded-4 animate__animated animate__fadeInDown">
      <h1 class="display-5 fw-bold mb-3">Welcome, <?php echo htmlspecialchars($_SESSION['userName']); ?> 🎓</h1>
      <p class="lead mb-4">You’re successfully logged in to your Student ERP Dashboard.</p>
      <div class="d-flex justify-content-center align-items-center gap-2">
        <div style="pointer-events:auto;">
          <a href="<?php echo BASE_URL; ?>/view/pages/profile.php" class="btn btn-primary btn-lg px-4 me-2">Go to Profile</a>
          <a href="<?php echo BASE_URL; ?>/view/pages/home.php" class="btn btn-outline-dark btn-lg px-4">View Dashboard</a>
        </div>
      </div>
    </div>
    <div class="row mt-5 g-4 justify-content-center">
    <div class="col-md-4"><div class="placeholder-box animate__animated animate__zoomIn"></div></div>
    <div class="col-md-4"><div class="placeholder-box animate__animated animate__zoomIn"></div></div>
    <div class="col-md-4"><div class="placeholder-box animate__animated animate__zoomIn"></div></div>
    <div class="col-md-4"><div class="placeholder-box animate__animated animate__zoomIn"></div></div>
    <div class="col-md-4"><div class="placeholder-box animate__animated animate__zoomIn"></div></div>
    <div class="col-md-4"><div class="placeholder-box animate__animated animate__zoomIn"></div></div>
    </div>

  </div>
</div>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/view/assets/css/home.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

<?php include __DIR__ . '/../includes/footer.php'; ?>


    