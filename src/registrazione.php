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
                               class="form-input"
                               value="" autocomplete="given-name" maxlength="50"
                               required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="cognome">Cognome <span class="required" aria-hidden="true">*</span></label>
                        <input type="text" id="cognome" name="cognome"
                               class="form-input"
                               value="" autocomplete="family-name" maxlength="50"
                               required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="cf">Codice Fiscale <span class="required" aria-hidden="true">*</span></label>
                    <input type="text" id="cf" name="cf"
                           class="form-input"
                           value="" autocomplete="off" maxlength="16"
                           required
                           aria-describedby="cf-hint">
                    <span class="form-hint" id="cf-hint">16 caratteri alfanumerici - es. BNCMRA80A01H501X</span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Indirizzo email <span class="required" aria-hidden="true">*</span></label>
                    <input type="email" id="email" name="email"
                           class="form-input"
                           value="" autocomplete="email"
                           required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="telefono">
                        Numero di telefono
                        <span class="text-muted text-sm optional-text">(facoltativo)</span>
                    </label>
                    <input type="tel" id="telefono" name="telefono"
                           class="form-input" value=""
                           autocomplete="tel" maxlength="20" aria-describedby="telefono-hint">
                    <span class="form-hint" id="telefono-hint">es. 3333333333</span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password <span class="required" aria-hidden="true">*</span></label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password"
                               class="form-input"
                               autocomplete="new-password" required
                               minlength="4"
                               aria-describedby="password-hint">
                        <button type="button" class="password-toggle" id="toggle-password"
                                aria-label="Mostra password" aria-pressed="false">👁</button>
                    </div>
                    <span class="form-hint" id="password-hint">minimo 4 caratteri</span>
                </div>

                <div class="form-group">
                    <label class="form-check">
                        <input type="checkbox" name="privacy" id="privacy" required>
                        <span>Ho letto e accetto la <a href="#" class="link-privacy"><span lang="en">Privacy Policy</span></a> e il trattamento dei dati personali ai sensi del <abbr title="General Data Protection Regulation">GDPR</abbr>.
                        <span class="required" aria-hidden="true">*</span></span>
                    </label>
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