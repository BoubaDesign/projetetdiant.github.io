
-- Création de la base de données
CREATE DATABASE IF NOT EXISTS notes_etudiants;
USE notes_etudiants;

-- Table des formations
CREATE TABLE IF NOT EXISTS formations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(100)
);

-- Table des matières
CREATE TABLE IF NOT EXISTS matieres (
    code VARCHAR(20) PRIMARY KEY,
    libelle VARCHAR(100),
    formation_id INT,
    FOREIGN KEY (formation_id) REFERENCES formations(id)
);

-- Table des étudiants
CREATE TABLE IF NOT EXISTS etudiants (
    matricule VARCHAR(20) PRIMARY KEY,
    nom VARCHAR(50),
    prenom VARCHAR(50),
    adresse VARCHAR(100),
    telephone VARCHAR(20),
    mot_de_passe VARCHAR(255),
    formation_id INT,
    FOREIGN KEY (formation_id) REFERENCES formations(id)
);

-- Table des administrateurs
CREATE TABLE IF NOT EXISTS administrateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE,
    mot_de_passe VARCHAR(255)
);

-- Table des notes
CREATE TABLE IF NOT EXISTS notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    matricule_etudiant VARCHAR(20),
    code_matiere VARCHAR(20),
    note VARCHAR(10),
    commentaire TEXT,
    FOREIGN KEY (matricule_etudiant) REFERENCES etudiants(matricule),
    FOREIGN KEY (code_matiere) REFERENCES matieres(code)
);
