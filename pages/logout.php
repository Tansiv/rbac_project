<?php
require_once __DIR__ . '/../includes/auth.php';
session_destroy();
header('Location: /rbac_project/pages/login.php');
exit;
?>
