<?php
/**
 * Classe Permission - Gestion des permissions basées sur les rôles (RBAC)
 * Système de contrôle d'accès pour limiter les actions selon le rôle de l'utilisateur
 */

class Permission {
    private $role;
    private $userId;
    
    // Définition des permissions par rôle
    private static $rolePermissions = [
        'Admin' => [
            'view_dashboard' => true,
            'view_clients' => true,
            'create_clients' => true,
            'edit_clients' => true,
            'delete_clients' => true,
            'view_products' => true,
            'create_products' => true,
            'edit_products' => true,
            'delete_products' => true,
            'view_orders' => true,
            'create_orders' => true,
            'edit_orders' => true,
            'delete_orders' => true,
            'view_projects' => true,
            'create_projects' => true,
            'edit_projects' => true,
            'delete_projects' => true,
            'view_users' => true,
            'create_users' => true,
            'edit_users' => true,
            'delete_users' => true,
        ],
        'Directeur' => [
            'view_dashboard' => true,
            'view_clients' => true,
            'create_clients' => false,
            'edit_clients' => false,
            'delete_clients' => false,
            'view_products' => true,
            'create_products' => false,
            'edit_products' => false,
            'delete_products' => false,
            'view_orders' => true,
            'create_orders' => true,
            'edit_orders' => true,
            'delete_orders' => true,
            'view_projects' => true,
            'create_projects' => true,
            'edit_projects' => true,
            'delete_projects' => true,
            'view_users' => false,
            'create_users' => false,
            'edit_users' => false,
            'delete_users' => false,
        ],
        'Commercial' => [
            'view_dashboard' => true,
            'view_clients' => true,
            'create_clients' => true,
            'edit_clients' => true,
            'delete_clients' => true,
            'view_products' => true,
            'create_products' => false,
            'edit_products' => false,
            'delete_products' => false,
            'view_orders' => true,
            'create_orders' => true,
            'edit_orders' => true,
            'delete_orders' => true,
            'view_projects' => true,
            'create_projects' => false,
            'edit_projects' => false,
            'delete_projects' => false,
            'view_users' => false,
            'create_users' => false,
            'edit_users' => false,
            'delete_users' => false,
        ],
        'RH' => [
            'view_dashboard' => true,
            'view_clients' => false,
            'create_clients' => false,
            'edit_clients' => false,
            'delete_clients' => false,
            'view_products' => false,
            'create_products' => false,
            'edit_products' => false,
            'delete_products' => false,
            'view_orders' => false,
            'create_orders' => false,
            'edit_orders' => false,
            'delete_orders' => false,
            'view_projects' => false,
            'create_projects' => false,
            'edit_projects' => false,
            'delete_projects' => false,
            'view_users' => true,
            'create_users' => true,
            'edit_users' => true,
            'delete_users' => true,
        ],
        'Developpeur' => [
            'view_dashboard' => true,
            'view_clients' => false,
            'create_clients' => false,
            'edit_clients' => false,
            'delete_clients' => false,
            'view_products' => false,
            'create_products' => false,
            'edit_products' => false,
            'delete_products' => false,
            'view_orders' => false,
            'create_orders' => false,
            'edit_orders' => false,
            'delete_orders' => false,
            'view_projects' => true,
            'create_projects' => false,
            'edit_projects' => false,
            'delete_projects' => false,
            'view_users' => false,
            'create_users' => false,
            'edit_users' => false,
            'delete_users' => false,
        ],
    ];
    
    /**
     * Constructeur
     * @param string $role Rôle de l'utilisateur
     * @param int $userId ID de l'utilisateur
     */
    public function __construct($role, $userId = null) {
        $this->role = $role;
        $this->userId = $userId;
    }
    
    /**
     * Vérifier si l'utilisateur a une permission spécifique
     * @param string $permission Nom de la permission
     * @return bool
     */
    public function can($permission) {
        if (!isset(self::$rolePermissions[$this->role])) {
            return false;
        }
        
        return self::$rolePermissions[$this->role][$permission] ?? false;
    }
    
    /**
     * Vérifier si l'utilisateur peut voir une ressource
     * @param string $resource Nom de la ressource (clients, products, orders, projects, users)
     * @return bool
     */
    public function canView($resource) {
        return $this->can("view_{$resource}");
    }
    
    /**
     * Vérifier si l'utilisateur peut créer une ressource
     * @param string $resource Nom de la ressource
     * @return bool
     */
    public function canCreate($resource) {
        return $this->can("create_{$resource}");
    }
    
    /**
     * Vérifier si l'utilisateur peut modifier une ressource
     * @param string $resource Nom de la ressource
     * @return bool
     */
    public function canEdit($resource) {
        return $this->can("edit_{$resource}");
    }
    
    /**
     * Vérifier si l'utilisateur peut supprimer une ressource
     * @param string $resource Nom de la ressource
     * @return bool
     */
    public function canDelete($resource) {
        return $this->can("delete_{$resource}");
    }
    
    /**
     * Obtenir les éléments de menu basés sur les permissions
     * @return array
     */
    public function getMenuItems() {
        $menu = [];
        
        // Déterminer le préfixe de chemin basé sur l'emplacement du script
        $scriptPath = $_SERVER['SCRIPT_NAME'] ?? '';
        $pathPrefix = '';
        
        if (strpos($scriptPath, '/public/pages/') !== false) {
            // On est dans public/pages/, donc on remonte à public/
            $pathPrefix = '../';
        } elseif (strpos($scriptPath, '/public/') !== false) {
            // On est dans public/, les chemins sont relatifs
            $pathPrefix = '';
        } else {
            // On est à la racine, on descend dans public/
            $pathPrefix = 'public/';
        }
        
        // Dashboard - toujours visible
        $menu[] = [
            'url' => $pathPrefix . 'dashboard.php',
            'icon' => '📊',
            'label' => 'Tableau de Bord',
            'active' => true
        ];
        
        // Clients
        if ($this->canView('clients')) {
            $menu[] = [
                'url' => $pathPrefix . 'pages/clients.php',
                'icon' => '👥',
                'label' => 'Clients',
                'active' => true
            ];
        }
        
        // Produits
        if ($this->canView('products')) {
            $menu[] = [
                'url' => $pathPrefix . 'pages/produits.php',
                'icon' => '📦',
                'label' => 'Produits',
                'active' => true
            ];
        }
        
        // Commandes
        if ($this->canView('orders')) {
            $menu[] = [
                'url' => $pathPrefix . 'pages/commandes.php',
                'icon' => '🛒',
                'label' => 'Commandes',
                'active' => true
            ];
        }
        
        // Projets
        if ($this->canView('projects')) {
            $menu[] = [
                'url' => $pathPrefix . 'pages/projets.php',
                'icon' => '💼',
                'label' => 'Projets',
                'active' => true
            ];
        }
        
        // Utilisateurs
        if ($this->canView('users')) {
            $menu[] = [
                'url' => $pathPrefix . 'pages/utilisateurs.php',
                'icon' => '👤',
                'label' => 'Utilisateurs',
                'active' => true  // Page créée et active
            ];
        }
        
        return $menu;
    }
    
    /**
     * Obtenir une instance de Permission pour l'utilisateur connecté
     * @return Permission|null
     */
    public static function current() {
        if (!isset($_SESSION['utilisateur_role']) || !isset($_SESSION['utilisateur_id'])) {
            return null;
        }
        
        return new self($_SESSION['utilisateur_role'], $_SESSION['utilisateur_id']);
    }
}
