<?php
session_start();
session_destroy();
header("Location: index.html"); // Vervang door je inlogpagina
exit();
?>
