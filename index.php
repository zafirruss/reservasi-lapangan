<?php
require 'auth.php';
startSession();
if (isLoggedIn()) {
    header('Location: ' . (isAdmin() ? 'admin/dashboard.php' : 'user/dashboard.php'));
} else {
    header('Location: login.php');
}
exit;
?>
