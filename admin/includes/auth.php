<?php
require_once __DIR__ . '/../../includes/functions.php';
require_login();

$current_admin_id       = $_SESSION['admin_id'];
$current_admin_username = $_SESSION['admin_username'] ?? '';
