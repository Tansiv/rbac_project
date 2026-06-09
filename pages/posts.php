<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

// Guests can view — no login required here
$db = getDB();

// Fetch all posts with author info
$posts = $db->query("
    SELECT p.*, u.username, r.name AS role, r.label AS role_label
    FROM posts p
    JOIN users u ON p.user_id = u.id
    JOIN roles  r ON u.role_id = r.id
    ORDER BY p.created_at DESC
")->fetchAll();

// Fetch all comments for all posts in one query
$comments = $db->query("
    SELECT c.*, u.username, r.name AS role, r.label AS role_label
    FROM comments c
    JOIN users u ON c.user_id = u.id
    JOIN roles  r ON u.role_id = r.id
    ORDER BY c.created_at ASC
")->fetchAll();

// Group comments by post_id
$commentsByPost = [];
foreach ($comments as $cmt) {
    $commentsByPost[$cmt['post_id']][] = $cmt;
}

$pageTitle = 'Posts — RBAC App';
include __DIR__ . '/../includes/header.php';
?>

<div class="page-wrap">
    <div class="page-header">
        <h1 class="page-title">📋 Posts</h1>
        <?php if (canCreatePost()): ?>
            <button class="btn btn-primary" onclick="document.getElementById('newPostModal').classList.add('active')">
                + New Post
            </button>
        <?php endif; ?>
    </div>

    <?php if (empty($posts)): ?>
        <div class="empty-state">
            <div class="empty-icon">📭</div>
            <h3>No posts yet</h3>
            <p>Be the first to create a post!</p>
        </div>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
        <div class="card post-card" id="post-<?= $post['id'] ?>">
            <div class="card-header">
                <div>
                    <div class="card-title"><?= htmlspecialchars($post['title']) ?></div>
                    <div class="card-meta">
                        <span>✍️ <?= htmlspecialchars($post['username']) ?></span>
                        <span class="badge <?= roleBadgeClass($post['role']) ?>"><?= htmlspecialchars($post['role_label']) ?></span>
                        <span>🕐 <?= date('M j, Y · g:i A', strtotime($post['created_at'])) ?></span>
                    </div>
                </div>
                <div class="card-actions">
                    <?php if (canEditPost($post['user_id'])): ?>
                        <a href="/rbac_project/pages/edit_post.php?id=<?= $post['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <?php endif; ?>
                    <?php if (canDeletePost($post['user_id'])): ?>
                        <button class="btn btn-danger btn-sm" onclick="deletePost(<?= $post['id'] ?>)">Delete</button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body"><?= nl2br(htmlspecialchars($post['content'])) ?></div>

            <!-- Comments section -->
            <div class="comments-section">
                <div class="comments-title">
                    💬 Comments (<?= count($commentsByPost[$post['id']] ?? []) ?>)
                    <?php if (canCreateComment()): ?>
                        <button class="btn btn-ghost btn-sm" onclick="toggleCommentForm(<?= $post['id'] ?>)" style="margin-left:.5rem;font-size:.78rem;">+ Add</button>
                    <?php endif; ?>
                </div>

                <div id="cmtList-<?= $post['id'] ?>">
                <?php foreach ($commentsByPost[$post['id']] ?? [] as $cmt): ?>
                    <div class="comment-item" id="cmt-<?= $cmt['id'] ?>">
                        <div class="comment-avatar av-<?= abs(crc32($cmt['username'])) % 6 ?>">
                            <?= strtoupper($cmt['username'][0]) ?>
                        </div>
                        <div class="comment-content">
                            <span class="comment-author"><?= htmlspecialchars($cmt['username']) ?></span>
                            <span class="badge <?= roleBadgeClass($cmt['role']) ?>" style="font-size:.65rem;margin-left:.35rem;"><?= htmlspecialchars($cmt['role_label']) ?></span>
                            <p class="comment-text"><?= htmlspecialchars($cmt['content']) ?></p>
                            <span class="comment-time"><?= date('M j, Y · g:i A', strtotime($cmt['created_at'])) ?></span>
                        </div>
                        <?php if (canDeleteComment($cmt['user_id'], $post['user_id'])): ?>
                            <button class="comment-del" onclick="deleteComment(<?= $cmt['id'] ?>)">✕</button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                </div>

                <?php if (canCreateComment()): ?>
                <form class="comment-form" id="cf-<?= $post['id'] ?>" style="display:none;" onsubmit="addComment(event, <?= $post['id'] ?>)">
                    <input type="text" id="cmtInput-<?= $post['id'] ?>" placeholder="Write a comment…" autocomplete="off"/>
                    <button class="btn btn-primary btn-sm" type="submit">Post</button>
                </form>
                <?php elseif (!isLoggedIn()): ?>
                    <p style="font-size:.8rem;color:var(--text-muted);margin-top:.5rem;">
                        <a href="/rbac_project/pages/login.php" style="color:var(--primary);">Login</a> to comment.
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- New Post Modal -->
<?php if (canCreatePost()): ?>
<div class="modal-overlay" id="newPostModal">
    <div class="modal" style="max-width:560px;">
        <div class="modal-title">✍️ Create New Post</div>
        <form method="POST" action="/rbac_project/api/create_post.php">
            <div class="form-group" style="margin-top:.75rem;">
                <label class="form-label">Title</label>
                <input class="form-input" type="text" name="title" placeholder="Post title…" required/>
            </div>
            <div class="form-group">
                <label class="form-label">Content</label>
                <textarea class="form-textarea" name="content" placeholder="Write your post content here…" required></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-ghost" onclick="document.getElementById('newPostModal').classList.remove('active')">Cancel</button>
                <button type="submit" class="btn btn-primary">Publish Post →</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
