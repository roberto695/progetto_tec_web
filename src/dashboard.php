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

// Funzione per formattare la data in italiano (per dashboard)
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
    <meta name="keywords" content="area personale, gestisci dati personali, appuntamenti, prenotazioni, centro prelievi Padova, VitalPath">
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Salta al contenuto principale (accessibilità tastiera) -->
<a href="#main-content" class="skip-link">Salta al contenuto principale</a>

    <!-- HEADER -->
    <header id="intestazione">
        <div class="header-container">
            <?php include 'logo.php'; ?>
            <nav id="nav-principale" aria-label="Navigazione principale">
                <ul>
                    <li><a href="index.php"><span lang="en">Home</span></a></li>
                    <li><span class="nav-current" aria-current="page">Area Personale</span></li>
                    <?php if (empty($prenotazioni_attive)): ?>
                    <li><a href="prenotazioni.php">Prenota</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Esci (<?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>)</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <!-- MAIN -->
    <main id="main-content" tabindex="-1">
        <h1 class="hero__title">Area Personale</h1>
        <h2 class="section-title">Gestisci i tuoi dati personali, i tuoi appuntamenti e visualizza lo storico delle prenotazioni.</h2>
        <section class="user-welcome-section" aria-labelledby="welcome-title">
            <div class="card welcome-card">
                <div class="welcome-header">
                <h2 id="welcome-title">Ciao, <?php echo htmlspecialchars($nome . ' ' . $cognome); ?></h2>
                <p id="section-description">In questa sezione puoi visualizzare e gestire i tuoi dati personali.</p>
                <button id="toggle-modifica" class="btn btn--secondary btn--sm">
                    Modifica dati
                </button>
            </div>
                
            <?php if (!empty($messaggio)): ?>
            <div class="alert alert--success" role="status" aria-live="polite">
                <span class="alert__icon" aria-hidden="true">✓</span>
                <span><?= htmlspecialchars($messaggio, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($errore_modifica)): ?>
            <div class="alert alert--error" role="alert" aria-live="assertive">
                <span class="alert__icon" aria-hidden="true">⚠</span>
                <span><?= htmlspecialchars($errore_modifica, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        <?php endif; ?>

                <div id="dati-visualizzazione" class="user-meta">
                    <p><strong>Codice Fiscale:</strong> <?php echo htmlspecialchars($_SESSION['cf'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><strong>Nome:</strong> <span id="vis-nome"><?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?></span></p>
                    <p><strong>Cognome:</strong> <span id="vis-cognome"><?= htmlspecialchars($cognome, ENT_QUOTES, 'UTF-8') ?></span></p>
                    <p><strong>Telefono:</strong> <span id="vis-telefono"><?= htmlspecialchars($telefono ?: 'Non disponibile', ENT_QUOTES, 'UTF-8') ?></span></p>
                    <p><strong>Email:</strong> <span id="vis-email"><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></span></p>
                </div>
                <div id="form-modifica" class="form-modifica">
            <form method="POST" action="dashboard.php" novalidate>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="edit-nome">Nome *</label>
                        <input type="text" id="edit-nome" name="nome" required autocomplete="given-name" class="form-input" value="<?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="edit-cognome">Cognome *</label>
                        <input type="text" id="edit-cognome" name="cognome" required autocomplete="family-name" class="form-input" value="<?= htmlspecialchars($cognome, ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit-telefono">Telefono</label>
                    <input type="tel" id="edit-telefono" name="telefono" autocomplete="tel" class="form-input" value="<?= htmlspecialchars($telefono !== 'Non disponibile' ? $telefono : '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit-email">Email *</label>
                    <input type="email" id="edit-email" name="email" required autocomplete="email" class="form-input" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="form-actions">
                    <button type="submit" name="aggiorna_dati" class="btn btn--primary">Salva modifiche</button>
                    <button type="button" id="annulla-modifica" class="btn btn--ghost">Annulla</button>
                </div>
            </form>
        </div>
        </div>
        </section>
        <?php if (!empty($warning)): ?>
        <div class="alert alert--warning" role="alert" aria-live="polite">
            <span class="alert__icon" aria-hidden="true">⚠</span>
            <div>
                <strong><?php echo htmlspecialchars($warning); ?></strong>
                <p class="warning-text">
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

        <!-- Appuntamento attivo -->
        <section aria-labelledby="attivo-title">
            <h2 id="attivo-title" class="section-title">Appuntamento attivo</h2>

            <?php if (!empty($prenotazioni_attive)): 
                $attivo = $prenotazioni_attive[0];
            ?>
                <div class="appointment-card" role="group" aria-label="Appuntamento prenotato">
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

                    <div class="card card--warning mt-16 card-warning-sm">
                        <p class="text-sm mb-0">
                            <strong>⚠ Ricorda:</strong> Porta con te la tessera sanitaria.
                        </p>
                    </div>

                    <div class="appointment-card__actions">
                        <a href="cancella_prenotazione.php?id=<?= urlencode($attivo['id']) ?>" 
                           class="btn btn--danger btn--sm"
                           onclick="return confirm('Sei sicuro di voler cancellare questo appuntamento?');">
                            Cancella appuntamento
                        </a>
                    </div>
                </div>

            <?php else: ?>
                <div class="empty-state card">
                    <span class="empty-state__icon" aria-hidden="true">📅</span>
                    <h3 class="empty-state__title">Nessun appuntamento attivo</h3>
                    <p class="empty-state__text">
                        Non hai prenotazioni in corso. Prenota subito il tuo prossimo esame.
                    </p>
                    <a href="prenotazioni.php" class="btn btn--primary">
                        Prenota un esame
                    </a>
                </div>
            <?php endif; ?>
        </section>

        <!-- Storico -->
        <section aria-labelledby="storico-title">
            <h2 id="storico-title" class="section-title">Storico appuntamenti</h2>

            <?php if (empty($prenotazioni_storico)): ?>
                <div class="empty-state card">
                    <span class="empty-state__icon" aria-hidden="true">🗂</span>
                    <h3 class="empty-state__title">Nessuno storico disponibile</h3>
                    <p class="empty-state__text">Qui appariranno i tuoi appuntamenti passati.</p>
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <p id="storico-desc" class="sr-only">
                        Tabella che mostra lo storico delle prenotazioni passate dell'utente.
                        Ogni riga contiene: Data e ora, Stato della prenotazione.
                    </p>
                    <table class="table" id="tabella-storico" aria-describedby="storico-desc">
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
                <ul aria-labelledby="prep-title"
                    class="prep-list">
                    <li>Digiunare per almeno <strong>8 ore</strong> prima del prelievo.</li>
                    <li>È consentito bere <strong>acqua naturale</strong>.</li>
                    <li>Evitare attività fisica intensa nelle 24 ore precedenti.</li>
                    <li>Portare la <strong>tessera sanitaria</strong>.</li>
                    <li>In caso di farmaci, consultare il proprio medico prima dell'esame.</li>
                </ul>
            </div>
        </section>

    </main>

    <footer class="site-footer">
        <div class="footer-container">
            <p>
                <strong>VitalPath</strong> – Centro Prelievi del Sangue
                Via Roma 12 – Padova &bull; Tel. 049 000 0000 &bull;
                <a href="mailto:info@vitalpath.it"
                   class="link-mail">info@vitalpath.it</a>
            </p>
            <p>
                &copy; 2026 VitalPath &bull; Corso di Tecnologie Web &bull;
                Università di Padova
            </p>
            <p>
                Sito realizzato in conformità alle linee guida di accessibilità
                <abbr title="Web Content Accessibility Guidelines">WCAG</abbr> 2.1 AA
            </p>
        </div>
    </footer>

<script src="dashboard.js"></script>
</body>
</html>