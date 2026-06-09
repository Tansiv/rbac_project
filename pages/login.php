<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

if (isLoggedIn()) {
    header('Location: /rbac_project/pages/posts.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username && $password) {
        $db   = getDB();
        $stmt = $db->prepare("SELECT u.*, r.name AS role FROM users u JOIN roles r ON u.role_id = r.id WHERE u.username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];
            $_SESSION['email']    = $user['email'];
            header('Location: /rbac_project/pages/posts.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}

$pageTitle = 'Login — RBAC App';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= $pageTitle ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="/rbac_project/assets/css/style.css"/>
</head>
<body>
<div class="form-wrap">
    <div class="form-card">
        <div class="auth-hero">
            <div class="form-title">Welcome back ✦</div>
            <div class="form-subtitle">Sign in to your RBAC account</div>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label class="form-label">Username</label>
                <input class="form-input" type="text" name="username" placeholder="e.g. alice" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required/>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input class="form-input" type="password" name="password" placeholder="••••••••" required/>
            </div>
            <button class="btn btn-primary form-btn" type="submit">Sign In →</button>
        </form>
        <div class="form-footer">
            No account? <a href="/rbac_project/pages/register.php">Register here</a>
        </div>

        <!-- Demo credentials box -->
        <div style="margin-top:1.5rem;background:var(--bg);border-radius:10px;padding:1rem;font-size:.8rem;">
            <div style="font-weight:700;margin-bottom:.5rem;color:var(--text);">🔑 Demo accounts (password: <code>password</code>)</div>
            <table style="width:100%;border-collapse:collapse;">
                <tr><td style="padding:.2rem .4rem;color:var(--text-muted);">superadmin</td><td><span class="badge badge-super">Super Admin</span></td></tr>
                <tr><td style="padding:.2rem .4rem;color:var(--text-muted);">moderator</td><td><span class="badge badge-mod">Moderator</span></td></tr>
                <tr><td style="padding:.2rem .4rem;color:var(--text-muted);">alice</td><td><span class="badge badge-user">Regular User</span></td></tr>
                <tr><td style="padding:.2rem .4rem;color:var(--text-muted);">bob</td><td><span class="badge badge-user">Regular User</span></td></tr>
                <tr><td style="padding:.2rem .4rem;color:var(--text-muted);">guest</td><td><span class="badge badge-guest">Guest</span></td></tr>
            </table>
        </div>
    </div>
</div>
<script src="/rbac_project/assets/js/main.js"></script>
</body>
</html>
