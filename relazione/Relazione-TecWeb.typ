#set document(title: "VitalPath - Sistema di Prenotazione Prelievi", author: ("Roberto Doroftei", "Francesco Marcon", "Andrea Menegaldo", "Edis Hodja"))
#set text(font: "New Computer Modern", size: 11.2pt, lang: "it")
#set par(justify: true, leading: 0.72em, first-line-indent: 1.5em)
#set heading(numbering: "1.1")
#set page(
  paper: "a4",
  margin: (x: 2.5cm, y: 2.5cm),
  numbering: "1",
  number-align: center,
  header: context {
    if counter(page).get().first() > 1 [
      #set text(size: 9pt, fill: rgb("#666666"))
      Relazione di progetto: VitalPath
      #v(-0.5em)
      #line(length: 100%, stroke: 0.5pt + rgb("#cccccc"))
    ]
  }
)
// --- FRONTPAGE ---
#page(numbering: none)[
  #set align(center)
  #v(1cm)
  #grid(
    columns: (auto, 1fr), // La prima colonna si adatta alla larghezza del logo, la seconda prende il resto dello spazio
    gutter: 1.5cm,        // Spazio (margine) tra il logo e il testo
    align: horizon,       // Centra verticalmente il logo e il testo sulla stessa linea
    
    // Colonna 1: Il logo
    image("logo_unipd.png", width: 4cm),
    
    // Colonna 2: Il testo
    align(left)[
      #text(size: 18pt, weight: "bold")[Università degli Studi di Padova] \
      #text(size: 14pt)[Corso di Laurea in Informatica] \
    ]
  )
  
  #image("logo_sito.png")
  #text(size: 22pt, weight: "bold", fill: rgb("#0066cc"))[Vital]#text(size: 22pt, weight: "bold", fill: rgb("#000000"))[Path] \
  #v(0.5cm)
  #text(size: 16pt, weight: "medium")[Sistema di prenotazione prelievi online] \
  #text(size: 12pt)[Relazione di Progetto — A.A. 2025-2026]
  
  #v(2.5cm)
  #grid(
    columns: (1fr, 1fr),
    align: center,
    [
      #text(size: 11pt, weight: "bold")[Indirizzo del sito:] \
      #text(fill: rgb("#0066cc"))[tecweb.studenti.math.unipd.it/amenegal]
    ],
    [
      #text(size: 11pt, weight: "bold")[Mail Referente:] \
      #text(fill: rgb("#0066cc"))[andrea.menegaldo\@studenti.unipd.it]
    ]
  )
  
  #v(1.5cm)
  #text(size: 11pt, weight: "bold")[Credenziali di Accesso per il Test:] \
  #v(0.2cm)
  #table(
    columns: (1fr, 1.5fr, 1fr),
    align: (left, left, left),
    stroke: 0.5pt + rgb("#888888"),
    inset: 8pt,
    [*Ruolo*], [*Username / Codice Fiscale*], [*Password*],
    [Utente], [user], [user],
    [Amministratore], [admin], [admin],
    [Utente], [RSSMRA80A01H501X], [user],
    [Utente], [BNCANN85B45F205Y], [user]
    
  )
  
  #v(1.5cm)
  #text(size: 11pt, weight: "bold")[Gruppo Componenti:] \
  #v(0.2cm)
  #table(
    columns: (2fr, 1fr),
    align: (left, center),
    stroke: 0.5pt + rgb("#888888"),
    inset: 8pt,
    [*Nome e Cognome*], [*Matricola*],
    [Roberto Doroftei], [2111031],
    [Francesco Marcon], [2110990],
    [Andrea Menegaldo], [2116426],
    [Edis Hodja], [2116422]
  )
]

#pagebreak()

// --- INDICE ---
#outline(title: [Indice], indent: auto, depth: 3)
#pagebreak()

// --- 1. INTRODUZIONE (ABSTRACT) ---
= Introduzione
#v(0.5cm)
Il progetto VitalPath nasce dall'esigenza di realizzare una piattaforma web dedicata alla gestione e alla prenotazione degli appuntamenti per i prelievi del sangue all'interno di una struttura sanitaria o di un laboratorio d'analisi locale. Nel panorama attuale, l'interazione tra il cittadino e le strutture sanitarie per questo tipo di servizi è spesso delegata a centralini telefonici o a portali complessi, che presentano grossi problemi in termini di usabilità ed accessibilità.

L'applicazione proposta vuole abbattere queste criticità offrendo un'interfaccia efficace. Da un lato, fornisce ai pazienti un ambiente privo di frizioni informatiche per monitorare lo stato del proprio profilo sanitario e riservare un singolo slot temporale per l'esame. Dall'altro lato mette a disposizione del personale amministrativo un pannello di controllo globale per supervisionare il flusso delle richieste, verificare le statistiche del centro e intervenire con azioni di annullamento o revoca in caso di necessità.

L'intero impianto applicativo è stato sviluppato rifiutando l'adozione di framework preimpostati, focalizzandosi sull'utilizzo nativo delle tecnologie web standard. Questa scelta ci ha permesso di esercitare un controllo più preciso sulla pulizia del codice garantendo la totale separazione tra la struttura dei contenuti, lo stile della presentazione e le logiche di comportamento lato client e server. Un aspetto importante è stato quello del rispetto dei requisiti di accessibilità e inclusività, assicurando che la piattaforma risulti pienamente navigabile anche tramite tecnologie assistive o dispositivi a schermo ridotto.

#v(1cm)

// --- 2. ANALISI DEI REQUISITI ---
= Analisi dei requisiti
== Target di utenza e obiettivi di business
#v(0.5cm)
La piattaforma che abbiamo realizzato è progettata per interfacciarsi con due macro-categorie di utenti, ciascuna caratterizzata da necessità differenti:
- *Utente(User):* Rappresenta il soggetto che necessita del servizio. L'obiettivo principale è minimizzare il tempo speso per la registrazione o il login e per la prenotazione dell'esame. Garantire una consultazione immediata dei propri dati e appuntamenti. L'interfaccia deve accogliere con la stessa efficacia sia utenti famigliari con le tecnolgie sia fasce di popolazione meno avanzate con gli strumenti informatici (utenti anziani), eliminando ambiguità testuali o flussi inutili.
- *Amministratore(Admin):* Necessita di una panoramica immediata e centralizzata del carico di lavoro. L'obiettivo è supervisionare le prenotazioni attive per prevenire il sovraffollamento della struttura.

#v(1cm)

== Requisiti di funzionamento
#v(0.5cm)
L'analisi delle specifiche ha portato alla definizione di regole ben definite, implementate per garantire l'integrità dei flussi di lavoro:

+ *Vincolo del singolo appuntamento:* Al fine di evitare coincidenze con gli slot temporali, per evitare un attacco DOS (denial of service)  e garantire la disponibilità del servizio a tutta l'utenza, un paziente registrato può mantenere un solo appuntamento attivo nello stato "prenotato". Il sistema vieta l'accesso all'area di prenotazione finché l'appuntamento non viene effettuato o cancellato.

+ *Stati della prenotazione:* Il modello dati implementato prevede tre stati principali per una prenotazione: `prenotato`, `effettuato` e `cancellato`. Quando si prenota un esame il suo stato è `prenotato` di default.

+ *Finestra temporale di prenotazione:* Il sistema genera gli slot disponibili esclusivamente per una finestra di sei giorni lavorativi consecutivi a partire dal giorno successivo a quello corrente (dal lunedì al sabato, dalle 08:00 alle 12:30).

+ *Ripristino della possibilità di prenotazione:* Nel momento in cui un amministratore cancella un appuntamento già fissato da un paziente, l'utente riacquisisce la possibilità di inserire una nuova prenotazione nel sistema.

#v(1cm)

// --- 3. ORGANIZZAZIONE DEL LAVORO E PROGETTAZIONE ---
= Organizzazione del lavoro e progettazione

== Approccio metodologico e suddivisione dei ruoli
#v(0.5cm)
Lo sviluppo della web app ha seguito un approccio incrementale basato su continui cicli di integrazione e revisione del codice. La collaborazione del gruppo si è strutturata attraverso l'utilizzo di un repository GitHub e sessioni di allineamento costanti sulla piattaforma vocale Discord, per le comunicazioni semplici si è oprato per un gruppo WhatsApp, consentendo una comprensione maggiore dell'intera applicazione web riunendo le attività individuali.
#v(0.5cm)

=== Dettaglio delle attività individuali
#v(0.2cm)
Per garantire un avanzamento parallelo e sincronizzato del progetto, le diverse sezioni di lavoro sono state suddivise cercando di ottenere un equilibrio tra i carichi di lavoro di backend, frontend e infrastruttura.

- *Roberto Doroftei:*
  Ha ingegnerizzato l'intero ecosistema di virtualizzazione usando Docker, configurando i servizi di networking per Apache/PHP, MariaDB e l'interfaccia di amministrazione. Ha centralizzato la gestione dei flussi automatizzando le istruzioni del terminale tramite un `Makefile` per il team.

- *Francesco Marcon:*
  Ha progettato la logica applicativa core in PHP nativo implementando i controlli di sicurezza sia per la fase di login che di registrazione degli utenti. Ha inoltre coordinato il workflow dei rami di sviluppo e la risoluzione dei conflitti sul repository.

- *Andrea Menegaldo:*
  Si è occupato della modellazione concettuale e logica del database, strutturando le tabelle in forma normale e definendo i vincoli di integrità referenziale. Sul fronte del frontend, ha architettato il layout responsivo del sito e il sistema globale di impaginazione e variabili in CSS puro. Ha inoltre eseguito i test di contrasto cromatico e usabilità visiva.

- *Edis Hodja:*
  Si è occupato dell' interfaccia e dei comportamenti dell'area amministratore, integrando le relative metriche e funzioni di cancellazione degli slot. Ha implementato gli script JavaScript  per i controlli interattivi e reattivi del client.

#v(1cm)

== Gestione dei file e della directory
#v(0.5cm)
La cartella dei file evidenzia una separazione distinta tra i componenti di configurazione dell'ambiente di virtualizzazione locale Docker, gli script SQL di inizializzazione della persistenza e la cartella sorgente dell'applicazione web, eliminando accoppiamenti pesanti tra codice e infrastruttura:

#set text(font: "New Computer Modern", size: 11pt)
```text
progetto_tec_web/
├── docker-compose.yml
├── Makefile
├── README.md
├── db/
│   └── init_scripts/
    ├── 0_init_db.sql
    └── 1_grant_permissions.sql
|── src/
|   ├── admin_dashboard.js
    ├── admin_dashboard.php
    ├── cancella_prenotazione.php
    ├── dashboard.js
    ├── dashboard.php
    ├── db.php
    ├── favicon.svg
    ├── index.php
    ├── login.js
    ├── login.php
    ├── logo.php
    ├── logout.php
    ├── prenotazioni.js
    ├── prenotazioni.php
    ├── registrazione.js
    ├── registrazione.php
    └── style.css
└── web/
    └── Dokerfile```
#set text(font: "New Computer Modern", size: 11pt)

#pagebreak()

== Architettura dell'ambiente di sviluppo (Docker e Makefile)
#v(0.5cm)
Per evitare il riscontro di anomalie software dovute dalle differenze tra i sistemi operativi dei componenti del gruppo, l'intero stack è stato virtualizzato tramite Docker Compose in tre container isolati nella medesima rete virtuale:
- #box(fill: luma(240), radius: 3pt, inset: 2pt)[web]: Espone un server HTTP Apache basato su PHP 8.2 sulla porta locale 8000.

- #box(fill: luma(240), radius: 3pt, inset: 2pt)[database]: Definisce un motore relazionale MariaDB con script SQL.

- #box(fill: luma(240), radius: 3pt, inset: 2pt)[phpmyadmin]: Un'interfaccia grafica esposta sulla porta 8080.

L'interazione con l'ambiente è semplificata da un #box(fill: luma(240), radius: 3pt, inset: 2pt)[Makefile] tramite il singolo comando #box(fill: luma(240), radius: 3pt, inset: 2pt)[make rebuild]. Tutte le informazioni per accedere al sito sono contenute nel file #box(fill: luma(240), radius: 3pt, inset: 2pt)[README.md].

#v(1cm)

// --- 4. IMPLEMENTAZIONI LATO FRONT-END ---
= Implementazioni lato front-end

== Struttura delle sezioni e funzionalità per ruolo
#v(0.5cm)
L'interfaccia utente è stata progettata seguendo una gerarchia, adattando dinamicamente i componenti visibili in base all'autenticazione presente nella sessione dell'utente che visita il sito.

- *Visitatore:* Accedendo alla homepage si ritrova di fronte ad una panoramica informativa sui servizi del centro, sulle modalità di preparazione agli esami e sugli orari della struttura. La barra di navigazione mostra i collegamenti alla finestra di autenticazione e registrazione.
- *Utente registrato:* Una volta autenticato, il menu mostra anche il collegamento all'area personale e se non è presente alcuna prenotazione attiva del link per accedere alla finestra di prenotazione.
- *Amministratore:* Il login con credenziali amministrative reindirizza al pannello di controllo dell'intero sistema. Questa sezione non presenta le funzioni di prenotazione ma porta ad una schermata di monitoraggio avanzata contenente le statistiche del centro e l'elenco di tutte le visite associate al rispettivo utenti registrati.

#pagebreak()

== Mappa di navigazione del sito
#v(0.5cm)
Di seguito è riportata la mappa di navigazione del sito, pagina per pagina. Per le pagine con un form (login e registrazione) sono distinti i collegamenti di navigazione statici dall'esito dell'invio del form.

#v(0.5cm)

#set text(font: "New Computer Modern", size: 10.5pt)
```text
Home (index.php)                            [non autenticato]
  ├--> Login (login.php)
  └--> Registrazione (registrazione.php)

Registrazione (registrazione.php)
  ├--> Login (login.php)
  ├--> Home (index.php)
  └==> Dashboard (dashboard.php)             [esito: registrazione riuscita]

Login (login.php)
  ├--> Registrazione (registrazione.php)
  ├--> Home (index.php)
  ├==> Dashboard (dashboard.php)             [esito: utente]
  └==> Admin Dashboard (admin_dashboard.php) [esito: admin]

Dashboard (dashboard.php)                    [utente autenticato]
  ├--> Home (index.php)
  ├--> Prenotazioni (prenotazioni.php)       [solo se nessuna prenotazione attiva]
  └--> Logout ------------------------------> Home (index.php)

Admin Dashboard (admin_dashboard.php)        [admin]
  ├--> Home (index.php)
  └--> Logout ------------------------------> Home (index.php)

Prenotazioni (prenotazioni.php)              [utente autenticato, non admin, senza prenotazioni attive]
  ├--> Home (index.php)
  └--> Dashboard (dashboard.php)
```
#set text(font: "New Computer Modern", size: 11pt)

#v(0.5cm)

Legenda: #box(fill: luma(240), radius: 3pt, inset: 2pt)[`-->`] indica un collegamento di navigazione presente nella pagina (link o pulsante), #box(fill: luma(240), radius: 3pt, inset: 2pt)[`==>`] indica il reindirizzamento a seguito dell'invio riuscito di un form. Le pagine di errore non sono rappresentate.

#v(1cm)

== Validazione dei dati e sicurezza
#v(0.5cm)
La sicurezza e l'integrità dei dati inseriti sono garantite sul client tramite script dedicati in JavaScript standard puro prevenendo l'inoltro di richieste errate o incomplete verso il server.

Nella pagina di registrazione e di modifica dei dati, dietro alla compilazione del form per verificare la conformità dei campi compilati dall'utente è presente una logica con delle limitazioni:
- L'indirizzo email deve corrispondere al pattern validato.
- Il codice fiscale deve corrispondere al pattern validato (eccetto per admin e user) e viene forzato in lettere maiuscole.
- Il numero di telefonoviene bloccato a una lunghezza fissa di 10 cifre numeriche.
- La password viene controllata in termini robustezza basata sulla lunghezza della stringa rifiutando password con meno di 4 caratteri.

In caso di anomalie, l'invio del form viene interrotto tramite un metodo #box(fill: luma(240), radius: 3pt, inset: 2pt)[`e.preventDefault()`] che attiva una funzione che mostra un avviso in cima al form.

#v(1cm)

== Operazioni dedicate all'amministratore
#v(0.5cm)
Per ottimizzare l'efficienza delle operazioni dell'amministratore, lo script #box(fill: luma(240), radius: 3pt, inset: 2pt)[`admin_dashboard.js`] presenta un sistema di filtraggio dei record della tabella in tempo reale senza richiedere il caricamento della pagina. Agganciando un `EventListener` all'input di ricerca viene analizzata la stringa digitata, confrontandola con il contenuto testuale di ogni riga della tabella.

Inoltre, è stato integrato un algoritmo di ordinamento per colonne tramite l'elemento #box(fill: luma(240), radius: 3pt, inset: 2pt)[`.sort()`] degli array JavaScript. Cliccando sulle intestazioni abilitate le righe vengono ordinate alfabeticamente o numericamente a seconda del tipo di dato (es. identificativo numerico, ordine alfabetico o per data), modificando l'attributo di accessibilità #box(fill: luma(240), radius: 3pt, inset: 2pt)[`aria-sort`] tra i valori #box(fill: luma(240), radius: 3pt, inset: 2pt)[`ascending`] e #box(fill: luma(240), radius: 3pt, inset: 2pt)[`descending`].

#v(1cm)

== Implementazione del CSS
#v(0.5cm)
Il foglio di stile del progetto è stato realizzato utilizzando CSS nativo, organizzando le regole in modo modulare e facendo uso di custom properties per centralizzare colori, tipografia, spaziature e altre caratteristiche grafiche comuni. Sono state inoltre definite alcune utility classes riutilizzabili per la gestione delle spaziature e dei layout Flexbox, riducendo la duplicazione delle regole e mantenendo uniforme la struttura delle pagine.
Particolare attenzione è stata dedicata al comportamento responsive. Attraverso diverse media query, il layout viene adattato alle dimensioni dello schermo, modificando la disposizione degli elementi, il numero di colonne delle griglie e la struttura di alcuni componenti. Per schermi inferiori a 960 px, ad esempio, le tabelle vengono trasformate in blocchi verticali, evitando lo scorrimento orizzontale e mantenendo leggibili i dati. Infine, è stata realizzata una modalità specifica per la stampa mediante media print, nella quale vengono nascosti gli elementi non necessari, come intestazione e footer, eliminate ombre ed effetti grafici e impostati colori e caratteri più adatti alla stampa.

#v(1cm)
// --- 5. ACCESSIBILITÀ E USABILITÀ ---
= Accessibilità e usabilità

== Studio dei colortest e conformità WCAG 2.1
#v(0.5cm)
La parte di frontend della piattaforma abbiamo cercato di farla rispettando i requisiti stabiliti dalle linee guida WCAG 2.1 al livello AA. Le scelte cromatiche, definite all'interno del file #box(fill: luma(240), radius: 3pt, inset: 2pt)[`style.css`], non sono solo una scelta estetica, ma sono state verificate con strumenti di colortest per assicurare un contrasto di luminanza adeguato. I Test eseguiti ci hanno permesso di selezionare un pantone adeguato.

Il testo principale adotta il colore nero su sfondo bianco puro sviluppando un rapporto di contrasto pari a 16.7:1, ampiamente superiore alla soglia minima di 4.5:1 richiesta per il testo normale. I messaggi di errore usano un background chiaro abbinato a un testo rosso scuro, garantendo leggibilità del testo e la chiara evidenziazione dell'allert.

#v(1cm)

== Colori utilizzati e palette del design system
#v(0.5cm)
Per rendere riconoscibile la piattaforma abbiamo selezionato una palette che richiama, sul piano percettivo e su quello culturale, l'ambito sanitario in cui il servizio si colloca.

Il colore primario è il blu (#box(fill: luma(240), radius: 3pt, inset: 2pt)[`#0066cc`]), applicato su sfondo bianco (#box(fill: luma(240), radius: 3pt, inset: 2pt)[`#f8fafc`]). La combinazione non è casuale. Nella percezione comune, occidentale e italiana in particolare, il blu è associato a fiducia, competenza e stabilità, per questo banche, assicurazioni e strutture sanitarie lo adottano diffusamente nella propria identità visiva, spesso a preferenza del rosso o del verde acceso, colori più energici ma anche più ansiogeni in un contesto clinico. Trattandosi di un servizio di prenotazione per prelievi del sangue, chi accede al sito può già trovarsi in uno stato di lieve apprensione legato all'esame stesso. La scelta del blu risponde proprio a questa condizione, trasmettendo calma e serietà senza risultare né freddo né allarmante.

Il bianco, usato come sfondo dominante, richiama a sua volta l'immaginario degli ambienti clinici, camici, pareti, strumentazione, culturalmente associato a pulizia, igiene e sterilità. Oltre al valore simbolico, garantisce anche il massimo contrasto di luminanza possibile con il testo scuro sovrapposto, un aspetto non secondario per un'utenza che può includere persone anziane o con ridotta capacità visiva, come indicato nei requisiti di accessibilità del progetto.

Il verde, il giallo e il rosso, riservati rispettivamente allo stato di successo, avviso e allo stato di errore, seguono una convenzione semantica ormai consolidata nell'interfaccia utente occidentale. Per questo sono usati con parsimonia, solo dove serve un segnale immediato, per non sovraccaricare visivamente un'interfaccia pensata, prima di tutto, per rassicurare chi la usa.

#v(0.5cm)

#figure(
  table(
    columns: (1fr, 1.2fr, 2fr),
    align: center + horizon,
    stroke: 0.5pt + black,
    [*Colore*], [*Codice esadecimale*], [*Ruolo semantico nel sito*],
    table.cell(fill: rgb("0066cc"))[], [#box(fill: luma(240), radius: 3pt, inset: 2pt)[`#0066cc`]], [Color Primary: Bottoni, elementi attivi],
    table.cell(fill: rgb("004d99"))[], [#box(fill: luma(240), radius: 3pt, inset: 2pt)[`#004d99`]], [Color Primary darck: hover dei bottoni],
    table.cell(fill: rgb("e8f0fb"))[], [#box(fill: luma(240), radius: 3pt, inset: 2pt)[`#e8f0fb`]], [Sfondi focus ring, avvisi info],
    table.cell(fill: rgb("1b7a3e"))[], [#box(fill: luma(240), radius: 3pt, inset: 2pt)[`#1b7a3e`]], [Notifiche positive di conferme],
    table.cell(fill: rgb("fff8e1"))[], [#box(fill: luma(240), radius: 3pt, inset: 2pt)[`#fff8e1`]], [Box preparazione esami e avvisi],
    table.cell(fill: rgb("d32f2f"))[], [#box(fill: luma(240), radius: 3pt, inset: 2pt)[`#d32f2f`]], [Allerta form, azioni distruttive],
    table.cell(fill: rgb("f8fafc"))[], [#box(fill: luma(240), radius: 3pt, inset: 2pt)[`#f8fafc`]], [Sfondo applicativo delle pagine],
    table.cell(fill: rgb("1a1a1a"))[], [#box(fill: luma(240), radius: 3pt, inset: 2pt)[`#1a1a1a`]], [Testo principale]
  ),
  caption: [Palette cromatica reale estratta dai fogli di stile]
)

#v(1cm)

== Implementazioni per la navigazione da tastiera e tecnologie assistive
#v(0.5cm)
Per consentire l'utilizzo autonomo della piattaforma a utenti con disabilità motorie o visive, è stata implementata una serie di accorgimenti tecnici nel codice HTML5 e CSS3:

- *Skip Links:* In cima a ogni documento è inserito un collegamento di salto strutturale (#box(fill: luma(240), radius: 3pt, inset: 2pt)[`<a href="#main-content" class="skip-link">`]). Questo elemento rimane invisibile a schermo durante la navigazione standard con il mouse, ma si palesa graficamente nell'angolo superiore non appena rileva il focus da tastiera (tasto #box(fill: luma(240), radius: 3pt, inset: 2pt)[`TAB`]), permettendo di superare i blocchi ripetitivi del menu superiore e di atterrare direttamente sul nucleo informativo della pagina.
- *Gestione del Focus Ring:* È stata interdetta la rimozione indiscriminata degli indicatori di focus. Nel foglio di stile è stato ingegnerizzato un doppio anello di focus tramite la pseudo-classe #box(fill: luma(240), radius: 3pt, inset: 2pt)[`:focus-visible`], che applica un outline marcato blu (#box(fill: luma(240), radius: 3pt, inset: 2pt)[`#0066cc`]) distanziato dall'elemento tramite un ombreggiatura interna bianca, assicurando visibilità su qualunque sfondo senza penalizzare l'estetica per gli utenti che utilizzano il mouse.
- *Elementi interattivi accessibili:* All'interno del modulo di prenotazione (#box(fill: luma(240), radius: 3pt, inset: 2pt)[`prenotazioni.php`]), i controlli di selezione del giorno e dell'orario, originariamente mappati come radio button nativi (nascosti graficamente per ragioni di layout), sono stati resi pienamente operativi per la tastiera. Ai relativi tag label è stato applicato l'attributo #box(fill: luma(240), radius: 3pt, inset: 2pt)[`tabindex="0"`] per l'inclusione nel flusso di tabulazione, integrando gli attributi semantici #box(fill: luma(240), radius: 3pt, inset: 2pt)[`role="radio"`] e #box(fill: luma(240), radius: 3pt, inset: 2pt)[`aria-checked`]. Tramite JavaScript, è stata mappata la navigazione nativa con le frecce direzionali (#box(fill: luma(240), radius: 3pt, inset: 2pt)[`ArrowRight`], #box(fill: luma(240), radius: 3pt, inset: 2pt)[`ArrowLeft`]) per scorrere ciclicamente all'interno degli slot disponibili, replicando fedelmente il comportamento dei componenti d'interfaccia predefiniti dei sistemi operativi.

Non è stato ritenuto utile l'implementazione di #box(fill: luma(240), radius: 3pt, inset: 2pt)[`stamp`], in quanto riteniamo che il nostro sito sia già facilmente navigabile.

#pagebreak()

// --- 6. IMPLEMENTAZIONI LATO BACK-END ---
= Implementazioni lato back-end

== Architettura dati e interazione tramite PDO
#v(0.5cm)
Il motore di persistenza si appoggia su una base di dati relazionale MariaDB, strutturata in forma normale per eliminare la ridondanza informativa. Il modello si articola attorno alle due entità principali `persona` e `prenotazione`, legate da un vincolo di integrità referenziale uno-a-molti (#box(fill: luma(240), radius: 3pt, inset: 2pt)[`persona_id -> cf` con clausola ON DELETE CASCADE]).

#figure(
  image("erdiagram.png", width: 60%),
  caption: [Modello logico/relazionale del database]
)

#v(1cm)

L'intera logica di interazione con la base di dati è centralizzata nel modulo #box(fill: luma(240), radius: 3pt, inset: 2pt)[`db.php`], il quale istanzia un oggetto globale appartenente all'estensione #box(fill: luma(240), radius: 3pt, inset: 2pt)[`PDO` (PHP Data Objects)]. Per inibire sistematicamente attacchi di tipo SQL Injection nei form applicativi, tutte le query che accettano parametri inseriti dall'utente sfruttano i *prepared statement* con parametri posizionali o nominali. Un esempio significativo è riscontrabile nella logica di login, dove la query viene preparata con un segnaposto nominale o posizionale prima di legarvi in modo sicuro le variabili provenienti dagli input di testo.

#v(1cm)

== Gestione delle sessioni, inserimento e cancellazione sicura
#v(0.5cm)
Lo stato di autenticazione dell'utente è preservato attraverso l'uso delle sessioni native di PHP, inizializzate tramite la chiamata #box(fill: luma(240), radius: 3pt, inset: 2pt)[`session_start()`]. Per mitigare le vulnerabilità di Session Fixation, la procedura esegue un #box(fill: luma(240), radius: 3pt, inset: 2pt)[`session_regenerate_id(true)`] non appena l'utente supera la verifica delle credenziali nel file #box(fill: luma(240), radius: 3pt, inset: 2pt)[`login.php`].

Il sistema implementa logiche server-side distinte per le operazioni di manipolazione dei record:
- *Inserimento (Prenotazione slot):* All'interno del file #box(fill: luma(240), radius: 3pt, inset: 2pt)[`prenotazioni.php`], prima di eseguire la query di inserimento, viene verificato lato server che lo slot cronologico scelto dall'utente non sia già occupato. La validazione è controllata tramite una tabella di slot attivi e una verifica preventiva sull'orario scelto.
- *Cancellazione sicura (Paziente):* La revoca di un appuntamento da parte dell'utente è gestita dallo script #box(fill: luma(240), radius: 3pt, inset: 2pt)[`cancella_prenotazione.php`]. In questa versione del progetto l'azione viene attivata con un parametro `id` passato tramite `GET`, e la query aggiorna lo stato solo se la prenotazione appartiene alla sessione corrente e se è ancora nello stato `prenotato`.
- *Cancellazione e revoca (Amministratore):* Nel file #box(fill: luma(240), radius: 3pt, inset: 2pt)[`admin_dashboard.php`], l'amministratore può annullare un appuntamento attivo inviando una richiesta `POST` con l'ID della prenotazione. Il controllo di autorizzazione è basato sul codice fiscale in sessione, confrontato con la costante di amministrazione. In caso di accesso non autorizzato, il codice reindirizza verso `login.php`.

#v(1cm)

// --- 7. MOTIVAZIONE DELLE SCELTE IMPLEMENTATIVE E PARTI CRITICHE ---
= Motivazione delle scelte e parti critiche

== Sviluppo in PHP puro senza framework ausiliari
#v(0.5cm)
La scelta di non adottare framework moderni (quali Laravel o Symfony) è stata guidata dalla volontà di comprendere a fondo le dinamiche fondamentali del protocollo HTTP e della gestione dello stato sul web. L'implementazione di un sistema di templating rudimentale basato sulla funzione nativa #box(fill: luma(240), radius: 3pt, inset: 2pt)[`str_replace()`] per i menu e le inclusioni dei file di intestazione ha permesso di rispettare il vincolo di separazione tra struttura e logica senza introdurre la complessità di configurazione di un motore di template esterno o librerie esterne non necessarie per il dominio dell'applicazione.

== Utilizzo del codice fiscale come identificatore univoco logico
#v(0.5cm)
Nel contesto specifico di un portale sanitario, l'identificazione certa del cittadino è un prerequisito fondamentale. L'utilizzo del Codice Fiscale come chiave logica per l'autenticazione e la memorizzazione in sessione, anziché l'adozione di un classico nome utente di fantasia, risponde a criteri di utilità reale e accessibilità cognitiva. L'utente non è costretto a memorizzare un'ulteriore credenziale astratta, sfruttando un identificatore univoco statale già in suo possesso. Ai fini della tutela della riservatezza dei dati, tale stringa viene gestita esclusivamente in memoria lato server e mai veicolata in chiaro all'interno dei parametri URL o nelle richieste di tipo GET.

#v(1cm)

== Punti critici e limitazioni dell'architettura attuale
#v(0.5cm)
Il principale punto di attenzione emerso in fase di collaudo riguarda la gestione della concorrenza degli slot di prenotazione. Qualora due utenti inviino simultaneamente una richiesta per il medesimo orario, la validazione server esegue una lettura che potrebbe risultare non aggiornata prima dell'inserimento effettivo. Sebbene l'occorrenza sia mitigata dalla rapidità di esecuzione di MariaDB, un'evoluzione robusta del sistema richiederebbe l'introduzione di transazioni SQL con livello di isolamento stringente o l'applicazione di un vincolo di unicità sulla combinazione dei campi data, ora e stato direttamente sulla struttura della tabella. Questa possibile problematica è mitigata in parte dal ruolo dell'admin che può cancellare un eventuale prenotazione in caso di sovapposizione.

#v(1cm)

// --- 8. Test ---
= Test
#v(0.5cm)

Durante lo sviluppo di VitalPath, oltre al controllo visivo diretto nel browser, abbiamo affiancato una serie di verifiche automatiche e strumentate, mirate a intercettare errori di markup, criticità di accessibilità e problemi di prestazioni prima della consegna.

#v(1cm)

== Validazione del markup
#v(0.5cm)
Il primo livello di controllo riguarda la correttezza sintattica del codice prodotto. Abbiamo utilizzato Total Validator per verificare l'HTML5 generato dinamicamente da PHP, individuando gli errori in maniera più efficace ed efficente. Lo stesso lavoro è stato ripetuto con diversi validatori online per il CSS e per il markup XML degli elementi SVG inline, come il logo presente nell'header, poiché una sintassi scorretta in un file SVG può impedirne il rendering in alcuni browser senza restituire nessun errore visibile in console. Le pagine PHP sono state validate dopo il rendering lato server, in modo da controllare l'HTML effettivamente inviato al browser e non il template sorgente.

#v(1cm)

== Estensioni Chrome per l'analisi di accessibilità
#v(0.5cm)
Per approfondire il controllo sull'accessibilità abbiamo affiancato ai validatori sintattici quattro estensioni del browser. Dtools - Web Developer Tools ha permesso di ispezionare rapidamente la struttura del DOM e la gerarchia degli heading. WAVE segnala a colpo d'occhio, tramite icone sovrapposte direttamente sulla pagina, errori strutturali, alert e funzionalità mancanti per le tecnologie assistive. WCAG Contrast Checker ha reso possibile controllare il rapporto di contrasto sugli elementi effettivamente renderizzati, un riscontro pratico rispetto ai valori calcolati manualmente nella sezione precedente. Accessibility Insights for Web ha aggiunto una scansione automatica e una modalità di verifica guidata passo per passo, quest'ultima particolarmente utile per controllare l'ordine di tabulazione e la coerenza del focus visibile tra gli elementi interattivi. Silktide Accessibility Checker è stato lo strumento più utile tra i cinque, grazie all'ampiezza della copertura WCAG 2.2. A differenza degli altri strumenti, evidenzia i problemi direttamente sulla pagina analizzata e affianca a ogni segnalazione una spiegazione passo per passo su come correggerla, riducendo il tempo necessario per interpretare l'errore. Ha inoltre un simulatore di screen reader integrato, che ha permesso di verificare rapidamente come il contenuto della pagina viene annunciato. Abbiamo inoltre usato la possibilità di cambiare dispositivo per testare il nostro sito su diversi dispositivi con dimensioni dello schemo differenti.

#v(1cm)

== Audit con Lighthouse
#v(0.5cm)
Tramite il pannello sviluppatore di Chrome (F12) abbiamo eseguito più volte l'audit di Lighthouse, che restituisce un punteggio sintetico su quattro categorie, prestazioni, accessibilità, best practice e SEO. Lo strumento si è rivelato utile soprattutto nelle fasi finali dello sviluppo, quando le correzioni puntuali suggerite dai singoli controlli rischiavano di far perdere di vista il quadro generale della pagina.

#v(1cm)

// --- 9. NOTE CONCLUSIVE ---
= Note conclusive
#v(0.5cm)
Lo sviluppo della piattaforma VitalPath ha permesso al gruppo di affrontare l'intero ciclo di vita di un'applicazione web accessibile, traducendo le specifiche teoriche del corso in scelte ingegneristiche concrete. Il lavoro ha evidenziato come sia possibile ottenere un'interfaccia moderna, fluida e inclusiva senza ricorrere a librerie esterne pesanti, affidandosi esclusivamente alla pulizia del codice semantico HTML5 e alle potenzialità dei layout CSS Grid e Flexbox.
L'adozione di strumenti di virtualizzazione (Docker) si è rivelata determinante per il successo del lavoro di squadra, annullando i tempi morti legati al disallineamento degli ambienti di sviluppo locali. Il risultato finale è un prototipo solido, scalabile e pienamente rispondente ai criteri normativi di accessibilità visiva e motoria, che unisce la rigorosità della sfida tecnica a un obiettivo di forte utilità sociale.