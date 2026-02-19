<?php
/**
 * Script de Configuration Automatique de la Base de Données
 * Ce script crée la base de données et insère les données de test
 */

// Configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'gestion_abi';

echo "=== Configuration de la Base de Données ABI ===\n\n";

try {
    // Connexion sans sélectionner de base de données
    echo "1. Connexion au serveur MySQL...\n";
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "   ✓ Connexion réussie!\n\n";
    
    // Créer la base de données
    echo "2. Création de la base de données '$dbname'...\n";
    $pdo->exec("DROP DATABASE IF EXISTS $dbname");
    $pdo->exec("CREATE DATABASE $dbname CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "   ✓ Base de données créée!\n\n";
    
    // Sélectionner la base de données
    $pdo->exec("USE $dbname");
    
    // Créer les tables
    echo "3. Création des tables...\n";
    
    // Table utilisateurs
    $pdo->exec("
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
        ) ENGINE=InnoDB
    ");
    echo "   ✓ Table 'utilisateurs' créée\n";
    
    // Table clients
    $pdo->exec("
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
        ) ENGINE=InnoDB
    ");
    echo "   ✓ Table 'clients' créée\n";
    
    // Table produits
    $pdo->exec("
        CREATE TABLE produits (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nom VARCHAR(150) NOT NULL,
            description TEXT,
            prix DECIMAL(10, 2) NOT NULL,
            categorie VARCHAR(100),
            stock INT DEFAULT 0,
            date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_categorie (categorie)
        ) ENGINE=InnoDB
    ");
    echo "   ✓ Table 'produits' créée\n";
    
    // Table commandes
    $pdo->exec("
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
        ) ENGINE=InnoDB
    ");
    echo "   ✓ Table 'commandes' créée\n";
    
    // Table commande_details
    $pdo->exec("
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
        ) ENGINE=InnoDB
    ");
    echo "   ✓ Table 'commande_details' créée\n";
    
    // Table projets
    $pdo->exec("
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
        ) ENGINE=InnoDB
    ");
    echo "   ✓ Table 'projets' créée\n";
    
    // Table equipes
    $pdo->exec("
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
        ) ENGINE=InnoDB
    ");
    echo "   ✓ Table 'equipes' créée\n";
    
    // Table activites
    $pdo->exec("
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
        ) ENGINE=InnoDB
    ");
    echo "   ✓ Table 'activites' créée\n\n";
    
    // Insérer les données de test
    echo "4. Insertion des données de test...\n";
    
    // Hash du mot de passe 'admin123'
    $passwordHash = password_hash('admin123', PASSWORD_DEFAULT);
    
    // Utilisateurs
    $stmt = $pdo->prepare("
        INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES
        ('Dupont', 'Jean', 'admin@abi.fr', ?, 'Admin'),
        ('Martin', 'Sophie', 'sophie.martin@abi.fr', ?, 'Directeur'),
        ('Bernard', 'Pierre', 'pierre.bernard@abi.fr', ?, 'Commercial'),
        ('Dubois', 'Marie', 'marie.dubois@abi.fr', ?, 'RH'),
        ('Leroy', 'Thomas', 'thomas.leroy@abi.fr', ?, 'Developpeur'),
        ('Moreau', 'Julie', 'julie.moreau@abi.fr', ?, 'Developpeur')
    ");
    $stmt->execute([$passwordHash, $passwordHash, $passwordHash, $passwordHash, $passwordHash, $passwordHash]);
    echo "   ✓ Utilisateurs insérés (mot de passe: admin123)\n";
    
    // Clients
    $pdo->exec("
        INSERT INTO clients (nom, prenom, entreprise, email, telephone, adresse, ville, code_postal, pays) VALUES
        ('Leclerc', 'Antoine', 'TechCorp SARL', 'antoine.leclerc@techcorp.fr', '0612345678', '15 Rue de la Paix', 'Paris', '75001', 'France'),
        ('Petit', 'Camille', 'InnoSoft', 'camille.petit@innosoft.fr', '0623456789', '28 Avenue des Champs', 'Lyon', '69001', 'France'),
        ('Roux', 'Nicolas', 'Digital Solutions', 'nicolas.roux@digitalsol.fr', '0634567890', '42 Boulevard Voltaire', 'Marseille', '13001', 'France'),
        ('Fournier', 'Emma', 'WebAgency Pro', 'emma.fournier@webagency.fr', '0645678901', '7 Rue du Commerce', 'Toulouse', '31000', 'France'),
        ('Girard', 'Lucas', 'StartUp Innovante', 'lucas.girard@startup.fr', '0656789012', '33 Place de la République', 'Nantes', '44000', 'France')
    ");
    echo "   ✓ Clients insérés\n";
    
    // Produits
    $pdo->exec("
        INSERT INTO produits (nom, description, prix, categorie, stock) VALUES
        ('Site Web Vitrine', 'Création d\\'un site web vitrine responsive avec 5 pages', 2500.00, 'Développement Web', 0),
        ('Site E-commerce', 'Plateforme e-commerce complète avec paiement en ligne', 8500.00, 'Développement Web', 0),
        ('Application Mobile', 'Application mobile iOS et Android native', 15000.00, 'Développement Mobile', 0),
        ('Logo Professionnel', 'Création de logo avec charte graphique complète', 800.00, 'Design', 5),
        ('Hébergement Web Annuel', 'Hébergement web professionnel avec SSL', 250.00, 'Services', 50),
        ('Maintenance Mensuelle', 'Maintenance et mise à jour mensuelle du site', 150.00, 'Services', 100),
        ('Formation WordPress', 'Formation complète à l\\'utilisation de WordPress (2 jours)', 1200.00, 'Formation', 10),
        ('Référencement SEO', 'Optimisation SEO complète avec suivi mensuel', 1800.00, 'Marketing Digital', 20)
    ");
    echo "   ✓ Produits insérés\n";
    
    // Commandes
    $pdo->exec("
        INSERT INTO commandes (client_id, utilisateur_id, statut, montant_total, notes) VALUES
        (1, 3, 'Livree', 3300.00, 'Site web vitrine + hébergement + logo'),
        (2, 3, 'En cours', 8500.00, 'Plateforme e-commerce en développement'),
        (3, 3, 'En attente', 15000.00, 'Application mobile - devis accepté'),
        (4, 3, 'Livree', 2950.00, 'Site vitrine + hébergement'),
        (5, 3, 'En cours', 1950.00, 'Logo + référencement SEO')
    ");
    echo "   ✓ Commandes insérées\n";
    
    // Détails de commandes
    $pdo->exec("
        INSERT INTO commande_details (commande_id, produit_id, quantite, prix_unitaire) VALUES
        (1, 1, 1, 2500.00), (1, 5, 1, 250.00), (1, 4, 1, 800.00),
        (2, 2, 1, 8500.00),
        (3, 3, 1, 15000.00),
        (4, 1, 1, 2500.00), (4, 5, 1, 250.00), (4, 6, 1, 150.00),
        (5, 4, 1, 800.00), (5, 8, 1, 1800.00)
    ");
    echo "   ✓ Détails de commandes insérés\n";
    
    // Projets
    $pdo->exec("
        INSERT INTO projets (nom, description, client_id, responsable_id, date_debut, date_fin, statut, budget) VALUES
        ('Refonte Site TechCorp', 'Refonte complète du site web de TechCorp avec nouveau design', 1, 2, '2026-01-15', '2026-03-15', 'Termine', 3500.00),
        ('E-commerce InnoSoft', 'Développement plateforme e-commerce pour InnoSoft', 2, 2, '2026-02-01', '2026-04-30', 'En cours', 9000.00),
        ('App Mobile Digital Solutions', 'Application mobile de gestion pour Digital Solutions', 3, 2, '2026-03-01', '2026-06-30', 'Planifie', 16000.00),
        ('Site Vitrine WebAgency', 'Création site vitrine pour WebAgency Pro', 4, 2, '2026-01-20', '2026-02-28', 'Termine', 3000.00)
    ");
    echo "   ✓ Projets insérés\n";
    
    // Équipes
    $pdo->exec("
        INSERT INTO equipes (projet_id, utilisateur_id, role_projet) VALUES
        (1, 5, 'Developpeur Lead'), (1, 6, 'Developpeur Frontend'),
        (2, 5, 'Developpeur Backend'), (2, 6, 'Developpeur Frontend'),
        (3, 5, 'Developpeur Mobile'),
        (4, 6, 'Developpeur Fullstack')
    ");
    echo "   ✓ Équipes insérées\n";
    
    // Activités
    $pdo->exec("
        INSERT INTO activites (utilisateur_id, action, table_concernee, details) VALUES
        (1, 'Connexion', NULL, 'Connexion réussie au système'),
        (3, 'Création', 'commandes', 'Nouvelle commande créée pour le client TechCorp'),
        (2, 'Création', 'projets', 'Nouveau projet E-commerce InnoSoft créé'),
        (5, 'Modification', 'projets', 'Mise à jour du statut du projet Refonte Site TechCorp'),
        (3, 'Création', 'clients', 'Nouveau client StartUp Innovante ajouté')
    ");
    echo "   ✓ Activités insérées\n\n";
    
    echo "=== Configuration Terminée avec Succès! ===\n\n";
    echo "Comptes de test disponibles:\n";
    echo "┌─────────────────────────────┬──────────┬─────────────┐\n";
    echo "│ Email                       │ Password │ Rôle        │\n";
    echo "├─────────────────────────────┼──────────┼─────────────┤\n";
    echo "│ admin@abi.fr                │ admin123 │ Admin       │\n";
    echo "│ sophie.martin@abi.fr        │ admin123 │ Directeur   │\n";
    echo "│ pierre.bernard@abi.fr       │ admin123 │ Commercial  │\n";
    echo "│ marie.dubois@abi.fr         │ admin123 │ RH          │\n";
    echo "│ thomas.leroy@abi.fr         │ admin123 │ Développeur │\n";
    echo "│ julie.moreau@abi.fr         │ admin123 │ Développeur │\n";
    echo "└─────────────────────────────┴──────────┴─────────────┘\n\n";
    echo "Vous pouvez maintenant vous connecter sur: http://localhost:8000\n";
    
} catch (PDOException $e) {
    echo "\n❌ ERREUR: " . $e->getMessage() . "\n";
    exit(1);
}
