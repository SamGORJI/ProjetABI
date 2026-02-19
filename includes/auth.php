<?php
/**
 * Fonctions d'authentification et de gestion des sessions
 */

/**
 * Vérifier si l'utilisateur est connecté
 * @return bool
 */
function estConnecte() {
    return isset($_SESSION['utilisateur_id']);
}

/**
 * Obtenir l'utilisateur connecté
 * @return array|null
 */
function utilisateurConnecte() {
    if (estConnecte()) {
        return [
            'id' => $_SESSION['utilisateur_id'],
            'nom' => $_SESSION['utilisateur_nom'] ?? '',
            'prenom' => $_SESSION['utilisateur_prenom'] ?? '',
            'email' => $_SESSION['utilisateur_email'] ?? '',
            'role' => $_SESSION['utilisateur_role'] ?? ''
        ];
    }
    return null;
}

/**
 * Connecter un utilisateur
 * @param array $user
 */
function connecterUtilisateur($user) {
    $_SESSION['utilisateur_id'] = $user['id'];
    $_SESSION['utilisateur_nom'] = $user['nom'];
    $_SESSION['utilisateur_prenom'] = $user['prenom'];
    $_SESSION['utilisateur_email'] = $user['email'];
    $_SESSION['utilisateur_role'] = $user['role'];
}

/**
 * Déconnecter l'utilisateur
 */
function deconnecterUtilisateur() {
    session_unset();
    session_destroy();
}

/**
 * Rediriger si non connecté
 * @param string $url URL de redirection (null = auto-détection)
 */
function requireAuth($url = null) {
    if (!estConnecte()) {
        // Auto-détection du chemin vers index.php
        if ($url === null) {
            // Si on est dans public/ ou public/pages/, on remonte
            $scriptPath = $_SERVER['SCRIPT_NAME'];
            if (strpos($scriptPath, '/public/pages/') !== false) {
                $url = '../../index.php';
            } elseif (strpos($scriptPath, '/public/') !== false) {
                $url = '../index.php';
            } else {
                $url = 'index.php';
            }
        }
        header("Location: $url");
        exit;
    }
}

/**
 * Vérifier le rôle de l'utilisateur
 * @param array $rolesAutorises
 * @return bool
 */
function verifierRole($rolesAutorises) {
    if (!estConnecte()) {
        return false;
    }
    return in_array($_SESSION['utilisateur_role'], $rolesAutorises);
}

/**
 * Exiger une permission spécifique
 * @param string $permission Nom de la permission
 * @param string $redirectUrl URL de redirection en cas d'échec (null = auto-détection)
 */
function requirePermission($permission, $redirectUrl = null) {
    require_once __DIR__ . '/../classes/Permission.php';
    
    if (!estConnecte()) {
        requireAuth();
        return;
    }
    
    $perm = Permission::current();
    if (!$perm || !$perm->can($permission)) {
        // Auto-détection du chemin vers 403.php
        if ($redirectUrl === null) {
            $scriptPath = $_SERVER['SCRIPT_NAME'];
            if (strpos($scriptPath, '/public/pages/') !== false) {
                $redirectUrl = '../403.php';
            } elseif (strpos($scriptPath, '/public/') !== false) {
                $redirectUrl = '403.php';
            } else {
                $redirectUrl = 'public/403.php';
            }
        }
        header("Location: $redirectUrl");
        exit;
    }
}

/**
 * Vérifier si l'utilisateur a une permission
 * @param string $permission Nom de la permission
 * @return bool
 */
function hasPermission($permission) {
    require_once __DIR__ . '/../classes/Permission.php';
    
    if (!estConnecte()) {
        return false;
    }
    
    $perm = Permission::current();
    return $perm && $perm->can($permission);
}

/**
 * Obtenir l'objet Permission pour l'utilisateur connecté
 * @return Permission|null
 */
function getPermission() {
    require_once __DIR__ . '/../classes/Permission.php';
    return Permission::current();
}
