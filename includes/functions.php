<?php
/**
 * Fonctions utilitaires
 */

/**
 * Nettoyer une chaîne de caractères
 * @param string $data
 * @return string
 */
function nettoyer($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Formater une date
 * @param string $date
 * @param string $format
 * @return string
 */
function formaterDate($date, $format = 'd/m/Y') {
    if (empty($date)) return '';
    return date($format, strtotime($date));
}

/**
 * Formater un montant en euros
 * @param float $montant
 * @return string
 */
function formaterMontant($montant) {
    return number_format($montant, 2, ',', ' ') . ' €';
}

/**
 * Générer un message flash
 * @param string $type (success, error, warning, info)
 * @param string $message
 */
function ajouterFlash($type, $message) {
    if (!isset($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

/**
 * Afficher et supprimer les messages flash
 * @return array
 */
function obtenirFlash() {
    if (isset($_SESSION['flash'])) {
        $messages = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $messages;
    }
    return [];
}

/**
 * Valider un email
 * @param string $email
 * @return bool
 */
function validerEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Générer un badge de statut
 * @param string $statut
 * @return string
 */
function badgeStatut($statut) {
    $classes = [
        'En attente' => 'badge-warning',
        'En cours' => 'badge-info',
        'Livree' => 'badge-success',
        'Annulee' => 'badge-danger',
        'Planifie' => 'badge-secondary',
        'Termine' => 'badge-success'
    ];
    
    $class = $classes[$statut] ?? 'badge-secondary';
    return "<span class='badge $class'>$statut</span>";
}
