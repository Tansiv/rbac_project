<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$postId = intval($_POST['post_id'] ?? 0);
$csrf   = $_POST['_csrf'] ?? '';
if (!validateCsrfToken($csrf)) {
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}
if (!$postId) {
    echo json_encode(['error' => 'Invalid post ID']);
    exit;
}

$db   = getDB();
$stmt = $db->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$postId]);
$post = $stmt->fetch();

if (!$post) {
    echo json_encode(['error' => 'Post not found']);
    exit;
}

if (!canDeletePost($post['user_id'])) {
    echo json_encode(['error' => 'You do not have permission to delete this post']);
    exit;
}

$db->prepare("DELETE FROM posts WHERE id = ?")->execute([$postId]);
echo json_encode(['success' => true]);
?>
