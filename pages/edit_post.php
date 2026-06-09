<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

$db     = getDB();
$postId = intval($_GET['id'] ?? 0);

$stmt = $db->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$postId]);
$post = $stmt->fetch();

if (!$post) {
    header('Location: /rbac_project/pages/posts.php');
    exit;
}

if (!canEditPost($post['user_id'])) {
    http_response_code(403);
    die('<div style="text-align:center;padding:4rem;font-family:sans-serif;"><h2>403 — Forbidden</h2><p>You cannot edit this post.</p><a href="/rbac_project/pages/posts.php">← Back</a></div>');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['_csrf'] ?? '')) {
        $error = 'Invalid form submission.';
    }
    $title   = trim($_POST['title']   ?? '');
    $content = trim($_POST['content'] ?? '');
    if (!$error) {
        if (!$title || !$content) {
            $error = 'Title and content are required.';
        } else {
            $upd = $db->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
            $upd->execute([$title, $content, $postId]);
            header('Location: /rbac_project/pages/posts.php');
            exit;
        }
    }
}

$pageTitle = 'Edit Post — RBAC App';
include __DIR__ . '/../includes/header.php';
?>
<div class="page-wrap" style="max-width:640px;">
    <div class="page-header">
        <h1 class="page-title">✏️ Edit Post</h1>
        <a href="/rbac_project/pages/posts.php" class="btn btn-outline">← Back</a>
    </div>
    <div class="card">
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(generateCsrfToken()) ?>"/>
            <div class="form-group">
                <label class="form-label">Title</label>
                <input class="form-input" type="text" name="title" value="<?= htmlspecialchars($post['title']) ?>" required/>
            </div>
            <div class="form-group">
                <label class="form-label">Content</label>
                <textarea class="form-textarea" name="content" style="min-height:160px;" required><?= htmlspecialchars($post['content']) ?></textarea>
            </div>
            <div style="display:flex;gap:.75rem;justify-content:flex-end;margin-top:.5rem;">
                <a href="/rbac_project/pages/posts.php" class="btn btn-ghost">Cancel</a>
                <button class="btn btn-primary" type="submit">Save Changes →</button>
            </div>
        </form>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
