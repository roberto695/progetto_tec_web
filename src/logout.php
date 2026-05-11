<?php
require_once __DIR__ . "/db.php";

logoutUser();

header("Location: login.php");
exit();
?>