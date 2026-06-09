<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

if (!canCreateComment()) {
    echo json_encode(['error' => 'You do not have permission to comment']);
    exit;
}

$postId  = intval($_POST['post_id'] ?? 0);
$content = trim($_POST['content'] ?? '');
$csrf    = $_POST['_csrf'] ?? '';

if (!validateCsrfToken($csrf)) {
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}

if (!$postId || !$content) {
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

$db = getDB();

// Verify post exists and get post owner
$stmt = $db->prepare("SELECT user_id FROM posts WHERE id = ?");
$stmt->execute([$postId]);
$post = $stmt->fetch();

if (!$post) {
    echo json_encode(['error' => 'Post not found']);
    exit;
}

// Insert comment
$ins = $db->prepare("INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)");
$ins->execute([$postId, $_SESSION['user_id'], $content]);
$commentId = $db->lastInsertId();

// Return comment data for live rendering
echo json_encode([
    'success' => true,
    'comment' => [
        'id'         => $commentId,
        'username'   => $_SESSION['username'],
        'content'    => $content,
        'badge_class'=> roleBadgeClass(currentRole()),
        'role_label' => roleLabel(currentRole()),
        'can_delete' => canDeleteComment($_SESSION['user_id'], $post['user_id']),
    ]
]);
?>
