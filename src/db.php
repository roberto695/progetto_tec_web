<?php


$host = "database";
$user = "user_web";
$pass = "8A2cU25SoU9zmUrewcib2FgGsY9juEyPrSnFdXBJypa6xfhOmC";
$dbname = "prelievi_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Errore di connessione: " . $conn->connect_error);
}
?>