<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student ERP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-bootstrap-4@5/bootstrap-4.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>/view/assets/css/app.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>/view/assets/css/header_css.css" rel="stylesheet">
    <!-- Bootstrap JS for mobile nav dropdowns -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo BASE_URL; ?>/view/assets/js/header_js.js"></script>
    <script>

    </script>
</head>
<body>
<!-- Mobile Top Navbar (normal top nav, burger right, logo left) -->
<nav class="navbar navbar-expand-lg mobile-navbar d-lg-none px-2">
    <a class="navbar-brand me-auto" href="<?php echo BASE_URL; ?>/index.php">SIM</a>
    <button class="navbar-toggler ms-2" type="button" aria-controls="mobileNavBar" aria-expanded="false" aria-label="Toggle navigation" style="color: #fff !important;">
        <span class="navbar-toggler-icon" style="filter: invert(1) brightness(2);"></span>
    </button>
    <div class="collapse navbar-collapse mt-2" id="mobileNavBar">
        <hr style="height:1px;margin:0;margin-top:5px;padding:0;color:white;">
        <ul class="navbar-nav w-100">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="polyNavMobile" role="button" aria-expanded="false">Polytechnic</a>
                <ul class="dropdown-menu" aria-labelledby="polyNavMobile" style="display:none;">
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/polytechnic.php">Overview</a></li>
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/polytechnic.php?page=departments">Departments</a></li>
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/polytechnic.php?page=admissions">Admissions</a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="mgmtNavMobile" role="button" aria-expanded="false">Management</a>
                <ul class="dropdown-menu" aria-labelledby="mgmtNavMobile" style="display:none;">
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/management.php">Overview</a></li>
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/management.php?page=departments">Departments</a></li>
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/management.php?page=admissions">Admissions</a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="lawNavMobile" role="button" aria-expanded="false">Law</a>
                <ul class="dropdown-menu" aria-labelledby="lawNavMobile" style="display:none;">
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/law.php">Overview</a></li>
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/law.php?page=departments">Departments</a></li>
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/law.php?page=admissions">Admissions</a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="schoolNavMobile" role="button" aria-expanded="false">School</a>
                <ul class="dropdown-menu" aria-labelledby="schoolNavMobile" style="display:none;">
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/school.php">Overview</a></li>
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/school.php?page=departments">Departments</a></li>
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/school.php?page=admissions">Admissions</a></li>
                </ul>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="hospitalNavMobile" role="button" aria-expanded="false">Hospital</a>
                <ul class="dropdown-menu" aria-labelledby="hospitalNavMobile" style="display:none;">
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/hospital.php">Overview</a></li>
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/hospital.php?page=departments">Departments</a></li>
                    <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/hospital.php?page=services">Services</a></li>
                </ul>
            </li>
            <hr style="height:1px;margin:0;margin-bottom:10px;padding:0;color:white;">
            <?php if (isset($_SESSION['userId'])): ?>
              <div class="d-flex flex-row align-items-start gap-2">
                <a class="btn btn-success btn-sm my-1 px-3 py-1" href="<?php echo BASE_URL; ?>/view/pages/profile.php" style="font-size:0.85rem; border: 1px solid #fff; border-radius: 10px;">Edit Profile</a>
                <a class="btn btn-danger btn-sm my-1 px-3 py-1" href="<?php echo BASE_URL; ?>/controller/auth/logout.php" style="font-size:0.85rem; border: 1px solid #fff; border-radius: 10px;">Logout</a>
              </div>
            <?php else: ?>
              <div class="d-flex flex-row align-items-start gap-2">
                <a class="btn btn-primary btn-sm my-1 px-3 py-1" href="<?php echo BASE_URL; ?>/index.php" style="font-size:0.85rem; border: 1px solid #fff; border-radius: 10px;">Login</a>
                <a class="btn btn-secondary btn-sm my-1 px-3 py-1" href="<?php echo BASE_URL; ?>/view/pages/register.php" style="font-size:0.85rem; border: 1px solid #fff; border-radius: 10px;">Register</a>
              </div>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<!-- Side Navbar for Desktop -->
<nav id="sideNavbar" class="side-navbar d-none d-lg-flex flex-column">
    <div class="collapse-btn-container d-flex align-items-center" style="width: 100%;">
        <a class="navbar-brand me-auto" href="<?php echo BASE_URL; ?>/index.php" style="padding-left: 1.5rem;">SIM</a>
        <button class="collapse-toggle-btn d-flex align-items-center justify-content-center" id="collapseSidebarBtn" title="Collapse Sidebar" type="button" style="color: #fff !important; background: transparent; border: none; width: 40px; height: 40px;">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
            <rect x="2" y="3" width="12" height="2" rx="1" fill="white"/>
            <rect x="2" y="7" width="12" height="2" rx="1" fill="white"/>
            <rect x="2" y="11" width="12" height="2" rx="1" fill="white"/>
          </svg>
        </button>
    </div>
    <ul class="navbar-nav flex-column mb-2 mb-lg-0">
        <li class="nav-item dropdown">
            <a class="nav-link" href="#" id="polyNav" role="button"><span class="nav-text">Polytechnic</span></a>
            <ul class="dropdown-menu dropdown-menu-sm" aria-labelledby="polyNav">
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/polytechnic.php"><span class="nav-text">Overview</span></a></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/polytechnic.php?page=departments"><span class="nav-text">Departments</span></a></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/polytechnic.php?page=admissions"><span class="nav-text">Admissions</span></a></li>
            </ul>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link" href="#" id="mgmtNav" role="button"><span class="nav-text">Management</span></a>
            <ul class="dropdown-menu dropdown-menu-sm" aria-labelledby="mgmtNav">
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/management.php"><span class="nav-text">Overview</span></a></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/management.php?page=departments"><span class="nav-text">Departments</span></a></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/management.php?page=admissions"><span class="nav-text">Admissions</span></a></li>
            </ul>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link" href="#" id="lawNav" role="button"><span class="nav-text">Law</span></a>
            <ul class="dropdown-menu dropdown-menu-sm" aria-labelledby="lawNav">
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/law.php"><span class="nav-text">Overview</span></a></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/law.php?page=departments"><span class="nav-text">Departments</span></a></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/law.php?page=admissions"><span class="nav-text">Admissions</span></a></li>
            </ul>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link" href="#" id="schoolNav" role="button"><span class="nav-text">School</span></a>
            <ul class="dropdown-menu dropdown-menu-sm" aria-labelledby="schoolNav">
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/school.php"><span class="nav-text">Overview</span></a></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/school.php?page=departments"><span class="nav-text">Departments</span></a></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/school.php?page=admissions"><span class="nav-text">Admissions</span></a></li>
            </ul>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link" href="#" id="hospitalNav" role="button"><span class="nav-text">Hospital</span></a>
            <ul class="dropdown-menu dropdown-menu-sm" aria-labelledby="hospitalNav">
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/hospital.php"><span class="nav-text">Overview</span></a></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/hospital.php?page=departments"><span class="nav-text">Departments</span></a></li>
                <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/hospital.php?page=services"><span class="nav-text">Services</span></a></li>
            </ul>
        </li>
    </ul>
    <hr class="bg-secondary">
    <ul class="navbar-nav flex-column mb-3">
        <?php if (isset($_SESSION['userId'])): ?>
            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/view/pages/profile.php"><span class="nav-text">Edit Profile</span></a></li>
            <li><a class="dropdown-item text-danger" href="<?php echo BASE_URL; ?>/controller/auth/logout.php"><span class="nav-text">Logout</span></a></li>
        <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/index.php"><span class="nav-text">Login</span></a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>/view/pages/register.php"><span class="nav-text">Register</span></a></li>
        <?php endif; ?>
    </ul>
</nav>

<main id="mainContent" class="main-content container" style="">
  <div class="p-5">
<!-- The rest of your page content will go here, and will be pushed right of the sidebar on desktop -->
