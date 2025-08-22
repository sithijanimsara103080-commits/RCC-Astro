<?php
// go.php - Simple URL redirector
if (!isset($_GET['u'])) {
    http_response_code(400);
    echo 'Missing URL parameter.';
    exit;
}

$url = $_GET['u'];
$url = base64_decode($url, true);

// Validate URL
if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
    http_response_code(400);
    echo 'Invalid or missing URL.';
    exit;
}

// Optionally, restrict to http/https only
if (!preg_match('/^https?:\/\//i', $url)) {
    http_response_code(400);
    echo 'Only http and https URLs are allowed.';
    exit;
}

header('Location: ' . $url);
exit; 