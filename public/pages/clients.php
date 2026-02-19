<?php
require_once '../../config/config.php';
require_once '../../classes/Database.php';
require_once '../../classes/User.php';
require_once '../../classes/Client.php';
require_once '../../classes/Permission.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireAuth();
requirePermission('view_clients');
$user = utilisateurConnecte();
$permission = getPermission();

$clientModel = new Client();
$message = '';
$messageType = '';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create' && $permission->canCreate('clients')) {
        $data = [
            'nom' => nettoyer($_POST['nom']),
            'prenom' => nettoyer($_POST['prenom']),
            'entreprise' => nettoyer($_POST['entreprise'] ?? ''),
            'email' => nettoyer($_POST['email']),
            'telephone' => nettoyer($_POST['telephone'] ?? ''),
            'adresse' => nettoyer($_POST['adresse'] ?? ''),
            'ville' => nettoyer($_POST['ville'] ?? ''),
            'code_postal' => nettoyer($_POST['code_postal'] ?? ''),
            'pays' => nettoyer($_POST['pays'] ?? 'France')
        ];
        
        if ($clientModel->create($data)) {
            ajouterFlash('success', 'Client créé avec succès !');
            header('Location: clients.php');
            exit;
        } else {
            $message = 'Erreur lors de la création du client.';
            $messageType = 'error';
        }
    } elseif ($action === 'create') {
        ajouterFlash('error', 'Vous n\'avez pas la permission de créer des clients.');
        header('Location: clients.php');
        exit;
    }
    
    if ($action === 'update' && $permission->canEdit('clients')) {
        $id = (int)$_POST['id'];
        $data = [
            'nom' => nettoyer($_POST['nom']),
            'prenom' => nettoyer($_POST['prenom']),
            'entreprise' => nettoyer($_POST['entreprise'] ?? ''),
            'email' => nettoyer($_POST['email']),
            'telephone' => nettoyer($_POST['telephone'] ?? ''),
            'adresse' => nettoyer($_POST['adresse'] ?? ''),
            'ville' => nettoyer($_POST['ville'] ?? ''),
            'code_postal' => nettoyer($_POST['code_postal'] ?? ''),
            'pays' => nettoyer($_POST['pays'] ?? 'France')
        ];
        
        if ($clientModel->update($id, $data)) {
            ajouterFlash('success', 'Client modifié avec succès !');
            header('Location: clients.php');
            exit;
        } else {
            $message = 'Erreur lors de la modification du client.';
            $messageType = 'error';
        }
    } elseif ($action === 'update') {
        ajouterFlash('error', 'Vous n\'avez pas la permission de modifier des clients.');
        header('Location: clients.php');
        exit;
    }
    
    if ($action === 'delete' && $permission->canDelete('clients')) {
        $id = (int)$_POST['id'];
        if ($clientModel->delete($id)) {
            ajouterFlash('success', 'Client supprimé avec succès !');
        } else {
            ajouterFlash('error', 'Erreur lors de la suppression du client.');
        }
        header('Location: clients.php');
        exit;
    } elseif ($action === 'delete') {
        ajouterFlash('error', 'Vous n\'avez pas la permission de supprimer des clients.');
        header('Location: clients.php');
        exit;
    }
}

// Récupérer tous les clients
$clients = $clientModel->getAll();

// Récupérer un client pour modification
$clientEdit = null;
if (isset($_GET['edit'])) {
    $clientEdit = $clientModel->getById((int)$_GET['edit']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Clients - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="../../assets/css/style.css">
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
                <a href="../dashboard.php">
                    <span>📊</span> Tableau de Bord
                </a>
                <a href="clients.php" class="active">
                    <span>👥</span> Clients
                </a>
                <a href="produits.php">
                    <span>📦</span> Produits
                </a>
                <a href="commandes.php">
                    <span>🛒</span> Commandes
                </a>
                <a href="projets.php">
                    <span>💼</span> Projets
                </a>
                <a href="../logout.php">
                    <span>🚪</span> Déconnexion
                </a>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <div class="header">
                <h1>Gestion des Clients</h1>
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
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <!-- Form -->
            <?php if ($permission->canCreate('clients') || ($clientEdit && $permission->canEdit('clients'))): ?>
            <div class="card">
                <div class="card-header">
                    <h3><?php echo $clientEdit ? 'Modifier le Client' : 'Nouveau Client'; ?></h3>
                </div>
                
                <form method="POST" action="">
                    <input type="hidden" name="action" value="<?php echo $clientEdit ? 'update' : 'create'; ?>">
                    <?php if ($clientEdit): ?>
                        <input type="hidden" name="id" value="<?php echo $clientEdit['id']; ?>">
                    <?php endif; ?>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                        <div class="form-group">
                            <label for="nom">Nom *</label>
                            <input type="text" id="nom" name="nom" class="form-control" 
                                   value="<?php echo htmlspecialchars($clientEdit['nom'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="prenom">Prénom *</label>
                            <input type="text" id="prenom" name="prenom" class="form-control" 
                                   value="<?php echo htmlspecialchars($clientEdit['prenom'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="entreprise">Entreprise</label>
                            <input type="text" id="entreprise" name="entreprise" class="form-control" 
                                   value="<?php echo htmlspecialchars($clientEdit['entreprise'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" class="form-control" 
                                   value="<?php echo htmlspecialchars($clientEdit['email'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="telephone">Téléphone</label>
                            <input type="tel" id="telephone" name="telephone" class="form-control" 
                                   value="<?php echo htmlspecialchars($clientEdit['telephone'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="ville">Ville</label>
                            <input type="text" id="ville" name="ville" class="form-control" 
                                   value="<?php echo htmlspecialchars($clientEdit['ville'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="code_postal">Code Postal</label>
                            <input type="text" id="code_postal" name="code_postal" class="form-control" 
                                   value="<?php echo htmlspecialchars($clientEdit['code_postal'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="pays">Pays</label>
                            <input type="text" id="pays" name="pays" class="form-control" 
                                   value="<?php echo htmlspecialchars($clientEdit['pays'] ?? 'France'); ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="adresse">Adresse Complète</label>
                        <textarea id="adresse" name="adresse" class="form-control" rows="2"><?php echo htmlspecialchars($clientEdit['adresse'] ?? ''); ?></textarea>
                    </div>
                    
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="btn btn-primary">
                            <?php echo $clientEdit ? 'Modifier' : 'Créer'; ?>
                        </button>
                        <?php if ($clientEdit): ?>
                            <a href="clients.php" class="btn btn-secondary">Annuler</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            <?php endif; ?>
            
            <!-- Liste des clients -->
            <div class="card">
                <div class="card-header">
                    <h3>Liste des Clients (<?php echo count($clients); ?>)</h3>
                </div>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom</th>
                                <th>Entreprise</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Ville</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($clients)): ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 30px;">
                                        Aucun client trouvé
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($clients as $client): ?>
                                    <tr>
                                        <td><strong>#<?php echo $client['id']; ?></strong></td>
                                        <td><?php echo htmlspecialchars($client['prenom'] . ' ' . $client['nom']); ?></td>
                                        <td><?php echo htmlspecialchars($client['entreprise'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($client['email']); ?></td>
                                        <td><?php echo htmlspecialchars($client['telephone'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($client['ville'] ?? '-'); ?></td>
                                        <td>
                                            <?php if ($permission->canEdit('clients')): ?>
                                                <a href="?edit=<?php echo $client['id']; ?>" class="btn btn-sm btn-primary">Modifier</a>
                                            <?php endif; ?>
                                            <?php if ($permission->canDelete('clients')): ?>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $client['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                                                </form>
                                            <?php endif; ?>
                                            <?php if (!$permission->canEdit('clients') && !$permission->canDelete('clients')): ?>
                                                <span style="color: #94a3b8;">Lecture seule</span>
                                            <?php endif; ?>
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
