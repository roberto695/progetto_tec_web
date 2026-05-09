<?php
session_start();

include __DIR__ . '/db.php';

/*
$sql = "SELECT * FROM persona";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    echo $row['nome'] . " " . $row['cognome'] ."<br> " ;
}*/

$cf = "";
$password = "";
$errore_cf="";
$errore_password="";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cf = trim($_POST['cf']);
    $password = $_POST['password'];

    $result = loginUser($cf, $password);

    if ($result === "ok") {
        $_SESSION['cf'] = $cf;
        header("Location: index.php");
        exit();
    }
    elseif ($result === "errore_cf") {
        $errore_cf = $errore_cf = "<div class='errore_login'>Codice fiscale non trovato.</div>";
    }
    elseif ($result === "errore_password") {
        $errore_password = "<div class='errore_login'>Password errata.</div>";
    }
}

$content = file_get_contents(__DIR__ . "/template/login.html");

$content = str_replace("[errore_cf]", $errore_cf, $content);
$content = str_replace("[errore_password]", $errore_password, $content);

echo $content;
?>
