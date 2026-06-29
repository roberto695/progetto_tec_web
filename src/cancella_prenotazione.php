<?php
// ============================================================
// cancella_prenotazione.php – Cancella un appuntamento attivo
// Accetta solo POST da utente loggato; aggiorna stato su 'cancellato'
// ============================================================
session_start();

// Verifica login
if (!isset($_SESSION['cf'])) {
    header('Location: login.php');
    exit;
}

// Verifica che sia una richiesta GET con ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    require_once __DIR__ . '/db.php';
    $cf = $_SESSION['cf'];
    // Aggiorna solo se la prenotazione appartiene all'utente ed è ancora attiva
    $stmt = $pdo->prepare(
        "UPDATE prenotazione
         SET stato = 'cancellato'
         WHERE id = ?
           AND persona_id = ?
           AND stato IN ('prenotato')"
    );
    $stmt->execute([$id, $cf]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['success'] = 'Prenotazione annullata con successo.';
    } else {
        $_SESSION['error'] = 'Impossibile annullare la prenotazione.';
    }
}

header('Location: dashboard.php');
exit;