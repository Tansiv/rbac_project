<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

if (!isLoggedIn()) {
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$commentId = intval($_POST['comment_id'] ?? 0);
if (!$commentId) {
    echo json_encode(['error' => 'Invalid comment ID']);
    exit;
}

$db   = getDB();
$stmt = $db->prepare("
    SELECT c.*, p.user_id AS post_owner_id
    FROM comments c
    JOIN posts p ON c.post_id = p.id
    WHERE c.id = ?
");
$stmt->execute([$commentId]);
$comment = $stmt->fetch();

if (!$comment) {
    echo json_encode(['error' => 'Comment not found']);
    exit;
}

if (!canDeleteComment($comment['user_id'], $comment['post_owner_id'])) {
    echo json_encode(['error' => 'You do not have permission to delete this comment']);
    exit;
}

$db->prepare("DELETE FROM comments WHERE id = ?")->execute([$commentId]);
echo json_encode(['success' => true]);
?>
