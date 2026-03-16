<?php
require_once 'db.php';
if(!defined('SWIFTCAP_SECURE')) exit('Direct access prohibited');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Super Admin', 'Sub-Admin'])) {
    header("Location: ../login.php");
    exit();
}
?>
