<?php


$host = "database";
$user = "user_web";
$pass = "8A2cU25SoU9zmUrewcib2FgGsY9juEyPrSnFdXBJypa6xfhOmC";
$dbname = "prelievi_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Errore di connessione: " . $conn->connect_error);
}

//funzione login
function loginUser($cf, $password) {
    global $conn;

    $sql = "SELECT * FROM persona WHERE cf = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $cf);
    $stmt->execute();
    $result = $stmt->get_result();


    if ($result->num_rows === 0) {
        return "errore_cf";
    }

    $user = $result->fetch_assoc();

    if (!password_verify($password, $user['password'])) {
        return "errore_password";
    }

    return "ok";
    
}

?>