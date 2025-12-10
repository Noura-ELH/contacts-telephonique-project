<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

require_once 'includes/connexionbd.php';

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    header("Location: dashboard.php?error=invalid");
    exit;
}

$contact_id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM contacts WHERE id = ? AND user_id = ?");
$stmt->execute([$contact_id, $user_id]);
$contact = $stmt->fetchObject();

if (!$contact) {
    header("Location: dashboard.php?error=unauthorized");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ;
    $email = $_POST['email'] ;
    $phone = $_POST['phone'] ;
    $tag_id = $_POST['tag_id'];

$image_name = $contact->image;
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
            $image_name = $contact->image; 
        }
    } else {
        $image_name = $contact->image; 
    }
}

    $update = $conn->prepare("UPDATE contacts SET name = ?, email = ?, phone_number = ?, tag_ids = ?, image = ? WHERE id = ? AND user_id = ?");
    $update->execute([$name, $email, $phone, $tag_id,  $image_name,$contact_id, $user_id]);

    header("Location: dashboard.php");
    exit;
}

$tagsStmt = $conn->query("SELECT id, name FROM tags");
$tags = $tagsStmt->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Modify Contact</title>
    <style>
        body {
            background-color: #0d0d0d;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-card {
            background-color: #1f1f2e;
            padding: 40px 40px;
            border-radius: 15px;
            width: 400px;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.5);
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-card h2 {
            margin: 0;
            text-align: center;
            color: #f5f5f5;
        }

        label {
            font-weight: bold;
            font-size: 17px;
            margin-bottom: 10px !important;
            color: #ccc;
        }
        input{
            color: #fff;
        }
        input, select {
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            width: 100%;
            transition: background 0.3s;
            margin-bottom: 9px;
            margin-top: 9px;
            
        }

        input:focus, select:focus {
            outline: none;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .btn-submit {
            margin: 10px;
            background-color: #5c67f2;
            border: none;
            padding: 12px;
            border-radius: 25px;
            font-weight: bold;
            font-size: 15px;
            cursor: pointer;
            color: white;
            transition: background 0.3s ease;
        }

        .btn-submit:hover {
            background-color: #4048c9;
        }

        .btn-cancel {
            padding: 10px;
            text-align: center;
            display: block;
            margin-top: 10px;
            color: #aaa;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
            background-color:rgb(67, 68, 88);
            border-radius: 25px;       
           
        }

        .btn-cancel:hover {
            color: #fff;
            background-color:rgb(113, 114, 135);
        }
    </style>
</head>
<body>

<div class="form-card">
    <h2>Modify Contact</h2>
    <form method="POST" action="" enctype="multipart/form-data">
        <div>
            <label>Name</label>
            <input type="text" name="name" value="<?= htmlspecialchars($contact->name) ?>" required>
        </div>
        <div>
            <label>Email</label>
            <input type="email" name="email" value="<?= htmlspecialchars($contact->email) ?>" required>
        </div>
        <div>
            <label>Phone</label>
            <input type="text" name="phone" value="<?= htmlspecialchars($contact->phone_number) ?>" required>
        </div>
        <div>
            <label>Tag</label>
            <select name="tag_id">
                <option value="">-- select Tag --</option>
                <?php foreach ($tags as $tag): ?>
                    <option value="<?= $tag->id ?>" <?= ($contact->tag_ids == $tag->id ? 'selected' : '') ?>>
                        <?= htmlspecialchars($tag->name) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label>Photo</label>
            <input type="file" name="image" accept="image/*">
        </div>

        <button type="submit" class="btn-submit">Save Changes</button>
        <a href="dashboard.php" class="btn-cancel">Cancel</a>
    </form>
</div>

</body>
</html>
