<?php
require_once 'includes/connexionbd.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $tag_id = intval($_POST['tag']);  
    $user_id = $_SESSION['user_id'];
    $date  = date("Y-m-d");



$image_name = 'profile.jpeg'; 
if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0755);

    $image_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
    $image_size = $_FILES['image']['size'];

    if (in_array($image_ext, $allowed_ext) && $image_size <= 5 * 1024 * 1024) {
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;

        if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_name = 'profile.jpeg'; 
        }
    } else {
        $image_name = 'profile.jpeg'; 
    }
}


    if (!empty($name) && !empty($email) && !empty($phone) && $tag_id > 0) {
        $stmt = $conn->prepare("
            INSERT INTO contacts (name, email, phone_number, tag_ids, image, user_id, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$name, $email, $phone, $tag_id, $image_name, $user_id, $date]);

        header("Location: dashboard.php");
        exit;
    } else {
        echo "<script>alert('Please fill in all fields correctly.'); window.location.href='dashboard.php';</script>";
        exit;
    }
} else {
    echo "Invalid request method.";
}
?>
