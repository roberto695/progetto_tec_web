<?php
    /*
    try {
        $pdo = new PDO("mysql:host=database;dbname=prelievi_db", "user_web", "8A2cU25SoU9zmUrewcib2FgGsY9juEyPrSnFdXBJypa6xfhOmC");
    }
    catch (PDOException $e) {
    }
    */
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Centro Prelievi Sanitario</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <a href="#main-content" class="skip-link">Salta al contenuto principale</a>

    <header id="intestazione">
        <div class="header-container">
            <div class="logo-area">
                <img src="immagini/logo.png" alt="Logo ufficiale del Centro Prelievi" id="logo">
                <h1>Centro Prelievi</h1> 
            </div>
            
            <nav aria-label="Navigazione principale">
                <ul>
                    <li><a href="index.php" aria-current="page" class="active">Home</a></li>
                    <li><a href="prenotazioni.php">Prenotazioni</a></li>
                    <li><a href="account.php">Area Personale</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main id="main-content">
        
        <section class="hero-section" aria-labelledby="hero-title">
            <div class="hero-body">
                <h2 id="hero-title">La tua salute, con serenità e precisione</h2>
                <p class="lead-text">
                    Benvenuti nel portale del Centro Prelievi. Offriamo servizi diagnostici rapidi, 
                    accessibili e guidati da personale specializzato in un ambiente sicuro e protetto.
                </p>
                <a href="prenotazioni.php" class="cta-button">Prenota un esame ora</a>
            </div>
        </section>

        <section class="news-section" aria-labelledby="notizie-title">
            <h2 id="notizie-title" class="section-heading">Ultime notizie e avvisi</h2>
            <div class="grid-news">
                <article class="card news-card">
                    <div class="card-badge">Avviso</div>
                    <h3>Orari durante le festività</h3>
                    <p>Si avvisano i gentili pazienti che il centro osserverà variazioni di orario nei prossimi giorni festivi. Consultare la tabella orari aggiornata.</p>
                </article>
                <article class="card news-card">
                    <div class="card-badge novità">Servizio</div>
                    <h3>Nuovo servizio di refertazione online</h3>
                    <p>Da oggi è possibile scaricare i propri referti direttamente dall'area personale in modo sicuro, rapido e conforme alle normative sulla privacy.</p>
                </article>
            </div>
        </section>

        <section class="info-section" aria-labelledby="prelievi-title">
            <div class="card info-card">
                <h2 id="prelievi-title" class="section-heading">Perché fare un prelievo?</h2>
                <div class="info-body-text">
                    <p>
                        Fare un prelievo non è solo un controllo di routine: è un modo semplice
                        e veloce per prenderti cura della tua salute. In pochi minuti puoi ottenere
                        informazioni preziose che ti aiutano a prevenire problemi, capire meglio
                        come stai e intervenire in tempo se qualcosa non va.
                    </p>
                    <p>
                        Spesso basta un piccolo gesto oggi per evitare preoccupazioni domani.
                        E se hai dubbi o timori, ricorda che il nostro personale è preparato per accompagnarti,
                        spiegarti ogni passaggio e rendere tutto il più tranquillo possibile.
                    </p>
                </div>
            </div>
        </section>

    </main>

    <footer class="site-footer">
        <div class="footer-container">
            <p>&copy; 2026 Centro Prelievi &bull; Corso di Tecnologie Web &bull; Università di Padova</p>
            <p class="footer-sub">Sito sviluppato in conformità alle linee guida di accessibilità WCAG 2.2 AA</p>
        </div>
    </footer>

</body>
</html>