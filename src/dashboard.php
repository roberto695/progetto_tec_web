<?php
// ============================================================
// dashboard.php – Area personale utente
// ============================================================
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
$telefono = $_SESSION['telefono'] ?? 'Non disponibile';
$email = $_SESSION['email'] ?? 'Non disponibile';

$prenotazioni_attive = [];
$prenotazioni_storico = [];
$error = '';

try {
    // Recupera le prenotazioni attive (stato: prenotato)
    $stmt = $pdo->prepare("
        SELECT id, data_ora, stato
        FROM prenotazione 
        WHERE persona_id = :cf AND stato IN ('prenotato')
    ");
    $stmt->execute([':cf' => $cf]);
    $prenotazioni_attive = $stmt->fetchAll();
    
    // Recupera lo storico (stato: effettuato, cancellato, expired)
    $stmt = $pdo->prepare("
        SELECT id, data_ora, stato
        FROM prenotazione 
        WHERE persona_id = :cf AND stato IN ('effettuato', 'cancellato', 'expired')
        ORDER BY data_ora DESC
        LIMIT 10
    ");
    $stmt->execute([':cf' => $cf]);
    $prenotazioni_storico = $stmt->fetchAll();
    
    if (!empty($prenotazioni_attive)) {
        $warning = 'Hai già una prenotazione attiva! Non puoi prenotare un nuovo esame fino al completamento o all\'annullamento di quella esistente.';
    }

} catch (PDOException $e) {
    $error = 'Si è verificato un errore nel caricamento dei dati.';
}

// Funzione per lo stato leggibile
function statoLeggibile($stato) {
    $stati = [
        'prenotato' => 'Prenotato',
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
        'effettuato' => 'status-completed',
        'cancellato' => 'status-cancelled',
        'expired' => 'status-expired'
    ];
    return $classi[$stato] ?? 'status-pending';
}

// Funzione per formattare la data in italiano (versione dashboard)
function formattaDataDashboard($data_ora) {
    $dt = new DateTime($data_ora);
    $giorni = ['Dom', 'Lun', 'Mar', 'Mer', 'Gio', 'Ven', 'Sab'];
    $mesi = ['', 'Gennaio', 'Febbraio', 'Marzo', 'Aprile', 'Maggio', 'Giugno',
             'Luglio', 'Agosto', 'Settembre', 'Ottobre', 'Novembre', 'Dicembre'];
    return $giorni[(int)$dt->format('w')] . ' ' . $dt->format('j') . ' ' .
           $mesi[(int)$dt->format('n')] . ' ' . $dt->format('Y');
}

// Funzione per formattare la data in italiano
function formattaDataItaliana($data_ora) {
    $dt = new DateTime($data_ora);
    return $dt->format('d/m/Y') . ' alle ' . $dt->format('H:i');
}

// Funzione per formattare data breve (per tabella storico)
function formattaDataBreve($data_ora) {
    $dt = new DateTime($data_ora);
    return $dt->format('d/m/Y') . ' alle ' . $dt->format('H:i');
}

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Area Personale - Centro Prelievi Sanitario</title>
    <meta name="description" content="Gestisci i tuoi appuntamenti e visualizza lo storico delle prenotazioni.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <a href="#main-content" class="skip-link">Salta al contenuto principale</a>

    <header id="intestazione" role="banner">
        <div class="header-container">
            <?php include 'logo.php'; ?>
            
            <nav id="nav-principale" aria-label="Navigazione principale">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <?php if (empty($prenotazioni_attive)): ?>
                    <li><a href="prenotazioni.php">Prenota</a></li>
                    <?php endif; ?>
                    <li><a href="dashboard.php" aria-current="page" class="active">Area Personale</a></li>
                    <li><a href="logout.php">Esci (<?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>)</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main id="main-content" tabindex="-1">
        
        <!-- Benvenuto -->
        <section class="user-welcome-section" aria-labelledby="welcome-title">
            <div class="card welcome-card">
                <h2 id="welcome-title">Bentornato, <?php echo htmlspecialchars($nome . ' ' . $cognome); ?></h2>
                <p>In questa schermata puoi gestire i tuoi appuntamenti prenotati, controllare lo stato degli esami e consultare lo storico delle tue visite in totale sicurezza.</p>
                <div class="user-meta">
                    <p><strong>Codice Fiscale:</strong> <?php echo htmlspecialchars($_SESSION['cf']); ?></p>
                    <p><strong>Telefono:</strong> <?php echo htmlspecialchars($telefono); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
                </div>
            </div>
        </section>
        <?php if (!empty($warning)): ?>
        <div class="alert alert--warning" role="alert" aria-live="polite">
            <span class="alert__icon" aria-hidden="true">⚠</span>
            <div>
                <strong><?php echo htmlspecialchars($warning); ?></strong>
                <p style="margin-top: 8px; font-weight: 400;">
                    Puoi visualizzare la tua prenotazione qui sotto o annullarla se necessario.
                </p>
            </div>
        </div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
        <div class="error-message" role="alert">
            <span aria-hidden="true">⚠</span>
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <!-- Appuntamento attivo (versione dashboard) -->
        <section aria-labelledby="attivo-title">
            <h2 id="attivo-title" class="section-title">Appuntamento attivo</h2>

            <?php if (!empty($prenotazioni_attive)): 
                $attivo = $prenotazioni_attive[0]; // Prendi il primo appuntamento attivo
            ?>
                <div class="appointment-card" aria-label="Appuntamento prenotato">
                    <div class="appointment-card__header">
                        <div>
                            <div class="appointment-card__date">
                                <?= formattaDataDashboard($attivo['data_ora']) ?>
                            </div>
                            <div class="appointment-card__time">
                                Orario: <?= (new DateTime($attivo['data_ora']))->format('H:i') ?>
                            </div>
                        </div>
                        <span class="status-badge <?= statoClasse($attivo['stato']) ?>">
                            <?= statoLeggibile($attivo['stato']) ?>
                        </span>
                    </div>

                    <div class="card card--warning mt-16" style="padding:var(--space-16);">
                        <p class="text-sm mb-0">
                            <strong>⚠ Ricorda:</strong> Porta con te la tessera sanitaria.
                        </p>
                    </div>

                    <div class="appointment-card__actions">
                        <a href="annulla-prenotazione.php?id=<?= $attivo['id'] ?>" 
                           class="btn btn--danger btn--sm"
                           onclick="return confirm('Sei sicuro di voler cancellare questo appuntamento?');">
                            Cancella appuntamento
                        </a>
                    </div>
                </div>

            <?php else: ?>
                <div class="empty-state card">
                    <span class="empty-state__icon" aria-hidden="true">📅</span>
                    <p class="empty-state__title">Nessun appuntamento attivo</p>
                    <p class="empty-state__text">
                        Non hai prenotazioni in corso. Prenota subito il tuo prossimo esame.
                    </p>
                    <a href="prenotazioni.php" class="btn btn--primary">
                        Prenota un esame
                    </a>
                </div>
            <?php endif; ?>
        </section>

        <!-- Storico (versione dashboard) -->
        <section aria-labelledby="storico-title">
            <h2 id="storico-title" class="section-title">Storico appuntamenti</h2>

            <?php if (empty($prenotazioni_storico)): ?>
                <div class="empty-state card">
                    <span class="empty-state__icon" aria-hidden="true">🗂</span>
                    <p class="empty-state__title">Nessuno storico disponibile</p>
                    <p class="empty-state__text">Qui appariranno i tuoi appuntamenti passati.</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <caption>Storico delle prenotazioni passate</caption>
                        <thead>
                            <tr>
                                <th scope="col">Data e ora</th>
                                <th scope="col">Stato</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($prenotazioni_storico as $p): ?>
                            <tr>
                                <td><?= htmlspecialchars(formattaDataBreve($p['data_ora']), ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <span class="status-badge <?= statoClasse($p['stato']) ?>">
                                        <?= statoLeggibile($p['stato']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

        <!-- Promemoria -->
        <!-- INFO PREPARAZIONE -->
        <section aria-labelledby="prep-title">
            <div class="card card--warning">
                <h2 id="prep-title">
                    <span aria-hidden="true">⚠ </span>
                    Preparazione all'esame del sangue
                </h2>
                <p class="mt-16">
                    Per garantire la correttezza dei risultati è importante seguire
                    queste indicazioni prima di presentarsi al centro:
                </p>
                <ul style="
                    list-style: disc;
                    padding-left: var(--space-24);
                    margin-top: var(--space-16);
                    display: flex;
                    flex-direction: column;
                    gap: var(--space-8);
                ">
                    <li>Digiunare per almeno <strong>8 ore</strong> prima del prelievo.</li>
                    <li>È consentito bere <strong>acqua naturale</strong>.</li>
                    <li>Evitare attività fisica intensa nelle 24 ore precedenti.</li>
                    <li>Portare la <strong>tessera sanitaria</strong>.</li>
                    <li>In caso di farmaci, consultare il proprio medico prima dell'esame.</li>
                </ul>
            </div>
        </section>

    </main>

    <footer class="site-footer" role="contentinfo">
        <div class="footer-container">
            <p>
                <strong>VitalPath</strong> – Centro Prelievi del Sangue<br>
                Via Roma 12 – Padova &bull; Tel. 049 000 0000 &bull;
                <a href="mailto:info@vitalpath.it"
                   style="color: #93c5fd;">info@vitalpath.it</a>
            </p>
            <p>
                &copy; 2026 VitalPath &bull; Corso di Tecnologie Web &bull;
                Università di Padova
            </p>
            <p>
                Sito realizzato in conformità alle linee guida di accessibilità
                <abbr title="Web Content Accessibility Guidelines">WCAG</abbr> 2.2 AA
            </p>
        </div>
    </footer>

</body>
</html>