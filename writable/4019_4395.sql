PRAGMA foreign_keys = ON;

CREATE TABLE operateurs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nomOperateur TEXT NOT NULL
);

-- TABLE : prefixes_operateur
-- Préfixes autorisés par l'opérateur
CREATE TABLE prefixes_operateur (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    idOperateur INTEGER NOT NULL,
    prefixe TEXT NOT NULL UNIQUE,
    actif INTEGER NOT NULL DEFAULT 1,
    FOREIGN KEY (idOperateur)
        REFERENCES operateurs(id)
);

-- TABLE : comptes
-- Compte créé automatiquement avec le numéro
CREATE TABLE comptes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    numero TEXT NOT NULL UNIQUE,
    solde DECIMAL(12,2) NOT NULL DEFAULT 0,
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- TABLE : types_operations
-- depot / retrait / transfert
CREATE TABLE types_operations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    libelle TEXT NOT NULL UNIQUE,
    actif INTEGER NOT NULL DEFAULT 1
);

-- TABLE : baremes_frais
-- Frais par tranche de montant
CREATE TABLE baremes_frais (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type_operation_id INTEGER NOT NULL,
    montant_min DECIMAL(12,2) NOT NULL,
    montant_max DECIMAL(12,2) NOT NULL,
    frais DECIMAL(12,2) NOT NULL,

    FOREIGN KEY (type_operation_id)
        REFERENCES types_operations(id)
);

CREATE TABLE frais_sup (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    pourcentage DECIMAL(5,2) NOT NULL
);

-- TABLE : transactions
-- Historique des opérations
CREATE TABLE transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,

    type_operation_id INTEGER NOT NULL,

    compte_source_id INTEGER,
    compte_destination_id INTEGER,

    montant DECIMAL(12,2) NOT NULL,
    frais DECIMAL(12,2) NOT NULL DEFAULT 0,

    statut TEXT NOT NULL DEFAULT 'SUCCES',

    date_transaction DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (type_operation_id)
        REFERENCES types_operations(id),

    FOREIGN KEY (compte_source_id)
        REFERENCES comptes(id),

    FOREIGN KEY (compte_destination_id)
        REFERENCES comptes(id)
);

CREATE INDEX idx_transactions_source
ON transactions(compte_source_id);

CREATE INDEX idx_transactions_destination
ON transactions(compte_destination_id);

CREATE INDEX idx_transactions_date
ON transactions(date_transaction);


-- DONNÉES INITIALES

INSERT INTO operateurs(nomOperateur) VALUES
('Orange'),
('Yas'),
('Airtel');

-- Préfixes autorisés
INSERT INTO prefixes_operateur(idOperateur, prefixe) VALUES
(1, '032'),
(3, '033'),
(2, '034'),
(1, '037'),
(2, '038');

-- Types d'opérations
INSERT INTO types_operations(libelle) VALUES
('Dépôt'),
('Retrait'),
('Transfert');

-- BARÈMES DES FRAIS

-- DEPOT : gratuit
INSERT INTO baremes_frais(type_operation_id, montant_min, montant_max, frais)
VALUES
(1, 0, 999999999, 0);

-- RETRAIT
INSERT INTO baremes_frais(type_operation_id, montant_min, montant_max, frais)
VALUES
(2, 0, 10000, 200),
(2, 10001, 50000, 500),
(2, 50001, 100000, 1000),
(2, 100001, 999999999, 2000);

-- TRANSFERT
INSERT INTO baremes_frais(type_operation_id, montant_min, montant_max, frais)
VALUES
(3, 0, 10000, 100),
(3, 10001, 50000, 300),
(3, 50001, 100000, 700),
(3, 100001, 999999999, 1500);

INSERT INTO frais_sup(pourcentage) VALUES
(10);

-- COMPTES DE TEST
INSERT INTO comptes(numero, solde) VALUES
('0381329729', 0),
('0349384791', 0);


-- VUE : situation des comptes clients
CREATE VIEW vue_situation_comptes AS
SELECT
    id,
    numero,
    solde,
    date_creation
FROM comptes;

-- VUE : gains de l'opérateur
-- (retrait + transfert)
CREATE VIEW vue_gains_operateur AS
SELECT
    t.id,
    op.nomOperateur AS nom_operateur,
    toper.libelle AS type_operation,
    t.montant,
    t.frais,
    t.date_transaction
FROM transactions t
JOIN types_operations toper ON toper.id = t.type_operation_id
JOIN comptes c ON c.id = t.compte_source_id
JOIN prefixes_operateur pref ON pref.prefixe = SUBSTR(c.numero, 1, 3)
JOIN operateurs op ON op.id = pref.idOperateur
WHERE toper.libelle IN ('Retrait', 'Transfert');

-- VUE : total des gains par operateur
CREATE VIEW vue_total_gains_operateur AS
SELECT
    op.nomOperateur AS nom_operateur,
    COALESCE(SUM(t.frais), 0) AS total_frais
FROM operateurs op
LEFT JOIN prefixes_operateur pref ON pref.idOperateur = op.id
LEFT JOIN comptes c ON SUBSTR(c.numero, 1, 3) = pref.prefixe
LEFT JOIN transactions t ON t.compte_source_id = c.id
LEFT JOIN types_operations toper ON toper.id = t.type_operation_id AND toper.libelle IN ('Retrait', 'Transfert')
GROUP BY op.nomOperateur;

-- VUE : historique détaillé des transactions
CREATE VIEW vue_historique_transactions AS
SELECT
    t.id,
    toper.libelle AS operation,
    src.numero AS numero_source,
    dest.numero AS numero_destination,
    t.montant,
    t.frais,
    t.statut,
    t.date_transaction
FROM transactions t
JOIN types_operations toper
    ON toper.id = t.type_operation_id
LEFT JOIN comptes src
    ON src.id = t.compte_source_id
LEFT JOIN comptes dest
    ON dest.id = t.compte_destination_id;