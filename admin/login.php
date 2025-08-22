<?php
session_start();
$ADMIN_USER = "Sithija";
$ADMIN_HASHED_PASS = '$2y$10$l.POJG/jg5J.TACXHeDBpuQDj.HWetdbPW10TfxV37HcwNlLVUiRa';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    if ($user === $ADMIN_USER && password_verify($pass, $ADMIN_HASHED_PASS)) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: event_dash.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Login</title>
  <style>
    body {
      background: #121212;
      color: #eee;
      font-family: Arial, sans-serif;
      display: flex;
      height: 100vh;
      justify-content: center;
      align-items: center;
      margin: 0;
    }
    form {
      background: #222;
      padding: 2em;
      border-radius: 8px;
      width: 300px;
      box-sizing: border-box;
    }
    input[type="text"], input[type="password"] {
      width: 100%;
      padding: 0.6em;
      margin-bottom: 1em;
      background: #333;
      border: none;
      color: #eee;
      border-radius: 4px;
      box-sizing: border-box;
    }
    button {
      width: 100%;
      padding: 0.6em;
      background: #0af;
      border: none;
      border-radius: 4px;
      font-weight: bold;
      color: white;
      cursor: pointer;
    }
    .error {
      background: #a00;
      padding: 0.5em;
      margin-bottom: 1em;
      border-radius: 4px;
      color: #fcc;
      text-align: center;
    }
    h2 {
      margin-top: 0;
      margin-bottom: 1em;
      text-align: center;
    }
  </style>
</head>
<body>
  <form method="POST" action="">
    <h2>Admin Login</h2>
    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <input type="text" name="username" placeholder="Username" required autofocus />
    <input type="password" name="password" placeholder="Password" required />
    <button type="submit">Login</button>
  </form>
</body>
</html>
