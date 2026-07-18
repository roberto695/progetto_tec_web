<?php
// ============================================================
// admin.php – Dashboard amministratore
// ============================================================
session_start();

// Includi la connessione al database
require_once __DIR__ . '/db.php';

// ============================================================
// CONTROLLO ADMIN: solo il CF specificato può accedere
// ============================================================
define('ADMIN_CF', 'ADMINVTLPTH00A00'); // CF dell'admin
// Se l'utente non è loggato, reindirizzalo al login
if (!isset($_SESSION['cf'])) {
    header('Location: login.php');
    exit;
}

// Se il CF non corrisponde a quello dell'admin, reindirizza all'area personale
if ($_SESSION['cf'] !== ADMIN_CF) {
    header('Location: account.php');
    exit;
}

// Recupera i dati dell'admin dalla sessione
$cf_admin = $_SESSION['cf'];
$nome_admin = $_SESSION['nome'] ?? 'Admin';
$cognome_admin = $_SESSION['cognome'] ?? '';

// ============================================================
// Gestione POST – annulla appuntamento (azione admin)
// ============================================================
$msg_successo = '';
$msg_errore   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['annulla_id'])) {
    $annulla_id = (int)$_POST['annulla_id'];
    if ($annulla_id > 0) {
        $stmt = $pdo->prepare(
            "UPDATE prenotazione
             SET stato = 'cancellato'
             WHERE id = ? AND stato IN ('prenotato',)"
        );
        $stmt->execute([$annulla_id]);
        if ($stmt->rowCount() > 0) {
            $msg_successo = 'Appuntamento #' . $annulla_id . ' annullato con successo.';
        } else {
            $msg_errore = 'Impossibile annullare l\'appuntamento #' . $annulla_id . ' (già annullato o non trovato).';
        }
    }
}

// ============================================================
// Filtro / ricerca
// ============================================================
$filtro      = trim($_GET['q'] ?? '');
$filtro_safe = htmlspecialchars($filtro, ENT_QUOTES, 'UTF-8');

// ============================================================
// Statistiche rapide per i widget in cima
// ============================================================
$stats = $pdo->query(
    "SELECT
        COUNT(*) AS totale,
        SUM(stato IN ('prenotato')) AS attive,
        SUM(stato = 'effettuato')   AS effettuate,
        SUM(stato = 'cancellato')   AS cancellate
     FROM prenotazione"
)->fetch();

// ============================================================
// Query principale – tutti gli appuntamenti con dati paziente
// ============================================================
if ($filtro !== '') {
    $like = '%' . $filtro . '%';
    $stmt = $pdo->prepare(
        "SELECT p.id, p.data_ora, p.stato, p.creato_il,
                pe.cf, pe.nome, pe.cognome, pe.email, pe.telefono
         FROM prenotazione p
         JOIN persona pe ON pe.cf = p.persona_id
         WHERE pe.nome    LIKE ?
            OR pe.cognome LIKE ?
            OR pe.cf      LIKE ?
         ORDER BY p.data_ora DESC"
    );
    $stmt->execute([$like, $like, $like]);
} else {
    $stmt = $pdo->query(
        "SELECT p.id, p.data_ora, p.stato, p.creato_il,
                pe.cf, pe.nome, pe.cognome, pe.email, pe.telefono
         FROM prenotazione p
         JOIN persona pe ON pe.cf = p.persona_id
         ORDER BY p.data_ora DESC"
    );
}
$prenotazioni = $stmt->fetchAll();

// Helper
function fmt_data_admin(string $data_ora): string {
    $dt = new DateTime($data_ora);
    return $dt->format('d/m/Y') . ' ' . $dt->format('H:i');
}

function etichetta_stato(string $stato): string {
    return match($stato) {
        'prenotato'  => 'Prenotato',
        'effettuato' => 'Effettuato',
        'cancellato' => 'Cancellato',
        default      => ucfirst($stato),
    };
}

function statoClasse($stato) {
    $classi = [
        'prenotato' => 'status-pending',
        'cancellato' => 'status-cancelled',
        'expired' => 'status-expired'
    ];
    return $classi[$stato] ?? 'status-pending';
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin – Centro Prelievi</title>
    <meta name="description" content="Pannello di controllo amministratore Centro Prelievi.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<a href="#main-content" class="skip-link">Salta al contenuto principale</a>

<!-- HEADER -->
<header id="intestazione">
    <div class="header-container">
        
        <?php include 'logo.php'; ?>
        
        <nav id="nav-principale" aria-label="Navigazione principale">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="admin_dashboard.php" aria-current="page" class="active">Dashboard Admin</a></li>
                <li><a href="logout.php">Esci (<?= htmlspecialchars($nome_admin, ENT_QUOTES, 'UTF-8') ?>)</a></li>
            </ul>
        </nav>
    </div>
</header>

<!-- MAIN -->
<main id="main-content" tabindex="-1">

    <!-- Titolo pagina -->
    <div class="flex items-center justify-between flex-wrap gap-16 mb-32">
        <div>
            <h1>Dashboard Amministratore</h1>
            <p class="text-muted mt-8">Gestione appuntamenti &bull; Centro Prelievi</p>
        </div>
        <span class="badge badge--info" style="font-size:var(--font-size-sm);padding:var(--space-8) var(--space-16);">
            Admin: <?= htmlspecialchars($nome_admin, ENT_QUOTES, 'UTF-8') ?>
        </span>
    </div>

    <!-- FEEDBACK -->
    <?php if ($msg_successo): ?>
    <div class="alert alert--success" role="status" aria-live="polite">
        <span class="alert__icon" aria-hidden="true">✓</span>
        <span><?= htmlspecialchars($msg_successo, ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <?php endif; ?>

    <?php if ($msg_errore): ?>
    <div class="alert alert--error" role="alert" aria-live="assertive">
        <span class="alert__icon" aria-hidden="true">⚠</span>
        <span><?= htmlspecialchars($msg_errore, ENT_QUOTES, 'UTF-8') ?></span>
    </div>
    <?php endif; ?>

    <!-- STATISTICHE -->
    <section aria-labelledby="stats-title">
        <h2 id="stats-title" class="sr-only">Statistiche appuntamenti</h2>
        <div class="grid grid--4 mb-32">

            <div class="card text-center">
                <div style="font-size:2rem;font-weight:800;color:var(--color-primary);">
                    <?= (int)$stats['totale'] ?>
                </div>
                <div class="text-sm text-muted mt-8">Totale prenotazioni</div>
            </div>

            <div class="card text-center">
                <div style="font-size:2rem;font-weight:800;color:var(--status-booked-text);">
                    <?= (int)$stats['attive'] ?>
                </div>
                <div class="text-sm text-muted mt-8">Attive</div>
            </div>

            <div class="card text-center">
                <div style="font-size:2rem;font-weight:800;color:var(--status-confirmed-text);">
                    <?= (int)$stats['effettuate'] ?>
                </div>
                <div class="text-sm text-muted mt-8">Effettuate</div>
            </div>

            <div class="card text-center">
                <div style="font-size:2rem;font-weight:800;color:var(--status-cancelled-text);">
                    <?= (int)$stats['cancellate'] ?>
                </div>
                <div class="text-sm text-muted mt-8">Cancellate</div>
            </div>

        </div>
    </section>

    <!-- RICERCA -->
    <section aria-labelledby="ricerca-title">
        <h2 id="ricerca-title" class="section-title">Tutti gli appuntamenti</h2>

        <form method="GET" action="admin_dashboard.php"
              role="search" aria-label="Cerca appuntamento"
              class="search-bar">
            <label class="sr-only" for="q">Cerca per nome, cognome o Codice Fiscale</label>
            <input
                type="search"
                id="q"
                name="q"
                class="form-input"
                value="<?= $filtro_safe ?>"
                placeholder="Cerca per nome, cognome o Codice Fiscale..."
                aria-label="Cerca appuntamento per nome, cognome o Codice Fiscale"
                autocomplete="off"
            >
            <button type="submit" class="btn btn--primary">Cerca</button>
            <?php if ($filtro !== ''): ?>
                <a href="admin_dashboard.php" class="btn btn--ghost">Azzera filtro</a>
            <?php endif; ?>
        </form>

        <?php if ($filtro !== ''): ?>
        <p class="text-sm text-muted mb-16" aria-live="polite">
            <?= count($prenotazioni) ?> risultati per
            "<strong><?= $filtro_safe ?></strong>"
        </p>
        <?php endif; ?>

        <!-- TABELLA -->
        <?php if (empty($prenotazioni)): ?>
        <div class="empty-state card">
            <span class="empty-state__icon" aria-hidden="true">🔍</span>
            <p class="empty-state__title">Nessun risultato trovato</p>
            <p class="empty-state__text">
                <?php if ($filtro !== ''): ?>
                    Nessun appuntamento corrisponde a "<?= $filtro_safe ?>".
                    <a href="admin_dashboard.php">Azzera il filtro</a>
                <?php else: ?>
                    Non ci sono ancora prenotazioni nel sistema.
                <?php endif; ?>
            </p>
        </div>

        <?php else: ?>
        <div class="table-wrapper">
            <table class="table" id="tabella-prenotazioni">
                <caption>Elenco di tutti gli appuntamenti registrati nel sistema</caption>
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Paziente</th>
                        <th scope="col">Codice Fiscale</th>
                        <th scope="col">Data e ora</th>
                        <th scope="col">Stato</th>
                        <th scope="col">Azione</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($prenotazioni as $p):
                        $attiva = in_array($p['stato'], ['prenotato'], true);
                    ?>
                    <tr data-id="<?= (int)$p['id'] ?>"
                        data-stato="<?= htmlspecialchars($p['stato'], ENT_QUOTES, 'UTF-8') ?>">

                        <td><?= (int)$p['id'] ?></td>

                        <td>
                            <div style="font-weight:700;">
                                <?= htmlspecialchars($p['nome'] . ' ' . $p['cognome'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <?php if ($p['email']): ?>
                            <div class="text-sm text-muted">
                                <?= htmlspecialchars($p['email'], ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <?php endif; ?>
                        </td>

                        <td>
                            <code style="font-size:var(--font-size-sm);background:#f1f5f9;
                                         padding:2px 6px;border-radius:4px;">
                                <?= htmlspecialchars($p['cf'], ENT_QUOTES, 'UTF-8') ?>
                            </code>
                        </td>

                        <td style="white-space:nowrap;">
                            <?= htmlspecialchars(fmt_data_admin($p['data_ora']), ENT_QUOTES, 'UTF-8') ?>
                        </td>

                        <td>
                            <span class="status-badge <?= statoClasse($p['stato']) ?>">
                                <?= etichetta_stato($p['stato']) ?>
                            </span>
                            </td>

                        <td>
                            <?php if ($attiva): ?>
                            <form method="POST" action="admin_dashboard.php"
                                  class="form-annulla"
                                  data-nome="<?= htmlspecialchars($p['nome'] . ' ' . $p['cognome'], ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="annulla_id" value="<?= (int)$p['id'] ?>">
                                <button type="submit" class="btn btn--danger btn--sm"
                                        aria-label="Annulla appuntamento di <?= htmlspecialchars($p['nome'] . ' ' . $p['cognome'], ENT_QUOTES, 'UTF-8') ?> del <?= htmlspecialchars(fmt_data_admin($p['data_ora']), ENT_QUOTES, 'UTF-8') ?>">
                                    Annulla
                                </button>
                            </form>
                            <?php else: ?>
                                <span class="text-muted text-sm">—</span>
                            <?php endif; ?>
                        </td>

                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <p class="text-sm text-muted mt-8">
            Totale risultati visualizzati: <strong><?= count($prenotazioni) ?></strong>
        </p>
        <?php endif; ?>

    </section>

</main>

<!-- =====================================================
         FOOTER
    ===================================================== -->
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

<script src="admin_dashboard.js"></script>

</body>
</html>