<?php
// prenotazioni.php
session_start();

// Includi la connessione al database
require_once __DIR__ . '/db.php';

// Se l'utente non è loggato, reindirizzalo al login
if (!isset($_SESSION['cf'])) {
    header('Location: login.php');
    exit;
}

$cf = $_SESSION['cf'];
$error = '';
$success = '';
$data_selezionata = $_POST['data_esame'] ?? '2024-01-20';
$ora_selezionata = $_POST['ora_esame'] ?? '09:00';
$note = trim($_POST['note'] ?? '');

// Controlla se l'utente ha già una prenotazione attiva
$ha_prenotazione_attiva = false;
try {
    $stmt = $pdo->prepare("
        SELECT id FROM prenotazione 
        WHERE persona_id = :cf AND stato IN ('prenotato', 'confermato')
    ");
    $stmt->execute([':cf' => $cf]);
    $ha_prenotazione_attiva = $stmt->fetch() !== false;
} catch (PDOException $e) {
    // Ignora
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['conferma'])) {
    $data_ora = $data_selezionata . ' ' . $ora_selezionata . ':00';
    
    try {
        // Verifica se lo slot è libero
        $stmt = $pdo->prepare("
            SELECT id FROM prenotazione 
            WHERE data_ora = :data_ora AND stato != 'cancellato'
        ");
        $stmt->execute([':data_ora' => $data_ora]);
        
        if ($stmt->fetch()) {
            $error = 'Questo orario non è più disponibile. Scegli un altro slot.';
        } else {
            // Inserisci la prenotazione
            $stmt = $pdo->prepare("
                INSERT INTO prenotazione (persona_id, data_ora, stato, note) 
                VALUES (:cf, :data_ora, 'prenotato', :note)
            ");
            $stmt->execute([
                ':cf' => $cf,
                ':data_ora' => $data_ora,
                ':note' => $note
            ]);
            
            $success = '✅ Prenotazione confermata con successo!';
        }
    } catch (PDOException $e) {
        $error = 'Si è verificato un errore tecnico. Riprova più tardi.';
    }
}

// Mappa per i nomi dei giorni
$giorni = [
    '2024-01-20' => 'Oggi, 20 Gennaio',
    '2024-01-21' => 'Dom, 21 Gennaio',
    '2024-01-22' => 'Lun, 22 Gennaio',
    '2024-01-23' => 'Mar, 23 Gennaio',
    '2024-01-24' => 'Mer, 24 Gennaio',
];
$giorno_selezionato = $giorni[$data_selezionata] ?? $data_selezionata;
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
                    <li><a href="prenotazioni.php" aria-current="page" class="active">Prenotazioni</a></li>
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

        <?php if (!empty($error)): ?>
        <div class="alert alert--error" role="alert" aria-live="polite">
            <span class="alert__icon" aria-hidden="true">⚠️</span>
            <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
        <div class="alert alert--success" role="status" aria-live="polite">
            <span class="alert__icon" aria-hidden="true">✅</span>
            <?php echo htmlspecialchars($success); ?>
            <p style="margin-top: var(--space-8);">
                <a href="account.php" class="btn btn--primary" style="display: inline-block;">Vai alla tua area personale</a>
            </p>
        </div>
        <?php endif; ?>

        <?php if (empty($success) && !$ha_prenotazione_attiva): ?>
        <form method="POST" action="prenotazioni.php" class="prenotazione-layout">
            
            <div class="selezioni-area">
                
                <div class="card">
                    <h3 class="sotto-titolo">Seleziona il giorno</h3>
                    
                    <fieldset class="scelta-gruppo">
                        <legend class="sr-only">Seleziona il giorno del prelievo</legend>
                        <div class="griglia-giorni">
                            
                            <div class="opzione-giorno">
                                <input type="radio" name="data_esame" id="data-20" value="2024-01-20" <?php echo $data_selezionata === '2024-01-20' ? 'checked' : ''; ?>>
                                <label for="data-20" class="label-giorno">
                                    <span class="giorno-testo">OGGI</span>
                                    <span class="giorno-numero">20</span>
                                    <span class="giorno-mese">Gen</span>
                                </label>
                            </div>

                            <div class="opzione-giorno">
                                <input type="radio" name="data_esame" id="data-21" value="2024-01-21" <?php echo $data_selezionata === '2024-01-21' ? 'checked' : ''; ?>>
                                <label for="data-21" class="label-giorno">
                                    <span class="giorno-testo">DOM</span>
                                    <span class="giorno-numero">21</span>
                                    <span class="giorno-mese">Gen</span>
                                </label>
                            </div>

                            <div class="opzione-giorno">
                                <input type="radio" name="data_esame" id="data-22" value="2024-01-22" <?php echo $data_selezionata === '2024-01-22' ? 'checked' : ''; ?>>
                                <label for="data-22" class="label-giorno">
                                    <span class="giorno-testo">LUN</span>
                                    <span class="giorno-numero">22</span>
                                    <span class="giorno-mese">Gen</span>
                                </label>
                            </div>

                            <div class="opzione-giorno">
                                <input type="radio" name="data_esame" id="data-23" value="2024-01-23" <?php echo $data_selezionata === '2024-01-23' ? 'checked' : ''; ?>>
                                <label for="data-23" class="label-giorno">
                                    <span class="giorno-testo">MAR</span>
                                    <span class="giorno-numero">23</span>
                                    <span class="giorno-mese">Gen</span>
                                </label>
                            </div>

                            <div class="opzione-giorno">
                                <input type="radio" name="data_esame" id="data-24" value="2024-01-24" <?php echo $data_selezionata === '2024-01-24' ? 'checked' : ''; ?>>
                                <label for="data-24" class="label-giorno">
                                    <span class="giorno-testo">MER</span>
                                    <span class="giorno-numero">24</span>
                                    <span class="giorno-mese">Gen</span>
                                </label>
                            </div>

                        </div>
                    </fieldset>

                    <h3 class="sotto-titolo" style="margin-top: var(--space-32);">Orari disponibili</h3>
                    
                    <fieldset class="scelta-gruppo">
                        <legend class="sr-only">Seleziona l'orario del prelievo</legend>
                        <div class="griglia-orari">
                            
                            <div class="opzione-orario">
                                <input type="radio" name="ora_esame" id="ora-0800" value="08:00" <?php echo $ora_selezionata === '08:00' ? 'checked' : ''; ?>>
                                <label for="ora-0800">08:00</label>
                            </div>
                            <div class="opzione-orario">
                                <input type="radio" name="ora_esame" id="ora-0830" value="08:30" <?php echo $ora_selezionata === '08:30' ? 'checked' : ''; ?>>
                                <label for="ora-0830">08:30</label>
                            </div>
                            <div class="opzione-orario">
                                <input type="radio" name="ora_esame" id="ora-0900" value="09:00" <?php echo $ora_selezionata === '09:00' ? 'checked' : ''; ?>>
                                <label for="ora-0900">09:00</label>
                            </div>
                            <div class="opzione-orario">
                                <input type="radio" name="ora_esame" id="ora-0930" value="09:30" <?php echo $ora_selezionata === '09:30' ? 'checked' : ''; ?>>
                                <label for="ora-0930">09:30</label>
                            </div>
                            <div class="opzione-orario">
                                <input type="radio" name="ora_esame" id="ora-1000" value="10:00" <?php echo $ora_selezionata === '10:00' ? 'checked' : ''; ?>>
                                <label for="ora-1000">10:00</label>
                            </div>
                            <div class="opzione-orario">
                                <input type="radio" name="ora_esame" id="ora-1030" value="10:30" <?php echo $ora_selezionata === '10:30' ? 'checked' : ''; ?>>
                                <label for="ora-1030">10:30</label>
                            </div>
                            <div class="opzione-orario">
                                <input type="radio" name="ora_esame" id="ora-1100" value="11:00" <?php echo $ora_selezionata === '11:00' ? 'checked' : ''; ?>>
                                <label for="ora-1100">11:00</label>
                            </div>
                            <div class="opzione-orario">
                                <input type="radio" name="ora_esame" id="ora-1130" value="11:30" <?php echo $ora_selezionata === '11:30' ? 'checked' : ''; ?>>
                                <label for="ora-1130">11:30</label>
                            </div>
                            <div class="opzione-orario">
                                <input type="radio" name="ora_esame" id="ora-1200" value="12:00" <?php echo $ora_selezionata === '12:00' ? 'checked' : ''; ?>>
                                <label for="ora-1200">12:00</label>
                            </div>
                            <div class="opzione-orario">
                                <input type="radio" name="ora_esame" id="ora-1230" value="12:30" <?php echo $ora_selezionata === '12:30' ? 'checked' : ''; ?>>
                                <label for="ora-1230">12:30</label>
                            </div>

                        </div>
                    </fieldset>

                    <!-- Campo note (testo libero) -->
                    <div class="form-group" style="margin-top: var(--space-24);">
                        <label class="form-label" for="note">Note aggiuntive (opzionale)</label>
                        <textarea 
                            id="note" 
                            name="note" 
                            class="form-textarea"
                            rows="3"
                            placeholder="Eventuali richieste particolari o informazioni utili per il prelievo"
                            aria-describedby="note-help"
                        ><?php echo htmlspecialchars($note); ?></textarea>
                        <span id="note-help" class="form-hint">Campo opzionale per richieste specifiche</span>
                    </div>
                </div>

                <div class="card card--warning scheda-preparazione">
                    <h3 style="font-size: 1.25rem; color: var(--color-text); margin-bottom: var(--space-8);">Preparazione all'esame</h3>
                    <p style="font-size: 1rem; margin: 0;">Si consiglia di presentarsi a digiuno da almeno 8 ore. È possibile bere acqua.</p>
                </div>

            </div>

            <aside class="sidebar-area">
                <div class="card card--accent">
                    <h3 class="sotto-titolo" style="font-size: 1.5rem; margin-bottom: var(--space-24);">Riepilogo</h3>
                    
                    <div class="riepilogo-lista">
                        <div class="riepilogo-item">
                            <span class="riepilogo-etichetta">Data selezionata</span>
                            <strong class="riepilogo-valore date-highlight" id="riepilogo-data"><?php echo $giorno_selezionato; ?></strong>
                        </div>
                        
                        <div class="riepilogo-item">
                            <span class="riepilogo-etichetta">Orario selezionato</span>
                            <strong class="riepilogo-valore" id="riepilogo-ora"><?php echo $ora_selezionata; ?></strong>
                        </div>
                        
                        <div class="riepilogo-item" style="border-bottom: none; padding-bottom: 0; margin-bottom: 0;">
                            <span class="riepilogo-etichetta">Luogo</span>
                            <strong class="riepilogo-valore" style="font-weight: normal; font-size: 1rem;">Centro Prelievi, Via Roma 12</strong>
                        </div>
                    </div>

                    <button type="submit" name="conferma" class="btn btn--primary btn--full" style="margin-top: var(--space-32);">
                        Conferma Prenotazione
                    </button>
                    
                    <p class="disclaimer-prenotazione">Nessun costo di prenotazione online</p>
                </div>
            </aside>

        </form>
        <?php elseif ($ha_prenotazione_attiva && empty($success)): ?>
        <div class="card card--warning">
            <h3 style="font-size: 1.25rem; color: var(--color-text); margin-bottom: var(--space-8);">
                📋 Hai già una prenotazione attiva
            </h3>
            <p style="font-size: 1rem; margin-bottom: var(--space-16);">
                Non puoi prenotare un nuovo esame finché non completi o annulli la prenotazione esistente.
            </p>
            <a href="account.php" class="btn btn--primary">Vedi la tua prenotazione</a>
        </div>
        <?php endif; ?>

    </main>

    <footer class="site-footer">
        <div class="footer-container">
            <p>&copy; 2026 Centro Prelievi &bull; Corso di Tecnologie Web &bull; Università di Padova</p>
            <p class="footer-sub">Sito sviluppato in conformità alle linee guida di accessibilità WCAG 2.2 AA</p>
        </div>
    </footer>

    <script>
        // Aggiorna il riepilogo quando l'utente seleziona data e ora
        const giorniMap = {
            '2024-01-20': 'Oggi, 20 Gennaio',
            '2024-01-21': 'Dom, 21 Gennaio',
            '2024-01-22': 'Lun, 22 Gennaio',
            '2024-01-23': 'Mar, 23 Gennaio',
            '2024-01-24': 'Mer, 24 Gennaio'
        };

        document.querySelectorAll('input[name="data_esame"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const data = this.value;
                document.getElementById('riepilogo-data').textContent = giorniMap[data] || data;
            });
        });

        document.querySelectorAll('input[name="ora_esame"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('riepilogo-ora').textContent = this.value;
            });
        });
    </script>

</body>
</html>