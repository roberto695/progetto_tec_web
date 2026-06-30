<?php
// ============================================================
// test-db.php – Test connessione al database
// ============================================================

require_once __DIR__ . '/db.php';

echo "<!DOCTYPE html>
<html lang='it'>
<head>
    <meta charset='utf-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Test Database</title>
    <style>
        body { font-family: 'Inter', Arial, sans-serif; background: #f8fafc; padding: 20px; }
        h1 { color: #0066cc; }
        h2 { color: #1a1a1a; margin-top: 30px; }
        table { border-collapse: collapse; width: 100%; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.08); }
        th { background: #0066cc; color: white; padding: 12px 16px; text-align: left; }
        td { padding: 10px 16px; border-bottom: 1px solid #e2e8f0; }
        tr:hover { background: #f1f5f9; }
        .success { color: #1b7a3e; font-weight: 700; }
        .error { color: #d32f2f; font-weight: 700; }
        .badge {
            display: inline-block;
            padding: 2px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        .badge-prenotato { background: #e0f0ff; color: #005a9e; }
        .badge-effettuato { background: #dcfce7; color: #166534; }
        .badge-cancellato { background: #fee2e2; color: #b91c1c; }
        .badge-expired { background: #fef3c7; color: #92400e; }
    </style>
</head>
<body>";

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
    echo "<table>";
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

// 3. Verifica dati nella tabella persona
try {
    $stmt = $pdo->query("SELECT cf, nome, cognome, telefono, email, password FROM persona");
    $utenti = $stmt->fetchAll();
    
    echo "<h2>👤 Utenti nel database</h2>";
    if (count($utenti) > 0) {
        echo "<table>";
        echo "<tr><th>CF</th><th>Nome</th><th>Cognome</th><th>Telefono</th><th>Email</th><th>Password</th></tr>";
        foreach ($utenti as $u) {
            echo "<tr>";
            echo "<td>" . $u['cf'] . "</td>";
            echo "<td>" . $u['nome'] . "</td>";
            echo "<td>" . $u['cognome'] . "</td>";
            echo "<td>" . ($u['telefono'] ?? '<span style="color:#999;">—</span>') . "</td>";
            echo "<td>" . $u['email'] . "</td>";
            echo "<td>" . $u['password'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>⚠️ Nessun utente trovato.</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red;'>❌ Errore: " . $e->getMessage() . "</p>";
}

// 4. ✅ VERIFICA PRENOTAZIONI (AGGIUNTO)
try {
    $stmt = $pdo->query("
        SELECT p.id, p.data_ora, p.stato, 
               pe.cf, pe.nome, pe.cognome, pe.telefono
        FROM prenotazione p
        JOIN persona pe ON p.persona_id = pe.cf
        ORDER BY p.data_ora DESC
    ");
    $prenotazioni = $stmt->fetchAll();
    
    echo "<h2>📅 Prenotazioni</h2>";
    if (count($prenotazioni) > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Paziente</th><th>CF</th><th>Data e Ora</th><th>Stato</th></tr>";
        
        // Array per le classi badge
        $badgeClass = [
            'prenotato' => 'badge-prenotato',
            'effettuato' => 'badge-effettuato',
            'cancellato' => 'badge-cancellato',
            'expired' => 'badge-expired'
        ];
        
        foreach ($prenotazioni as $p) {
            $badge = $badgeClass[$p['stato']] ?? 'badge-prenotato';
            $statoLeggibile = [
                'prenotato' => 'Prenotato',
                'effettuato' => 'Effettuato',
                'cancellato' => 'Cancellato',
                'expired' => 'Scaduto'
            ][$p['stato']] ?? $p['stato'];
            
            echo "<tr>";
            echo "<td>" . $p['id'] . "</td>";
            echo "<td><strong>" . $p['nome'] . ' ' . $p['cognome'] . "</strong></td>";
            echo "<td>" . $p['cf'] . "</td>";
            echo "<td>" . date('d/m/Y H:i', strtotime($p['data_ora'])) . "</td>";
            echo "<td><span class='badge $badge'>$statoLeggibile</span></td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p><strong>Totale prenotazioni:</strong> " . count($prenotazioni) . "</p>";
    } else {
        echo "<p>⚠️ Nessuna prenotazione trovata.</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color:red;'>❌ Errore nel caricamento delle prenotazioni: " . $e->getMessage() . "</p>";
}

// 5. Test login con Mario Rossi
echo "<h2>🔐 Test Login</h2>";
$cf_test = 'RSSMRA80A01H501X';
$password_test = 'user1234';

try {
    $stmt = $pdo->prepare("SELECT cf, nome, cognome, password FROM persona WHERE cf = ?");
    $stmt->execute([$cf_test]);
    $utente = $stmt->fetch();
    
    if ($utente) {
        echo "<p>✅ Utente trovato: " . $utente['nome'] . ' ' . $utente['cognome'] . "</p>";
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

// 6. Test login Admin
echo "<h2>🔐 Test Login Admin</h2>";
$cf_admin = 'ADMINVTLPTH00A00';
$password_admin = 'admin1234';

try {
    $stmt = $pdo->prepare("SELECT cf, nome, cognome, password FROM persona WHERE cf = ?");
    $stmt->execute([$cf_admin]);
    $utente = $stmt->fetch();
    
    if ($utente) {
        echo "<p>✅ Admin trovato: " . $utente['nome'] . ' ' . $utente['cognome'] . "</p>";
        if ($utente['password'] === $password_admin) {
            echo "<p style='color:green;'>✅ Password corretta per " . $cf_admin . "</p>";
        } else {
            echo "<p style='color:red;'>❌ Password non corretta per " . $cf_admin . "</p>";
        }
    } else {
        echo "<p style='color:red;'>❌ Admin non trovato: " . $cf_admin . "</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color:red;'>❌ Errore: " . $e->getMessage() . "</p>";
}

// 7. Statistiche prenotazioni
try {
    $stmt = $pdo->query("
        SELECT stato, COUNT(*) as totale 
        FROM prenotazione 
        GROUP BY stato
    ");
    $stats = $stmt->fetchAll();
    
    echo "<h2>📊 Statistiche prenotazioni</h2>";
    if (count($stats) > 0) {
        echo "<table>";
        echo "<tr><th>Stato</th><th>Totale</th></tr>";
        $statiLeggibili = [
            'prenotato' => 'Prenotato',
            'effettuato' => 'Effettuato',
            'cancellato' => 'Cancellato',
            'expired' => 'Scaduto'
        ];
        foreach ($stats as $s) {
            $label = $statiLeggibili[$s['stato']] ?? $s['stato'];
            echo "<tr><td>$label</td><td><strong>" . $s['totale'] . "</strong></td></tr>";
        }
        echo "</table>";
    }
} catch (PDOException $e) {
    // Ignora se la tabella non esiste
}

echo "</body></html>";
?>