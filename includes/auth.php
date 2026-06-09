<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── Auth helpers ─────────────────────────────────────────────
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function currentUser() {
    return $_SESSION ?? [];
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /rbac_project/pages/login.php');
        exit;
    }
}

function currentRole() {
    return $_SESSION['role'] ?? 'guest';
}

// ── Role checks ───────────────────────────────────────────────
function isSuperAdmin() { return currentRole() === 'super_admin'; }
function isModerator()  { return currentRole() === 'moderator'; }
function isRegularUser(){ return currentRole() === 'regular_user'; }
function isGuest()      { return currentRole() === 'guest'; }

// ── Permission matrix ─────────────────────────────────────────
function canCreatePost() {
    return in_array(currentRole(), ['super_admin', 'moderator', 'regular_user']);
}

function canDeletePost($postOwnerId) {
    if (isSuperAdmin()) return true;
    if (isModerator())  return true;
    if (isRegularUser() && $_SESSION['user_id'] == $postOwnerId) return true;
    return false;
}

function canEditPost($postOwnerId) {
    if (isSuperAdmin()) return true;
    if (isRegularUser() && $_SESSION['user_id'] == $postOwnerId) return true;
    return false;
}

function canCreateComment() {
    return in_array(currentRole(), ['super_admin', 'moderator', 'regular_user']);
}

function canDeleteComment($commentOwnerId, $postOwnerId) {
    if (isSuperAdmin()) return true;
    if (isModerator())  return true;
    // Comment owner can delete their own comment
    if (isLoggedIn() && $_SESSION['user_id'] == $commentOwnerId) return true;
    // Post owner can delete any comment on their post
    if (isLoggedIn() && $_SESSION['user_id'] == $postOwnerId) return true;
    return false;
}

function canManageUsers() {
    return isSuperAdmin();
}

// ── Role badge color ──────────────────────────────────────────
function roleBadgeClass($role) {
    return match($role) {
        'super_admin'  => 'badge-super',
        'moderator'    => 'badge-mod',
        'regular_user' => 'badge-user',
        default        => 'badge-guest',
    };
}

function roleLabel($role) {
    return match($role) {
        'super_admin'  => 'Super Admin',
        'moderator'    => 'Moderator',
        'regular_user' => 'Regular User',
        default        => 'Guest',
    };
}
?>
