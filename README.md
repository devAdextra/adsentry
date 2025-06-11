# AdsEntry - Gestione Lead, Movimenti e Scoring

AdsEntry è una piattaforma web sviluppata in Laravel per la gestione avanzata di file CSV, lead, movimenti e analisi di scoring. Il sistema permette di caricare, processare, analizzare e scaricare dati in modo semplice e intuitivo tramite un'interfaccia moderna.

## Funzionalità principali

- **Upload e gestione file CSV**: Carica file CSV tramite interfaccia o inserimento manuale nella cartella `uploads`. I file possono essere processati per generare movimenti e lead.
- **Processamento asincrono**: I file vengono processati tramite job in background, con barra di avanzamento e stato visibile in tempo reale.
- **Dashboard**: Statistiche aggregate su lead, movimenti e attività recenti.
- **Scoring e analisi**: Visualizza la distribuzione degli score, filtra per variabili (macro, micro, nano, extra), analizza la distribuzione per database e per periodo.
- **Download**: Esporta lead filtrati in CSV, con selezione del database e filtri avanzati.
- **Gestione utenti**: Supporto multiutente con autenticazione.

## Come usare il progetto

### 1. Requisiti
- PHP >= 8.0
- Composer
- Database MySQL/MariaDB
- Node.js/NPM (per asset frontend, opzionale)

### 2. Installazione

1. Clona il repository:
   ```bash
   git clone <repo-url>
   cd adsentry-main
   ```
2. Installa le dipendenze PHP:
   ```bash
   composer install
   ```
3. Copia il file di esempio delle variabili ambiente:
   ```bash
   cp .env.example .env
   ```
4. Configura le variabili nel file `.env` (DB, mail, ecc.)
5. Genera la chiave applicativa:
   ```bash
   php artisan key:generate
   ```
6. Esegui le migration per creare le tabelle:
   ```bash
   php artisan migrate
   ```
7. (Opzionale) Compila gli asset frontend:
   ```bash
   npm install && npm run dev
   ```
8. Avvia il server di sviluppo:
   ```bash
   php artisan serve
   ```

### 3. Utilizzo
- Accedi all'applicazione da browser (`http://localhost:8000` o URL configurato).
- Carica file CSV dalla sezione "Gestione File CSV".
- Processa i file per generare movimenti e lead.
- Analizza i dati tramite la dashboard e la sezione scoring.
- Scarica i lead filtrati dalla sezione download.

### 4. Note tecniche
- I file caricati vengono salvati in `storage/app/uploads`.
- Il processamento aggiorna lo stato e la barra di avanzamento in tempo reale.
- La distribuzione scoring e per database è visualizzata con grafici interattivi.
- Il sistema supporta job in background (configura la coda per produzione).

## Struttura principale del progetto
- `app/Models/` — Modelli Eloquent (Lead, Movement, Upload, Download, User)
- `app/Http/Controllers/` — Controller web e API
- `resources/views/` — Blade templates per upload, scoring, dashboard, download
- `database/migrations/` — Migration per tabelle principali (users, uploads, movements, downloads)

## Licenza
Questo progetto è distribuito sotto licenza MIT.
