<?php
// SwiftCapital Logout Script
session_start();
session_unset();
session_destroy();
header("Location: login.php");
exit();
?>
