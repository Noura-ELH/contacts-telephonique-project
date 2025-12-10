<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

require_once 'includes/connexionbd.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $contact_id = intval($_POST['id']);
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT id FROM contacts WHERE id = ? AND user_id = ?");
    $stmt->execute([$contact_id, $user_id]);
    $contact = $stmt->fetchObject();

    if ($contact) {
        $deleteStmt = $conn->prepare("DELETE FROM contacts WHERE id = ?");
        $deleteStmt->execute([$contact_id]);
        header("Location: dashboard.php");
        exit;
    } else {
        header("Location: dashboard.php?error=unauthorized");
        exit;
    }
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php?error=invalid");
    exit;
}

$contact_id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id FROM contacts WHERE id = ? AND user_id = ?");
$stmt->execute([$contact_id, $user_id]);
$contact = $stmt->fetchObject();

if (!$contact) {
    header("Location: dashboard.php?error=unauthorized");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <title>delete</title>
  <style>
    body {
      background-color: #000;
      font-family: sans-serif;
      color: white;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .delete-card {
      background-color: #1e1e2f;
      padding: 30px;
      border-radius: 12px;
      width: 400px;
      box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
      text-align: center;
    }

    .delete-card h2 {
      margin-bottom: 20px;
      font-size: 20px;
    }

    .buttons {
      display: flex;
      justify-content: space-between;
      margin-top: 25px;
    }

    .buttons a,
    .buttons button {
      padding: 10px 20px;
      border: none;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      cursor: pointer;
      font-size: 14px;
    }

    .cancel-btn {
      background-color: #444;
      color: white;
    }

    .cancel-btn:hover {
      background-color: #666;
    }

    .delete-btn {
      background-color: #d11a2a;
      color: white;
    }

    .delete-btn:hover {
      background-color: #b3001b;
    }
  </style>
</head>
<body>

  <div class="delete-card">
    <h2>Are you sure you want to delete this contact?</h2>
    <form method="POST">
      <input type="hidden" name="id" value="<?= htmlspecialchars($contact_id) ?>">
      <div class="buttons">
        <a href="dashboard.php" class="cancel-btn">Cancel</a>
        <button type="submit" class="delete-btn"> Yes ,Delete</button>
      </div>
    </form>
  </div>

</body>
</html>
