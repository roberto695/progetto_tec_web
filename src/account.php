<?php
// account.php
session_start();

// Includi la connessione al database
require_once __DIR__ . '/db.php';

// Se l'utente non è loggato, reindirizzalo al login
if (!isset($_SESSION['cf'])) {
    header('Location: login.php');
    exit;
}

$cf = $_SESSION['cf'];
$nome = $_SESSION['nome'] ?? '';
$cognome = $_SESSION['cognome'] ?? '';
$prenotazioni_attive = [];
$prenotazioni_storico = [];
$error = '';

try {
    // Recupera le prenotazioni attive (stato: prenotato, confermato)
    $stmt = $pdo->prepare("
        SELECT id, data_ora, stato, note 
        FROM prenotazione 
        WHERE persona_id = :cf AND stato IN ('prenotato', 'confermato')
        ORDER BY data_ora ASC
    ");
    $stmt->execute([':cf' => $cf]);
    $prenotazioni_attive = $stmt->fetchAll();
    
    // Recupera lo storico (stato: effettuato, cancellato, expired)
    $stmt = $pdo->prepare("
        SELECT id, data_ora, stato, note 
        FROM prenotazione 
        WHERE persona_id = :cf AND stato IN ('effettuato', 'cancellato', 'expired')
        ORDER BY data_ora DESC
        LIMIT 10
    ");
    $stmt->execute([':cf' => $cf]);
    $prenotazioni_storico = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $error = 'Si è verificato un errore nel caricamento dei dati.';
}

// Funzione per formattare la data
function formattaData($data_ora) {
    $timestamp = strtotime($data_ora);
    return date('d F Y \o\r\e H:i', $timestamp);
}

// Funzione per lo stato leggibile
function statoLeggibile($stato) {
    $stati = [
        'prenotato' => 'Prenotato',
        'confermato' => 'Confermato',
        'effettuato' => 'Effettuato',
        'cancellato' => 'Cancellato',
        'expired' => 'Scaduto'
    ];
    return $stati[$stato] ?? $stato;
}

// Funzione per la classe CSS dello stato
function statoClasse($stato) {
    $classi = [
        'prenotato' => 'status-pending',
        'confermato' => 'status-pending',
        'effettuato' => 'status-completed',
        'cancellato' => 'status-cancelled',
        'expired' => 'status-expired'
    ];
    return $classi[$stato] ?? 'status-pending';
}

// Funzione per formattare la data in italiano
function formattaDataItaliana($data_ora) {
    $timestamp = strtotime($data_ora);
    $mesi = [
        'January' => 'Gennaio',
        'February' => 'Febbraio',
        'March' => 'Marzo',
        'April' => 'Aprile',
        'May' => 'Maggio',
        'June' => 'Giugno',
        'July' => 'Luglio',
        'August' => 'Agosto',
        'September' => 'Settembre',
        'October' => 'Ottobre',
        'November' => 'Novembre',
        'December' => 'Dicembre'
    ];
    $mese_inglese = date('F', $timestamp);
    $mese_italiano = $mesi[$mese_inglese];
    return date('d', $timestamp) . ' ' . $mese_italiano . ' ' . date('Y', $timestamp);
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Area Personale - Centro Prelievi Sanitario</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <a href="#main-content" class="skip-link">Salta al contenuto principale</a>

    <header id="intestazione">
        <div class="header-container">
            <div class="logo-area">
                <img src="immagini/logo.png" alt="Logo ufficiale del Centro Prelievi" id="logo">
                <h1>Centro Prelievi</h1> 
            </div>
            
            <nav aria-label="Navigazione principale">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="prenotazioni.php">Prenotazioni</a></li>
                    <li><a href="account.php" aria-current="page" class="active">Area Personale</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main id="main-content">
        
        <section class="user-welcome-section" aria-labelledby="welcome-title">
            <div class="card welcome-card">
                <h2 id="welcome-title">Bentornato, <?php echo htmlspecialchars($nome . ' ' . $cognome); ?></h2>
                <p>In questa schermata puoi gestire i tuoi appuntamenti prenotati, controllare lo stato degli esami e consultare lo storico delle tue visite in totale sicurezza.</p>
                <div class="user-meta">
                    <p><strong>Codice Fiscale:</strong> <?php echo htmlspecialchars($_SESSION['cf']); ?></p>
                    <p><strong>Telefono:</strong> <?php echo htmlspecialchars($_SESSION['telefono'] ?? 'Non disponibile'); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email'] ?? 'Non disponibile'); ?></p>
                </div>
                <div style="margin-top: var(--space-16);">
                    <a href="logout.php" class="action-link-sec" style="color: #dc2626;">Logout</a>
                </div>
            </div>
        </section>

        <?php if (!empty($error)): ?>
        <div class="error-message" role="alert">
            <span aria-hidden="true">⚠️</span>
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <section class="dashboard-section" aria-labelledby="appuntamenti-title">
            <h2 id="appuntamenti-title" class="section-heading">I tuoi prossimi prelievi</h2>
            
            <div class="table-responsive">
                <table class="accessible-table">
                    <caption>Elenco dei prelievi attualmente prenotati e da eseguire</caption>
                    <thead>
                        <tr>
                            <th scope="col">Data e Ora</th>
                            <th scope="col">Stato</th>
                            <th scope="col">Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($prenotazioni_attive)): ?>
                        <tr>
                            <td colspan="3" style="text-align: center; padding: var(--space-24); color: var(--text-main);">
                                Nessuna prenotazione attiva.
                                <br>
                                <a href="prenotazioni.php" class="cta-button" style="display: inline-block; margin-top: var(--space-16);">
                                    Prenota un esame
                                </a>
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($prenotazioni_attive as $prenotazione): ?>
                            <tr>
                                <td>
                                    <strong class="date-highlight"><?php echo formattaDataItaliana($prenotazione['data_ora']); ?></strong>
                                    <br>
                                    <span style="font-size: 0.9rem; color: var(--text-main);">
                                        ore <?php echo date('H:i', strtotime($prenotazione['data_ora'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge <?php echo statoClasse($prenotazione['stato']); ?>">
                                        <?php echo statoLeggibile($prenotazione['stato']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($prenotazione['stato'] === 'prenotato' || $prenotazione['stato'] === 'confermato'): ?>
                                    <a href="annulla-prenotazione.php?id=<?php echo $prenotazione['id']; ?>" 
                                       class="action-link-sec" 
                                       style="color: #dc2626;"
                                       onclick="return confirm('Sei sicuro di voler annullare questa prenotazione?');">
                                        Annulla
                                    </a>
                                    <?php else: ?>
                                    <span style="color: var(--text-main); font-size: 0.9rem;">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="dashboard-section" aria-labelledby="storico-title">
            <h2 id="storico-title" class="section-heading">Storico visite passate</h2>
            
            <div class="table-responsive">
                <table class="accessible-table">
                    <caption>Elenco storico delle visite e dei prelievi effettuati in passato</caption>
                    <thead>
                        <tr>
                            <th scope="col">Data Visita</th>
                            <th scope="col">Stato</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($prenotazioni_storico)): ?>
                        <tr>
                            <td colspan="2" style="text-align: center; padding: var(--space-24); color: var(--text-main);">
                                Nessuna visita passata.
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($prenotazioni_storico as $prenotazione): ?>
                            <tr>
                                <td><?php echo formattaDataItaliana($prenotazione['data_ora']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo statoClasse($prenotazione['stato']); ?>">
                                        <?php echo statoLeggibile($prenotazione['stato']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="info-section" aria-labelledby="note-title">
            <div class="card info-card warning-card">
                <h2 id="note-title" class="section-heading">Promemoria importante per il prelievo</h2>
                <div class="info-body-text">
                    <ul>
                        <li><strong>Digiuno:</strong> Per tutti gli esami del sangue è necessario presentarsi a digiuno da almeno 8 ore. È consentito bere solo un bicchiere d'acqua naturale.</li>
                        <li><strong>Documenti da portare:</strong> Ricordati di portare la Tessera Sanitaria (Carta Regionale dei Servizi) e il documento d'identità valido.</li>
                        <li><strong>Disdetta:</strong> Se non puoi presentarti, ti chiediamo di annullare la prenotazione almeno 48 ore prima per liberare il posto per un altro paziente.</li>
                    </ul>
                </div>
            </div>
        </section>

    </main>

    <footer class="site-footer">
        <div class="footer-container">
            <p>&copy; 2026 Centro Prelievi &bull; Corso di Tecnologie Web &bull; Università di Padova</p>
            <p class="footer-sub">Sito sviluppato in conformità alle linee guida di accessibilità WCAG 2.2 AA</p>
        </div>
    </footer>

</body>
</html>