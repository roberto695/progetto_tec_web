<?php
    session_start();

//blocco per il menu dinamico 
$header_menu = include __DIR__ . "/header.php";

$content = file_get_contents(__DIR__ . "/template/prenotazioni.html");

$content = str_replace("[header_menu]", $header_menu, $content);

echo $content;
?>