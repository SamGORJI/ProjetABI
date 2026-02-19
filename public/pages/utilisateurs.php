<?php
require_once '../../config/config.php';
require_once '../../classes/Database.php';
require_once '../../classes/User.php';
require_once '../../classes/Permission.php';
require_once '../../includes/auth.php';
require_once '../../includes/functions.php';

requireAuth();
requirePermission('view_users');
$user = utilisateurConnecte();
$permission = getPermission();

$userModel = new User();
$message = '';
$messageType = '';

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create' && $permission->canCreate('users')) {
        $data = [
            'nom' => nettoyer($_POST['nom']),
            'prenom' => nettoyer($_POST['prenom']),
            'email' => nettoyer($_POST['email']),
            'password' => $_POST['password'],
            'role' => nettoyer($_POST['role'])
        ];
        
        if ($userModel->create($data)) {
            ajouterFlash('success', 'Utilisateur créé avec succès !');
            header('Location: utilisateurs.php');
            exit;
        } else {
            ajouterFlash('error', 'Erreur lors de la création de l\'utilisateur.');
        }
    }
    
    if ($action === 'update' && $permission->canEdit('users')) {
        $id = (int)$_POST['id'];
        $data = [
            'nom' => nettoyer($_POST['nom']),
            'prenom' => nettoyer($_POST['prenom']),
            'email' => nettoyer($_POST['email']),
            'role' => nettoyer($_POST['role'])
        ];
        
        // فقط اگر رمز جدید وارد شده باشد
        if (!empty($_POST['password'])) {
            $data['password'] = $_POST['password'];
        }
        
        if ($userModel->update($id, $data)) {
            ajouterFlash('success', 'Utilisateur modifié avec succès !');
            header('Location: utilisateurs.php');
            exit;
        } else {
            ajouterFlash('error', 'Erreur lors de la modification.');
        }
    }
    
    if ($action === 'delete' && $permission->canDelete('users')) {
        $id = (int)$_POST['id'];
        if ($id !== $user['id']) { // نمی‌توان خودش را حذف کند
            if ($userModel->delete($id)) {
                ajouterFlash('success', 'Utilisateur supprimé avec succès !');
            } else {
                ajouterFlash('error', 'Erreur lors de la suppression.');
            }
        } else {
            ajouterFlash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }
        header('Location: utilisateurs.php');
        exit;
    }
}

$users = $userModel->getAll();
$userEdit = null;
if (isset($_GET['edit'])) {
    $userEdit = $userModel->getById((int)$_GET['edit']);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs - <?php echo APP_NAME; ?></title>
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
                <?php 
                $menuItems = $permission->getMenuItems();
                foreach ($menuItems as $item): 
                    if ($item['active']):
                ?>
                    <a href="<?php echo $item['url']; ?>" class="<?php echo basename($_SERVER['PHP_SELF']) == basename($item['url']) ? 'active' : ''; ?>">
                        <span><?php echo $item['icon']; ?></span> <?php echo $item['label']; ?>
                    </a>
                <?php 
                    endif;
                endforeach; 
                ?>
                <a href="../logout.php"><span>🚪</span> Déconnexion</a>
            </nav>
        </aside>
        
        <main class="main-content">
            <div class="header">
                <h1>Gestion des Utilisateurs</h1>
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
            
            <?php if ($permission->canCreate('users')): ?>
            <div class="card">
                <div class="card-header">
                    <h3><?php echo $userEdit ? 'Modifier l\'Utilisateur' : 'Nouvel Utilisateur'; ?></h3>
                </div>
                
                <form method="POST">
                    <input type="hidden" name="action" value="<?php echo $userEdit ? 'update' : 'create'; ?>">
                    <?php if ($userEdit): ?>
                        <input type="hidden" name="id" value="<?php echo $userEdit['id']; ?>">
                    <?php endif; ?>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
                        <div class="form-group">
                            <label for="prenom">Prénom *</label>
                            <input type="text" id="prenom" name="prenom" class="form-control" 
                                   value="<?php echo htmlspecialchars($userEdit['prenom'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="nom">Nom *</label>
                            <input type="text" id="nom" name="nom" class="form-control" 
                                   value="<?php echo htmlspecialchars($userEdit['nom'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email *</label>
                            <input type="email" id="email" name="email" class="form-control" 
                                   value="<?php echo htmlspecialchars($userEdit['email'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="role">Rôle *</label>
                            <select id="role" name="role" class="form-control" required>
                                <option value="">Sélectionner...</option>
                                <option value="Admin" <?php echo ($userEdit['role'] ?? '') === 'Admin' ? 'selected' : ''; ?>>Admin</option>
                                <option value="Commercial" <?php echo ($userEdit['role'] ?? '') === 'Commercial' ? 'selected' : ''; ?>>Commercial</option>
                                <option value="RH" <?php echo ($userEdit['role'] ?? '') === 'RH' ? 'selected' : ''; ?>>RH</option>
                                <option value="Développeur" <?php echo ($userEdit['role'] ?? '') === 'Développeur' ? 'selected' : ''; ?>>Développeur</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Mot de passe <?php echo $userEdit ? '(laisser vide pour ne pas changer)' : '*'; ?></label>
                            <input type="password" id="password" name="password" class="form-control" 
                                   <?php echo $userEdit ? '' : 'required'; ?>>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 10px; margin-top: 20px;">
                        <button type="submit" class="btn btn-primary">
                            <?php echo $userEdit ? 'Modifier' : 'Créer'; ?>
                        </button>
                        <?php if ($userEdit): ?>
                            <a href="utilisateurs.php" class="btn btn-secondary">Annuler</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header">
                    <h3>Liste des Utilisateurs (<?php echo count($users); ?>)</h3>
                </div>
                
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nom Complet</th>
                                <th>Email</th>
                                <th>Rôle</th>
                                <th>Date Création</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($users)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 30px;">Aucun utilisateur trouvé</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($users as $u): ?>
                                    <tr>
                                        <td><strong>#<?php echo $u['id']; ?></strong></td>
                                        <td><?php echo htmlspecialchars($u['prenom'] . ' ' . $u['nom']); ?></td>
                                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                                        <td>
                                            <span class="badge" style="background: <?php 
                                                echo $u['role'] === 'Admin' ? '#10b981' : 
                                                    ($u['role'] === 'Commercial' ? '#3b82f6' : 
                                                    ($u['role'] === 'RH' ? '#f59e0b' : '#8b5cf6')); 
                                            ?>">
                                                <?php echo htmlspecialchars($u['role']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo formaterDate($u['created_at'] ?? date('Y-m-d')); ?></td>
                                        <td>
                                            <?php if ($permission->canEdit('users')): ?>
                                                <a href="?edit=<?php echo $u['id']; ?>" class="btn btn-sm btn-primary">Modifier</a>
                                            <?php endif; ?>
                                            
                                            <?php if ($permission->canDelete('users') && $u['id'] !== $user['id']): ?>
                                                <form method="POST" style="display: inline;" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
                                                </form>
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
