<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

if (isLoggedIn()) {
    header('Location: /rbac_project/pages/posts.php');
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';
    $confirm  = $_POST['confirm']       ?? '';

    if (!$username || !$email || !$password || !$confirm) {
        $error = 'Please fill in all fields.';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $db = getDB();
        // Check duplicate
        $s = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $s->execute([$username, $email]);
        if ($s->fetch()) {
            $error = 'Username or email already taken.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            // New users get Regular User role (id=3) by default
            $ins = $db->prepare("INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, 3)");
            $ins->execute([$username, $email, $hash]);
            $success = 'Account created! You can now <a href="/rbac_project/pages/login.php">login</a>.';
        }
    }
}

$pageTitle = 'Register — RBAC App';
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
            <div class="form-title">Create Account ✦</div>
            <div class="form-subtitle">Join as a Regular User — explore RBAC</div>
        </div>
        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label class="form-label">Username</label>
                <input class="form-input" type="text" name="username" placeholder="e.g. johndoe" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required/>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input class="form-input" type="email" name="email" placeholder="you@example.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required/>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input class="form-input" type="password" name="password" placeholder="Min 6 characters" required/>
            </div>
            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input class="form-input" type="password" name="confirm" placeholder="Repeat password" required/>
            </div>
            <button class="btn btn-primary form-btn" type="submit">Create Account →</button>
        </form>
        <div class="form-footer">
            Already have an account? <a href="/rbac_project/pages/login.php">Sign in</a>
        </div>
    </div>
</div>
<script src="/rbac_project/assets/js/main.js"></script>
</body>
</html>
