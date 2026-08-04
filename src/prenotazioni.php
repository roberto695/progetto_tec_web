<?php
// ============================================================
// prenotazioni.php – Selezione data e ora esame
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

// Blocca se l'utente ha già un appuntamento attivo (solo 'prenotato')
$stmt = $pdo->prepare(
    "SELECT id FROM prenotazione
     WHERE persona_id = ? AND stato = 'prenotato'
     LIMIT 1"
);
$stmt->execute([$cf]);
if ($stmt->fetch()) {
    header('Location: dashboard.php');
    exit;
}

// Genera gli slot disponibili: lunedì–sabato, 08:00–12:30
$orari = ['08:00','08:30','09:00','09:30','10:00','10:30','11:00','11:30','12:00','12:30'];

$giorni_disp = [];
$data        = new DateTime('tomorrow');
$trovati     = 0;

while ($trovati < 6) {
    $dow = (int)$data->format('N'); // 1=lun … 7=dom
    if ($dow <= 6) {                // lun–sab
        $giorni_disp[] = clone $data;
        $trovati++;
    }
    $data->modify('+1 day');
}

$date_str = array_map(fn($d) => $d->format('Y-m-d'), $giorni_disp);
$in       = implode(',', array_fill(0, count($date_str), '?'));

$stmt = $pdo->prepare(
    "SELECT DATE(data_ora) AS giorno, TIME(data_ora) AS ora
     FROM prenotazione
     WHERE DATE(data_ora) IN ($in)
       AND stato = 'prenotato'"
);
$stmt->execute($date_str);
$occupati = [];
foreach ($stmt->fetchAll() as $row) {
    $occupati[$row['giorno']][$row['ora']] = true;
}

// ============================================================
// Gestione POST: conferma prenotazione
// ============================================================
$errori  = [];
$success = false;
$booking = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data_scelta = trim($_POST['data_esame'] ?? '');
    $ora_scelta  = trim($_POST['ora_esame']  ?? '');

    // Validazione server
    if ($data_scelta === '') {
        $errori[] = 'Seleziona un giorno per l\'esame.';
    } elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data_scelta)) {
        $errori[] = 'Data non valida.';
    }

    if ($ora_scelta === '') {
        $errori[] = 'Seleziona un orario per l\'esame.';
    } elseif (!in_array($ora_scelta, $orari, true)) {
        $errori[] = 'Orario non valido.';
    }

    if (empty($errori)) {
        // Verifica che lo slot non sia già occupato
        if (isset($occupati[$data_scelta][$ora_scelta . ':00'])) {
            $errori[] = 'Lo slot selezionato è già occupato. Scegli un altro orario.';
        }
    }

    if (empty($errori)) {
        $data_ora_db = $data_scelta . ' ' . $ora_scelta . ':00';

        $stmt = $pdo->prepare(
            "INSERT INTO prenotazione (persona_id, data_ora, stato)
             VALUES (?, ?, 'prenotato')"
        );
        $stmt->execute([$cf, $data_ora_db]);

        $success = true;
        $booking = ['data_ora' => $data_ora_db];
    }
}

function fmt_dow(DateTime $d): string {
    $g = ['Dom','Lun','Mar','Mer','Gio','Ven','Sab'];
    return $g[(int)$d->format('w')];
}

function fmt_mese(DateTime $d): string {
    $m = ['','Gen','Feb','Mar','Apr','Mag','Giu','Lug','Ago','Set','Ott','Nov','Dic'];
    return $m[(int)$d->format('n')];
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prenotazione - Centro Prelievi Sanitario</title>
    <meta name="description" content="Scegli data e ora per il tuo prelievo del sangue presso il Centro Prelievi.">
    <meta name="keywords" content="prenota esame del sangue, centro prelievi Padova, VitalPath">
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
                <li><a href="dashboard.php">Area Personale</a></li>
                <li><span class="nav-current" aria-current="page">Prenota</span></li>
                <li><a href="logout.php">Esci (<?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>)</a></li>
            </ul>
        </nav>
        </div>
    </header>

<!-- MAIN -->
    <main id="main-content" tabindex="-1">

        <h1 class="section-title">Prenota un esame del sangue</h1>

        <!-- CONFERMA SUCCESSO -->
        <?php if ($success): ?>
        <section aria-labelledby="conferma-title">
            <div class="card card--success card--success-centered">
                <div class="icon-large" aria-hidden="true">✅</div>
                <h2 id="conferma-title" class="confirmation-title">
                    Prenotazione confermata!
                </h2>
                <p class="confirmation-detail">
                    Il tuo appuntamento è fissato per: 
                    <strong><?= htmlspecialchars(
                        (new DateTime($booking['data_ora']))->format('d/m/Y') . ' alle ' .
                        (new DateTime($booking['data_ora']))->format('H:i'),
                        ENT_QUOTES, 'UTF-8'
                    ) ?></strong>
                </p>

                <div class="card card--warning mt-24 card--warning-inner">
                    <p class="mb-0 text-sm">
                        <strong>⚠ Ricorda:</strong> presentati a digiuno da almeno 8 ore.
                        È consentito bere acqua. Porta con te documento d'identità e tessera sanitaria.
                    </p>
                </div>

                <a href="dashboard.php" class="btn btn--primary mt-24">
                    Vai alla tua area personale
                </a>
            </div>
        </section>

        <?php else: ?>

        <!-- ERRORI -->
        <?php if (!empty($errori)): ?>
        <div class="error-summary" role="alert" aria-live="assertive">
            <h2><span aria-hidden="true">⚠</span> Si sono verificati <?= count($errori) ?> errori</h2>
            <ul><?php foreach ($errori as $e): ?><li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li><?php endforeach; ?></ul>
        </div>
        <?php endif; ?>

        <!-- FORM PRENOTAZIONE -->
        <form id="form-prenotazione" method="POST" action="prenotazioni.php"
              novalidate aria-label="Modulo di prenotazione esame">

            <div class="booking-layout">

                <!-- COLONNA SINISTRA: selezione data/ora -->
                <div class="booking-main">

                    <!-- SELEZIONE GIORNO -->
                    <div class="card mb-24">
                        <h2 class="booking-title">
                            1. Scegli il giorno
                        </h2>
                        <fieldset class="booking-fieldset">
                            <legend class="sr-only">Seleziona il giorno del prelievo</legend>
                            <div class="booking-grid" id="griglia-giorni">
                                <?php foreach ($giorni_disp as $i => $giorno):
                                    $val_data  = $giorno->format('Y-m-d');
                                    $id_radio  = 'data-' . $val_data;
                                    $checked   = ($i === 0) ? 'checked' : '';
                                ?>
                                <div class="day-option">
                                    <input type="radio" name="data_esame"
                                           id="<?= $id_radio ?>"
                                           value="<?= $val_data ?>"
                                           <?= $checked ?>
                                           aria-label="<?= fmt_dow($giorno) . ' ' . $giorno->format('j') . ' ' . fmt_mese($giorno) ?>">
                                    <label for="<?= $id_radio ?>" class="day-label" tabindex="0">
                                        <span class="day-label__dow"><?= fmt_dow($giorno) ?></span>
                                        <span class="day-label__num"><?= $giorno->format('j') ?></span>
                                        <span class="day-label__month"><?= fmt_mese($giorno) ?></span>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </fieldset>
                    </div>

                    <!-- SELEZIONE ORARIO -->
                    <div class="card mb-24">
                        <h2 class="booking-title">
                            2. Scegli l'orario
                        </h2>
                        <fieldset class="booking-fieldset">
                            <legend class="sr-only">Seleziona l'orario del prelievo</legend>
                            <?php
                            // Costruisce mappa JSON degli slot occupati per tutti i giorni
                            $occupati_json = [];
                            foreach ($giorni_disp as $gd) {
                                $gk = $gd->format('Y-m-d');
                                $occupati_json[$gk] = array_keys($occupati[$gk] ?? []);
                            }
                            ?>
                            <div class="booking-grid time-booking-grid" id="griglia-orari"
                                 data-occupati="<?= htmlspecialchars(json_encode($occupati_json), ENT_QUOTES, 'UTF-8') ?>">
                                <?php
                                $primo_giorno = $giorni_disp[0]->format('Y-m-d');
                                foreach ($orari as $j => $ora):
                                    $ora_db     = $ora . ':00';
                                    $occupato   = isset($occupati[$primo_giorno][$ora_db]);
                                    $id_ora     = 'ora-' . str_replace(':', '', $ora);
                                    $checked    = ($j === 0 && !$occupato) ? 'checked' : '';
                                    $disabled   = $occupato ? 'disabled' : '';
                                    $cls_dis    = $occupato ? ' time-option--disabled' : '';
                                    $aria_label = $occupato ? $ora . ' – non disponibile' : $ora;
                                ?>
                                <div class="time-option<?= $cls_dis ?>">
                                    <input type="radio" name="ora_esame"
                                           id="<?= $id_ora ?>"
                                           value="<?= $ora ?>"
                                           <?= $checked ?>
                                           <?= $disabled ?>
                                           aria-label="<?= $aria_label ?>">
                                    <label for="<?= $id_ora ?>" tabindex="0">
                                        <?= $ora ?>
                                        <?php if ($occupato): ?>
                                            <span class="sr-only"> – non disponibile</span>
                                        <?php endif; ?>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </fieldset>
                    </div>

                </div>

                <!-- COLONNA DESTRA: riepilogo + conferma -->
                <aside class="booking-sidebar" aria-label="Riepilogo prenotazione">
                    <div class="card card--accent">
                        <h2 class="summary-title">
                            Riepilogo
                        </h2>

                        <dl class="summary-list">
                            <div class="summary-item">
                                <dt class="summary-label">Paziente</dt>
                                <dd class="summary-value summary-value-text">
                                    <?= htmlspecialchars($nome . ' ' . $cognome, ENT_QUOTES, 'UTF-8') ?>
                                </dd>
                            </div>
                            <div class="summary-item">
                                <dt class="summary-label">Giorno selezionato</dt>
                                <dd class="summary-value" id="riepilogo-data">
                                    <?= fmt_dow($giorni_disp[0]) . ' ' . $giorni_disp[0]->format('j') . ' ' . fmt_mese($giorni_disp[0]) ?>
                                </dd>
                            </div>
                            <div class="summary-item">
                                <dt class="summary-label">Orario selezionato</dt>
                                <dd class="summary-value" id="riepilogo-ora">—</dd>
                            </div>
                            <div class="summary-item">
                                <dt class="summary-label">Luogo</dt>
                                <dd class="summary-location">
                                    Centro Prelievi - Via Roma 12, Padova
                                </dd>
                            </div>
                        </dl>

                        <button type="submit" class="btn btn--primary btn--full mt-24">
                            Conferma prenotazione
                        </button>
                        <p class="text-sm text-muted text-center mt-8">
                            Nessun costo di prenotazione online
                        </p>
                    </div>

                    <!-- Promemoria preparazione -->
                    <div class="card card--warning mt-24">
                        <h3 id="prep-title" class="prep-header">
                            ⚠ Preparazione all'esame
                        </h3>
                        <ul aria-labelledby="prep-title"
                            class="prep-list-sidebar">
                            <li class="text-sm">Digiuno da almeno <strong>8 ore</strong></li>
                            <li class="text-sm">Puoi bere <strong>acqua naturale</strong></li>
                            <li class="text-sm">Porta <strong>tessera sanitaria</strong></li>
                            
                        </ul>
                    </div>
                </aside>

            </div>
        </form>

        <?php endif; ?>

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

    <script src="prenotazioni.js"></script>

</body>
</html>