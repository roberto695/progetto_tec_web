<?php
 /*
    try {
        $pdo = new PDO("mysql:host=database;dbname=prelievi_db", "user_web", "8A2cU25SoU9zmUrewcib2FgGsY9juEyPrSnFdXBJypa6xfhOmC");
        echo "Database: Connessione Riuscita!";
    }
    catch (PDOException $e) {
        echo "Database: Errore: " . $e->getMessage();
    }
*/

session_start();

//blocco per il menu dinamico 
$header_menu = include __DIR__ . "/header.php";

$content = file_get_contents(__DIR__ . "/template/index.html");

$content = str_replace("[header_menu]", $header_menu, $content);

echo $content;

?>