<?php
require_once 'includes/connexionbd.php';
session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.html");
  exit;
}

$tagStmt = $conn->prepare("SELECT id, name FROM tags");
$tagStmt->execute();
$tags = $tagStmt->fetchAll(PDO::FETCH_OBJ);

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
  SELECT contacts.id, contacts.name AS contact_name, contacts.email, contacts.phone_number,contacts.image, tags.name AS tag_name
  FROM contacts
  JOIN tags ON contacts.tag_ids = tags.id
  WHERE contacts.user_id = ?
");
$stmt->execute([$user_id]);
$contacts = $stmt->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="icon" type="image/png"  href="images/logoweb.png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <title>ContactEAS - Dashboard</title>
   <link rel="stylesheet" href="css/style_dashboard.css">
</head>
<body>
<div class="container">

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="logo">
      <img src="images/logo.png" alt="logo" width="100" height="70">
      <span class="logo-text">ContactEAS</span>
    </div>

    <input type="text" class="search-box" placeholder="Search" id="searchInput">
    <button onclick="filterBy('All')" class="all-people-btn"> <i class="fa-solid fa-users"></i> All People</button>

    <div class="tags-dropdown">
      <button onclick="toggleTags()"> <i class="fa-solid fa-tag"></i> Tags ▼</button>
      <div class="tags-list" id="tagsList">
        <button onclick="filterBy('Job')">  <i class="fa-solid fa-briefcase"></i> Job</button>
        <button onclick="filterBy('Family')"> <i class="fa-solid fa-house"></i> Family</button>
        <button onclick="filterBy('Friend')"> <i class="fa-solid fa-user-group"></i> Friends</button>
      </div>
    </div>

    <a href="logout.php" class="logout-btn"> <i class="fa-solid fa-right-from-bracket"></i> Log out</a>
  </aside>

  <!-- Main Content -->
  <main class="main-panel">
    <nav>
      <div class="header-text">
        <h1><?php echo "Welcome " . htmlspecialchars($_SESSION['user_name']) . " to your account"; ?></h1>
      </div>
      <div class="menu-bar">
        <div class="add" onclick="openModal()"> <i class="fa-solid fa-plus"></i> Add</div>
      </div>
    </nav>

    <!-- Modal Add Contact -->
    <div id="addContactModal" class="modal">
      <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Add New Contact</h2>
        <form action="add_contact.php" method="POST"  enctype="multipart/form-data">
          <label for="name">Name:</label>
          <input type="text" name="name" required>

          <label for="email">Email:</label>
          <input type="email" name="email" required>

          <label for="phone">Phone:</label>
          <input type="text" name="phone" required>
          <label for="tag">Tag:</label>
          <select name="tag" required>
            <option value="">-- Select Tag --</option>
            <?php foreach ($tags as $tag): ?>
              <option value="<?= htmlspecialchars($tag->id) ?>"><?= htmlspecialchars($tag->name) ?></option>
            <?php endforeach; ?>
          </select>
            <label>Image:</label>
            <input type="file" name="image" accept="image/*">
          <button type="submit">Save Contact</button>
        </form>
      </div>
    </div>
    <div class="themsection">
    <button id="themeToggle" class="theme-btn">
        <i class="fa-solid fa-sun"></i>
    </button>
    <span> Toogle them </span>
  </div>
    <h1>Contacts<img src="images/telicone_.png" alt="icon" with='50' height='50'></h1>
    <div class="contacts-list">
      <?php foreach ($contacts as $contact): ?>
        <div class="contact-card" data-tag="<?= htmlspecialchars($contact->tag_name) ?>">
          <div class="contact-info-left">
            <img src="uploads/<?php echo htmlspecialchars($contact->image)?>"  alt="<?= htmlspecialchars($contact->contact_name) ?>" class="contact-avatar">
            <div class="contact-info">
              <h3><?= htmlspecialchars($contact->contact_name) ?></h3>
              <p class="tags"><?= htmlspecialchars($contact->tag_name) ?></p>
            </div>
          </div>
          <div class="contact-info-right">
            <div class="buttonscm">
          <a href="mailto:<?= $contact->email ?>" class="btn mail">
              <i class="fa-solid fa-envelope"></i>
          </a>

          <a href="tel:<?= $contact->phone_number ?>" class="btn call">
              <i class="fa-solid fa-phone"></i>
          </a>
      </div>

          </div>
          <div>
            <button class="btnshow" onclick="openDetails(
              '<?= htmlspecialchars($contact->contact_name) ?>',
              '<?= htmlspecialchars($contact->tag_name) ?>',
              '<?= htmlspecialchars($contact->email) ?>',
              '<?= htmlspecialchars($contact->phone_number) ?>',
              '<?= htmlspecialchars($contact->image) ?>',
              <?= $contact->id ?>
            )">...</button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </main>
</div>


<!-- Contact Details Modal -->
<div class="contact-details-modal" id="detailsModal" style="display: none;">
  <div class="contact-card-large">
    <span class="close" onclick="closeDetails()">&times;</span>
    <div class="header">
      <img src="uploads/profile.jpeg" alt="Avatar" id='imagecontact'>
      <div>
        <h2 id="detailName"></h2>
        <p id="detailTag"></p>
      </div>
    </div>    

    <div class="details">
      <div><i class="fa-solid fa-phone"></i><span id="detailPhone"></span></div>
      <div><i class="fa-solid fa-envelope"></i> <span id="detailEmail"></span></div>
      <div><i class="fa-solid fa-user"></i> <span id="detailRelation"></span></div>
    </div>
    <div class="actions">
      <a href="#" id="modifyLink"> <i class="fa-solid fa-pen-to-square"></i> Modify</a>
      <a href="#" id="deleteLink"><i class="fa-solid fa-trash"></i> Delete</a>
    </div>
  </div>
</div>
<script src="js/index.js"></script>
</body>
</html>
