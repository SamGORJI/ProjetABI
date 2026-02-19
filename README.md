# 🏢 Système de Gestion Commerciale ABI

Système de gestion commerciale développé en PHP et MySQL pour Active Bretagne Informatique (ABI).

## 📋 Fonctionnalités

- ✅ **Gestion des Clients** : CRUD complet avec recherche
- ✅ **Gestion des Produits** : Catalogue avec catégories et stock
- ✅ **Gestion des Commandes** : Création et suivi des commandes
- ✅ **Gestion des Projets** : Suivi des projets et équipes
- ✅ **Tableau de Bord** : Statistiques et aperçu global
- ✅ **Authentification** : Système de connexion sécurisé
- ✅ **Multi-rôles** : Admin, Directeur, Commercial, RH, Développeur
- 🔄 **Contrôle d'Accès (RBAC)** : Permissions basées sur les rôles (en cours)

## 🛠️ Technologies Utilisées

- **Backend** : PHP 7.4+
- **Base de données** : MySQL 5.7+
- **Frontend** : HTML5, CSS3, JavaScript
- **Architecture** : MVC simplifié
- **Sécurité** : PDO Prepared Statements, Password Hashing

## 📦 Installation

### 1. Prérequis

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache/Nginx) ou PHP Built-in Server

### 2. Configuration de la Base de Données

#### Option A : Configuration Automatique (Recommandé)

```bash
# Exécuter le script de configuration automatique
php setup_database.php
```

Ce script va :
- Créer automatiquement la base de données `gestion_abi`
- Créer toutes les tables nécessaires
- Insérer les données de test
- Créer les comptes utilisateurs de test

#### Option B : Configuration Manuelle

```bash
# Se connecter à MySQL
mysql -u root -p

# Créer la base de données et importer le schéma
mysql -u root -p < database/schema.sql

# Importer les données de test
mysql -u root -p < database/seed.sql
```

### 3. Configuration de l'Application

Modifier le fichier `config/config.php` avec vos paramètres MySQL :

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'gestion_abi');
define('DB_USER', 'root');
define('DB_PASS', '');  // Votre mot de passe MySQL
```

### 4. Démarrage du Serveur

#### Option A : Serveur PHP Built-in (Développement)

```bash
cd ProjetABI
php -S localhost:8000
```

Puis ouvrir : http://localhost:8000

#### Option B : Apache/Nginx

Copier le projet dans le dossier `htdocs` ou `www` et accéder via :
http://localhost/ProjetABI

## 🔐 Comptes de Test

| Email | Mot de passe | Rôle | Permissions |
|-------|--------------|------|-------------|
| admin@abi.fr | admin123 | Admin | Accès complet à tout |
| sophie.martin@abi.fr | admin123 | Directeur | Voir tout + Gérer commandes/projets |
| pierre.bernard@abi.fr | admin123 | Commercial | Gérer clients/commandes |
| marie.dubois@abi.fr | admin123 | RH | Gérer utilisateurs uniquement |
| thomas.leroy@abi.fr | admin123 | Développeur | Voir ses projets uniquement |
| julie.moreau@abi.fr | admin123 | Développeur | Voir ses projets uniquement |
5. **Accéder à l'application**

Ouvrir votre navigateur: `http://localhost:8000`

## 👥 Comptes de Test

Tous les comptes utilisent le mot de passe: **admin123**

| Email | Rôle | Permissions |
|-------|------|-------------|
| admin@abi.fr | Admin | Accès complet à tout |
| sophie.martin@abi.fr | Directeur | Voir tout + Gérer commandes et projets |
| pierre.bernard@abi.fr | Commercial | Gérer clients et commandes |
| marie.dubois@abi.fr | RH | Gérer uniquement les utilisateurs |
| thomas.leroy@abi.fr | Développeur | Voir uniquement les projets |
| julie.moreau@abi.fr | Développeur | Voir uniquement les projets |

## 🔐 Système RBAC (Contrôle d'Accès)

### Matrice des Permissions

| Rôle | Clients | Produits | Commandes | Projets | Utilisateurs |
|------|---------|----------|-----------|---------|--------------|
| **Admin** | ✅ Tout | ✅ Tout | ✅ Tout | ✅ Tout | ✅ Tout |
| **Directeur** | 👁️ Voir | 👁️ Voir | ✅ Tout | ✅ Tout | ❌ Aucun |
| **Commercial** | ✅ Tout | 👁️ Voir | ✅ Tout | 👁️ Voir | ❌ Aucun |
| **RH** | ❌ Aucun | ❌ Aucun | ❌ Aucun | ❌ Aucun | ✅ Tout |
| **Développeur** | ❌ Aucun | ❌ Aucun | ❌ Aucun | 👁️ Voir | ❌ Aucun |

**Légende:**
- ✅ Tout = Voir, Créer, Modifier, Supprimer
- 👁️ Voir = Lecture seule
- ❌ Aucun = Pas d'accès

### Fonctionnement

1. **Protection des pages** - Chaque page vérifie les permissions requises
2. **Menu dynamique** - Seules les options autorisées sont affichées
3. **Contrôles UI** - Les boutons/formulaires s'adaptent aux permissions
4. **Page 403** - Redirection élégante en cas d'accès non autorisé

## 🏗️ Structure du Projet

```
ProjetABI/
├── index.php                    # Page de connexion (point d'entrée)
├── .htaccess                    # Configuration Apache
├── README.md                    # Documentation
│
├── public/                      # Fichiers publics accessibles
│   ├── dashboard.php           # Tableau de bord principal
│   ├── logout.php              # Déconnexion
│   ├── 403.php                 # Page d'accès refusé
│   └── pages/                  # Pages de gestion
│       ├── clients.php         # Gestion des clients
│       ├── produits.php        # Gestion des produits
│       ├── commandes.php       # Gestion des commandes
│       └── projets.php         # Gestion des projets
│
├── config/                      # Configuration
│   └── config.php              # Paramètres de l'application
│
├── classes/                     # Classes PHP (POO)
│   ├── Database.php            # Connexion base de données
│   ├── User.php                # Gestion des utilisateurs
│   ├── Client.php              # Gestion des clients
│   ├── Produit.php             # Gestion des produits
│   ├── Commande.php            # Gestion des commandes
│   ├── Projet.php              # Gestion des projets
│   └── Permission.php          # Système RBAC
│
├── includes/                    # Fichiers utilitaires
│   ├── auth.php                # Fonctions d'authentification
│   └── functions.php           # Fonctions générales
│
├── assets/                      # Ressources statiques
│   └── css/
│       └── style.css           # Styles CSS
│
├── database/                    # Scripts SQL
│   ├── schema.sql              # Structure de la base
│   └── seed.sql                # Données de test
│
├── scripts/                     # Scripts utilitaires
│   └── setup_database.php      # Installation automatique de la DB
│
└── tests/                       # Tests
    └── test_permissions.php    # Tests du système RBAC
```

## 🗄️ Base de Données

### Tables Principales

- **utilisateurs** - Comptes utilisateurs avec rôles
- **clients** - Informations clients
- **produits** - Catalogue de produits
- **commandes** - Commandes clients
- **commande_details** - Détails des commandes
- **projets** - Projets en cours
- **equipes** - Affectation des équipes aux projets
- **activites** - Journal des activités

### Relations

- Les commandes sont liées aux clients et utilisateurs
- Les projets sont liés aux clients et responsables
- Les équipes associent utilisateurs et projets
- Contraintes d'intégrité référentielle (CASCADE)

## 🧪 Tests

### Tests Automatiques

Exécuter les tests RBAC:
```bash
php tests/test_permissions.php
```

### Tests Manuels

1. Se connecter avec différents rôles
2. Vérifier les menus affichés
3. Tester l'accès aux pages
4. Vérifier les actions disponibles (créer, modifier, supprimer)
5. Tester l'accès direct aux URLs non autorisées

## 🛠️ Technologies Utilisées

- **Backend**: PHP 7.4+ (POO)
- **Base de données**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript vanilla
- **Architecture**: MVC simplifié
- **Sécurité**: Password hashing, RBAC, Protection CSRF

## � Documentation Technique

### Classes Principales

- `Database` - Gestion de la connexion PDO
- `User` - Authentification et gestion utilisateurs
- `Permission` - Système RBAC complet
- `Client`, `Produit`, `Commande`, `Projet` - Modèles métier

### Fonctions Utilitaires

- `requireAuth()` - Vérifier l'authentification
- `requirePermission($permission)` - Vérifier une permission
- `hasPermission($permission)` - Tester une permission
- `getPermission()` - Obtenir l'objet Permission actuel

## � Dépannage

### Problème de connexion à la base de données
```bash
# Vérifier que MySQL est démarré
# Vérifier les identifiants dans config/config.php
# Réexécuter le script de setup
php scripts/setup_database.php
```

### Page blanche
```bash
# Activer l'affichage des erreurs dans config/config.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
```

### Problème de permissions
```bash
# Vérifier le rôle de l'utilisateur dans la base
# Tester avec le compte admin@abi.fr
# Consulter tests/test_permissions.php
```

## � Développement Futur

- [ ] Page de gestion des utilisateurs (pages/utilisateurs.php)
- [ ] Filtrage des projets par développeur
- [ ] Statistiques avancées par rôle
- [ ] Export de données (PDF, Excel)
- [ ] API REST
### La connexion ne fonctionne pas

1. Vérifiez que la base de données est créée :
   ```bash
   php setup_database.php
   ```

2. Vérifiez les paramètres dans `config/config.php`

3. Vérifiez que le serveur PHP est démarré :
   ```bash
   php -S localhost:8000
   ```

### Erreur de connexion à la base de données

- Vérifiez que MySQL est démarré
- Vérifiez les identifiants dans `config/config.php`
- Exécutez `setup_database.php` pour recréer la base

## 📄 Licence

© 2026 Active Bretagne Informatique (ABI). Tous droits réservés.
