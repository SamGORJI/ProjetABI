<?php
require_once '../../config/config.php';
require_once '../../classes/Database.php';
require_once '../../classes/User.php';
require_once '../../classes/Commande.php';
require_once '../../classes/Permission.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireAuth();
requirePermission('view_orders');
$user = utilisateurConnecte();
$permission = getPermission();

$commandeModel = new Commande();
$commandes = $commandeModel->getAll();

// Traitement du changement de statut
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_statut') {
        $id = (int)$_POST['id'];
        $statut = nettoyer($_POST['statut']);
        
        if ($commandeModel->updateStatut($id, $statut)) {
            ajouterFlash('success', 'Statut mis à jour avec succès !');
            header('Location: commandes.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Commandes - <?php echo APP_NAME; ?></title>
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
                <a href="commandes.php" class="active"><span>🛒</span> Commandes</a>
                <a href="projets.php"><span>💼</span> Projets</a>
                <a href="../logout.php"><span>🚪</span> Déconnexion</a>
            </nav>
        </aside>
        
        <main class="main-content">
            <div class="header">
                <h1>Gestion des Commandes</h1>
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
            
            <?php foreach (obtenirFlash() as $flash): ?>
                <div class="alert alert-<?php echo $flash['type']; ?>">
                    <?php echo htmlspecialchars($flash['message']); ?>
                </div>
            <?php endforeach; ?>
            
            <div class="card">
                <div class="card-header">
                    <h3>Liste des Commandes (<?php echo count($commandes); ?>)</h3>
                </div>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Client</th>
                                <th>Commercial</th>
                                <th>Date</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($commandes)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 30px;">Aucune commande trouvée</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($commandes as $commande): ?>
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
                                        <td><?php echo htmlspecialchars($commande['utilisateur_prenom'] . ' ' . $commande['utilisateur_nom']); ?></td>
                                        <td><?php echo formaterDate($commande['date_commande'], 'd/m/Y H:i'); ?></td>
                                        <td><strong><?php echo formaterMontant($commande['montant_total']); ?></strong></td>
                                        <td><?php echo badgeStatut($commande['statut']); ?></td>
                                        <td>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="update_statut">
                                                <input type="hidden" name="id" value="<?php echo $commande['id']; ?>">
                                                <select name="statut" class="form-control" style="width: auto; display: inline-block; padding: 5px;" onchange="this.form.submit()">
                                                    <option value="">Changer statut...</option>
                                                    <option value="En attente">En attente</option>
                                                    <option value="En cours">En cours</option>
                                                    <option value="Livree">Livrée</option>
                                                    <option value="Annulee">Annulée</option>
                                                </select>
                                            </form>
                                        </td>
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
