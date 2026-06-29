<?php
// ============================================================
// registrazione.php – Registrazione nuovo utente VitalPath
// ============================================================
session_start();

if (isset($_SESSION['cf'])) {
    header('Location: dashboard.php');
    exit;
}

require_once __DIR__ . '/db.php';

$errori  = [];
$success = false;
$val     = ['nome'=>'','cognome'=>'','cf'=>'','email'=>'','telefono'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nome     = trim($_POST['nome']     ?? '');
    $cognome  = trim($_POST['cognome']  ?? '');
    $cf       = strtoupper(trim($_POST['cf'] ?? ''));
    $email    = trim($_POST['email']    ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $password = $_POST['password']      ?? '';
    $privacy  = isset($_POST['privacy']);

    $val = [
        'nome'     => htmlspecialchars($nome,     ENT_QUOTES, 'UTF-8'),
        'cognome'  => htmlspecialchars($cognome,  ENT_QUOTES, 'UTF-8'),
        'cf'       => htmlspecialchars($cf,       ENT_QUOTES, 'UTF-8'),
        'email'    => htmlspecialchars($email,    ENT_QUOTES, 'UTF-8'),
        'telefono' => htmlspecialchars($telefono, ENT_QUOTES, 'UTF-8'),
    ];

    if ($nome === '')                         $errori['nome']     = 'Il nome è obbligatorio.';
    elseif (mb_strlen($nome) > 50)            $errori['nome']     = 'Il nome non può superare i 50 caratteri.';

    if ($cognome === '')                      $errori['cognome']  = 'Il cognome è obbligatorio.';
    elseif (mb_strlen($cognome) > 50)         $errori['cognome']  = 'Il cognome non può superare i 50 caratteri.';

    if ($cf === '')                           $errori['cf']       = 'Il Codice Fiscale è obbligatorio.';
    elseif (!preg_match('/^[A-Z0-9]{16}$/', $cf)) $errori['cf']  = 'Il Codice Fiscale deve essere di 16 caratteri alfanumerici.';

    if ($email === '')                        $errori['email']    = "L'indirizzo email è obbligatorio.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errori['email'] = 'Inserisci un indirizzo email valido.';

    if ($password === '')                     $errori['password'] = 'La password è obbligatoria.';
    elseif (mb_strlen($password) < 6)         $errori['password'] = 'La password deve contenere almeno 6 caratteri.';

    if (!$privacy)                            $errori['privacy']  = 'Devi accettare la privacy policy per registrarti.';

    if (empty($errori)) {
        $stmt = $pdo->prepare('SELECT cf FROM persona WHERE cf = ?');
        $stmt->execute([$cf]);
        if ($stmt->fetch()) $errori['cf'] = 'Questo Codice Fiscale è già registrato.';
    }

    if (empty($errori)) {
        $stmt = $pdo->prepare('SELECT cf FROM persona WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) $errori['email'] = 'Questa email è già associata a un account.';
    }

    if (empty($errori)) {
        $stmt = $pdo->prepare(
            'INSERT INTO persona (cf, nome, cognome, telefono, email, password)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$cf, $nome, $cognome, $telefono !== '' ? $telefono : null, $email, $password]);
        $success = true;
        $val = ['nome'=>'','cognome'=>'','cf'=>'','email'=>'','telefono'=>''];
    }
}

function err_class(string $f, array $e): string { return isset($e[$f]) ? ' form-input--error' : ''; }
function err_aria(string $f, array $e): string  { return isset($e[$f]) ? ' aria-describedby="'.$f.'-error" aria-invalid="true"' : ''; }
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione – VitalPath</title>
    <meta name="description" content="Crea il tuo account VitalPath per prenotare esami del sangue online.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../style.css">
</head>
<body>

<a href="#main-content" class="skip-link">Salta al contenuto principale</a>

<header id="intestazione" role="banner">
    <?php include 'logo.php'; ?>
        <nav id="nav-principale" aria-label="Navigazione principale">
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="login.php">Accedi</a></li>
                <li><a href="registrazione.php" aria-current="page" class="active">Registrati</a></li>
            </ul>
        </nav>
    </div>
</header>

<main id="main-content" tabindex="-1">
    <div class="auth-page" style="align-items:flex-start;padding-top:var(--space-32);">
        <div class="auth-card" style="max-width:560px;">

            <div class="auth-card__logo" aria-hidden="true">
                <svg width="48" height="48" viewBox="0 0 36 36" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="36" height="36" rx="8" fill="#0066cc"/>
                    <path d="M8 18h4l3-8 4 16 3-10 2 4h4" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>

            <h1 class="auth-card__title">Crea il tuo account</h1>
            <p class="auth-card__subtitle">Registrati per prenotare i tuoi esami del sangue online.</p>

            <?php if ($success): ?>
            <div class="alert alert--success" role="status" aria-live="polite">
                <span class="alert__icon" aria-hidden="true">✓</span>
                <div><strong>Registrazione completata!</strong><br>
                Il tuo account è stato creato. <a href="login.php">Accedi ora</a> per prenotare il tuo esame.</div>
            </div>
            <?php endif; ?>

            <?php if (!empty($errori)): ?>
            <div class="error-summary" role="alert" aria-live="assertive">
                <h2><span aria-hidden="true">⚠</span> Si sono verificati <?= count($errori) ?> errori</h2>
                <ul><?php foreach ($errori as $e): ?><li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li><?php endforeach; ?></ul>
            </div>
            <?php endif; ?>

            <?php if (!$success): ?>
            <form id="form-registrazione" method="POST" action="registrazione.php" novalidate aria-label="Modulo di registrazione">

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="nome">Nome <span class="required" aria-label="obbligatorio">*</span></label>
                        <input type="text" id="nome" name="nome"
                               class="form-input<?= err_class('nome',$errori) ?>"
                               value="<?= $val['nome'] ?>" autocomplete="given-name" maxlength="50"
                               required aria-required="true" <?= err_aria('nome',$errori) ?>>
                        <?php if (isset($errori['nome'])): ?>
                            <span class="form-error" id="nome-error" role="alert"><?= htmlspecialchars($errori['nome'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="cognome">Cognome <span class="required" aria-label="obbligatorio">*</span></label>
                        <input type="text" id="cognome" name="cognome"
                               class="form-input<?= err_class('cognome',$errori) ?>"
                               value="<?= $val['cognome'] ?>" autocomplete="family-name" maxlength="50"
                               required aria-required="true" <?= err_aria('cognome',$errori) ?>>
                        <?php if (isset($errori['cognome'])): ?>
                            <span class="form-error" id="cognome-error" role="alert"><?= htmlspecialchars($errori['cognome'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="cf">Codice Fiscale <span class="required" aria-label="obbligatorio">*</span></label>
                    <input type="text" id="cf" name="cf"
                           class="form-input<?= err_class('cf',$errori) ?>"
                           value="<?= $val['cf'] ?>" autocomplete="off" maxlength="16"
                           required aria-required="true"
                           aria-describedby="cf-hint<?= isset($errori['cf']) ? ' cf-error' : '' ?>"
                           <?php if (isset($errori['cf'])): ?>aria-invalid="true"<?php endif; ?>
                           style="text-transform:uppercase;">
                    <span class="form-hint" id="cf-hint">16 caratteri alfanumerici, es. RSSMRA80A01H501X</span>
                    <?php if (isset($errori['cf'])): ?>
                        <span class="form-error" id="cf-error" role="alert"><?= htmlspecialchars($errori['cf'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Indirizzo email <span class="required" aria-label="obbligatorio">*</span></label>
                    <input type="email" id="email" name="email"
                           class="form-input<?= err_class('email',$errori) ?>"
                           value="<?= $val['email'] ?>" autocomplete="email"
                           required aria-required="true" <?= err_aria('email',$errori) ?>>
                    <?php if (isset($errori['email'])): ?>
                        <span class="form-error" id="email-error" role="alert"><?= htmlspecialchars($errori['email'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="telefono">
                        Numero di telefono
                        <span class="text-muted text-sm" style="font-weight:400;">(facoltativo)</span>
                    </label>
                    <input type="tel" id="telefono" name="telefono"
                           class="form-input" value="<?= $val['telefono'] ?>"
                           autocomplete="tel" maxlength="20" aria-describedby="telefono-hint">
                    <span class="form-hint" id="telefono-hint">Es. 333 1234567</span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password <span class="required" aria-label="obbligatoria">*</span></label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password"
                               class="form-input<?= err_class('password',$errori) ?>"
                               autocomplete="new-password" required aria-required="true"
                               aria-describedby="password-hint<?= isset($errori['password']) ? ' password-error' : '' ?>"
                               <?php if (isset($errori['password'])): ?>aria-invalid="true"<?php endif; ?>>
                        <button type="button" class="password-toggle" id="toggle-password"
                                aria-label="Mostra password" aria-pressed="false">👁</button>
                    </div>
                    <span class="form-hint" id="password-hint">Minimo 6 caratteri.</span>
                    <?php if (isset($errori['password'])): ?>
                        <span class="form-error" id="password-error" role="alert"><?= htmlspecialchars($errori['password'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-check">
                        <input type="checkbox" name="privacy" id="privacy" required aria-required="true"
                               <?= isset($errori['privacy']) ? 'aria-describedby="privacy-error" aria-invalid="true"' : '' ?>>
                        <span>Ho letto e accetto la <a href="#">Privacy Policy</a> e il trattamento dei dati personali ai sensi del GDPR.
                        <span class="required" aria-label="obbligatorio">*</span></span>
                    </label>
                    <?php if (isset($errori['privacy'])): ?>
                        <span class="form-error" id="privacy-error" role="alert"><?= htmlspecialchars($errori['privacy'], ENT_QUOTES, 'UTF-8') ?></span>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn--primary btn--full">Crea account</button>

                <p class="text-sm text-muted text-center mt-16">
                    I campi con <span class="required" aria-hidden="true">*</span> sono obbligatori.
                </p>

            </form>
            <?php endif; ?>

            <hr class="divider">
            <p class="auth-card__footer">Hai già un account? <a href="login.php">Accedi</a></p>

        </div>
    </div>
</main>

<footer class="site-footer" role="contentinfo">
    <div class="footer-container">
        <p>&copy; 2026 VitalPath &bull; Corso di Tecnologie Web &bull; Università di Padova</p>
        <p>Sito realizzato in conformità alle linee guida WCAG 2.2 AA</p>
    </div>
</footer>

<script src="registrazione.js"></script>
</body>
</html>