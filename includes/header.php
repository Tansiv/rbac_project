<?php
require_once __DIR__ . '/auth.php';
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= $pageTitle ?? 'RBAC App' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="/rbac_project/assets/css/style.css"/>
</head>
<body>

<nav class="navbar">
    <div class="nav-brand">
        <span class="brand-icon">⬡</span>
        <span class="brand-name">RBAC<span class="brand-accent">App</span></span>
    </div>
    <div class="nav-links">
        <a href="/rbac_project/pages/posts.php" class="nav-link">Posts</a>
        <?php if (canManageUsers()): ?>
        <a href="/rbac_project/pages/users.php" class="nav-link">Users</a>
        <?php endif; ?>
    </div>
    <div class="nav-right">
        <?php if (isLoggedIn()): ?>
            <span class="badge <?= roleBadgeClass(currentRole()) ?>"><?= roleLabel(currentRole()) ?></span>
            <span class="nav-username">@<?= htmlspecialchars($user['username']) ?></span>
            <a href="/rbac_project/pages/logout.php" class="btn btn-outline-sm">Logout</a>
        <?php else: ?>
            <a href="/rbac_project/pages/login.php" class="btn btn-primary-sm">Login</a>
            <a href="/rbac_project/pages/register.php" class="btn btn-outline-sm">Register</a>
        <?php endif; ?>
    </div>
</nav>
