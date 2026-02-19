<?php
require_once '../../config/config.php';
require_once '../../classes/Database.php';
require_once '../../classes/User.php';
require_once '../../classes/Projet.php';
require_once '../../classes/Permission.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireAuth();
requirePermission('view_projects');
$user = utilisateurConnecte();
$permission = getPermission();

$projetModel = new Projet();
$projets = $projetModel->getAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Projets - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>🏢 ABI</h2>
                <p>Gestion Commerciale</p>
            </div>
            <nav class="sidebar-menu">
                <a href="../dashboard.php"><span>📊</span> Tableau de Bord</a>
                <a href="clients.php"><span>👥</span> Clients</a>
                <a href="produits.php"><span>📦</span> Produits</a>
                <a href="commandes.php"><span>🛒</span> Commandes</a>
                <a href="projets.php" class="active"><span>💼</span> Projets</a>
                <a href="../logout.php"><span>🚪</span> Déconnexion</a>
            </nav>
        </aside>
        
        <main class="main-content">
            <div class="header">
                <h1>Gestion des Projets</h1>
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
            
            <div class="card">
                <div class="card-header">
                    <h3>Liste des Projets (<?php echo count($projets); ?>)</h3>
                </div>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom du Projet</th>
                                <th>Client</th>
                                <th>Responsable</th>
                                <th>Dates</th>
                                <th>Budget</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($projets)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 30px;">Aucun projet trouvé</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($projets as $projet): ?>
                                    <tr>
                                        <td><strong>#<?php echo $projet['id']; ?></strong></td>
                                        <td><?php echo htmlspecialchars($projet['nom']); ?></td>
                                        <td>
                                            <?php 
                                            if ($projet['client_entreprise']) {
                                                echo htmlspecialchars($projet['client_entreprise']);
                                            } elseif ($projet['client_nom']) {
                                                echo htmlspecialchars($projet['client_prenom'] . ' ' . $projet['client_nom']);
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($projet['responsable_prenom'] . ' ' . $projet['responsable_nom']); ?></td>
                                        <td>
                                            <?php 
                                            if ($projet['date_debut'] && $projet['date_fin']) {
                                                echo formaterDate($projet['date_debut']) . ' - ' . formaterDate($projet['date_fin']);
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </td>
                                        <td><?php echo $projet['budget'] ? formaterMontant($projet['budget']) : '-'; ?></td>
                                        <td><?php echo badgeStatut($projet['statut']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
