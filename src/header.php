<?php

$menu_login = "";
$menu_account = "";

if (isset($_SESSION['cf'])) {
    $menu_login = "";
    $menu_account = '<a href="account.php">Account</a>';
} else {
    $menu_login = '<a href="login.php">Login</a>';
    $menu_account = "";
}

$header_menu = file_get_contents(__DIR__ . "/template/header.html");

$header_menu = str_replace("[menu_login]", $menu_login, $header_menu);
$header_menu = str_replace("[menu_account]", $menu_account, $header_menu);

return  $header_menu;
?>