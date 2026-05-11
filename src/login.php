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

$menu_login = "";
$menu_account = "";

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

// pagina login e utente loggato dinamica
if (isset($_SESSION['cf'])) {
    $menu_login = "";
    $menu_account = '<a href="account.php">Account</a>';
} else {
    $menu_login = '<a href="login.php">Login</a>';
    $menu_account = "";
}


$content = file_get_contents(__DIR__ . "/template/login.html");

$content = str_replace("[errore_cf]", $errore_cf, $content);
$content = str_replace("[errore_password]", $errore_password, $content);
$content = str_replace("[menu_login]", $menu_login, $content);
$content = str_replace("[menu_account]", $menu_account, $content);

echo $content;
?>
