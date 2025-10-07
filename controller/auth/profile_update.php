<?php
include __DIR__ . '/../../config.php';

if (!isset($_SESSION['userId'])) {
	$_SESSION['flash'] = 'Please login to continue';
	$_SESSION['flash_type'] = 'info';
	header('Location: ' . BASE_URL . '/index.php');
	exit;
}

// Gather inputs
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$regNo = trim($_POST['reg_no'] ?? '');
$collegeId = trim($_POST['college_id'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$course = trim($_POST['course'] ?? '');
$department = '';
$newPassword = trim($_POST['new_password'] ?? '');

if ($name === '' || $email === '' || $gender === '' || $course === '' || $collegeId === '') {
	$_SESSION['flash'] = 'Please fill all required fields';
	$_SESSION['flash_type'] = 'error';
	header('Location: ' . BASE_URL . '/view/pages/profile.php');
	exit;
}

// Build update query
$fields = 'name = ?, email = ?, reg_no = ?, college_id = ?, gender = ?, course = ?';
$types = 'ssssss';
$params = [$name, $email, $regNo, $collegeId, $gender, $course];

if ($newPassword !== '') {
	$passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);
	$fields .= ', password_hash = ?';
	$types .= 's';
	$params[] = $passwordHash;
}

$types .= 'i';
$params[] = intval($_SESSION['userId']);

$sql = "UPDATE users SET $fields WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
	$_SESSION['flash'] = 'Failed to prepare update';
	$_SESSION['flash_type'] = 'error';
	header('Location: ' . BASE_URL . '/view/pages/profile.php');
	exit;
}

$stmt->bind_param($types, ...$params);
$ok = $stmt->execute();
$err = $stmt->error;
$stmt->close();

if ($ok) {
	// Refresh session details for quick display
	$_SESSION['userName'] = $name;
$_SESSION['department'] = null;
	$_SESSION['flash'] = 'Profile updated successfully';
	$_SESSION['flash_type'] = 'success';
} else {
	$_SESSION['flash'] = $err ?: 'Update failed';
	$_SESSION['flash_type'] = 'error';
}

header('Location: ' . BASE_URL . '/view/pages/profile.php');
exit;


