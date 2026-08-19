<?php
// ============================================================
// registrazione.php – Registrazione nuovo utente VitalPath
// ============================================================
session_start();

// Includi la connessione al database
require_once __DIR__ . '/db.php';

// Inizializza variabili per il form
$dati = [
    'nome'      => '',
    'cognome'   => '',
    'cf'        => '',
    'email'     => '',
    'telefono'  => '',
];
$errori = [];
$messaggio = '';

// ============================================================
// GESTIONE DEL POST (invio del form)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recupera e sanitizza i dati
    $dati['nome']      = trim($_POST['nome'] ?? '');
    $dati['cognome']   = trim($_POST['cognome'] ?? '');
    $dati['cf']        = trim(strtoupper($_POST['cf'] ?? ''));
    $dati['email']     = trim($_POST['email'] ?? '');
    $dati['telefono']  = trim($_POST['telefono'] ?? '');
    $password          = $_POST['password'] ?? '';
    $privacy           = isset($_POST['privacy']);

    // --- Validazione Nome ---
    if ($dati['nome'] === '') {
        $errori['nome'] = 'Il nome è obbligatorio.';
    }

    // --- Validazione Cognome ---
    if ($dati['cognome'] === '') {
        $errori['cognome'] = 'Il cognome è obbligatorio.';
    }

    // --- Validazione Codice Fiscale ---
    $cf_upper = strtoupper($dati['cf']);
    $reserved_cf = ['ADMIN', 'USER'];
    if ($dati['cf'] === '') {
        $errori['cf'] = 'Il Codice Fiscale è obbligatorio.';
    } elseif (in_array($cf_upper, $reserved_cf)) {
        $errori['cf'] = 'Questo Codice Fiscale è riservato e non può essere registrato.';
    } elseif (!preg_match('/^[A-Z]{6}\d{2}[A-Z]\d{2}[A-Z]\d{3}[A-Z]$/', $cf_upper)) {
        $errori['cf'] = 'Il Codice Fiscale non è valido (16 caratteri alfanumerici).';
    }

    // --- Validazione Email ---
    if ($dati['email'] === '') {
        $errori['email'] = 'L\'indirizzo email è obbligatorio.';
    } elseif (!filter_var($dati['email'], FILTER_VALIDATE_EMAIL)) {
        $errori['email'] = 'Inserisci un indirizzo email valido.';
    }

    // --- Validazione Telefono (opzionale, 10 cifre) ---
    if (!empty($dati['telefono']) && !preg_match('/^\d{10}$/', $dati['telefono'])) {
        $errori['telefono'] = 'Il numero di telefono deve essere composto da 10 cifre.';
    }

    // --- Validazione Password ---
    if ($password === '') {
        $errori['password'] = 'La password è obbligatoria.';
    } elseif (strlen($password) < 4) {
        $errori['password'] = 'La password deve contenere almeno 4 caratteri.';
    }

    // --- Validazione Privacy ---
    if (!$privacy) {
        $errori['privacy'] = 'Devi accettare la privacy policy per registrarti.';
    }

    // --- Se non ci sono errori, procedi con l'inserimento ---
    if (empty($errori)) {
        try {
            // Verifica se il CF è già registrato
            $stmt = $pdo->prepare("SELECT cf FROM persona WHERE cf = ?");
            $stmt->execute([$cf_upper]);
            if ($stmt->fetch()) {
                $errori['cf'] = 'Questo Codice Fiscale è già registrato.';
            }
        } catch (PDOException $e) {
            $errori['generale'] = 'Errore nel controllo del CF. Riprova più tardi.';
        }

        if (empty($errori)) {
            try {
                // Verifica se l'email è già registrata
                $stmt = $pdo->prepare("SELECT email FROM persona WHERE email = ?");
                $stmt->execute([$dati['email']]);
                if ($stmt->fetch()) {
                    $errori['email'] = 'Questo indirizzo email è già registrato.';
                }
            } catch (PDOException $e) {
                $errori['generale'] = 'Errore nel controllo dell\'email. Riprova più tardi.';
            }
        }

        if (empty($errori)) {
            try {
                // Inserisci il nuovo utente
                $stmt = $pdo->prepare("
                    INSERT INTO persona (cf, nome, cognome, telefono, email, password)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $cf_upper,
                    $dati['nome'],
                    $dati['cognome'],
                    $dati['telefono'] ?: null, // se vuoto, inserisci NULL
                    $dati['email'],
                    $password
                ]);

                // Registrazione riuscita: login automatico
                session_regenerate_id(true);
                $_SESSION['cf']      = $cf_upper;
                $_SESSION['nome']    = $dati['nome'];
                $_SESSION['cognome'] = $dati['cognome'];
                $_SESSION['telefono'] = $dati['telefono'] ?: 'Non disponibile';
                $_SESSION['email']   = $dati['email'];
                $_SESSION['ha_appuntamento_attivo'] = false;

                header('Location: dashboard.php');
                exit;
            } catch (PDOException $e) {
                $errori['generale'] = 'Errore durante la registrazione. Riprova più tardi.';
                // Opzionale: log dell'errore per il debug
                // error_log('Registrazione fallita: ' . $e->getMessage());
            }
        }
    }
}

// Prepara i valori da ristampare nel form (con escape HTML)
$nome_val      = htmlspecialchars($dati['nome'], ENT_QUOTES, 'UTF-8');
$cognome_val   = htmlspecialchars($dati['cognome'], ENT_QUOTES, 'UTF-8');
$cf_val        = htmlspecialchars($dati['cf'], ENT_QUOTES, 'UTF-8');
$email_val     = htmlspecialchars($dati['email'], ENT_QUOTES, 'UTF-8');
$telefono_val  = htmlspecialchars($dati['telefono'], ENT_QUOTES, 'UTF-8');

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione – VitalPath</title>
    <meta name="description" content="Crea il tuo account VitalPath per prenotare esami del sangue online.">
    <meta name="keywords" content="account, registrati, prenota esami, centro prelievi Padova, prenota online, VitalPath">
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
                <li><a href="login.php">Accedi</a></li>
                <li><span class="nav-current" aria-current="page">Registrati</span></li>
            </ul>
        </nav>
    </div>
</header>

<!-- MAIN -->
<main id="main-content" tabindex="-1">
    <div class="auth-page">
        <div class="auth-card auth-card--large">

            <div class="auth-card__logo">
                <?php $logo_class = 'logo-no-link'; include 'logo.php'; ?>
            </div>

            <h1 class="auth-card__title">Crea il tuo <span lang="en">account</span></h1>
            <p class="auth-card__subtitle">Registrati per prenotare i tuoi esami del sangue <span lang="en">online</span>.</p>

            <form id="form-registrazione" method="POST" action="registrazione.php" novalidate aria-label="Modulo di registrazione">

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="nome">Nome <span class="required" aria-hidden="true">*</span></label>
                        <input type="text" id="nome" name="nome"
                               class="form-input<?= isset($errori['nome']) ? ' form-input--error' : '' ?>"
                               value="<?= $nome_val ?>" autocomplete="given-name" maxlength="50"
                               required>
                            <?php if (isset($errori['nome'])): ?>
                            <span class="form-error" id="nome-error" role="alert"><?= htmlspecialchars($errori['nome'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="cognome">Cognome <span class="required" aria-hidden="true">*</span></label>
                        <input type="text" id="cognome" name="cognome"
                               class="form-input<?= isset($errori['cognome']) ? ' form-input--error' : '' ?>"
                               value="<?= $cognome_val ?>" autocomplete="family-name" maxlength="50"
                               required>
                            <?php if (isset($errori['cognome'])): ?>
                            <span class="form-error" id="cognome-error" role="alert"><?= htmlspecialchars($errori['cognome'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="cf">Codice Fiscale <span class="required" aria-hidden="true">*</span></label>
                    <input type="text" id="cf" name="cf"
                           class="form-input<?= isset($errori['cf']) ? ' form-input--error' : '' ?>"
                           value="<?= $cf_val ?>" autocomplete="off" maxlength="16"
                           required
                           aria-describedby="<?= isset($errori['cf']) ? 'cf-error' : 'cf-hint' ?>">
                    <span class="form-hint" id="cf-hint">16 caratteri alfanumerici - es. BNCMRA80A01H501X</span>
                <?php if (isset($errori['cf'])): ?>
                    <span class="form-error" id="cf-error" role="alert"><?= htmlspecialchars($errori['cf'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Indirizzo email <span class="required" aria-hidden="true">*</span></label>
                    <input type="email" id="email" name="email"
                           class="form-input<?= isset($errori['email']) ? ' form-input--error' : '' ?>"
                           value="<?= $email_val ?>" autocomplete="email"
                           required>
                        <?php if (isset($errori['email'])): ?>
                            <span class="form-error" id="email-error" role="alert"><?= htmlspecialchars($errori['email'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="telefono">
                        Numero di telefono
                        <span class="text-muted text-sm optional-text">(facoltativo)</span>
                    </label>
                    <input type="tel" id="telefono" name="telefono"
                           class="form-input<?= isset($errori['telefono']) ? ' form-input--error' : '' ?>" value="<?= $telefono_val ?>"
                           autocomplete="tel" maxlength="10" aria-describedby="<?= isset($errori['telefono']) ? 'telefono-error' : 'telefono-hint' ?>">
                    <span class="form-hint" id="telefono-hint">es. 3333333333</span>
                <?php if (isset($errori['telefono'])): ?>
                    <span class="form-error" id="telefono-error" role="alert"><?= htmlspecialchars($errori['telefono'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password <span class="required" aria-hidden="true">*</span></label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password"
                               class="form-input<?= isset($errori['password']) ? ' form-input--error' : '' ?>"
                               autocomplete="new-password" required
                               minlength="4"
                               aria-describedby="<?= isset($errori['password']) ? 'password-error' : 'password-hint' ?>">
                        <button type="button" class="password-toggle" id="toggle-password"
                                aria-label="Mostra password" aria-pressed="false">👁</button>
                    </div>
                    <?php if (isset($errori['password'])): ?>
                        <span class="form-error" id="password-error" role="alert"><?= htmlspecialchars($errori['password'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php else: ?>
                        <span class="form-hint" id="password-hint">minimo 4 caratteri</span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-check">
                        <input type="checkbox" name="privacy" id="privacy" <?= isset($_POST['privacy']) ? 'checked' : '' ?> required>
                        <span>Ho letto e accetto la <a href="#" class="link-privacy"><span lang="en">Privacy Policy</span></a> e il trattamento dei dati personali ai sensi del <abbr title="General Data Protection Regulation">GDPR</abbr>.
                        <span class="required" aria-hidden="true">*</span></span>
                    </label>
                    <?php if (isset($errori['privacy'])): ?>
                        <span class="form-error" id="privacy-error" role="alert"><?= htmlspecialchars($errori['privacy'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn--primary btn--full">Crea <span lang="en">account</span></button>

                <p class="text-sm text-muted text-center mt-16">
                    I campi con <span class="required" aria-hidden="true">*</span> sono obbligatori.
                </p>

            </form>

            <hr class="divider">
            <p class="auth-card__footer">Hai già un <span lang="en">account</span>? <a href="login.php" class="link-privacy">Accedi</a></p>

        </div>
    </div>
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

<script src="registrazione.js"></script>
</body>
</html>