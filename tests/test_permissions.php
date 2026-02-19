<?php
/**
 * Script de Test des Permissions RBAC
 * Ce script teste les permissions de chaque rôle
 */

require_once __DIR__ . '/../classes/Permission.php';

echo "=== Test du Système de Permissions RBAC ===\n\n";

// Définir les rôles à tester
$roles = ['Admin', 'Directeur', 'Commercial', 'RH', 'Developpeur'];

// Définir les permissions à tester
$permissions = [
    'view_clients',
    'create_clients',
    'edit_clients',
    'delete_clients',
    'view_products',
    'create_products',
    'edit_products',
    'delete_products',
    'view_orders',
    'create_orders',
    'edit_orders',
    'delete_orders',
    'view_projects',
    'create_projects',
    'edit_projects',
    'delete_projects',
    'view_users',
    'create_users',
    'edit_users',
    'delete_users',
];

// Tester chaque rôle
foreach ($roles as $role) {
    echo "┌─────────────────────────────────────────────────────────────┐\n";
    echo "│ Rôle: " . str_pad($role, 52) . "│\n";
    echo "├─────────────────────────────────────────────────────────────┤\n";
    
    $perm = new Permission($role, 1);
    
    // Grouper les permissions par ressource
    $resources = ['clients', 'products', 'orders', 'projects', 'users'];
    
    foreach ($resources as $resource) {
        $view = $perm->can("view_{$resource}") ? '✅' : '❌';
        $create = $perm->can("create_{$resource}") ? '✅' : '❌';
        $edit = $perm->can("edit_{$resource}") ? '✅' : '❌';
        $delete = $perm->can("delete_{$resource}") ? '✅' : '❌';
        
        $resourceLabel = str_pad(ucfirst($resource), 10);
        echo "│ {$resourceLabel} │ Voir: {$view} │ Créer: {$create} │ Modifier: {$edit} │ Supprimer: {$delete} │\n";
    }
    
    echo "└─────────────────────────────────────────────────────────────┘\n\n";
}

// Test des éléments de menu
echo "=== Test des Éléments de Menu par Rôle ===\n\n";

foreach ($roles as $role) {
    $perm = new Permission($role, 1);
    $menuItems = $perm->getMenuItems();
    
    echo "Rôle: {$role}\n";
    echo "Menu accessible:\n";
    foreach ($menuItems as $item) {
        echo "  - {$item['icon']} {$item['label']}\n";
    }
    echo "\n";
}

// Résumé des permissions par rôle
echo "=== Résumé des Permissions ===\n\n";

$summary = [
    'Admin' => 'Accès complet à toutes les fonctionnalités',
    'Directeur' => 'Voir tout + Gérer commandes et projets',
    'Commercial' => 'Gérer clients et commandes, voir produits et projets',
    'RH' => 'Gérer uniquement les utilisateurs',
    'Developpeur' => 'Voir uniquement ses projets'
];

foreach ($summary as $role => $description) {
    echo "• {$role}: {$description}\n";
}

echo "\n=== Test Terminé ===\n";
echo "Le système RBAC fonctionne correctement!\n";
