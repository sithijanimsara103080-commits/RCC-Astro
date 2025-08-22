<?php
session_start();

// SIMPLE LOGIN CHECK (replace with your own auth)
if (!isset($_SESSION['admin_logged_in'])) {
  header("Location: login.php");
  exit;
}

$dataFile = __DIR__ . "/../uploads/data.json";
$logFile = __DIR__ . "/../uploads/admin_log.txt";
$uploadsDir = __DIR__ . "/../uploads/";

if (!file_exists($dataFile)) file_put_contents($dataFile, json_encode([]));
if (!file_exists($logFile)) file_put_contents($logFile, "");

function loadData() {
  global $dataFile;
  $json = file_get_contents($dataFile);
  $data = json_decode($json, true);
  if (!$data) $data = [];
  return $data;
}

function saveData($data) {
  global $dataFile;
  file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));
}

function logAction($action) {
  global $logFile;
  $time = date('Y-m-d H:i:s');
  file_put_contents($logFile, "[$time] $action\n", FILE_APPEND);
}

function findIndexByFilename($data, $filename) {
  foreach ($data as $i => $item) {
    if ($item['filename'] === $filename) return $i;
  }
  return false;
}

function validateImage($file) {
  $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
  $maxSize = 5 * 1024 * 1024; // 5MB
  
  if (!in_array($file['type'], $allowedTypes)) {
    return "Only JPG, PNG, GIF, and WebP files are allowed.";
  }
  
  if ($file['size'] > $maxSize) {
    return "File size must be less than 5MB.";
  }
  
  return true;
}

$message = "";
$error = "";
$data = loadData();

if (!empty($data)) {
    $data = array_reverse($data);
}

// HANDLE UPLOAD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
  if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $title = trim($_POST['title'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $link = trim($_POST['link'] ?? '');

    $validation = validateImage($_FILES['image']);
    if ($validation !== true) {
      $error = $validation;
    } elseif (empty($title)) {
      $error = "Title is required.";
    } else {
      $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
      $filename = uniqid() . "." . $ext;
      $dest = $uploadsDir . $filename;
      if (move_uploaded_file($_FILES['image']['tmp_name'], $dest)) {
        $data[] = [
          'filename' => $filename,
          'title' => $title,
          'description' => $desc,
          'link' => $link,
          'upload_date' => date('Y-m-d H:i:s')
        ];
        saveData($data);
        logAction("Uploaded post $filename with title '$title'");
        $message = "Post uploaded successfully!";
        // Redirect to main menu after successful upload
        header("Location: event_dash.php?message=" . urlencode("Post uploaded successfully!"));
        exit;
      } else {
        $error = "Failed to move uploaded file.";
      }
    }
  } else {
    $error = "No image uploaded or error uploading.";
  }
}

// HANDLE EDIT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
  $filename = $_POST['filename'] ?? '';
  $title = trim($_POST['title'] ?? '');
  $desc = trim($_POST['description'] ?? '');
  $link = trim($_POST['link'] ?? '');

  $index = findIndexByFilename($data, $filename);
  if ($index !== false) {
    if (empty($title)) {
      $error = "Title is required.";
    } else {
      // Handle new image upload if provided
      if (isset($_FILES['new_image']) && $_FILES['new_image']['error'] === 0) {
        $validation = validateImage($_FILES['new_image']);
        if ($validation !== true) {
          $error = $validation;
        } else {
          // Delete old image
          $oldFilePath = $uploadsDir . $filename;
          if (file_exists($oldFilePath)) {
            unlink($oldFilePath);
          }
          
          // Upload new image
          $ext = pathinfo($_FILES['new_image']['name'], PATHINFO_EXTENSION);
          $newFilename = uniqid() . "." . $ext;
          $dest = $uploadsDir . $newFilename;
          
          if (move_uploaded_file($_FILES['new_image']['tmp_name'], $dest)) {
            $data[$index]['filename'] = $newFilename;
            logAction("Changed image for post $filename to $newFilename");
          } else {
            $error = "Failed to upload new image.";
          }
        }
      }
      
      if (empty($error)) {
        $data[$index]['title'] = $title;
        $data[$index]['description'] = $desc;
        $data[$index]['link'] = $link;
        $data[$index]['last_modified'] = date('Y-m-d H:i:s');
        saveData($data);
        logAction("Edited post $filename");
        $message = "Post information updated successfully!";
        // Redirect to main menu after successful edit
        header("Location: event_dash.php?message=" . urlencode("Post updated successfully!"));
        exit;
      }
    }
  } else {
    $error = "Post not found.";
  }
}

// HANDLE DELETE
if (isset($_GET['delete'])) {
  $filename = basename($_GET['delete']);
  $index = findIndexByFilename($data, $filename);
  if ($index !== false) {
    $filePath = $uploadsDir . $filename;
    if (file_exists($filePath)) unlink($filePath);
    unset($data[$index]);
    $data = array_values($data);
    saveData($data);
    logAction("Deleted post $filename");
    $message = "Post deleted successfully.";
    // Redirect to main menu after successful delete
    header("Location: event_dash.php?message=" . urlencode("Post deleted successfully!"));
    exit;
  } else {
    $error = "Post not found.";
  }
}

// SEARCH
$search = trim($_GET['search'] ?? '');
$filteredData = array_filter($data, function($item) use ($search) {
  if ($search !== '') {
    return stripos($item['title'], $search) !== false || stripos($item['description'], $search) !== false;
  }
  return true;
});

// For editing form preload
$editItem = null;
if (isset($_GET['edit'])) {
  $filename = basename($_GET['edit']);
  $index = findIndexByFilename($data, $filename);
  if ($index !== false) {
    $editItem = $data[$index];
  }
}

// Get message from URL parameter
if (isset($_GET['message'])) {
  $message = urldecode($_GET['message']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Event Dashboard - Our Posts & Explore</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    *, *::before, *::after {
      box-sizing: border-box;
    }
    body {
      background: #121212;
      color: #eee;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 1em;
      max-width: 1200px;
      margin-left: auto;
      margin-right: auto;
      line-height: 1.6;
    }
    h1 {
      text-align: center;
      color: #f7ca04;
      margin-bottom: 1.5em;
      font-size: 2.5em;
      font-weight: 700;
      letter-spacing: 1px;
    }
    h2 {
      color: #f7ca04;
      margin-bottom: 1em;
      font-weight: 600;
    }
    form, .search-container, .post-card, .stat-card {
      margin-bottom: 1em;
      background: #181818;
      padding: 1.5em;
      border-radius: 8px;
      border: 1px solid #222;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      box-sizing: border-box;
    }
    input[type="text"], input[type="url"], textarea, .file-input-container input[type="file"] {
      width: 100%;
      padding: 0.8em;
      margin-top: 0.3em;
      margin-bottom: 1em;
      background: #232323;
      border: 1px solid #444;
      color: #eee;
      border-radius: 4px;
      font-size: 1em;
      box-sizing: border-box;
      display: block;
    }
    textarea {
      resize: vertical;
      min-height: 100px;
    }
    label {
      font-weight: 600;
      display: block;
      margin-top: 0.5em;
      color: #f7ca04;
      letter-spacing: 0.5px;
    }
    button, .btn {
      background: linear-gradient(45deg, #f7ca04, #ff6b35);
      border: none;
      padding: 0.8em 1.5em;
      color: #000;
      font-weight: bold;
      border-radius: 4px;
      cursor: pointer;
      font-size: 1em;
      margin: 0.5em 0 0 0;
      transition: background 0.2s;
      box-shadow: 0 2px 6px rgba(0,0,0,0.07);
    }
    button.danger, .btn.danger {
      background: linear-gradient(45deg, #ff4757, #ff3742);
      color: white;
    }
    button.secondary, .btn.secondary {
      background: #555;
      color: #eee;
    }
    .logout {
      display: block;
      text-align: center;
      margin-top: 2em;
      color: #ff4757;
      text-decoration: none;
      font-weight: bold;
      padding: 1em;
      border: 2px solid #333;
      border-radius: 8px;
      background: #181818;
      transition: border 0.2s, background 0.2s;
    }
    .logout:hover {
      border: 2px solid #ff4757;
      background: #222;
    }
    .message {
      background: linear-gradient(45deg, #2ed573, #1e90ff);
      padding: 1em;
      border-radius: 4px;
      margin-bottom: 1em;
      color: white;
      font-weight: bold;
    }
    .error {
      background: linear-gradient(45deg, #ff4757, #ff3742);
      padding: 1em;
      border-radius: 4px;
      margin-bottom: 1em;
      color: white;
      font-weight: bold;
    }
    .search-container {
      position: relative;
      text-align: center;
      margin-bottom: 2em;
      background: #181818;
      padding: 1.5em;
      border-radius: 8px;
      border: 1px solid #222;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .search-bar {
      display: flex;
      flex-direction: row;
      align-items: stretch;
      gap: 0.5em;
      width: 100%;
      position: relative;
      justify-content: center;
    }
    .search-bar input[type="text"] {
      flex: 1 1 0;
      min-width: 180px;
      height: 48px;
      padding: 0 1em;
      font-size: 1em;
      border-radius: 4px;
      border: 1px solid #444;
      background: #232323;
      color: #eee;
      box-sizing: border-box;
      margin: 0;
    }
    .search-bar button, .search-bar .btn {
      height: 48px;
      padding: 0 1.5em;
      font-size: 1em;
      border-radius: 4px;
      font-weight: bold;
      display: flex;
      align-items: center;
      border: none;
      margin: 0;
      box-shadow: none;
      transition: background 0.2s, color 0.2s;
    }
    .search-bar .btn.secondary {
      background: #444;
      color: #eee;
      border: 1px solid #666;
      margin-left: 0.5em;
    }
    .search-bar .btn.secondary:hover {
      background: #666;
      color: #fff;
    }
    .search-suggestions {
      position: absolute;
      top: 100%;
      left: 0;
      min-width: 200px;
      background: #232323;
      border: 1px solid #444;
      border-radius: 4px;
      max-height: 200px;
      overflow-y: auto;
      z-index: 1000;
      display: none;
      margin-top: 0.2em;
    }
    .search-suggestion {
      padding: 0.8em;
      cursor: pointer;
      border-bottom: 1px solid #444;
      transition: background 0.3s ease;
    }
    .search-suggestion:hover {
      background: #333;
    }
    .search-suggestion:last-child {
      border-bottom: none;
    }
    .suggestion-title {
      color: #f7ca04;
      font-weight: bold;
      margin-bottom: 0.3em;
    }
    .suggestion-desc {
      color: #ccc;
      font-size: 0.9em;
    }
    .posts-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 2em;
      margin-bottom: 2em;
    }
    .post-card {
      background: #222;
      border-radius: 12px;
      padding: 1em;
      position: relative;
      border: 1px solid #333;
      transition: all 0.3s ease;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      margin-bottom: 1em;
    }
    .post-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
      border-color: #f7ca04;
    }
    .post-card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 8px;
      display: block;
      margin-bottom: 1em;
      cursor: pointer;
      transition: transform 0.3s ease;
      background: #181818;
    }
    .post-card img:hover {
      transform: scale(1.05);
    }
    .post-info h3 {
      margin: 0 0 0.5em 0;
      color: #f7ca04;
      font-size: 1.2em;
      font-weight: 600;
    }
    .post-info p {
      margin: 0 0 0.5em 0;
      font-size: 0.95em;
      color: #ccc;
      line-height: 1.4;
    }
    .post-meta {
      font-size: 0.85em;
      color: #888;
      margin-bottom: 0.5em;
    }
    .post-info a.more-btn {
      color: #f7ca04;
      text-decoration: none;
      font-weight: bold;
      display: inline-flex;
      align-items: center;
      gap: 0.3em;
    }
    .post-actions {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 1em;
      padding-top: 1em;
      border-top: 1px solid #333;
    }
    .post-actions a, .post-actions button {
      background: #444;
      color: #eee;
      text-decoration: none;
      padding: 0.5em 0.8em;
      border-radius: 4px;
      font-size: 0.95em;
      margin-left: 0.3em;
      cursor: pointer;
      border: none;
      transition: background 0.2s;
    }
    .post-actions .edit-btn {
      background: #2ed573;
      color: #000;
    }
    .post-actions .delete-btn {
      background: #ff4757;
      color: #fff;
    }
    .stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1em;
      margin-bottom: 2em;
    }
    .stat-card {
      background: #222;
      padding: 1.5em;
      border-radius: 8px;
      text-align: center;
      border: 1px solid #333;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .stat-number {
      font-size: 2em;
      font-weight: bold;
      color: #f7ca04;
    }
    .stat-label {
      color: #ccc;
      margin-top: 0.5em;
      font-size: 1em;
    }
    .image-modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.9);
      z-index: 1000;
      align-items: center;
      justify-content: center;
    }
    .image-modal.active {
      display: flex;
    }
    .modal-content {
      position: relative;
      max-width: 90%;
      max-height: 90%;
    }
    .modal-image {
      max-width: 100%;
      max-height: 100%;
      display: block;
      border-radius: 8px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
    }
    .modal-close {
      position: absolute;
      top: -40px;
      right: 0;
      background: rgba(255, 255, 255, 0.2);
      color: white;
      border: none;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      font-size: 1.5rem;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .modal-close:hover {
      background: rgba(255, 255, 255, 0.3);
    }
    .upload-preview {
      margin: 1em 0;
      text-align: center;
    }
    .upload-preview img {
      max-width: 200px;
      max-height: 150px;
      border-radius: 8px;
      border: 2px solid #555;
      background: #181818;
    }
    .file-input-container {
      position: relative;
      margin-bottom: 1em;
      width: 100%;
      box-sizing: border-box;
    }
    @media (max-width: 600px) {
      form, .search-container, .post-card, .stat-card {
        padding: 1em 0.5em;
        border-radius: 0;
        width: 100%;
        min-width: unset;
      }
      .stats {
        grid-template-columns: 1fr;
      }
      .posts-grid {
        grid-template-columns: 1fr;
        gap: 1em;
      }
      .upload-preview,
      .file-input-container,
      .post-card img,
      .post-card,
      .search-bar input[type="text"],
      .search-bar button,
      .search-bar .btn {
        width: 100% !important;
        min-width: unset !important;
        max-width: 100% !important;
      }
      .post-card img,
      .upload-preview img {
        max-width: 100% !important;
        height: auto !important;
      }
      .file-input-container input[type="file"] {
        font-size: 1em;
        padding: 0.7em;
      }
      label {
        font-size: 1em;
      }
      button, .btn, .post-actions a, .post-actions button {
        font-size: 1em;
        padding: 0.7em 1em;
        width: 100%;
        margin: 0.5em 0 0 0;
      }
      .search-bar {
        flex-direction: column;
        align-items: stretch;
        gap: 0.5em;
      }
      .search-bar input[type="text"],
      .search-bar button,
      .search-bar .btn {
        width: 100%;
        min-width: unset;
        margin: 0;
        height: 44px;
      }
      .search-bar .btn.secondary {
        margin-left: 0;
      }
      .image-modal .modal-content {
        max-width: 98vw;
        max-height: 90vh;
      }
    }
    form .file-input-container,
    form input[type="text"],
    form input[type="url"],
    form textarea {
      margin-bottom: 1.2em;
    }
  </style>
</head>
<body>
  <h1><i class="fas fa-newspaper"></i> Event Dashboard - Our Posts & Explore</h1>

  <?php if($message): ?>
    <div class="message"><i class="fas fa-check-circle"></i> <?=htmlspecialchars($message)?></div>
  <?php endif; ?>
  <?php if($error): ?>
    <div class="error"><i class="fas fa-exclamation-circle"></i> <?=htmlspecialchars($error)?></div>
  <?php endif; ?>

  <!-- STATISTICS -->
  <div class="stats">
    <div class="stat-card">
      <div class="stat-number"><?=count($data)?></div>
      <div class="stat-label">Total Posts</div>
    </div>
    <div class="stat-card">
      <div class="stat-number"><?=count(array_filter($data, function($item) { return !empty($item['link']); }))?></div>
      <div class="stat-label">With Links</div>
    </div>
  </div>

  <a href="logout.php" class="logout"><i class="fas fa-sign-out-alt"></i> Logout</a>

  <!-- SEARCH WITH SUGGESTIONS -->
  <div class="search-container">
    <form method="GET" action="">
      <div class="search-bar" id="searchBar">
        <input type="text" name="search" id="searchInput" placeholder="Search title or description..." value="<?=htmlspecialchars($search)?>" autocomplete="off" />
        <button type="submit"><i class="fas fa-search"></i> Search</button>
        <a href="event_dash.php" class="btn secondary"><i class="fas fa-times"></i> Clear</a>
        <div class="search-suggestions" id="searchSuggestions"></div>
      </div>
    </form>
  </div>

  <!-- UPLOAD / EDIT FORM -->
  <form method="POST" enctype="multipart/form-data">
    <?php if ($editItem): ?>
      <h2><i class="fas fa-edit"></i> Edit Post Information</h2>
      <input type="hidden" name="filename" value="<?=htmlspecialchars($editItem['filename'])?>" />
      
      <!-- Current Image and Change Image Stacked Vertically -->
      <div style="margin-bottom: 1em; display: flex; flex-direction: column; gap: 1.5em; align-items: flex-start;">
        <!-- Current Image -->
        <div style="background: #222; border-radius: 8px; border: 1px solid #444; padding: 1em; text-align: center; min-width: 240px; width: 100%;">
          <label style="color: #f7ca04; font-weight: bold; display: block; margin-bottom: 0.5em; text-align: left;">Current Image:</label>
          <img src="../uploads/<?=htmlspecialchars($editItem['filename'])?>" alt="Current Image" class="clickable-image" data-src="../uploads/<?=htmlspecialchars($editItem['filename'])?>" style="max-width: 200px; max-height: 150px; border-radius: 8px; border: 2px solid #555; cursor: pointer; display: block; margin: 0 auto;" />
          <small style="color: #888; display: block; margin-top: 0.5em;">Click to view full size</small>
        </div>
        <!-- Change Image -->
        <div style="background: #222; border-radius: 8px; border: 1px solid #444; padding: 1em; text-align: center; min-width: 240px; width: 100%;">
          <label style="color: #f7ca04; font-weight: bold; display: block; margin-bottom: 0.5em; text-align: left;">Change Image:</label>
          <div class="file-input-container" style="margin-bottom: 0.5em;">
            <input type="file" name="new_image" accept="image/*" id="editImageInput" />
          </div>
          <div class="upload-preview" id="editImagePreview" style="display: none;">
            <img id="editPreviewImg" src="" alt="Preview" style="max-width: 200px; max-height: 150px; border-radius: 8px; border: 2px solid #555; cursor: pointer; display: block; margin: 0 auto;" />
            <small style="color: #888; display: block; margin-top: 0.5em;">Click to view full size</small>
          </div>
        </div>
      </div>
      <small style="color: #888; display: block; margin-bottom: 1em;">JPG, PNG, GIF, WebP - Max 5MB</small>
      
      <label>Title:</label>
      <input type="text" name="title" required value="<?=htmlspecialchars($editItem['title'])?>" />
      <label>Description:</label>
      <textarea name="description" placeholder="Enter post description..."><?=htmlspecialchars($editItem['description'])?></textarea>
      <label>More Link (URL):</label>
      <input type="url" name="link" id="linkInput" value="<?=htmlspecialchars($editItem['link'])?>" placeholder="https://example.com" />
      <div id="linkPreview" style="margin:0.5em 0 1em 0; display:<?=!empty($editItem['link']) ? 'block' : 'none'?>;">
        <span style="color:#aaa;">Preview: </span>
        <a href="<?=htmlspecialchars($editItem['link'])?>" id="linkPreviewAnchor" target="_blank" style="color:#0af; text-decoration:underline; word-break:break-all;">
          <?=htmlspecialchars($editItem['link'])?>
        </a>
      </div>
      <button type="submit" name="edit"><i class="fas fa-save"></i> Save Changes</button>
      <a href="event_dash.php" class="btn secondary"><i class="fas fa-times"></i> Cancel Edit</a>
    <?php else: ?>
      <h2><i class="fas fa-upload"></i> Upload New Post</h2>
      <label>Image File (JPG, PNG, GIF, WebP - Max 5MB):</label>
      <div class="file-input-container">
        <input type="file" name="image" required accept="image/*" id="uploadImageInput" />
      </div>
      <div class="upload-preview" id="uploadImagePreview" style="display: none;">
        <img id="uploadPreviewImg" src="" alt="Preview" />
        <small style="color: #888; display: block; margin-top: 0.5em;">Image preview</small>
      </div>
      <label>Title:</label>
      <input type="text" name="title" required placeholder="Enter post title..." />
      <label>Description:</label>
      <textarea name="description" placeholder="Enter post description..."></textarea>
      <label>More Link (URL):</label>
      <input type="url" name="link" placeholder="https://example.com" />
      <button type="submit" name="upload"><i class="fas fa-upload"></i> Upload Post</button>
    <?php endif; ?>
  </form>

  <!-- IMAGE MODAL -->
  <div class="image-modal" id="imageModal">
    <div class="modal-content">
      <button class="modal-close" id="modalClose">&times;</button>
      <img class="modal-image" id="modalImage" src="" alt="Full size image" />
    </div>
  </div>

  <!-- POSTS GRID -->
  <div class="posts-grid">
    <?php if (count($filteredData) === 0): ?>
      <p style="grid-column:1/-1; text-align:center; color:#888; padding: 2em;">
        <i class="fas fa-newspaper" style="font-size: 3em; margin-bottom: 1em; display: block;"></i>
        No posts found matching your criteria.
      </p>
    <?php endif; ?>

    <?php foreach ($filteredData as $item): ?>
    <div class="post-card">
      <img src="../uploads/<?=htmlspecialchars($item['filename'])?>" alt="<?=htmlspecialchars($item['title'])?>" class="clickable-image" data-src="../uploads/<?=htmlspecialchars($item['filename'])?>" />
      <div class="post-info">
        <h3><?=htmlspecialchars($item['title'])?></h3>
        <p><?php
          $desc = htmlspecialchars($item['description']);
          if (mb_strlen($desc) > 120) {
            echo mb_substr($desc, 0, 120) . '...';
          } else {
            echo $desc;
          }
        ?></p>
        <div class="post-meta">
          <small>Uploaded: <?=date('M j, Y', strtotime($item['upload_date'] ?? 'now'))?></small>
          <?php if (isset($item['last_modified'])): ?>
            <br><small>Modified: <?=date('M j, Y', strtotime($item['last_modified']))?></small>
          <?php endif; ?>
        </div>
        <?php if (!empty($item['link'])): ?>
          <a class="more-btn" href="<?=htmlspecialchars($item['link'])?>" target="_blank" rel="noopener noreferrer">
            <i class="fas fa-external-link-alt"></i> View Link
          </a>
          <div style="margin-top:0.5em; font-size:0.95em; color:#0af; word-break:break-all;">
            <span style="color:#aaa;">Short Link:</span> 
            <a href="/go.php?u=<?=base64_encode($item['link'])?>" target="_blank" style="color:#0af; text-decoration:underline;">
              <?=htmlspecialchars((isset($_SERVER['HTTP_HOST']) ? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] : '') . '/go.php?u=' . base64_encode($item['link']))?>
            </a>
          </div>
        <?php endif; ?>
      </div>
      <div class="post-actions">
        <button type="button" class="edit-btn" onclick="editPost('<?=htmlspecialchars($item['filename'])?>')" title="Edit">
          <i class="fas fa-edit"></i>
        </button>
        <a href="?delete=<?=urlencode($item['filename'])?>" onclick="return confirm('Delete this post?');" class="delete-btn" title="Delete">
          <i class="fas fa-trash"></i>
        </a>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <script>
    // Search suggestions functionality
    const searchInput = document.getElementById('searchInput');
    const searchSuggestions = document.getElementById('searchSuggestions');
    const postsData = <?=json_encode($data)?>;

    searchInput.addEventListener('input', function() {
      const query = this.value.toLowerCase().trim();
      
      if (query.length < 2) {
        searchSuggestions.style.display = 'none';
        return;
      }

      // Filter posts based on search query
      const suggestions = postsData.filter(post => 
        post.title.toLowerCase().includes(query) || 
        post.description.toLowerCase().includes(query)
      ).slice(0, 5); // Limit to 5 suggestions

      if (suggestions.length > 0) {
        displaySuggestions(suggestions);
      } else {
        searchSuggestions.style.display = 'none';
      }
    });

    function displaySuggestions(suggestions) {
      searchSuggestions.innerHTML = '';
      
      suggestions.forEach(post => {
        const suggestionDiv = document.createElement('div');
        suggestionDiv.className = 'search-suggestion';
        suggestionDiv.innerHTML = `
          <div class="suggestion-title">${post.title}</div>
          <div class="suggestion-desc">${post.description.substring(0, 100)}${post.description.length > 100 ? '...' : ''}</div>
        `;
        
        suggestionDiv.addEventListener('click', function() {
          searchSuggestions.style.display = 'none';
          // Open editing section for this post
          window.location.href = `event_dash.php?edit=${encodeURIComponent(post.filename)}`;
        });
        
        searchSuggestions.appendChild(suggestionDiv);
      });
      
      searchSuggestions.style.display = 'block';
    }

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
      if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
        searchSuggestions.style.display = 'none';
      }
    });

    // Hide suggestions when pressing Escape
    searchInput.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        searchSuggestions.style.display = 'none';
      }
    });

    // Image Modal functionality
    const imageModal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const modalClose = document.getElementById('modalClose');

    // Make all clickable images open modal
    document.addEventListener('click', function(e) {
      if (e.target.classList.contains('clickable-image')) {
        const imageSrc = e.target.getAttribute('data-src');
        modalImage.src = imageSrc;
        imageModal.classList.add('active');
        document.body.style.overflow = 'hidden';
      }
    });

    // Close modal
    modalClose.addEventListener('click', function() {
      imageModal.classList.remove('active');
      document.body.style.overflow = 'auto';
    });

    // Close modal when clicking outside
    imageModal.addEventListener('click', function(e) {
      if (e.target === imageModal) {
        imageModal.classList.remove('active');
        document.body.style.overflow = 'auto';
      }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && imageModal.classList.contains('active')) {
        imageModal.classList.remove('active');
        document.body.style.overflow = 'auto';
      }
    });

    // Upload image preview functionality
    const uploadImageInput = document.getElementById('uploadImageInput');
    const uploadImagePreview = document.getElementById('uploadImagePreview');
    const uploadPreviewImg = document.getElementById('uploadPreviewImg');

    if (uploadImageInput) {
      uploadImageInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
          const reader = new FileReader();
          reader.onload = function(e) {
            uploadPreviewImg.src = e.target.result;
            uploadImagePreview.style.display = 'block';
          };
          reader.readAsDataURL(this.files[0]);
        } else {
          uploadImagePreview.style.display = 'none';
        }
      });
    }

    // Edit image preview functionality
    const editImageInput = document.getElementById('editImageInput');
    const editImagePreview = document.getElementById('editImagePreview');
    const editPreviewImg = document.getElementById('editPreviewImg');

    if (editImageInput) {
      editImageInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
          const reader = new FileReader();
          reader.onload = function(e) {
            editPreviewImg.src = e.target.result;
            editPreviewImg.classList.add('clickable-image');
            editPreviewImg.setAttribute('data-src', e.target.result);
            editImagePreview.style.display = 'block';
          };
          reader.readAsDataURL(this.files[0]);
        } else {
          editImagePreview.style.display = 'none';
        }
      });
    }

    function editPost(filename) {
      window.location.href = `event_dash.php?edit=${encodeURIComponent(filename)}`;
    }

    // Auto-hide success messages after 3 seconds
    setTimeout(function() {
      const messages = document.querySelectorAll('.message');
      messages.forEach(function(message) {
        message.style.opacity = '0';
        message.style.transition = 'opacity 0.5s ease';
        setTimeout(function() {
          message.style.display = 'none';
        }, 500);
      });
    }, 3000);

    // Ensure search suggestions dropdown matches the search bar width and position
    document.addEventListener('DOMContentLoaded', function() {
      const searchBar = document.getElementById('searchBar');
      const searchSuggestions = document.getElementById('searchSuggestions');
      function positionSuggestions() {
        if (!searchBar || !searchSuggestions) return;
        const rect = searchBar.getBoundingClientRect();
        const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        searchSuggestions.style.width = rect.width + 'px';
        searchSuggestions.style.left = 0;
        searchSuggestions.style.top = rect.height + 'px';
      }
      positionSuggestions();
      window.addEventListener('resize', positionSuggestions);
      window.addEventListener('scroll', positionSuggestions);
    });

    // Live link preview for More Link (URL)
    const linkInput = document.getElementById('linkInput');
    const linkPreview = document.getElementById('linkPreview');
    const linkPreviewAnchor = document.getElementById('linkPreviewAnchor');
    if (linkInput && linkPreview && linkPreviewAnchor) {
      linkInput.addEventListener('input', function() {
        const val = linkInput.value.trim();
        if (val) {
          linkPreview.style.display = 'block';
          linkPreviewAnchor.href = val;
          linkPreviewAnchor.textContent = val;
        } else {
          linkPreview.style.display = 'none';
        }
      });
    }
  </script>
</body>
</html> 