<?php
require_once '../config/config.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/Client.php';
require_once '../classes/Produit.php';
require_once '../classes/Commande.php';
require_once '../classes/Projet.php';
require_once '../classes/Permission.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';

// Vérifier l'authentification
requireAuth();

$user = utilisateurConnecte();
$permission = getPermission();
$menuItems = $permission->getMenuItems();

// Récupérer les statistiques
$clientModel = new Client();
$produitModel = new Produit();
$commandeModel = new Commande();
$projetModel = new Projet();

$totalClients = count($clientModel->getAll());
$totalProduits = count($produitModel->getAll());
$statsCommandes = $commandeModel->getStatistiques();
$totalProjets = count($projetModel->getAll());

// Récupérer les dernières commandes
$dernieresCommandes = array_slice($commandeModel->getAll(), 0, 5);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>🏢 ABI</h2>
                <p>Gestion Commerciale</p>
            </div>
            <nav class="sidebar-menu">
                <?php foreach ($menuItems as $item): ?>
                    <?php if ($item['active']): ?>
                        <a href="<?php echo $item['url']; ?>" class="<?php echo basename($_SERVER['PHP_SELF']) == basename($item['url']) ? 'active' : ''; ?>">
                            <span><?php echo $item['icon']; ?></span> <?php echo $item['label']; ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
                <a href="logout.php">
                    <span>🚪</span> Déconnexion
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <div class="header">
                <h1>Tableau de Bord</h1>
                <div class="user-info">
                    <div>
                        <strong><?php echo htmlspecialchars($user['prenom'] . ' ' . $user['nom']); ?></strong><br>
                        <small style="color: #64748b;"><?php echo htmlspecialchars($user['role']); ?></small>
                    </div>
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1)); ?>
                    </div>
                </div>
            </div>
            
            <!-- Flash Messages -->
            <?php foreach (obtenirFlash() as $flash): ?>
                <div class="alert alert-<?php echo $flash['type']; ?>">
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endforeach; ?>
            
            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stat-card" style="border-left-color: #2563eb;">
                    <h4>Total Clients</h4>
                    <div class="stat-value"><?php echo $totalClients; ?></div>
                </div>
                
                <div class="stat-card" style="border-left-color: #7c3aed;">
                    <h4>Total Produits</h4>
                    <div class="stat-value"><?php echo $totalProduits; ?></div>
                </div>
                
                <div class="stat-card" style="border-left-color: #10b981;">
                    <h4>Commandes</h4>
                    <div class="stat-value"><?php echo $statsCommandes['total_commandes'] ?? 0; ?></div>
                </div>
                
                <div class="stat-card" style="border-left-color: #f59e0b;">
                    <h4>Chiffre d'Affaires</h4>
                    <div class="stat-value" style="font-size: 1.5em;">
                        <?php echo formaterMontant($statsCommandes['montant_total'] ?? 0); ?>
                    </div>
                </div>
            </div>
            
            <!-- Recent Orders -->
            <div class="card">
                <div class="card-header">
                    <h3>Dernières Commandes</h3>
                    <a href="pages/commandes.php" class="btn btn-primary btn-sm">Voir Tout</a>
                </div>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Client</th>
                                <th>Date</th>
                                <th>Montant</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($dernieresCommandes)): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 30px; color: #94a3b8;">
                                        Aucune commande trouvée
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($dernieresCommandes as $commande): ?>
                                    <tr>
                                        <td><strong>#<?php echo $commande['id']; ?></strong></td>
                                        <td>
                                            <?php 
                                            if ($commande['client_entreprise']) {
                                                echo htmlspecialchars($commande['client_entreprise']);
                                            } else {
                                                echo htmlspecialchars($commande['client_prenom'] . ' ' . $commande['client_nom']);
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo formaterDate($commande['date_commande'], 'd/m/Y H:i'); ?></td>
                                        <td><strong><?php echo formaterMontant($commande['montant_total']); ?></strong></td>
                                        <td><?php echo badgeStatut($commande['statut']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Quick Stats -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="card">
                    <h3 style="margin-bottom: 15px; color: var(--dark-color);">Statut des Commandes</h3>
                    <div style="display: flex; flex-direction: column; gap: 10px;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span>En attente</span>
                            <span class="badge badge-warning"><?php echo $statsCommandes['en_attente'] ?? 0; ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span>En cours</span>
                            <span class="badge badge-info"><?php echo $statsCommandes['en_cours'] ?? 0; ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span>Livrées</span>
                            <span class="badge badge-success"><?php echo $statsCommandes['livrees'] ?? 0; ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <h3 style="margin-bottom: 15px; color: var(--dark-color);">Projets Actifs</h3>
                    <div class="stat-value" style="color: var(--secondary-color);"><?php echo $totalProjets; ?></div>
                    <p style="color: var(--text-color); margin-top: 10px;">projets en cours de développement</p>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
