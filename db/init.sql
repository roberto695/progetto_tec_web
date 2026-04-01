CREATE DATABASE IF NOT EXISTS prelievi_db;
USE prelievi_db;

CREATE TABLE persona (
    cf VARCHAR(16) PRIMARY KEY,
    nome VARCHAR(20) NOT NULL,
    cognome VARCHAR(20) NOT NULL,
    telefono VARCHAR(20)
);

CREATE TABLE prenotazione (
    id INT AUTO_INCREMENT PRIMARY KEY,
    persona_id VARCHAR(16) NOT NULL,
    data_ora DATETIME NOT NULL UNIQUE,
    stato VARCHAR(20) DEFAULT 'prenotato',
    FOREIGN KEY (persona_id) REFERENCES persona(cf)
);

INSERT INTO persona (nome, cognome, cf, telefono) VALUES
('Mario', 'Rossi', 'RSSMRA80A01H501X', '333 1234567'),
('Anna', 'Bianchi', 'BNCANN85B45F205Y', '333 7654321'),
('Luca', 'Verdi', 'VRDLCU90C15F205Z', '333 9876543');

INSERT INTO prenotazione (persona_id, data_ora, stato) VALUES
('RSSMRA80A01H501X', '2024-01-20 09:00:00', 'prenotato'),
('BNCANN85B45F205Y', '2024-01-20 10:30:00', 'prenotato'),
('VRDLCU90C15F205Z', '2024-01-21 09:30:00', 'effettuato');