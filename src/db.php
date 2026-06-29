<?php
// ============================================================
// includes/db.php – Connessione PDO al database VitalPath
// ============================================================

// Credenziali CORRETTE dal tuo progetto
define('DB_HOST', 'database');
define('DB_NAME', 'prelievi_db');      // ← CORRETTO: 'prelievi_db'
define('DB_USER', 'user_web');
define('DB_PASS', '8A2cU25SoU9zmUrewcib2FgGsY9juEyPrSnFdXBJypa6xfhOmC');  // ← CORRETTO
define('DB_CHARSET', 'utf8mb4');

$dsn = sprintf(
    'mysql:host=%s;dbname=%s;charset=%s',
    DB_HOST, DB_NAME, DB_CHARSET
);

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // DEBUG: mostra l'errore per capire cosa succede
    error_log('Connessione DB fallita: ' . $e->getMessage());
    die('Errore di connessione al database: ' . $e->getMessage());
}
?>