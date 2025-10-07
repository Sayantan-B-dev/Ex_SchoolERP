<?php
include __DIR__ . '/../../config.php';

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$gender = trim($_POST['gender'] ?? '');
$course = trim($_POST['course'] ?? '');
$collegeId = trim($_POST['college_id'] ?? '');
$password = trim($_POST['password'] ?? '');
$confirm = trim($_POST['confirm_password'] ?? '');

if ($name === '' || $email === '' || $gender === '' || $course === '' || $collegeId === '' || $password === '' || $confirm === '') {
    $_SESSION['flash'] = 'Please fill all required fields';
    $_SESSION['flash_type'] = 'error';
    header('Location: ' . BASE_URL . '/view/pages/register.php');
    exit;
}

if ($password !== $confirm) {
    $_SESSION['flash'] = 'Passwords do not match';
    $_SESSION['flash_type'] = 'error';
    header('Location: ' . BASE_URL . '/view/pages/register.php');
    exit;
}

$passwordHash = password_hash($password, PASSWORD_BCRYPT);

// No role or department used in this version
$stmt = $conn->prepare('INSERT INTO users (name, email, gender, course, college_id, password_hash, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
$stmt->bind_param('ssssss', $name, $email, $gender, $course, $collegeId, $passwordHash);
$ok = $stmt->execute();
$err = $stmt->error;
$newId = $stmt->insert_id;
$stmt->close();

if (!$ok) {
    $_SESSION['flash'] = $err ?: 'Registration failed';
    $_SESSION['flash_type'] = 'error';
    header('Location: ' . BASE_URL . '/view/pages/register.php');
    exit;
}

$_SESSION['userId'] = intval($newId);
$_SESSION['userName'] = $name;

header('Location: ' . BASE_URL . '/view/pages/home.php');
exit;
