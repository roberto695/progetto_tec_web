<?php
session_start();
require_once __DIR__ . "/db.php";
/*
if (!isset($_SESSION['cf'])) {
    header("Location: login.php");
    exit();
}*/
$utente = getUserInfo($_SESSION['cf']);

$header_menu = include __DIR__ . "/header.php";

$content = file_get_contents(__DIR__ . "/template/account.html");

$content = str_replace("[header_menu]", $header_menu, $content);
$content = str_replace("[nome]", $utente['nome'], $content);
$content = str_replace("[cognome]", $utente['cognome'], $content);
$content = str_replace("[email]", $utente['email'], $content);
$content = str_replace("[telefono]", $utente['telefono'], $content);

echo $content;
?>