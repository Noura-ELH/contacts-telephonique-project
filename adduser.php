<?php
require_once 'includes/connexionbd.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $_POST["name"];
    $email    = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $date    = date("Y-m-d");

    try {
        $check = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $check->execute([$email]);
        $exists = $check->fetchColumn();

        if ($exists > 0) {
            echo "<script>
                alert('⚠️ This email is already registered. Please use another one.');
                window.location.href = 'signup.html';
                </script>";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, created_at) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $password, $date]);
           header("Location: success.php");
            exit;

        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

