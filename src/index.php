<?php
// ============================================================
// index.php – Homepage VitalPath
// ============================================================
session_start();

define('ADMIN_CF', 'ADMINVTLPTH00A00');

$utente_loggato = isset($_SESSION['cf']);
$nome_utente    = $_SESSION['nome']  ?? null;
$cf_utente      = $_SESSION['cf']    ?? null;

$is_admin = ($utente_loggato && $cf_utente === ADMIN_CF);
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO -->
    <title>VitalPath – Centro Prelievi del Sangue | Prenotazione Online</title>
    <meta name="description"
          content="Prenota il tuo esame del sangue online con VitalPath. Servizio rapido, accessibile e professionale. Referti digitali, gestione appuntamenti semplice.">
    <meta name="keywords"
          content="prenotazione analisi del sangue, centro prelievi, esami del sangue, referti online, prenotazione online esami">

    <!-- Font Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../style.css">
</head>
<body>

    <!-- Salta al contenuto principale (accessibilità tastiera) -->
    <a href="#main-content" class="skip-link">Salta al contenuto principale</a>

    <!-- =====================================================
         HEADER
    ===================================================== -->
    <header id="intestazione" role="banner">
        <div class="header-container">

            <?php include 'logo.php'; ?>

            <nav id="nav-principale" aria-label="Navigazione principale">
                <ul>
                    <li><a href="index.php" aria-current="page" class="active">Home</a></li>
                    <?php if ($utente_loggato): ?>
                        <?php if ($is_admin): ?>
                            <li><a href="/admin_dashboard.php">Dashboard Admin</a></li>
                        <?php else: ?>
                            <li><a href="dashboard.php">Area Personale</a></li>
                            <li><a href="prenotazioni.php">Prenota</a></li>
                        <?php endif; ?>
                        <li>
                            <a href="logout.php">
                                Esci (<?= htmlspecialchars($nome_utente, ENT_QUOTES, 'UTF-8') ?>)
                            </a>
                        </li>
                    <?php else: ?>
                        <li><a href="registrazione.php">Registrati</a></li>
                        <li><a href="login.php">Accedi</a></li>
                    <?php endif; ?>
                </ul>
            </nav>

        </div>
    </header>

    <!-- =====================================================
         CONTENUTO PRINCIPALE
    ===================================================== -->
    <main id="main-content" tabindex="-1">

        <!-- HERO -->
        <section class="hero" aria-labelledby="hero-title">
            <h1 id="hero-title" class="hero__title">
                La tua salute, con serenità e precisione
            </h1>
            <p class="hero__subtitle">
                Benvenuto in VitalPath, il portale per prenotare i tuoi esami del sangue online.
                Servizio rapido, accessibile e guidato da personale specializzato.
            </p>
            <div class="hero__actions">
                <?php if ($utente_loggato && !$is_admin): ?>
                    <a href="/prenotazioni.php" class="btn btn--primary">
                        Prenota un esame
                    </a>
                    <a href="/dashboard.php" class="btn btn--secondary">
                        Vai alla tua area
                    </a>
                <?php elseif ($utente_loggato && $is_admin): ?>
                    <a href="admin/dashboard.php" class="btn btn--primary">
                        Dashboard Admin
                    </a>
                <?php else: ?>
                    <a href="registrazione.php" class="btn btn--primary">
                        Registrati
                    </a>
                    <a href="login.php" class="btn btn--secondary">
                        Accedi al tuo account
                    </a>
                <?php endif; ?>
            </div>
        </section>

        <!-- SERVIZI -->
        <section aria-labelledby="servizi-title">
            <h2 id="servizi-title" class="section-title">I nostri servizi</h2>
            <div class="grid grid--3">

                <article class="card" aria-labelledby="serv-1">
                    <div class="badge badge--info" aria-hidden="true">Prenotazione</div>
                    <h3 id="serv-1">Prenota online</h3>
                    <p>
                        Scegli data e ora direttamente dal tuo dispositivo,
                        senza code telefoniche. Ricevi conferma immediata.
                    </p>
                </article>

                <article class="card" aria-labelledby="serv-2">
                    <div class="badge badge--success" aria-hidden="true">Referti</div>
                    <h3 id="serv-2">Referti digitali</h3>
                    <p>
                        Consulta i tuoi referti direttamente nell'area personale,
                        in modo sicuro e conforme alle normative sulla privacy.
                    </p>
                </article>

                <article class="card" aria-labelledby="serv-3">
                    <div class="badge badge--warning" aria-hidden="true">Supporto</div>
                    <h3 id="serv-3">Assistenza dedicata</h3>
                    <p>
                        Il nostro staff è sempre disponibile per accompagnarti,
                        spiegarti ogni procedura e rispondere a ogni domanda.
                    </p>
                </article>

            </div>
        </section>

        <!-- COME FUNZIONA -->
        <section aria-labelledby="come-title">
            <h2 id="come-title" class="section-title">Come funziona</h2>
            <ol class="grid grid--3" style="list-style: none;">

                <li class="card card--accent">
                    <div aria-hidden="true" style="
                        font-size: 2rem;
                        font-weight: 800;
                        color: var(--color-primary);
                        margin-bottom: var(--space-12);
                    ">01</div>
                    <h3>Registrati</h3>
                    <p>
                        Crea il tuo account con nome, cognome, Codice Fiscale ed email.
                        Basta un minuto.
                    </p>
                </li>

                <li class="card card--accent">
                    <div aria-hidden="true" style="
                        font-size: 2rem;
                        font-weight: 800;
                        color: var(--color-primary);
                        margin-bottom: var(--space-12);
                    ">02</div>
                    <h3>Scegli data e ora</h3>
                    <p>
                        Seleziona il giorno e l'orario più comodo tra gli slot disponibili,
                        dal lunedì al sabato.
                    </p>
                </li>

                <li class="card card--accent">
                    <div aria-hidden="true" style="
                        font-size: 2rem;
                        font-weight: 800;
                        color: var(--color-primary);
                        margin-bottom: var(--space-12);
                    ">03</div>
                    <h3>Presentati al centro</h3>
                    <p>
                        Vieni a digiuno (minimo 8 ore), puoi bere acqua. Ti aspettiamo in Via Roma 12 Padova.
                    </p>
                </li>

            </ol>
        </section>

        <!-- NOTIZIE / AVVISI -->
        <section aria-labelledby="notizie-title">
            <h2 id="notizie-title" class="section-title">Avvisi e notizie</h2>
            <div class="grid grid--2">

                <article class="card" aria-labelledby="news-1">
                    <div class="badge badge--warning">Avviso</div>
                    <h3 id="news-1">Apertura durante le festività</h3>
                    <p>
                        Il centro potrebbe essere chiuso nei giorni festivi.
                        Vi invitiamo a chiamare la segreteria per informazioni (049 000 0000).
                    </p>
                </article>

                <article class="card" aria-labelledby="news-2">
                    <div class="badge badge--success">Novità</div>
                    <h3 id="news-2">Refertazione online attiva</h3>
                    <p>
                        È possibile scaricare i propri referti dall'area personale
                        in modo sicuro e conforme alla normativa GDPR.
                    </p>
                </article>

            </div>
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

    <!-- =====================================================
         FOOTER
    ===================================================== -->
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