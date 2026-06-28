<?php
    /* La connessione al database verrà inserita qui alla fine del corso.
    Per ora usiamo dati stub (finti) scritti direttamente in HTML.
    */
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Area Personale - Centro Prelievi Sanitario</title>
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
                    <li><a href="index.php">Home</a></li>
                    <li><a href="prenotazioni.php">Prenotazioni</a></li>
                    <li><a href="account.php" aria-current="page" class="active">Area Personale</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main id="main-content">
        
        <section class="user-welcome-section" aria-labelledby="welcome-title">
            <div class="card welcome-card">
                <h2 id="welcome-title">Bentornato, Mario Rossi</h2>
                <p>In questa schermata puoi gestire i tuoi appuntamenti prenotati, controllare lo stato degli esami e consultare lo storico delle tue visite in totale sicurezza.</p>
                <div class="user-meta">
                    <p><strong>Codice Fiscale:</strong> RSSMRA80A01L049K</p>
                    <p><strong>Tessera Sanitaria:</strong> 1234567890</p>
                </div>
            </div>
        </section>

        <section class="dashboard-section" aria-labelledby="appuntamenti-title">
            <h2 id="appuntamenti-title" class="section-heading">I tuoi prossimi prelievi</h2>
            
            <div class="table-responsive">
                <table class="accessible-table">
                    <caption>Elenco dei prelievi attualmente prenotati e da eseguire</caption>
                    <thead>
                        <tr>
                            <th scope="col">Data e Ora</th>
                            <th scope="col">Tipo di Esame</th>
                            <th scope="col">Stato</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong class="date-highlight">12 Luglio 2026</strong> ore 07:30</td>
                            <td>Esami del Sangue di Routine (Emocromo, Glicemia, Colesterolo)</td>
                            <td><span class="status-badge status-pending">Confermato</span></td>
                        </tr>
                        <tr>
                            <td><strong class="date-highlight">28 Luglio 2026</strong> ore 08:15</td>
                            <td>Curva Glicemica e Screening Tiroideo</td>
                            <td><span class="status-badge status-pending">Confermato</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="dashboard-section" aria-labelledby="storico-title">
            <h2 id="storico-title" class="section-heading">Storico visite passate</h2>
            
            <div class="table-responsive">
                <table class="accessible-table">
                    <caption>Elenco storico delle visite e dei prelievi effettuati in passato</caption>
                    <thead>
                        <tr>
                            <th scope="col">Data Visita</th>
                            <th scope="col">Tipologia Esame</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>15 Gennaio 2026</td>
                            <td>Screening Vitamina D e Calcio</td>
                        </tr>
                        <tr>
                            <td>04 Novembre 2025</td>
                            <td>Esami del Sangue Completi generali</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="info-section" aria-labelledby="note-title">
            <div class="card info-card warning-card">
                <h2 id="note-title" class="section-heading">Promemoria importante per il prelievo</h2>
                <div class="info-body-text">
                    <ul>
                        <li><strong>Digiuno:</strong> Per tutti gli esami del sangue è necessario presentarsi a digiuno da almeno 8 ore. È consentito bere solo un bicchiere d'acqua naturale.</li>
                        <li><strong>Documenti da portare:</strong> Ricordati di portare la Tessera Sanitaria (Carta Regionale dei Servizi) e il documento d'identità valido.</li>
                        <li><strong>Disdetta:</strong> Se non puoi presentarti, ti chiediamo di annullare la prenotazione almeno 48 ore prima per liberare il posto per un altro paziente.</li>
                    </ul>
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