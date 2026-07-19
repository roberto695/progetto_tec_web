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

    <link rel="stylesheet" media="all" href="style.css">
</head>
<body>

<a href="#main-content" class="skip-link">Salta al contenuto principale</a>

<header id="intestazione">
    <div class="header-container">
        <a href="index.php" class="logo-area" aria-label="VitalPath – torna alla home">
            <svg width="36" height="36" viewBox="0 0 36 36" fill="none"
                 xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                <rect width="36" height="36" rx="8" fill="#0066cc"/>
                <path d="M8 18h4l3-8 4 16 3-10 2 4h4"
                      stroke="white" stroke-width="2.5"
                      stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span class="logo-text">Vital<span>Path</span></span>
        </a>
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
                           aria-describedby="cf-hint"
                           style="text-transform:uppercase;">
                    <span class="form-hint" id="cf-hint">16 caratteri alfanumerici - es. RSSMRA80A01H501X</span>
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
                        <span class="text-muted text-sm" style="font-weight:400;">(facoltativo)</span>
                    </label>
                    <input type="tel" id="telefono" name="telefono"
                           class="form-input" value=""
                           autocomplete="tel" maxlength="20" aria-describedby="telefono-hint">
                    <span class="form-hint" id="telefono-hint">es. 3331234567</span>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password <span class="required" aria-hidden="true">*</span></label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password"
                               class="form-input"
                               autocomplete="new-password" required
                               aria-describedby="password-hint">
                        <button type="button" class="password-toggle" id="toggle-password"
                                aria-label="Mostra password" aria-pressed="false">👁</button>
                    </div>
                    <span class="form-hint" id="password-hint">minimo 6 caratteri</span>
                </div>

                <div class="form-group">
                    <label class="form-check">
                        <input type="checkbox" name="privacy" id="privacy" required>
                        <span>Ho letto e accetto la <a href="#">Privacy Policy</a> e il trattamento dei dati personali ai sensi del GDPR.
                        <span class="required" aria-hidden="true">*</span></span>
                    </label>
                </div>

                <button type="submit" class="btn btn--primary btn--full">Crea account</button>

                <p class="text-sm text-muted text-center mt-16">
                    I campi con <span class="required" aria-hidden="true">*</span> sono obbligatori.
                </p>

            </form>

            <hr class="divider">
            <p class="auth-card__footer">Hai già un account? <a href="login.php">Accedi</a></p>

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

<script src="registrazione.js"></script>
</body>
</html>