<?php
session_start();

header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Restul codului de logout
session_unset();
session_destroy();
header("Location: ../index.php");
exit;
?>
