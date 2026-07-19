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

// ============================================================
// Genera gli slot disponibili: lunedì–sabato, 08:00–12:30
// per i prossimi 6 giorni lavorativi a partire da domani
// ============================================================
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

// Slot già occupati nel DB (per le date generate) - solo 'prenotato'
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
// Gestione POST – conferma prenotazione
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

// Helpers
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" media="all" href="style.css">
</head>
<body>

    <a href="#main-content" class="skip-link">Salta al contenuto principale</a>

    <header id="intestazione">
        <?php include 'logo.php'; ?>
        <nav id="nav-principale" aria-label="Navigazione principale">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="prenotazioni.php" aria-current="page" class="active">Prenota</a></li>
                <li><a href="dashboard.php">Area Personale</a></li>
                <li><a href="logout.php">Esci (<?= htmlspecialchars($nome, ENT_QUOTES, 'UTF-8') ?>)</a></li>
            </ul>
        </nav>
    </header>

    <main id="main-content" tabindex="-1">

        <h1 class="sr-only">Prenota un esame del sangue</h1>

        <!-- CONFERMA SUCCESSO -->
        <?php if ($success): ?>
        <section aria-labelledby="conferma-title">
            <div class="card card--success" style="max-width:640px;margin:0 auto;text-align:center;padding:var(--space-48);">
                <div style="font-size:3rem;margin-bottom:var(--space-16);" aria-hidden="true">✅</div>
                <h2 id="conferma-title" style="color:var(--color-success);margin-bottom:var(--space-16);">
                    Prenotazione confermata!
                </h2>
                <p style="font-size:var(--font-size-lg);margin-bottom:var(--space-8);">
                    Il tuo appuntamento è fissato per:<br>
                    <strong><?= htmlspecialchars(
                        (new DateTime($booking['data_ora']))->format('d/m/Y') . ' alle ' .
                        (new DateTime($booking['data_ora']))->format('H:i'),
                        ENT_QUOTES, 'UTF-8'
                    ) ?></strong>
                </p>

                <div class="card card--warning mt-24" style="text-align:left;">
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
                        <h2 style="font-size:var(--font-size-lg);margin-bottom:var(--space-16);">
                            1. Scegli il giorno
                        </h2>
                        <fieldset style="border:none;margin:0;padding:0;">
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
                                    <label for="<?= $id_radio ?>" class="day-label">
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
                        <h2 style="font-size:var(--font-size-lg);margin-bottom:var(--space-16);">
                            2. Scegli l'orario
                        </h2>
                        <fieldset style="border:none;margin:0;padding:0;">
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
                                // Per il primo giorno disponibile, precarica gli slot occupati
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
                                    <label for="<?= $id_ora ?>">
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

                </div><!-- /booking-main -->

                <!-- COLONNA DESTRA: riepilogo + conferma -->
                <aside class="booking-sidebar" aria-label="Riepilogo prenotazione">
                    <div class="card card--accent">
                        <h2 style="font-size:var(--font-size-lg);margin-bottom:var(--space-24);">
                            Riepilogo
                        </h2>

                        <dl class="summary-list">
                            <div class="summary-item">
                                <dt class="summary-label">Paziente</dt>
                                <dd class="summary-value" style="font-size:var(--font-size-base);color:var(--color-text);">
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
                                <dd style="font-size:var(--font-size-base);color:var(--color-text-secondary);">
                                    Centro Prelievi<br>Via Roma 12, Padova
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
                        <h3 style="font-size:var(--font-size-base);margin-bottom:var(--space-8);">
                            ⚠ Preparazione all'esame
                        </h3>
                        <ul style="list-style:disc;padding-left:var(--space-16);display:flex;flex-direction:column;gap:var(--space-8);">
                            <li class="text-sm">Digiuno da almeno <strong>8 ore</strong></li>
                            <li class="text-sm">Puoi bere <strong>acqua naturale</strong></li>
                            <li class="text-sm">Porta <strong>tessera sanitaria</strong></li>
                            
                        </ul>
                    </div>
                </aside>

            </div><!-- /booking-layout -->
        </form>

        <?php endif; ?>

    </main>

    <footer class="site-footer">
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

    <script>
    // ============================================================
    // prenotazioni.js – Aggiorna il riepilogo e gestisce gli slot
    // ============================================================
    document.addEventListener('DOMContentLoaded', function() {

        // Riferimenti agli elementi
        const giorniRadios = document.querySelectorAll('input[name="data_esame"]');
        const orariContainer = document.getElementById('griglia-orari');
        const orariRadios = document.querySelectorAll('input[name="ora_esame"]');
        const riepilogoData = document.getElementById('riepilogo-data');
        const riepilogoOra = document.getElementById('riepilogo-ora');

        // Mappa dei giorni con i loro nomi
        const giorniMap = {};
        document.querySelectorAll('.day-option').forEach(function(opt) {
            const radio = opt.querySelector('input[type="radio"]');
            const label = opt.querySelector('.day-label');
            if (radio && label) {
                const dow = label.querySelector('.day-label__dow')?.textContent || '';
                const num = label.querySelector('.day-label__num')?.textContent || '';
                const month = label.querySelector('.day-label__month')?.textContent || '';
                giorniMap[radio.value] = dow + ' ' + num + ' ' + month;
            }
        });

        // Carica gli slot occupati dal data-occupati
        const occupatiJson = orariContainer.dataset.occupati || '{}';
        let occupati = {};
        try {
            occupati = JSON.parse(occupatiJson);
        } catch (e) {
            occupati = {};
        }

        // Aggiorna il riepilogo data
        giorniRadios.forEach(function(radio) {
            radio.addEventListener('change', function() {
                const dataVal = this.value;
                riepilogoData.textContent = giorniMap[dataVal] || dataVal;
                aggiornaOrari(dataVal);
            });
        });

        // Aggiorna il riepilogo orario
        orariRadios.forEach(function(radio) {
            radio.addEventListener('change', function() {
                if (this.checked && !this.disabled) {
                    riepilogoOra.textContent = this.value;
                }
            });
        });

        // Seleziona il primo orario disponibile
        function selezionaPrimoOrario() {
            let primo = null;
            orariRadios.forEach(function(radio) {
                if (!radio.disabled && !primo) {
                    primo = radio;
                }
            });
            if (primo) {
                primo.checked = true;
                riepilogoOra.textContent = primo.value;
            }
        }

        // Aggiorna gli orari in base al giorno selezionato
        function aggiornaOrari(dataVal) {
            const occupatiGiorno = occupati[dataVal] || [];
            const orari = document.querySelectorAll('.time-option');

            orari.forEach(function(container) {
                const radio = container.querySelector('input[type="radio"]');
                const ora = radio.value;
                const oraDb = ora + ':00';
                const occupato = occupatiGiorno.includes(oraDb);

                if (occupato) {
                    radio.disabled = true;
                    radio.checked = false;
                    container.classList.add('time-option--disabled');
                    radio.setAttribute('aria-label', ora + ' – non disponibile');
                } else {
                    radio.disabled = false;
                    container.classList.remove('time-option--disabled');
                    radio.setAttribute('aria-label', ora);
                }
            });

            // Seleziona il primo orario disponibile
            selezionaPrimoOrario();
        }

        // Inizializza: seleziona il primo orario disponibile
        selezionaPrimoOrario();

        // Validazione client-side
        const form = document.getElementById('form-prenotazione');
        if (form) {
            form.addEventListener('submit', function(e) {
                const dataSelezionata = document.querySelector('input[name="data_esame"]:checked');
                const oraSelezionata = document.querySelector('input[name="ora_esame"]:checked');

                if (!dataSelezionata) {
                    e.preventDefault();
                    alert('Seleziona un giorno per l\'esame.');
                    return;
                }

                if (!oraSelezionata) {
                    e.preventDefault();
                    alert('Seleziona un orario per l\'esame.');
                    return;
                }

                if (oraSelezionata.disabled) {
                    e.preventDefault();
                    alert('L\'orario selezionato non è disponibile. Scegli un altro orario.');
                    return;
                }
            });
        }

    });
    </script>

</body>
</html>