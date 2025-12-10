<?php
require_once 'includes/connexionbd.php';
session_start();
$remember_email = $_COOKIE['remember_email'] ?? '';
if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        if (!empty($_POST['remember'])) {
            setcookie('remember_email', $email, time() + (7 * 24 * 60 * 60), "/");
        }
        header("Location: dashboard.php");
        exit;
    } else {
        echo "<script>alert('Invalid email or password'); window.location.href='login.html';</script>";
        exit;
    }
} else {
    echo "Missing fields.";
}
