# Progetto VitalPath - Istruzioni

## Accesso al sito
1. Apri il terminale 1:  
   `ssh -L 8022:tecweb:22 -L 8080:tecweb:80 amenegal@paolotti.studenti.math.unipd.it`
2. Apri il terminale 2:  
   `ssh -L 8888:localhost:8000 -p 8022 amenegal@localhost`
3. Nel terminale 2, se il server PHP non è attivo, esegui:  
   `cd ~/public_html`
   `nohup /usr/bin/php -S 0.0.0.0:8000 -t src/ > php.log 2>&1 &`
4. Apri il browser su: `http://localhost:8888`

## Avvio con Docker (locale)
1. Avvia Docker
2. Apri il terminale 1 e nella cartella del progetto esegui:
`make rebuild`
4. Apri il browser su: `http://localhost:8000`

## Credenziali di test
- Admin: `admin` / `admin`
- Utente: `user` / `user`
- Mario Rossi: `RSSMRA80A01H501X` / `user`
- Anna Bianchi: `BNCANN85B45F205Y` / `user`

## Database
- Nome DB: `amenegal`
- Utente: `amenegal`
- Host: `localhost`

## Ruoli

### 👤 Utente
- Login e registrazione con salvataggio nel database
- Possibilità di prenotare un **unico appuntamento** scegliendo lo slot disponibile
- In caso di rifiuto da parte dell'admin o di data scaduta, l'utente può prenotare una nuova visita

### 🔐 Admin
- Visualizzazione di tutti gli appuntamenti effettuati dagli utenti
- Gestione delle prenotazioni con possibilità di rifiuto in caso di necessità o sovrapposizioni
