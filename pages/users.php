<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
requireLogin();

if (!canManageUsers()) {
    http_response_code(403);
    die('<div style="text-align:center;padding:4rem;font-family:sans-serif;"><h2>403 — Forbidden</h2><p>Only Super Admins can manage users.</p><a href="/rbac_project/pages/posts.php">← Back</a></div>');
}

$db = getDB();

// Handle role change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_role'])) {
    if (!validateCsrfToken($_POST['_csrf'] ?? '')) {
        http_response_code(400);
        die('Invalid CSRF token.');
    }
    $uid    = intval($_POST['user_id']);
    $roleId = intval($_POST['role_id']);
    if ($uid !== $_SESSION['user_id']) { // cannot change own role
        $db->prepare("UPDATE users SET role_id = ? WHERE id = ?")->execute([$roleId, $uid]);
    }
    header('Location: /rbac_project/pages/users.php');
    exit;
}

// Handle delete user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    if (!validateCsrfToken($_POST['_csrf'] ?? '')) {
        http_response_code(400);
        die('Invalid CSRF token.');
    }
    $uid = intval($_POST['user_id']);
    if ($uid !== $_SESSION['user_id']) {
        $db->prepare("DELETE FROM users WHERE id = ?")->execute([$uid]);
    }
    header('Location: /rbac_project/pages/users.php');
    exit;
}

$users = $db->query("
    SELECT u.*, r.name AS role, r.label AS role_label, r.id AS role_id
    FROM users u JOIN roles r ON u.role_id = r.id
    ORDER BY u.created_at DESC
")->fetchAll();

$roles = $db->query("SELECT * FROM roles")->fetchAll();

$pageTitle = 'User Management — RBAC App';
include __DIR__ . '/../includes/header.php';
?>
<div class="page-wrap">
    <div class="page-header">
        <h1 class="page-title">👥 User Management</h1>
        <span class="badge badge-super">Super Admin Only</span>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td>
                        <strong><?= htmlspecialchars($u['username']) ?></strong>
                        <?php if ($u['id'] == $_SESSION['user_id']): ?>
                            <span style="font-size:.72rem;color:var(--primary);font-weight:700;"> (you)</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><span class="badge <?= roleBadgeClass($u['role']) ?>"><?= htmlspecialchars($u['role_label']) ?></span></td>
                    <td><?= date('M j, Y', strtotime($u['created_at'])) ?></td>
                    <td>
                        <?php if ($u['id'] !== (int)$_SESSION['user_id']): ?>
                        <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
                            <!-- Change role form -->
                            <form method="POST" style="display:flex;gap:.35rem;align-items:center;">
                                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(generateCsrfToken()) ?>"/>
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>"/>
                                <select name="role_id" class="form-select" style="padding:.3rem .6rem;font-size:.8rem;width:auto;">
                                    <?php foreach ($roles as $r): ?>
                                        <option value="<?= $r['id'] ?>" <?= $r['id'] == $u['role_id'] ? 'selected' : '' ?>><?= htmlspecialchars($r['label']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit" name="change_role" class="btn btn-warning btn-sm">Save</button>
                            </form>
                            <!-- Delete user form -->
                            <form method="POST" onsubmit="return confirm('Delete user <?= htmlspecialchars($u['username']) ?>? This will remove all their posts and comments.')">
                                <input type="hidden" name="_csrf" value="<?= htmlspecialchars(generateCsrfToken()) ?>"/>
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>"/>
                                <button type="submit" name="delete_user" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                        <?php else: ?>
                            <span style="font-size:.8rem;color:var(--text-muted);">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
