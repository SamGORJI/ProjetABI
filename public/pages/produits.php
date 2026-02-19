<?php
require_once '../../config/config.php';
require_once '../../classes/Database.php';
require_once '../../classes/User.php';
require_once '../../classes/Produit.php';
require_once '../../classes/Permission.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireAuth();
requirePermission('view_products');
$user = utilisateurConnecte();
$permission = getPermission();

$produitModel = new Produit();

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $data = [
            'nom' => nettoyer($_POST['nom']),
            'description' => nettoyer($_POST['description'] ?? ''),
            'prix' => (float)$_POST['prix'],
            'categorie' => nettoyer($_POST['categorie'] ?? ''),
            'stock' => (int)($_POST['stock'] ?? 0)
        ];
        
        if ($produitModel->create($data)) {
            ajouterFlash('success', 'Produit créé avec succès !');
            header('Location: produits.php');
            exit;
        }
    }
    
    if ($action === 'update') {
        $id = (int)$_POST['id'];
        $data = [
            'nom' => nettoyer($_POST['nom']),
            'description' => nettoyer($_POST['description'] ?? ''),
            'prix' => (float)$_POST['prix'],
            'categorie' => nettoyer($_POST['categorie'] ?? ''),
            'stock' => (int)($_POST['stock'] ?? 0)
        ];
        
        if ($produitModel->update($id, $data)) {
            ajouterFlash('success', 'Produit modifié avec succès !');
            header('Location: produits.php');
            exit;
        }
    }
    
    if ($action === 'delete') {
        $id = (int)$_POST['id'];
        if ($produitModel->delete($id)) {
            ajouterFlash('success', 'Produit supprimé avec succès !');
        }
        header('Location: produits.php');
        exit;
    }
}

$produits = $produitModel->getAll();
$produitEdit = null;
if (isset($_GET['edit'])) {
    $produitEdit = $produitModel->getById((int)$_GET['edit']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Produits - <?php echo APP_NAME; ?></title>
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
                <a href="produits.php" class="active"><span>📦</span> Produits</a>
                <a href="commandes.php"><span>🛒</span> Commandes</a>
                <a href="projets.php"><span>💼</span> Projets</a>
                <a href="../logout.php"><span>🚪</span> Déconnexion</a>
            </nav>
        </aside>
        
        <main class="main-content">
            <div class="header">
                <h1>Gestion des Produits</h1>
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
                    <h3><?php echo $produitEdit ? 'Modifier le Produit' : 'Nouveau Produit'; ?></h3>
                </div>
                
                <form method="POST">
                    <input type="hidden" name="action" value="<?php echo $produitEdit ? 'update' : 'create'; ?>">
                    <?php if ($produitEdit): ?>
                        <input type="hidden" name="id" value="<?php echo $produitEdit['id']; ?>">
                    <?php endif; ?>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                        <div class="form-group">
                            <label for="nom">Nom du Produit *</label>
                            <input type="text" id="nom" name="nom" class="form-control" 
                                   value="<?php echo htmlspecialchars($produitEdit['nom'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="categorie">Catégorie</label>
                            <input type="text" id="categorie" name="categorie" class="form-control" 
                                   value="<?php echo htmlspecialchars($produitEdit['categorie'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="prix">Prix (€) *</label>
                            <input type="number" step="0.01" id="prix" name="prix" class="form-control" 
                                   value="<?php echo htmlspecialchars($produitEdit['prix'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="stock">Stock</label>
                            <input type="number" id="stock" name="stock" class="form-control" 
                                   value="<?php echo htmlspecialchars($produitEdit['stock'] ?? 0); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-control" rows="3"><?php echo htmlspecialchars($produitEdit['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-primary">
                            <?php echo $produitEdit ? 'Modifier' : 'Créer'; ?>
                        </button>
                        <?php if ($produitEdit): ?>
                            <a href="produits.php" class="btn btn-secondary">Annuler</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>Liste des Produits (<?php echo count($produits); ?>)</h3>
                </div>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Catégorie</th>
                                <th>Prix</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($produits)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 30px;">Aucun produit trouvé</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($produits as $produit): ?>
                                    <tr>
                                        <td><strong>#<?php echo $produit['id']; ?></strong></td>
                                        <td><?php echo htmlspecialchars($produit['nom']); ?></td>
                                        <td><?php echo htmlspecialchars($produit['categorie'] ?? '-'); ?></td>
                                        <td><strong><?php echo formaterMontant($produit['prix']); ?></strong></td>
                                        <td><?php echo $produit['stock']; ?></td>
                                        <td>
                                            <a href="?edit=<?php echo $produit['id']; ?>" class="btn btn-sm btn-primary">Modifier</a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Supprimer ce produit ?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $produit['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
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
