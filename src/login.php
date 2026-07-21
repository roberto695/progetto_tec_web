<?php
// ============================================================
// pages/login.php – Login utente VitalPath
// ============================================================
session_start();

// Se già loggato, reindirizza

require_once __DIR__ . '/db.php';

$errori = [];
$cf_val = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Recupero e sanitizzazione input ---
    $cf       = trim($_POST['cf']       ?? '');
    $password = trim($_POST['password'] ?? '');

    $cf_val = htmlspecialchars($cf, ENT_QUOTES, 'UTF-8');

    // --- Validazione server ---
    if ($cf === '') {
        $errori['cf'] = 'Il Codice Fiscale è obbligatorio.';
    }

    if ($password === '') {
        $errori['password'] = 'La password è obbligatoria.';
    }

    // --- Query DB (solo se non ci sono errori di formato) ---
    if (empty($errori)) {
        $stmt = $pdo->prepare(
        'SELECT cf, nome, cognome, telefono, email, password 
        FROM persona WHERE cf = ?'
        );
        $stmt->execute([$cf]);
        $utente = $stmt->fetch();

        if (!$utente || $utente['password'] !== $password) {
            $errori['generale'] = 'Codice Fiscale o password non corretti.';
        } else {
            // Login riuscito
            session_regenerate_id(true);
            $_SESSION['cf']      = $utente['cf'];
            $_SESSION['nome']    = $utente['nome'];
            $_SESSION['cognome'] = $utente['cognome'];
            $_SESSION['telefono'] = $utente['telefono'] ?? 'Non disponibile';
            $_SESSION['email']    = $utente['email'];
            
            
        if ($utente['cf'] === 'admin') { // Controllo se l'utente è l'admin
            header('Location: admin_dashboard.php');
        } else {
            header('Location: dashboard.php');
            exit;
        }
    }
}
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accedi – VitalPath</title>
    <meta name="description" content="Accedi al tuo account VitalPath per gestire i tuoi appuntamenti.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style.css">
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
                <li><a href="login.php" aria-current="page" class="active">Accedi</a></li>
                <li><a href="registrazione.php">Registrati</a></li>
            </ul>
        </nav>
</header>

<!-- MAIN -->
<main id="main-content" tabindex="-1">
    <div class="auth-page">
        <div class="auth-card">

            <!-- Logo centrato -->
            <div class="auth-card__logo" aria-hidden="true">
                <svg width="48" height="48" viewBox="0 0 36 36" fill="none"
                     xmlns="http://www.w3.org/2000/svg">
                    <rect width="36" height="36" rx="8" fill="#0066cc"/>
                    <path d="M8 18h4l3-8 4 16 3-10 2 4h4"
                          stroke="white" stroke-width="2.5"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <h1 class="auth-card__title">Accedi a VitalPath</h1>
            <p class="auth-card__subtitle">
                Inserisci il tuo Codice Fiscale e la password per accedere.
            </p>

            <!-- Riepilogo errori (accessibile con aria-live) -->
            <?php if (!empty($errori)): ?>
            <div class="error-summary" role="alert" aria-live="assertive">
                <h2>
                    <span aria-hidden="true">⚠</span>
                    Si sono verificati <?= count($errori) ?> errori
                </h2>
                <ul>
                    <?php foreach ($errori as $e): ?>
                        <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- FORM LOGIN -->
            <form
                id="form-login"
                method="POST"
                action="login.php"
                novalidate
                aria-label="Modulo di accesso"
            >

                <!-- Codice Fiscale -->
                <div class="form-group">
                    <label class="form-label" for="cf">
                        Codice Fiscale
                        <span class="required" aria-hidden="true">*</span>
                    </label>
                    <input
                        type="text"
                        id="cf"
                        name="cf"
                        class="form-input<?= isset($errori['cf']) ? ' form-input--error' : '' ?>"
                        value="<?= $cf_val ?>"
                        autocomplete="username"
                        maxlength="16"
                        required
                        aria-describedby="<?= isset($errori['cf']) ? 'cf-error' : 'cf-hint' ?>"
                    >
                    <?php if (isset($errori['cf'])): ?>
                        <span class="form-error" id="cf-error" role="alert">
                            <?= htmlspecialchars($errori['cf'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    <?php else: ?>
                        <span class="form-hint" id="cf-hint">
                        16 caratteri alfanumerici - es. RSSMRA80A01H501X
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <div class="label-row">
                        <label class="form-label" for="password">
                            Password
                            <span class="required" aria-hidden="true">*</span>
                        </label>
                    </div>
                    <div class="password-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input<?= isset($errori['password']) ? ' form-input--error' : '' ?>"
                            autocomplete="current-password"
                            required
                            <?php if (isset($errori['password'])): ?>
                            aria-describedby="password-error"
                            <?php endif; ?>
                        >
                        <button
                            type="button"
                            class="password-toggle"
                            id="toggle-password"
                            aria-label="Mostra password"
                            aria-pressed="false"
                        >👁</button>
                    </div>
                    <?php if (isset($errori['password'])): ?>
                        <span class="form-error" id="password-error" role="alert">
                            <?= htmlspecialchars($errori['password'], ENT_QUOTES, 'UTF-8') ?>
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn btn--primary btn--full">
                    Accedi
                </button>

            </form>

            <hr class="divider">

            <p class="auth-card__footer">
                Non hai ancora un account?
                <a href="registrazione.php">Registrati</a>
            </p>

        </div>
    </div>
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

<script src="login.js"></script>
</body>
</html>