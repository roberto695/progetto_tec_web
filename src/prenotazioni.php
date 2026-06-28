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
    <title>Prenotazione Prelievo - Centro Prelievi Sanitario</title>
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
                    <li><a href="prenotazioni.html" aria-current="page" class="active">Prenotazioni</a></li>
                    <li><a href="account.php">Area Personale</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main id="main-content">
        
        <div class="prenotazione-header">
            <h2 class="section-heading">Seleziona Data e Ora</h2>
            <p class="lead-text">Scegli il momento più comodo per il tuo esame del sangue.</p>
        </div>

        <form action="#" method="POST" class="prenotazione-layout">
            
            <div class="selezioni-area">
                
                <div class="card">
                    <h3 class="sotto-titolo">Ottobre 2023</h3>
                    
                    <fieldset class="scelta-gruppo">
                        <legend class="sr-only">Seleziona il giorno del prelievo</legend>
                        <div class="griglia-giorni">
                            
                            <div class="opzione-giorno">
                                <input type="radio" name="data_esame" id="data-24" value="2023-10-24" checked>
                                <label for="data-24" class="label-giorno">
                                    <span class="giorno-testo">OGGI</span>
                                    <span class="giorno-numero">24</span>
                                    <span class="giorno-mese">Ott</span>
                                </label>
                            </div>

                            <div class="opzione-giorno">
                                <input type="radio" name="data_esame" id="data-25" value="2023-10-25">
                                <label for="data-25" class="label-giorno">
                                    <span class="giorno-testo">DOM</span>
                                    <span class="giorno-numero">25</span>
                                    <span class="giorno-mese">Ott</span>
                                </label>
                            </div>

                            <div class="opzione-giorno">
                                <input type="radio" name="data_esame" id="data-26" value="2023-10-26">
                                <label for="data-26" class="label-giorno">
                                    <span class="giorno-testo">GIO</span>
                                    <span class="giorno-numero">26</span>
                                    <span class="giorno-mese">Ott</span>
                                </label>
                            </div>

                            <div class="opzione-giorno">
                                <input type="radio" name="data_esame" id="data-27" value="2023-10-27">
                                <label for="data-27" class="label-giorno">
                                    <span class="giorno-testo">VEN</span>
                                    <span class="giorno-numero">27</span>
                                    <span class="giorno-mese">Ott</span>
                                </label>
                            </div>

                            <div class="opzione-giorno">
                                <input type="radio" name="data_esame" id="data-28" value="2023-10-28">
                                <label for="data-28" class="label-giorno">
                                    <span class="giorno-testo">SAB</span>
                                    <span class="giorno-numero">28</span>
                                    <span class="giorno-mese">Ott</span>
                                </label>
                            </div>

                        </div>
                    </fieldset>

                    <h3 class="sotto-titolo" style="margin-top: var(--space-32);">Orari disponibili</h3>
                    
                    <fieldset class="scelta-gruppo">
                        <legend class="sr-only">Seleziona l'orario del prelievo</legend>
                        <div class="griglia-orari">
                            
                            <div class="opzione-orario"><input type="radio" name="ora_esame" id="ora-0800" value="08:00"><label for="ora-0800">08:00</label></div>
                            <div class="opzione-orario"><input type="radio" name="ora_esame" id="ora-0830" value="08:30"><label for="ora-0830">08:30</label></div>
                            <div class="opzione-orario"><input type="radio" name="ora_esame" id="ora-0900" value="09:00"><label for="ora-0900">09:00</label></div>
                            <div class="opzione-orario"><input type="radio" name="ora_esame" id="ora-0930" value="09:30" checked><label for="ora-0930">09:30</label></div>
                            <div class="opzione-orario"><input type="radio" name="ora_esame" id="ora-1000" value="10:00"><label for="ora-1000">10:00</label></div>
                            <div class="opzione-orario"><input type="radio" name="ora_esame" id="ora-1030" value="10:30"><label for="ora-1030">10:30</label></div>
                            <div class="opzione-orario"><input type="radio" name="ora_esame" id="ora-1100" value="11:00"><label for="ora-1100">11:00</label></div>
                            <div class="opzione-orario"><input type="radio" name="ora_esame" id="ora-1130" value="11:30"><label for="ora-1130">11:30</label></div>
                            <div class="opzione-orario"><input type="radio" name="ora_esame" id="ora-1200" value="12:00"><label for="ora-1200">12:00</label></div>
                            <div class="opzione-orario"><input type="radio" name="ora_esame" id="ora-1230" value="12:30"><label for="ora-1230">12:30</label></div>

                        </div>
                    </fieldset>
                </div>

                <div class="card warning-card scheda-preparazione">
                    <h3 style="font-size: 1.25rem; color: var(--primary-dark); margin-bottom: var(--space-8);">Preparazione all'esame</h3>
                    <p style="font-size: 1rem; margin: 0;">Si consiglia di presentarsi a digiuno da almeno 8 ore. È possibile bere acqua.</p>
                </div>

            </div>

            <aside class="sidebar-area">
                <div class="card welcome-card">
                    <h3 class="sotto-titolo" style="font-size: 1.5rem; margin-bottom: var(--space-24);">Riepilogo</h3>
                    
                    <div class="riepilogo-lista">
                        <div class="riepilogo-item">
                            <span class="riepilogo-etichetta">Data selezionata</span>
                            <strong class="riepilogo-valore date-highlight">Oggi, 24 Ottobre</strong>
                        </div>
                        
                        <div class="riepilogo-item">
                            <span class="riepilogo-etichetta">Orario selezionato</span>
                            <strong class="riepilogo-valore">09:30</strong>
                        </div>
                        
                        <div class="riepilogo-item" style="border-bottom: none; padding-bottom: 0; margin-bottom: 0;">
                            <span class="riepilogo-etichetta">Luogo</span>
                            <strong class="riepilogo-valore" style="font-weight: normal; font-size: 1rem;">Centro VitalPath, Via Roma 12</strong>
                        </div>
                    </div>

                    <button type="submit" class="cta-button" style="width: 100%; margin-top: var(--space-32); border: none; cursor: pointer;">
                        Conferma Prenotazione
                    </button>
                    
                    <p class="disclaimer-prenotazione">Nessun costo di prenotazione online</p>
                </div>
            </aside>

        </form>

    </main>

    <footer class="site-footer">
        <div class="footer-container">
            <p>&copy; 2026 Centro Prelievi &bull; Corso di Tecnologie Web &bull; Università di Padova</p>
            <p class="footer-sub">Sito sviluppato in conformità alle linee guida di accessibilità WCAG 2.2 AA</p>
        </div>
    </footer>

</body>
</html>