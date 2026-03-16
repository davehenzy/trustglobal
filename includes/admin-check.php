<?php
if(!defined('SWIFTCAP_SECURE')) exit('Direct access prohibited');
require_once 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../login.php");
    exit();
}
?>
