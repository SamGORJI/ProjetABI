-- =====================================================
-- Base de Données pour la Gestion Commerciale ABI
-- =====================================================

-- Créer la base de données
DROP DATABASE IF EXISTS gestion_abi;
CREATE DATABASE gestion_abi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gestion_abi;

-- =====================================================
-- Table: utilisateurs
-- =====================================================
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('Directeur', 'RH', 'Commercial', 'Developpeur', 'Secretaire', 'Admin') DEFAULT 'Commercial',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB;

-- =====================================================
-- Table: clients
-- =====================================================
CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    entreprise VARCHAR(150),
    email VARCHAR(150) UNIQUE NOT NULL,
    telephone VARCHAR(20),
    adresse VARCHAR(255),
    ville VARCHAR(100),
    code_postal VARCHAR(10),
    pays VARCHAR(100) DEFAULT 'France',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_entreprise (entreprise)
) ENGINE=InnoDB;

-- =====================================================
-- Table: produits
-- =====================================================
CREATE TABLE produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    description TEXT,
    prix DECIMAL(10, 2) NOT NULL,
    categorie VARCHAR(100),
    stock INT DEFAULT 0,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_categorie (categorie)
) ENGINE=InnoDB;

-- =====================================================
-- Table: commandes
-- =====================================================
CREATE TABLE commandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('En attente', 'En cours', 'Livree', 'Annulee') DEFAULT 'En attente',
    montant_total DECIMAL(10, 2) DEFAULT 0.00,
    notes TEXT,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_client (client_id),
    INDEX idx_utilisateur (utilisateur_id),
    INDEX idx_statut (statut)
) ENGINE=InnoDB;

-- =====================================================
-- Table: commande_details
-- =====================================================
CREATE TABLE commande_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    produit_id INT NOT NULL,
    quantite INT NOT NULL DEFAULT 1,
    prix_unitaire DECIMAL(10, 2) NOT NULL,
    sous_total DECIMAL(10, 2) GENERATED ALWAYS AS (quantite * prix_unitaire) STORED,
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE,
    INDEX idx_commande (commande_id),
    INDEX idx_produit (produit_id)
) ENGINE=InnoDB;

-- =====================================================
-- Table: projets
-- =====================================================
CREATE TABLE projets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(200) NOT NULL,
    description TEXT,
    client_id INT,
    responsable_id INT NOT NULL,
    date_debut DATE,
    date_fin DATE,
    statut ENUM('Planifie', 'En cours', 'Termine', 'Annule') DEFAULT 'Planifie',
    budget DECIMAL(12, 2),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE SET NULL,
    FOREIGN KEY (responsable_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    INDEX idx_client (client_id),
    INDEX idx_responsable (responsable_id),
    INDEX idx_statut (statut)
) ENGINE=InnoDB;

-- =====================================================
-- Table: equipes
-- =====================================================
CREATE TABLE equipes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    projet_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    role_projet VARCHAR(100) DEFAULT 'Developpeur',
    date_affectation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (projet_id) REFERENCES projets(id) ON DELETE CASCADE,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    UNIQUE KEY unique_affectation (projet_id, utilisateur_id),
    INDEX idx_projet (projet_id),
    INDEX idx_utilisateur (utilisateur_id)
) ENGINE=InnoDB;

-- =====================================================
-- Table: activites (Audit Trail)
-- =====================================================
CREATE TABLE activites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT,
    action VARCHAR(100) NOT NULL,
    table_concernee VARCHAR(50),
    date_action TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    details TEXT,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL,
    INDEX idx_utilisateur (utilisateur_id),
    INDEX idx_date (date_action)
) ENGINE=InnoDB;
