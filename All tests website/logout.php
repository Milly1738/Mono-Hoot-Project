<?php
session_start();
session_unset();   // Clear all session variables
session_destroy(); // Destroy the session structure completely
header("Location: login.php"); // Send them back to the login page
exit;
?>
