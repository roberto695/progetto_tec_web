<?php
session_start();


include __DIR__ . '/db.php';


$sql = "SELECT * FROM persona";
$result = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($result)) {
    echo $row['nome'] . " " . $row['cognome']  ;
}

include __DIR__ . "/template/login.html";
?>
