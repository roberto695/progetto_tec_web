<?php
session_start();
require_once __DIR__ . "/db.php";

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome = trim($_POST['nome']);
    $cognome = trim($_POST['cognome']);
    $cf = trim($_POST['cf']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    //controlli sau campi da aggiungere che cosi e troppo semplice 
    if (empty($nome) || empty($cognome) || empty($cf) || empty($email) || empty($password)) {
        $errors[] = "Tutti i campi obbligatori devono essere compilati.";
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO persona (nome, cognome, cf, telefono, email, password)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $nome, $cognome, $cf, $telefono, $email, $password_hash);

    //controllo se ha fatto poi da togliere
    if ($stmt->execute()) {
        echo "<p>Registrazione completata! Ora puoi effettuare il login.</p>";
        exit;
    } else {
        echo "<p>Errore durante la registrazione: " . $stmt->error . "</p>";
    }
}

include __DIR__ . "/template/registrazione.html";
?>

