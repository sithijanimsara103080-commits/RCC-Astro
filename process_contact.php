<?php
// Error reporting (සංවර්ධන කාලය සඳහා වැදගත්)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Form එක POST method එකෙන් submit කලාද බලන්න
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $name = htmlspecialchars(strip_tags(trim($_POST['name'])));
    $email = htmlspecialchars(strip_tags(trim($_POST['email'])));
    $message = htmlspecialchars(strip_tags(trim($_POST['message'])));

    // Basic validation
    if (empty($name) || empty($email) || empty($message)) {
        // Handle error: redirect back or show a message
        header("Location: index.php?status=error&msg=All fields are required.");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Handle error: invalid email format
        header("Location: index.php?status=error&msg=Invalid email format.");
        exit;
    }

    // --- Save to a Text File ---
    // මෙම ගොනුව public folder එකෙන් පිටත තැබීම ආරක්ෂිතයි
    // උදා: 'process_contact.php' ගොනුව 'public' folder එකේ නම්, 'messages.txt' 'New folder' එකේ හැදෙයි.
    $file_path = '../messages.txt';
    $timestamp = date("Y-m-d H:i:s");
    $data_to_save = "Time: " . $timestamp . "\n" .
                    "Name: " . $name . "\n" .
                    "Email: " . $email . "\n" .
                    "Message: " . $message . "\n" .
                    "-----------------------------------\n\n";

    // Append data to the file
    if (file_put_contents($file_path, $data_to_save, FILE_APPEND | LOCK_EX) === false) {
        // Error writing to file - මෙය user ට නොපෙන්වා log කිරීම වඩා සුදුසුයි
        error_log("Failed to write contact message to messages.txt file.");
        header("Location: index.php?status=error&msg=Failed to save your message. Please try again later.");
    } else {
        // Message saved successfully
        header("Location: index.php?status=success&msg=Your message has been saved successfully!");
    }
    exit;

} else {
    // If accessed directly without POST
    header("Location: index.php");
    exit;
}
?>