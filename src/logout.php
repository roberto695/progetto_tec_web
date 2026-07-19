<?php
// ============================================================
// pages/logout.php – Distrugge la sessione e reindirizza
// ============================================================
session_start();
session_unset();
session_destroy();
header('Location: index.php');
exit;