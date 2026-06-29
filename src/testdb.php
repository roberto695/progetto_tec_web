<?php
// ============================================================
// src/test-db.php – Test connessione al database
// ============================================================

require_once __DIR__ . '/db.php';

echo "<h1>🔍 Test Connessione Database</h1>";

// 1. Verifica connessione
echo "<h2>✅ Connessione riuscita!</h2>";
echo "<p>Host: " . DB_HOST . "</p>";
echo "<p>Database: " . DB_NAME . "</p>";

// 2. Verifica tabella persona
try {
    $stmt = $pdo->query("DESCRIBE persona");
    $colonne = $stmt->fetchAll();
    
    echo "<h2>📋 Struttura tabella 'persona'</h2>";
    echo "<table border='1' cellpadding='8'>";
    echo "<tr><th>Colonna</th><th>Tipo</th><th>Null</th><th>Chiave</th></tr>";
    foreach ($colonne as $col) {
        echo "<tr>";
        echo "<td>" . $col['Field'] . "</td>";
        echo "<td>" . $col['Type'] . "</td>";
        echo "<td>" . $col['Null'] . "</td>";
        echo "<td>" . $col['Key'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (PDOException $e) {
    echo "<p style='color:red;'>❌ Tabella 'persona' non trovata: " . $e->getMessage() . "</p>";
}

// 3. Verifica dati
try {
    $stmt = $pdo->query("SELECT cf, nome, cognome, email, password FROM persona");
    $utenti = $stmt->fetchAll();
    
    echo "<h2>👤 Utenti nel database</h2>";
    if (count($utenti) > 0) {
        echo "<table border='1' cellpadding='8'>";
        echo "<tr><th>CF</th><th>Nome</th><th>Cognome</th><th>Email</th><th>Password</th></tr>";
        foreach ($utenti as $u) {
            echo "<tr>";
            echo "<td>" . $u['cf'] . "</td>";
            echo "<td>" . $u['nome'] . "</td>";
            echo "<td>" . $u['cognome'] . "</td>";
            echo "<td>" . $u['email'] . "</td>";
            echo "<td>" . $u['password'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color:red;'>❌ Nessun utente trovato!</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red;'>❌ Errore: " . $e->getMessage() . "</p>";
}

// 4. Test login con l'utente di test
echo "<h2>🔐 Test Login</h2>";
$cf_test = 'RSSMRA80A01H501X';
$password_test = 'user123';

try {
    $stmt = $pdo->prepare("SELECT cf, nome, cognome, password FROM persona WHERE cf = ?");
    $stmt->execute([$cf_test]);
    $utente = $stmt->fetch();
    
    if ($utente) {
        echo "<p>✅ Utente trovato: " . $utente['nome'] . " " . $utente['cognome'] . "</p>";
        if ($utente['password'] === $password_test) {
            echo "<p style='color:green;'>✅ Password corretta per " . $cf_test . "</p>";
        } else {
            echo "<p style='color:red;'>❌ Password non corretta per " . $cf_test . "</p>";
            echo "<p>Password nel DB: " . $utente['password'] . "</p>";
            echo "<p>Password inserita: " . $password_test . "</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ Utente non trovato: " . $cf_test . "</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red;'>❌ Errore: " . $e->getMessage() . "</p>";
}
?>