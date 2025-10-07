<?php
include __DIR__ . '/config.php';


if (isset($_SESSION['userId'])) {
    header('Location: ./view/pages/home.php');
    exit;
} else {
    header('Location: ./view/pages/login.php');
    exit;
}
