<?php
// login.php
// Avvia la sessione per gestire lo stato di login
session_start();

// Se l'utente è già loggato, reindirizzalo alla dashboard
if (isset($_SESSION['cf'])) {
    header('Location: account.php');
    exit;
}

// Variabili per i messaggi di errore
$error = '';
$cf = '';

// Gestione del submit del form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validazione lato server
    $cf = strtoupper(trim($_POST['cf'] ?? ''));
    $telefono = trim($_POST['telefono'] ?? '');
    
    if (empty($cf) || empty($telefono)) {
        $error = 'Per favore, compila tutti i campi.';
    } elseif (strlen($cf) !== 16 || !preg_match('/^[A-Z0-9]{16}$/', $cf)) {
        $error = 'Inserisci un Codice Fiscale valido di 16 caratteri alfanumerici.';
    } else {
        // Connessione al database
        try {
            $pdo = new PDO("mysql:host=database;dbname=prelievi_db", "user_web", "8A2cU25SoU9zmUrewcib2FgGsY9juEyPrSnFdXBJypa6xfhOmC");
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt = $pdo->prepare("SELECT cf, nome, cognome, telefono FROM persona WHERE cf = :cf AND telefono = :telefono");
            $stmt->execute([
                ':cf' => $cf,
                ':telefono' => $telefono
            ]);
            $persona = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($persona) {
                // Login riuscito
                $_SESSION['cf'] = $persona['cf'];
                $_SESSION['nome'] = $persona['nome'];
                $_SESSION['cognome'] = $persona['cognome'];
                $_SESSION['telefono'] = $persona['telefono'];
                
                // Redirect alla dashboard
                header('Location: account.php');
                exit;
            } else {
                $error = 'Codice Fiscale o numero di telefono non validi.';
            }
        } catch (PDOException $e) {
            $error = 'Si è verificato un errore tecnico. Riprova più tardi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Centro Prelievi Sanitario</title>
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
                    <li><a href="account.php">Area Personale</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main id="main-content">
        <section class="login-section" aria-labelledby="login-title">
            <div class="login-card">
                <h2 id="login-title">Accedi al tuo account</h2>
                <p class="login-subtitle">Inserisci il tuo Codice Fiscale e numero di telefono per accedere all'area riservata</p>

                <?php if (!empty($error)): ?>
                <div class="error-message" role="alert" aria-live="polite">
                    <span aria-hidden="true">⚠️</span>
                    <?php echo htmlspecialchars($error); ?>
                </div>
                <?php endif; ?>

                <form method="POST" action="login.php" class="login-form" novalidate>
                    <div class="form-group">
                        <label for="cf">Codice Fiscale</label>
                        <input 
                            type="text" 
                            id="cf" 
                            name="cf" 
                            value="<?php echo htmlspecialchars($cf); ?>"
                            placeholder="RSSMRA80A01H501X"
                            required
                            autocomplete="off"
                            maxlength="16"
                            pattern="[A-Za-z0-9]{16}"
                            aria-describedby="cf-help"
                            style="text-transform: uppercase;"
                        >
                        <span id="cf-help" class="field-hint">Inserisci il tuo Codice Fiscale di 16 caratteri (es. RSSMRA80A01H501X)</span>
                    </div>

                    <div class="form-group">
                        <label for="telefono">Numero di telefono</label>
                        <div class="phone-wrapper">
                            <input 
                                type="tel" 
                                id="telefono" 
                                name="telefono" 
                                placeholder="333 1234567"
                                required
                                autocomplete="tel"
                                aria-describedby="telefono-help"
                            >
                        </div>
                        <span id="telefono-help" class="field-hint">Inserisci il numero di telefono registrato in centro</span>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="cta-button cta-button-full">
                            Accedi
                        </button>
                    </div>

                    <div class="login-links">
                        <span class="help-text">Non hai un account? <a href="registrazione.php" class="link-register">Registrati</a></span>
                    </div>
                </form>
            </div>
        </section>

        <!-- Sezione informativa sulla sicurezza -->
        <section class="security-section" aria-labelledby="security-title">
            <div class="security-card">
                <h3 id="security-title" class="security-heading">🔒 Accesso sicuro</h3>
                <p>I tuoi dati sono protetti con crittografia avanzata. Il centro rispetta le normative sulla privacy e il trattamento dei dati sanitari.</p>
                <ul class="security-list">
                    <li><span aria-hidden="true">✓</span> Connessione crittografata</li>
                    <li><span aria-hidden="true">✓</span> Accesso riservato ai soli pazienti</li>
                    <li><span aria-hidden="true">✓</span> Utilizzo del Codice Fiscale come identificativo univoco</li>
                </ul>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="footer-container">
            <p>&copy; 2026 Centro Prelievi &bull; Corso di Tecnologie Web &bull; Università di Padova</p>
            <p class="footer-sub">Sito sviluppato in conformità alle linee guida di accessibilità WCAG 2.2 AA</p>
        </div>
    </footer>

    <script>
        // Validazione lato client del form
        document.querySelector('.login-form').addEventListener('submit', function(e) {
            const cf = document.getElementById('cf');
            const telefono = document.getElementById('telefono');
            let hasError = false;
            
            // Rimuovi errori precedenti
            document.querySelectorAll('.form-group.error').forEach(el => {
                el.classList.remove('error');
            });
            document.querySelectorAll('.field-error').forEach(el => {
                el.remove();
            });

            // Validazione Codice Fiscale
            const cfValue = cf.value.trim().toUpperCase();
            cf.value = cfValue; // Converti in maiuscolo
            
            if (!cfValue) {
                showFieldError(cf, 'Inserisci il tuo Codice Fiscale.');
                hasError = true;
            } else if (!/^[A-Z0-9]{16}$/.test(cfValue)) {
                showFieldError(cf, 'Il Codice Fiscale deve essere composto da 16 caratteri alfanumerici.');
                hasError = true;
            }

            // Validazione telefono
            if (!telefono.value.trim()) {
                showFieldError(telefono, 'Inserisci il tuo numero di telefono.');
                hasError = true;
            } else if (!/^[\d\s+]{6,20}$/.test(telefono.value.trim())) {
                showFieldError(telefono, 'Inserisci un numero di telefono valido (es. 333 1234567).');
                hasError = true;
            }

            if (hasError) {
                e.preventDefault();
                // Focus sul primo campo con errore
                const firstError = document.querySelector('.form-group.error input');
                if (firstError) {
                    firstError.focus();
                }
            }
        });

        function showFieldError(input, message) {
            const group = input.closest('.form-group');
            group.classList.add('error');
            
            const error = document.createElement('span');
            error.className = 'field-error';
            error.setAttribute('role', 'alert');
            error.textContent = message;
            
            // Inserisci dopo l'input
            input.parentNode.insertBefore(error, input.nextSibling);
        }

        // Rimuovi lo stato di errore quando l'utente inizia a digitare
        document.querySelectorAll('.form-group input').forEach(input => {
            input.addEventListener('input', function() {
                const group = this.closest('.form-group');
                group.classList.remove('error');
                const error = group.querySelector('.field-error');
                if (error) {
                    error.remove();
                }
            });
        });

        // Converti automaticamente il CF in maiuscolo
        document.getElementById('cf').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });

        // Formatta automaticamente il telefono (opzionale)
        document.getElementById('telefono').addEventListener('input', function() {
            // Rimuovi spazi multipli e caratteri non numerici tranne + e spazio
            this.value = this.value.replace(/[^\d\s+]/g, '');
        });
    </script>
</body>
</html>