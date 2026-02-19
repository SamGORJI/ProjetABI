<?php
/**
 * Configuration de la Base de Données et Constantes Globales
 * Projet ABI - Système de Gestion Commerciale
 */

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'gestion_abi');
define('DB_USER', 'root');
define('DB_PASS', '');  // Modifier selon votre configuration MySQL
define('DB_CHARSET', 'utf8mb4');

// Configuration de l'application
define('APP_NAME', 'ABI Gestion Commerciale');
define('APP_URL', 'http://localhost:8000');

// Configuration des sessions
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0);  // Mettre à 1 si HTTPS

// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Timezone
date_default_timezone_set('Europe/Paris');

// Affichage des erreurs (à désactiver en production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
