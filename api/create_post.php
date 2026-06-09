<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

if (!canCreatePost()) {
    header('Location: /rbac_project/pages/posts.php');
    exit;
}

$title   = trim($_POST['title']   ?? '');
$content = trim($_POST['content'] ?? '');
$csrf    = $_POST['_csrf'] ?? '';

if (!validateCsrfToken($csrf)) {
    header('Location: /rbac_project/pages/posts.php');
    exit;
}

if ($title && $content) {
    $db = getDB();
    $db->prepare("INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)")
       ->execute([$_SESSION['user_id'], $title, $content]);
}

header('Location: /rbac_project/pages/posts.php');
exit;
?>
