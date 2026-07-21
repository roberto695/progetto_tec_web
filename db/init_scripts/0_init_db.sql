--DROP DATABASE IF EXISTS prelievi_db;
--CREATE DATABASE IF NOT EXISTS prelievi_db
--     CHARACTER SET utf8mb4
--     COLLATE utf8mb4_unicode_ci;
USE prelievi_db;

-- ============================================================
-- TABELLA: persona
-- La chiave primaria è il Codice Fiscale (cf).
-- Il login avviene con cf + password (in chiaro).
-- ============================================================
CREATE TABLE IF NOT EXISTS persona (
    cf          VARCHAR(16)  NOT NULL,
    nome        VARCHAR(50)  NOT NULL,
    cognome     VARCHAR(50)  NOT NULL,
    telefono    CHAR(10)  DEFAULT NULL,
    email       VARCHAR(50) NOT NULL UNIQUE,
    password    VARCHAR(20) NOT NULL,
    creato_il   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (cf)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 
-- ============================================================
-- TABELLA: prenotazione
-- Un utente può avere al massimo una prenotazione attiva
-- (stato 'prenotato') alla volta.
-- ============================================================
CREATE TABLE IF NOT EXISTS prenotazione (
    id          INT          NOT NULL AUTO_INCREMENT,
    persona_id  VARCHAR(16)  NOT NULL,
    data_ora    DATETIME     NOT NULL,
    stato       ENUM('prenotato','effettuato','cancellato')
                             NOT NULL DEFAULT 'prenotato',
    creato_il   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (persona_id) REFERENCES persona(cf)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
 
-- ============================================================
-- DATI DI DEFAULT
-- Admin: cf=ADMINVTLPTH00A00  password=admin
-- User:  cf=RSSMRA80A01H501X  password=user
--        cf=BNCANN85B45F205Y  password=user
-- ============================================================
INSERT INTO persona (cf, nome, cognome, telefono, email, password) VALUES
('admin', 'admin',  'VitalPath', NULL,           'admin@vitalpath.it', 'admin'),
('user', 'User',  'User',     '3333333333',  'user@gmail.com',  'user'),
('RSSMRA80A01H501X', 'Mario',  'Rossi',     '3331234567',  'mario.rossi@gmail.com',  'user'),
('BNCANN85B45F205Y', 'Anna',   'Bianchi',   '3337654321',  'anna.bianchi@gmail.com',  'user');

-- Prenotazioni di esempio
INSERT INTO prenotazione (persona_id, data_ora, stato) VALUES
('RSSMRA80A01H501X', '2026-07-15 09:00:00', 'prenotato'),
('RSSMRA80A01H501X', '2026-06-10 08:30:00', 'effettuato'),
('BNCANN85B45F205Y', '2026-05-20 11:00:00', 'cancellato');