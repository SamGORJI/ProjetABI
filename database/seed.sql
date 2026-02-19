-- =====================================================
-- Données de Test pour la Base de Données ABI
-- =====================================================

USE gestion_abi;

-- =====================================================
-- Insertion des utilisateurs
-- =====================================================
INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES
('Dupont', 'Jean', 'admin@abi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin'),
('Martin', 'Sophie', 'sophie.martin@abi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Directeur'),
('Bernard', 'Pierre', 'pierre.bernard@abi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Commercial'),
('Dubois', 'Marie', 'marie.dubois@abi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'RH'),
('Leroy', 'Thomas', 'thomas.leroy@abi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Developpeur'),
('Moreau', 'Julie', 'julie.moreau@abi.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Developpeur');
-- Tous les mots de passe: admin123


-- =====================================================
-- Insertion des clients
-- =====================================================
INSERT INTO clients (nom, prenom, entreprise, email, telephone, adresse, ville, code_postal, pays) VALUES
('Leclerc', 'Antoine', 'TechCorp SARL', 'antoine.leclerc@techcorp.fr', '0612345678', '15 Rue de la Paix', 'Paris', '75001', 'France'),
('Petit', 'Camille', 'InnoSoft', 'camille.petit@innosoft.fr', '0623456789', '28 Avenue des Champs', 'Lyon', '69001', 'France'),
('Roux', 'Nicolas', 'Digital Solutions', 'nicolas.roux@digitalsol.fr', '0634567890', '42 Boulevard Voltaire', 'Marseille', '13001', 'France'),
('Fournier', 'Emma', 'WebAgency Pro', 'emma.fournier@webagency.fr', '0645678901', '7 Rue du Commerce', 'Toulouse', '31000', 'France'),
('Girard', 'Lucas', 'StartUp Innovante', 'lucas.girard@startup.fr', '0656789012', '33 Place de la République', 'Nantes', '44000', 'France');

-- =====================================================
-- Insertion des produits
-- =====================================================
INSERT INTO produits (nom, description, prix, categorie, stock) VALUES
('Site Web Vitrine', 'Création d\'un site web vitrine responsive avec 5 pages', 2500.00, 'Développement Web', 0),
('Site E-commerce', 'Plateforme e-commerce complète avec paiement en ligne', 8500.00, 'Développement Web', 0),
('Application Mobile', 'Application mobile iOS et Android native', 15000.00, 'Développement Mobile', 0),
('Logo Professionnel', 'Création de logo avec charte graphique complète', 800.00, 'Design', 5),
('Hébergement Web Annuel', 'Hébergement web professionnel avec SSL', 250.00, 'Services', 50),
('Maintenance Mensuelle', 'Maintenance et mise à jour mensuelle du site', 150.00, 'Services', 100),
('Formation WordPress', 'Formation complète à l\'utilisation de WordPress (2 jours)', 1200.00, 'Formation', 10),
('Référencement SEO', 'Optimisation SEO complète avec suivi mensuel', 1800.00, 'Marketing Digital', 20);

-- =====================================================
-- Insertion des commandes
-- =====================================================
INSERT INTO commandes (client_id, utilisateur_id, statut, montant_total, notes) VALUES
(1, 3, 'Livree', 3300.00, 'Site web vitrine + hébergement + logo'),
(2, 3, 'En cours', 8500.00, 'Plateforme e-commerce en développement'),
(3, 3, 'En attente', 15000.00, 'Application mobile - devis accepté'),
(4, 3, 'Livree', 2950.00, 'Site vitrine + hébergement'),
(5, 3, 'En cours', 1950.00, 'Logo + référencement SEO');

-- =====================================================
-- Insertion des détails de commandes
-- =====================================================
-- Commande 1 (Client 1)
INSERT INTO commande_details (commande_id, produit_id, quantite, prix_unitaire) VALUES
(1, 1, 1, 2500.00),  -- Site Web Vitrine
(1, 5, 1, 250.00),   -- Hébergement
(1, 4, 1, 800.00);   -- Logo

-- Commande 2 (Client 2)
INSERT INTO commande_details (commande_id, produit_id, quantite, prix_unitaire) VALUES
(2, 2, 1, 8500.00);  -- Site E-commerce

-- Commande 3 (Client 3)
INSERT INTO commande_details (commande_id, produit_id, quantite, prix_unitaire) VALUES
(3, 3, 1, 15000.00); -- Application Mobile

-- Commande 4 (Client 4)
INSERT INTO commande_details (commande_id, produit_id, quantite, prix_unitaire) VALUES
(4, 1, 1, 2500.00),  -- Site Web Vitrine
(4, 5, 1, 250.00),   -- Hébergement
(4, 6, 1, 150.00);   -- Maintenance

-- Commande 5 (Client 5)
INSERT INTO commande_details (commande_id, produit_id, quantite, prix_unitaire) VALUES
(5, 4, 1, 800.00),   -- Logo
(5, 8, 1, 1800.00);  -- Référencement SEO

-- =====================================================
-- Insertion des projets
-- =====================================================
INSERT INTO projets (nom, description, client_id, responsable_id, date_debut, date_fin, statut, budget) VALUES
('Refonte Site TechCorp', 'Refonte complète du site web de TechCorp avec nouveau design', 1, 2, '2026-01-15', '2026-03-15', 'Termine', 3500.00),
('E-commerce InnoSoft', 'Développement plateforme e-commerce pour InnoSoft', 2, 2, '2026-02-01', '2026-04-30', 'En cours', 9000.00),
('App Mobile Digital Solutions', 'Application mobile de gestion pour Digital Solutions', 3, 2, '2026-03-01', '2026-06-30', 'Planifie', 16000.00),
('Site Vitrine WebAgency', 'Création site vitrine pour WebAgency Pro', 4, 2, '2026-01-20', '2026-02-28', 'Termine', 3000.00);

-- =====================================================
-- Insertion des équipes de projet
-- =====================================================
-- Projet 1: Refonte Site TechCorp
INSERT INTO equipes (projet_id, utilisateur_id, role_projet) VALUES
(1, 5, 'Developpeur Lead'),
(1, 6, 'Developpeur Frontend');

-- Projet 2: E-commerce InnoSoft
INSERT INTO equipes (projet_id, utilisateur_id, role_projet) VALUES
(2, 5, 'Developpeur Backend'),
(2, 6, 'Developpeur Frontend');

-- Projet 3: App Mobile Digital Solutions
INSERT INTO equipes (projet_id, utilisateur_id, role_projet) VALUES
(3, 5, 'Developpeur Mobile');

-- Projet 4: Site Vitrine WebAgency
INSERT INTO equipes (projet_id, utilisateur_id, role_projet) VALUES
(4, 6, 'Developpeur Fullstack');

-- =====================================================
-- Insertion des activités (exemples)
-- =====================================================
INSERT INTO activites (utilisateur_id, action, table_concernee, details) VALUES
(1, 'Connexion', NULL, 'Connexion réussie au système'),
(3, 'Création', 'commandes', 'Nouvelle commande créée pour le client TechCorp'),
(2, 'Création', 'projets', 'Nouveau projet E-commerce InnoSoft créé'),
(5, 'Modification', 'projets', 'Mise à jour du statut du projet Refonte Site TechCorp'),
(3, 'Création', 'clients', 'Nouveau client StartUp Innovante ajouté');
