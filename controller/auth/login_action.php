<?php
include __DIR__ . '/../../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $_SESSION['flash'] = 'Please enter a valid email and password';
        $_SESSION['flash_type'] = 'error';
        header('Location: ../../index.php');
        exit;
    }

    $stmt = $conn->prepare('SELECT id, name, email, password_hash FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        $_SESSION['flash'] = 'Incorrect email or password';
        $_SESSION['flash_type'] = 'error';
        header('Location: ../../index.php');
        exit;
    }

    $_SESSION['userId'] = $user['id'];
    $_SESSION['userName'] = $user['name'];

    $_SESSION['flash'] = 'Login successful';
    $_SESSION['flash_type'] = 'success';
    header('Location: ../../view/pages/home.php');
    exit;
}

echo 'Method not allowed';
