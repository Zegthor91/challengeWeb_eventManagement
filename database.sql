CREATE DATABASE gestion_evenements;


\c gestion_evenements;

CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL CHECK (role IN ('administrateur', 'chef_projet', 'collaborateur')),
    telephone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE events (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(200) NOT NULL,
    type_event VARCHAR(100),
    date_debut DATE NOT NULL,
    date_fin DATE,
    lieu VARCHAR(255),
    description TEXT,
    responsable_id INTEGER REFERENCES users(id) ON DELETE SET NULL,
    statut VARCHAR(50) DEFAULT 'en_preparation' CHECK (statut IN ('en_preparation', 'en_cours', 'termine')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE budgets (
    id SERIAL PRIMARY KEY,
    event_id INTEGER REFERENCES events(id) ON DELETE CASCADE,
    budget_total DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE budget_categories (
    id SERIAL PRIMARY KEY,
    budget_id INTEGER REFERENCES budgets(id) ON DELETE CASCADE,
    categorie VARCHAR(100) NOT NULL,
    montant_prevu DECIMAL(10, 2) NOT NULL,
    montant_reel DECIMAL(10, 2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE personnel (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150),
    telephone VARCHAR(20),
    poste VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE event_personnel (
    id SERIAL PRIMARY KEY,
    event_id INTEGER REFERENCES events(id) ON DELETE CASCADE,
    personnel_id INTEGER REFERENCES personnel(id) ON DELETE CASCADE,
    role_event VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE prestataires (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(200) NOT NULL,
    type_service VARCHAR(100),
    email VARCHAR(150),
    telephone VARCHAR(20),
    adresse TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE event_prestataires (
    id SERIAL PRIMARY KEY,
    event_id INTEGER REFERENCES events(id) ON DELETE CASCADE,
    prestataire_id INTEGER REFERENCES prestataires(id) ON DELETE CASCADE,
    cout DECIMAL(10, 2),
    evaluation INTEGER CHECK (evaluation >= 1 AND evaluation <= 5),
    commentaire TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE tasks (
    id SERIAL PRIMARY KEY,
    event_id INTEGER REFERENCES events(id) ON DELETE CASCADE,
    titre VARCHAR(200) NOT NULL,
    description TEXT,
    assigne_a INTEGER REFERENCES users(id) ON DELETE SET NULL,
    priorite VARCHAR(50) DEFAULT 'moyenne' CHECK (priorite IN ('basse', 'moyenne', 'haute')),
    statut VARCHAR(50) DEFAULT 'a_faire' CHECK (statut IN ('a_faire', 'en_cours', 'termine')),
    date_limite DATE,
    parent_task_id INTEGER REFERENCES tasks(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


INSERT INTO users (nom, email, password, role, telephone) VALUES
('Admin', 'admin@test.com', '$2y$10$abcdefghijklmnopqrstuv', 'administrateur', '0601020304'),
('Sabri', 'jean@test.com', '$2y$10$abcdefghijklmnopqrstuv', 'chef_projet', '0605060708'),
('Tazio', 'marie@test.com', '$2y$10$abcdefghijklmnopqrstuv', 'collaborateur', '0609101112');
('Idir', 'marie@test.com', '$2y$10$abcdefghijklmnopqrstuv', 'collaborateur', '0609101112');

INSERT INTO events (nom, type_event, date_debut, date_fin, lieu, description, responsable_id, statut) VALUES
('Seminaire annuel 2026', 'Séminaire', '2026-03-15', '2026-03-17', 'Paris', 'Séminaire annuel de l entreprise', 1, 'en_preparation'),
('Lancement produit X', 'Lancement', '2026-04-20', '2026-04-20', 'Lyon', 'Présentation du nouveau produit', 2, 'en_preparation');

INSERT INTO personnel (nom, prenom, email, telephone, poste) VALUES
('Alberto', 'Alfred', 'sophie@test.com', '0612131415', 'Chef de projet'),
('Leclerc', 'Paul', 'paul@test.com', '0616171819', 'Technicien');

INSERT INTO prestataires (nom, type_service, email, telephone) VALUES
('Traiteur', 'Restauration', 'contact@traiteur.com', '0620212223'),
('Son & Lumiere Pro', 'Audiovisuel', 'info@sonlumiere.com', '0624252627');